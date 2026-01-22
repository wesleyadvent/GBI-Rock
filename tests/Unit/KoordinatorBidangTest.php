<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bidang;
use App\Models\JadwalKebaktian;
use App\Models\Tugas;
use App\Models\PengajuanJadwal;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class KoordinatorBidangTest extends TestCase
{
    use RefreshDatabase;

    protected $koordinator;
    protected $pekerja;
    protected $bidang;
    protected $jadwal;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('bidang')) {
            Schema::create('bidang', function (Blueprint $table) {
                $table->id('id_bidang');
                $table->string('nama_bidang');
                $table->text('deskripsi')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id('id_user');
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role');
                $table->boolean('status_aktif')->default(true);
                $table->unsignedBigInteger('id_bidang')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('jadwal_kebaktian')) {
            Schema::create('jadwal_kebaktian', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->date('tanggal_pelayanan');
                $table->string('jenis_kebaktian');
                $table->time('waktu_mulai');
                $table->time('waktu_selesai');
                $table->string('status')->default('draft');
                $table->unsignedBigInteger('dibuat_oleh');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tugas')) {
            Schema::create('tugas', function (Blueprint $table) {
                $table->id('id_tugas');
                $table->unsignedBigInteger('id_jadwal');
                $table->unsignedBigInteger('id_user');
                $table->string('peran_tugas');
                $table->string('status_tugas')->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pengajuan_jadwal')) {
            Schema::create('pengajuan_jadwal', function (Blueprint $table) {
                $table->id('id_pengajuan');
                $table->unsignedBigInteger('id_koordinator');
                $table->unsignedBigInteger('id_jadwal');
                $table->unsignedBigInteger('id_bidang');
                $table->string('status_pengajuan')->default('pending');
                $table->timestamp('tanggal_pengajuan')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id('id_notifikasi');
                $table->unsignedBigInteger('id_user');
                $table->text('pesan');
                $table->string('tipe');
                $table->boolean('status_baca')->default(0);
                $table->timestamps();
            });
        }

        $this->bidang = Bidang::create(['id_bidang' => 1, 'nama_bidang' => 'usher']);

        $this->koordinator = User::create([
            'nama' => 'Koor Usher',
            'email' => 'koor@test.com',
            'password' => Hash::make('password'),
            'role' => 'koordinator_bidang',
            'id_bidang' => 1
        ]);

        $this->pekerja = User::create([
            'nama' => 'Pekerja 1',
            'email' => 'pekerja@test.com',
            'password' => Hash::make('password'),
            'role' => 'pekerja',
            'id_bidang' => 1
        ]);

        $this->jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->addDays(7)->format('Y-m-d'),
            'jenis_kebaktian' => 'Kebaktian Umum',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'dibuat_oleh' => $this->koordinator->id_user
        ]);
    }

    #[Test]
    public function koordinator_berhasil_assign_pekerja_ke_jadwal()
    {
        $response = $this->actingAs($this->koordinator)
            ->post(route('timPelayanan.assign'), [
                'id_jadwal' => $this->jadwal->id_jadwal,
                'id_user' => $this->pekerja->id_user,
                'peran_tugas' => 'Usher Pintu'
            ]);

        $response->assertSessionHas('success', 'Permintaan berhasil dikirim ke pekerja!');
        $this->assertDatabaseHas('tugas', ['id_user' => $this->pekerja->id_user]);
    }

    #[Test]
    public function koordinator_gagal_ajukan_ke_sekretaris_jika_pekerja_approved_kurang_dari_minimal()
    {
        Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Usher',
            'status_tugas' => 'pending'
        ]);

        $response = $this->actingAs($this->koordinator)
            ->post(route('timPelayanan.ajukanSekretaris'), [
                'id_jadwal' => $this->jadwal->id_jadwal
            ]);

        $response->assertSessionHas('error');
    }

    #[Test]
    public function koordinator_berhasil_ajukan_ke_sekretaris_jika_syarat_terpenuhi()
    {
        $pekerja2 = User::create([
            'nama' => 'Pekerja 2', 'email' => 'p2@test.com', 'password' => 'p', 
            'role' => 'pekerja', 'id_bidang' => 1
        ]);

        Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal, 'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'U1', 'status_tugas' => 'approved'
        ]);
        Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal, 'id_user' => $pekerja2->id_user,
            'peran_tugas' => 'U2', 'status_tugas' => 'approved'
        ]);

        $response = $this->actingAs($this->koordinator)
            ->post(route('timPelayanan.ajukanSekretaris'), [
                'id_jadwal' => $this->jadwal->id_jadwal
            ]);

        $response->assertSessionHas('success', 'Jadwal berhasil diajukan ke sekretaris.');
    }

    #[Test]
    public function koordinator_berhasil_menghapus_tugas_pekerja()
    {
        $tugas = Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Usher',
            'status_tugas' => 'pending'
        ]);
        $response = $this->actingAs($this->koordinator)
            ->delete(route('timPelayanan.batal', $tugas->id_tugas));

        $response->assertSessionHas('success', 'Pengajuan pelayanan berhasil dibatalkan.');
        $this->assertDatabaseMissing('tugas', ['id_tugas' => $tugas->id_tugas]);
    }
}