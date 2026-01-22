<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bidang;
use App\Models\JadwalKebaktian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class JadwalKebaktianControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $sekretaris;
    protected $kategoriId;

    protected function setUp(): void
    {
        parent::setUp();

        // =========================
        // TABLE BIDANG
        // =========================
        if (!Schema::hasTable('bidang')) {
            Schema::create('bidang', function (Blueprint $table) {
                $table->id('id_bidang');
                $table->string('nama_bidang');
                $table->timestamps();
            });
        }

        // =========================
        // TABLE USER
        // =========================
        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id('id_user');
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role');
                $table->boolean('status_aktif')->default(true);
                $table->unsignedBigInteger('id_bidang')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // =========================
        // TABLE JADWAL KEBAKTIAN
        // =========================
        if (!Schema::hasTable('jadwal_kebaktian')) {
            Schema::create('jadwal_kebaktian', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->date('tanggal_pelayanan');
                $table->string('jenis_kebaktian');
                $table->time('waktu_mulai');
                $table->time('waktu_selesai');
                $table->string('lokasi')->nullable();
                $table->string('tema')->nullable();
                $table->string('status')->default('draft');
                $table->unsignedBigInteger('dibuat_oleh');
                $table->unsignedBigInteger('disetujui_oleh')->nullable();
                $table->unsignedBigInteger('kategori_penolakan_id')->nullable();
                $table->unsignedBigInteger('asal_jadwal')->nullable();
                $table->string('alasan_penolakan')->nullable();
                $table->timestamps();
            });
        }

        // =========================
        // TABLE KATEGORI PENOLOKAN
        // =========================
        if (!Schema::hasTable('kategori_penolakan')) {
            Schema::create('kategori_penolakan', function (Blueprint $table) {
                $table->id();
                $table->string('nama')->nullable();
                $table->text('dampak')->nullable();
                $table->timestamps();
            });
        }

        // =========================
        // TABLE PENGAJUAN JADWAL
        // =========================
        if (!Schema::hasTable('pengajuan_jadwal')) {
            Schema::create('pengajuan_jadwal', function (Blueprint $table) {
                $table->id('id_pengajuan');
                $table->unsignedBigInteger('id_koordinator')->nullable();
                $table->unsignedBigInteger('id_jadwal');
                $table->unsignedBigInteger('id_bidang')->nullable();
                $table->string('status_pengajuan')->nullable();
                $table->string('alasan_penolakan')->nullable();
                $table->timestamp('tanggal_pengajuan')->nullable();
            });
        }

        // =========================
        // TABLE TUGAS
        // =========================
        if (!Schema::hasTable('tugas')) {
            Schema::create('tugas', function (Blueprint $table) {
                $table->id('id_tugas');
                $table->unsignedBigInteger('id_jadwal');
                $table->unsignedBigInteger('id_user')->nullable();
                $table->string('peran_tugas')->nullable();
                $table->string('status_tugas')->nullable();
                $table->string('alasan_penolakan')->nullable();
            });
        }

        // =========================
        // DATA SEKRETARIS
        // =========================
        $this->sekretaris = User::create([
            'nama'     => 'Sekretaris',
            'email'    => 'sekre@test.com',
            'password' => Hash::make('password'),
            'role'     => 'sekretaris',
            'status_aktif' => true,
        ]);

        $this->kategoriId = \DB::table('kategori_penolakan')->insertGetId([
            'nama' => 'Ubah Waktu/Tema',
            'dampak' => json_encode(['jadwal' => 'edit', 'tugas' => 'keep']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    // =====================================================
    // INDEX
    // =====================================================

    #[Test]
    public function sekretaris_dapat_melihat_halaman_index_jadwal()
    {
        $response = $this->actingAs($this->sekretaris)
            ->get(route('sekretaris.jadwal.index'));

        $response->assertSuccessful();
        $response->assertViewHas('calendarGrid');
        $response->assertViewHas('currentDate');
    }

    // =====================================================
    // STORE
    // =====================================================

    #[Test]
    public function sekretaris_dapat_membuat_jadwal_kebaktian_baru()
    {
        $data = [
            'tanggal_pelayanan' => now()->addDays(7)->toDateString(),
            'jenis_kebaktian'   => 'Ibadah Raya',
            'waktu_mulai'       => '09:00',
            'waktu_selesai'     => '11:00',
            'lokasi'            => 'GBI Rock',
            'tema'              => 'Iman dan Pengharapan',
        ];

        $response = $this->actingAs($this->sekretaris)
            ->post(route('sekretaris.jadwal.store'), $data);

        $response->assertRedirect(route('sekretaris.jadwal.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jadwal_kebaktian', [
            'jenis_kebaktian' => 'Ibadah Raya',
            'status' => 'draft',
        ]);
    }

    #[Test]
    public function gagal_menyimpan_jadwal_jika_data_wajib_kosong()
    {
        $response = $this->actingAs($this->sekretaris)
            ->post(route('sekretaris.jadwal.store'), []);

        $response->assertSessionHasErrors([
            'tanggal_pelayanan',
            'jenis_kebaktian',
            'waktu_mulai',
            'waktu_selesai',
        ]);
    }

    // =====================================================
    // UPDATE
    // =====================================================

    #[Test]
    public function sekretaris_dapat_memperbarui_jadwal_kebaktian()
    {
        $jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->addDays(3),
            'jenis_kebaktian'   => 'Ibadah Lama',
            'waktu_mulai'       => '08:00',
            'waktu_selesai'     => '10:00',
            'status'            => 'draft',
            'dibuat_oleh'       => $this->sekretaris->id_user,
            'kategori_penolakan_id' => $this->kategoriId,
        ]);

        $dataUpdate = [
            'tanggal_pelayanan' => now()->addDays(4)->toDateString(),
            'jenis_kebaktian'   => 'Ibadah Baru',
            'waktu_mulai'       => '09:00',
            'waktu_selesai'     => '11:00',
            'lokasi'            => 'Gedung Baru',
            'tema'              => 'Pemulihan',
        ];

        $response = $this->actingAs($this->sekretaris)
            ->put(route('sekretaris.jadwal.update', $jadwal->id_jadwal), $dataUpdate);

        $response->assertRedirect(route('sekretaris.jadwal.index'));

        $this->assertDatabaseHas('jadwal_kebaktian', [
            'id_jadwal' => $jadwal->id_jadwal,
            'jenis_kebaktian' => 'Ibadah Baru',
            'lokasi' => 'Gedung Baru',
        ]);
    }

    // =====================================================
    // DELETE
    // =====================================================

    #[Test]
    public function sekretaris_dapat_menghapus_jadwal_kebaktian()
    {
        $jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->addDays(2),
            'jenis_kebaktian'   => 'Ibadah Test',
            'waktu_mulai'       => '07:00',
            'waktu_selesai'     => '09:00',
            'status'            => 'draft',
            'dibuat_oleh'       => $this->sekretaris->id_user,
        ]);

        $response = $this->actingAs($this->sekretaris)
            ->delete(route('sekretaris.jadwal.delete', $jadwal->id_jadwal));

        $response->assertRedirect(route('sekretaris.jadwal.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('jadwal_kebaktian', [
            'id_jadwal' => $jadwal->id_jadwal
        ]);
    }
}
