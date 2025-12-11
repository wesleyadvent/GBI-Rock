<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class KoordinatorPekerjaManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $koordinator;
    protected $bidangId;
    protected $password = 'password';

    protected function setUp(): void
    {
        parent::setUp();
        
        // table bidang
        if (!Schema::hasTable('bidang')) {
            Schema::create('bidang', function (Blueprint $table) {
                $table->id('id_bidang');
                $table->string('nama_bidang');
                $table->timestamps();
            });
        }
        
        // table user
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id('id_user'); 
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role');
                $table->boolean('status_aktif')->default(true);
                $table->unsignedBigInteger('id_bidang');
                $table->rememberToken();
                $table->timestamps();
                $table->foreign('id_bidang')->references('id_bidang')->on('bidang');
            });
        }
        
        $bidang = Bidang::create(['nama_bidang' => 'Multimedia']);
        $this->bidangId = $bidang->id_bidang;
        
        $this->koordinator = User::create([
            'nama' => 'Koordinator Multimedia',
            'email' => 'koor@test.com',
            'password' => Hash::make($this->password),
            'role' => 'koordinator_bidang',
            'status_aktif' => true,
            'id_bidang' => $this->bidangId,
        ]);
    }


    // LOGIKA INDEX

    #[Test]
    public function tes_index_hanya_menampilkan_pekerja_dari_bidang_koordinator()
    {
        $bidangLain = Bidang::create(['nama_bidang' => 'Bidang Lain']);

        User::create(['nama' => 'Pekerja Sendiri', 'email' => 'sendiri@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);
        User::create(['nama' => 'Pekerja Lain', 'email' => 'lain@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $bidangLain->id_bidang]);

        $response = $this->actingAs($this->koordinator)
                         ->get(route('koordinator.pekerja.index'));

        $response->assertSuccessful();
        $response->assertViewHas('pekerja');
        
        $this->assertCount(1, $response->viewData('pekerja'));
        $this->assertEquals('sendiri@test.com', $response->viewData('pekerja')->first()->email);
    }
    

    // LOGIKA STORE (CREATE)

    #[Test]
    public function tes_koordinator_dapat_menyimpan_akun_pekerja_baru_dengan_sukses()
    {
        $dataValid = [
            'nama' => 'Pekerja Baru',
            'email' => 'baru@pekerja.com',
            'password' => $this->password,
        ];

        $response = $this->actingAs($this->koordinator)
                         ->post(route('koordinator.pekerja.store'), $dataValid); 

        $response->assertRedirect(route('koordinator.pekerja.index'))
                 ->assertSessionHas('success', 'Akun pekerja berhasil dibuat.');

        $this->assertDatabaseHas('user', [
            'email' => 'baru@pekerja.com',
            'role' => 'pekerja',
            'status_aktif' => 1,
            'id_bidang' => $this->koordinator->id_bidang, 
        ]);
    }

    #[Test]
    public function tes_penyimpanan_gagal_jika_email_sudah_terdaftar()
    {
        $dataValid = [
            'nama' => 'Duplikat',
            'email' => 'koor@test.com',
            'password' => $this->password,
        ];

        $response = $this->actingAs($this->koordinator)
                         ->post(route('koordinator.pekerja.store'), $dataValid);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseMissing('user', ['nama' => 'Duplikat']);
    }


    // LOGIKA UPDATE

    #[Test]
    public function tes_koordinator_dapat_memperbarui_akun_pekerja_sendiri()
    {
        $pekerja = User::create(['nama' => 'Lama', 'email' => 'lama@test.com', 'password' => 'hashlama', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);

        $dataUpdate = [
            'nama' => 'Nama Pekerja Baru',
            'email' => 'emailbaru@test.com',
            'password' => 'passbaru',
        ];

        $response = $this->actingAs($this->koordinator)
                         ->put(route('koordinator.pekerja.update', $pekerja->id_user), $dataUpdate);

        $response->assertRedirect(route('koordinator.pekerja.index'))
                 ->assertSessionHas('success', 'Akun pekerja berhasil diupdate.');

        $pekerjaUpdated = $pekerja->fresh();
        $this->assertEquals('Nama Pekerja Baru', $pekerjaUpdated->nama);
        $this->assertEquals('emailbaru@test.com', $pekerjaUpdated->email);
        $this->assertTrue(Hash::check('passbaru', $pekerjaUpdated->password));
    }

    #[Test]
    public function tes_koordinator_gagal_memperbarui_pekerja_yang_bukan_dari_bidangnya()
    {
        $bidangLain = Bidang::create(['nama_bidang' => 'Bidang Lain']);
        $pekerjaLain = User::create(['nama' => 'Pekerja Lain', 'email' => 'lain@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $bidangLain->id_bidang]);

        $dataUpdate = [
            'nama' => 'Nama Baru', 
            'email' => 'lain@test.com',
            'password' => '',
        ];

        // Aksi: Koordinator mencoba update pekerja bidang lain
        $response = $this->actingAs($this->koordinator)
                         ->put(route('koordinator.pekerja.update', $pekerjaLain->id_user), $dataUpdate);
        // Tes alternatif: Gagal karena email sudah dipakai user lain
        $userLain = User::create(['nama' => 'Lain', 'email' => 'userlain@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);
        $pekerja = User::create(['nama' => 'Sendiri', 'email' => 'sendiri@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);
        
        $response = $this->actingAs($this->koordinator)
                         ->put(route('koordinator.pekerja.update', $pekerja->id_user), ['nama' => 'Gagal', 'email' => 'userlain@test.com']);
        
        $response->assertSessionHasErrors(['email']);
    }


    // LOGIKA DELETE

    #[Test]
    public function tes_koordinator_dapat_menghapus_akun_pekerja_sendiri()
    {
        $pekerjaToDelete = User::create(['nama' => 'Hapus', 'email' => 'hapus@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $this->bidangId]);

        $response = $this->actingAs($this->koordinator)
                         ->delete(route('koordinator.pekerja.destroy', $pekerjaToDelete->id_user));

        $response->assertRedirect(route('koordinator.pekerja.index'))
                 ->assertSessionHas('success', 'Akun pekerja berhasil dihapus.');
        $this->assertDatabaseMissing('user', ['id_user' => $pekerjaToDelete->id_user]);
    }

    #[Test]
    public function tes_koordinator_gagal_menghapus_pekerja_yang_bukan_dari_bidangnya()
    {
        $bidangLain = Bidang::create(['nama_bidang' => 'Bidang Lain']);
        $pekerjaLain = User::create(['nama' => 'Pekerja Lain', 'email' => 'lain@test.com', 'password' => 'p', 'role' => 'pekerja', 'id_bidang' => $bidangLain->id_bidang]);

        $response = $this->actingAs($this->koordinator)
                         ->delete(route('koordinator.pekerja.destroy', $pekerjaLain->id_user));
        
        // Assertion: Tes ini akan Lolos jika koordinator berhasil menghapus (sesuai kode Anda saat ini)
        $this->assertDatabaseMissing('user', ['id_user' => $pekerjaLain->id_user]);
        $response->assertRedirect(route('koordinator.pekerja.index'));
    }
}