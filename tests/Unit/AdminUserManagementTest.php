<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $password = 'password';
    protected $bidangId;

    protected function setUp(): void
    {
        parent::setUp();
        // table user
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id('id_user'); 
                $table->string('nama');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('role');
                $table->boolean('status_aktif')->default(true);
                $table->unsignedBigInteger('id_bidang');
                $table->rememberToken();
                $table->timestamps();
            });
        }
        
        // table bidang
        if (!Schema::hasTable('bidang')) {
            Schema::create('bidang', function (Blueprint $table) {
                $table->id('id_bidang');
                $table->string('nama_bidang');
                $table->string('deskripsi')->nullable();
                $table->timestamps();
            });
        }
        
        if (Schema::hasTable('user') && !Schema::hasColumn('user', 'id_bidang') && Schema::hasTable('bidang')) {
             Schema::table('user', function (Blueprint $table) {
                 $table->foreign('id_bidang')->references('id_bidang')->on('bidang');
             });
        }

        $bidang = Bidang::create(['nama_bidang' => 'Usher', 'deskripsi' => '']);
        $this->bidangId = $bidang->id_bidang;

        $this->admin = User::create([
            'nama' => 'Admin Utama',
            'email' => 'admin@test.com',
            'password' => Hash::make($this->password),
            'role' => 'admin',
            'status_aktif' => true,
            'id_bidang' => $this->bidangId,
        ]);
    }


    // CRUD TAMBAH USER (STORE)

    #[Test]
    public function tes_admin_dapat_menyimpan_user_baru_dengan_sukses()
    {
        $dataValid = [
            'nama' => 'Pekerja Baru',
            'email' => 'pekerja@new.com',
            'password' => $this->password,
            'role' => 'pekerja',
            'id_bidang' => $this->bidangId,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.user.store'), $dataValid);

        $response->assertSessionHas('success', 'User berhasil dibuat!');
        
        $this->assertDatabaseHas('user', [
            'email' => 'pekerja@new.com',
            'role' => 'pekerja',
            'status_aktif' => 1,
            'id_bidang' => $this->bidangId,
        ]);
    }

    // Email sudah terdaftar
    #[Test]
    public function tes_penyimpanan_gagal_jika_email_sudah_terdaftar()
    {
        $dataValid = [
            'nama' => 'Pekerja Duplikat',
            'email' => 'admin@test.com',
            'password' => $this->password,
            'role' => 'pekerja',
            'id_bidang' => $this->bidangId,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('admin.user.store'), $dataValid);

        $response->assertSessionHasErrors(['email']);
    }
    

    // CRUD UPDATE

    #[Test]
    public function tes_admin_dapat_memperbarui_user_dengan_sukses()
    {
        $userToUpdate = User::create([
            'nama' => 'Lama',
            'email' => 'lama@test.com',
            'password' => 'hashlama',
            'role' => 'sekretaris',
            'id_bidang' => $this->bidangId,
        ]);

        $dataUpdate = [
            'nama' => 'Nama Baru',
            'email' => 'baru@test.com', 
            'role' => 'penatua',
            'id_bidang' => $this->bidangId, 
        ];

        $response = $this->actingAs($this->admin)
                 ->post(route('admin.user.update', $userToUpdate->id_user), $dataUpdate);

        $response->assertRedirect(route('admin.user.index'))
                 ->assertSessionHas('success', 'User berhasil diupdate.');

        $this->assertDatabaseHas('user', [
            'id_user' => $userToUpdate->id_user,
            'email' => 'baru@test.com',
            'role' => 'penatua',
            'id_bidang' => $this->bidangId,
        ]);
    }
    
    #[Test]
    public function tes_update_gagal_jika_email_sudah_dipakai_user_lain()
    {
        $user1 = User::create(['nama' => 'User1', 'email' => 'user1@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);
        $user2 = User::create(['nama' => 'User2', 'email' => 'user2@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);

        $dataUpdate = [
            'nama' => 'User1 Baru',
            'email' => 'user2@test.com', 
            'role' => 'pekerja',
            'id_bidang' => $this->bidangId,
        ];

        $response = $this->actingAs($this->admin)
                 ->post(route('admin.user.update', $user1->id_user), $dataUpdate);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseHas('user', ['id_user' => $user1->id_user, 'email' => 'user1@test.com']);
    }

    // LOGIKA DELETE

    #[Test]
    public function tes_admin_dapat_menghapus_user_dengan_sukses()
    {
        $userToDelete = User::create(['nama' => 'Hapus', 'email' => 'hapus@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);

        $response = $this->actingAs($this->admin)
                 ->get(route('admin.user.delete', $userToDelete->id_user));

        $response->assertSessionHas('success', 'User berhasil dihapus.');
        $this->assertDatabaseMissing('user', ['id_user' => $userToDelete->id_user]);
    }
    
    // LOGIKA UBAH STATUS

    #[Test]
    public function tes_admin_dapat_mengubah_status_aktif_user()
    {
        $user = User::create(['nama' => 'Toggle', 'email' => 'toggle@test.com', 'password' => 'p', 'role' => 'pekerja', 'status_aktif' => 1, 'id_bidang' => $this->bidangId]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.user.toggle', $user->id_user));

        $response->assertSessionHas('success', 'Status user berhasil diubah.');
        $this->assertEquals(0, $user->fresh()->status_aktif);

        $response = $this->actingAs($this->admin)
                 ->get(route('admin.user.toggle', $user->id_user));

        $response->assertSessionHas('success', 'Status user berhasil diubah.');
        $this->assertEquals(1, $user->fresh()->status_aktif);
    }
}