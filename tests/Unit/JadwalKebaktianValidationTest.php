<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\JadwalKebaktian;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;

class JadwalKebaktianValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // table user
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id('id_user');
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role')->default('sekretaris');
                $table->boolean('status_aktif')->default(true);
                $table->unsignedBigInteger('id_bidang')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // table jadwal_kebaktian
        if (!Schema::hasTable('jadwal_kebaktian')) {
            Schema::create('jadwal_kebaktian', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->date('tanggal_pelayanan');
                $table->string('jenis_kebaktian', 50);
                $table->time('waktu_mulai');
                $table->time('waktu_selesai');
                $table->string('lokasi', 100)->nullable();
                $table->string('tema', 255)->nullable();
                $table->string('status')->default('draft');
                $table->foreignId('dibuat_oleh')->constrained('user', 'id_user');
                $table->foreignId('disetujui_oleh')->nullable()->constrained('user', 'id_user');
                $table->timestamps();
            });
        }

        $this->user = User::create([
            'nama' => 'Sekretaris',
            'email' => 'sekretaris@example.test',
            'password' => Hash::make('password'),
            'role' => 'sekretaris',
            'status_aktif' => true,
        ]);
    }

    
    #[Test]
    public function sekretaris_dapat_melihat_jadwal()
    {
        $jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->toDateString(),
            'jenis_kebaktian' => 'Kebaktian Pagi',
            'waktu_mulai' => '07:00',
            'waktu_selesai' => '09:00',
            'lokasi' => 'Aula Utama',
            'tema' => 'Tema Pagi',
            'status' => 'draft',
            'dibuat_oleh' => $this->user->id_user,
        ]);

        $response = $this->actingAs($this->user)
                        ->get(route('sekretaris.jadwal.index'));

        $response->assertStatus(200);

        $response->assertSee('id="calendar"', false);

        $response->assertSee($jadwal->jenis_kebaktian);
    }


    #[Test]
    public function tes_pengguna_dapat_menyimpan_jadwal_baru()
    {
        $validData = [
            'tanggal_pelayanan' => now()->addDay()->toDateString(),
            'jenis_kebaktian' => 'Kebaktian Minggu Raya',
            'waktu_mulai' => '09:00',
            'waktu_selesai' => '11:00',
            'lokasi' => 'Gereja Induk',
            'tema' => 'Kasihi Sesama',
        ];

        $response = $this->actingAs($this->user)
                         ->post(route('sekretaris.jadwal.store'), $validData);

        $response->assertRedirect(route('sekretaris.jadwal.index'))
                 ->assertSessionHas('success', 'Jadwal kebaktian berhasil dibuat.');

        $this->assertDatabaseHas('jadwal_kebaktian', array_merge($validData, [
            'status' => 'draft',
            'dibuat_oleh' => $this->user->id_user,
        ]));
    }


    #[Test]
    public function sekretaris_dapat_melihat_detail_jadwal()
    {
        $jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->toDateString(),
            'jenis_kebaktian' => 'Kebaktian Malam',
            'waktu_mulai' => '18:00',
            'waktu_selesai' => '20:00',
            'lokasi' => 'Aula Baru',
            'tema' => 'Tema Malam',
            'status' => 'draft',
            'dibuat_oleh' => $this->user->id_user,
        ]);

        $response = $this->actingAs($this->user)
                        ->get('/sekretaris/jadwal/detail/' . $jadwal->id_jadwal);

        $response->assertStatus(200);

        $response->assertJson([
            'id_jadwal' => $jadwal->id_jadwal,
            'jenis_kebaktian' => 'Kebaktian Malam',
            'lokasi' => 'Aula Baru',
            'tema' => 'Tema Malam',
            'status' => 'draft',
        ]);
    }


    #[Test]
    public function tes_pengguna_dapat_memperbarui_jadwal()
    {
        $jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->toDateString(),
            'jenis_kebaktian' => 'Lama',
            'waktu_mulai' => '07:00',
            'waktu_selesai' => '09:00',
            'lokasi' => 'Aula Lama',
            'tema' => 'Tema Lama',
            'status' => 'draft',
            'dibuat_oleh' => $this->user->id_user,
        ]);

        $newData = [
            'tanggal_pelayanan' => now()->addDays(5)->toDateString(),
            'jenis_kebaktian' => 'Kebaktian Malam',
            'waktu_mulai' => '18:00',
            'waktu_selesai' => '20:00',
            'lokasi' => 'Aula Baru',
            'tema' => 'Malam Penuh Rahmat',
        ];

        $response = $this->actingAs($this->user)
                         ->put(route('sekretaris.jadwal.update', $jadwal->id_jadwal), $newData);

        $response->assertRedirect(route('sekretaris.jadwal.index'))
                 ->assertSessionHas('success', 'Jadwal kebaktian berhasil diperbarui.');

        $this->assertDatabaseHas('jadwal_kebaktian', array_merge($newData, [
            'id_jadwal' => $jadwal->id_jadwal,
            'dibuat_oleh' => $this->user->id_user,
        ]));
    }

    #[Test]
    public function tes_pengguna_dapat_menghapus_jadwal()
    {
        $jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->toDateString(),
            'jenis_kebaktian' => 'Kebaktian Siang',
            'waktu_mulai' => '12:00',
            'waktu_selesai' => '13:00',
            'lokasi' => 'Ruang Utama',
            'tema' => 'Tema Siang',
            'status' => 'draft',
            'dibuat_oleh' => $this->user->id_user,
        ]);

        $response = $this->actingAs($this->user)
                         ->delete(route('sekretaris.jadwal.delete', $jadwal->id_jadwal));

        $response->assertRedirect(route('sekretaris.jadwal.index'))
                 ->assertSessionHas('success', 'Jadwal berhasil dihapus');

        $this->assertDatabaseMissing('jadwal_kebaktian', [
            'id_jadwal' => $jadwal->id_jadwal
        ]);
    }
}