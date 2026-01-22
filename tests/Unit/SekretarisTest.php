<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bidang;
use App\Models\JadwalKebaktian;
use App\Models\Tugas;
use App\Models\PengajuanJadwal;
use App\Models\Notifikasi;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;

class SekretarisTest extends TestCase
{
    use RefreshDatabase;

    protected $sekretaris;
    protected $pekerja;
    protected $koordinator;
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
                $table->unsignedBigInteger('id_bidang')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('jadwal_kebaktian')) {
            Schema::create('jadwal_kebaktian', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->date('tanggal_pelayanan');
                $table->time('waktu_mulai');
                $table->time('waktu_selesai');
                $table->string('jenis_kebaktian');
                $table->string('status')->default('draft');
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
                $table->unsignedBigInteger('id_jadwal');
                $table->unsignedBigInteger('id_koordinator');
                $table->unsignedBigInteger('id_bidang');
                $table->string('status_pengajuan')->default('pending');
                $table->text('alasan_penolakan')->nullable();
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

        if (!Schema::hasTable('pembicara_eksternal')) {
            Schema::create('pembicara_eksternal', function (Blueprint $table) {
                $table->id('id_pembicara');
                $table->string('nama_pembicara');
                $table->string('asal_gereja')->nullable();
                $table->unsignedBigInteger('id_jadwal');
                $table->timestamps();
            });
        }


        $this->bidang = Bidang::create([
            'id_bidang' => 2, 
            'nama_bidang' => 'Pembicara'
        ]);

        $this->sekretaris = User::create([
            'nama' => 'Sekretaris Utama',
            'email' => 'sekretaris@test.com',
            'password' => Hash::make('password'),
            'role' => 'sekretaris'
        ]);

        $this->koordinator = User::create([
            'nama' => 'Koordinator Test',
            'email' => 'koor@test.com',
            'password' => Hash::make('password'),
            'role' => 'koordinator_bidang',
            'id_bidang' => 1
        ]);

        $this->pekerja = User::create([
            'nama' => 'Pekerja Pembicara',
            'email' => 'pekerja@test.com',
            'password' => Hash::make('password'),
            'role' => 'pekerja',
            'id_bidang' => 2
        ]);

        $this->jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->addDays(1)->format('Y-m-d'),
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00',
            'jenis_kebaktian' => 'Kebaktian Umum 1',
            'status' => 'draft'
        ]);
    }


    #[Test]
    public function sekretaris_berhasil_assign_pekerja_langsung()
    {
        $this->actingAs($this->sekretaris);

        $response = $this->post(route('sekretaris.pengajuan.assign'), [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Pengkhotbah Utama'
        ]);

        $response->assertSessionHas('success', 'Pekerja berhasil diajukan!');
        $this->assertDatabaseHas('tugas', [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'status_tugas' => 'pending'
        ]);
    }

    #[Test]
    public function sekretaris_berhasil_approve_pengajuan_koordinator()
    {
        $this->actingAs($this->sekretaris);

        $koor = User::create([
            'nama' => 'Koor Usher', 'email' => 'k@t.com', 'password' => 'p', 
            'role' => 'koordinator_bidang', 'id_bidang' => 1
        ]);

        PengajuanJadwal::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_koordinator' => $koor->id_user,
            'id_bidang' => 1,
            'status_pengajuan' => 'pending'
        ]);

        $response = $this->post(route('sekretaris.pengajuan.approveBidang'), [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_bidang' => 1
        ]);

        $response->assertSessionHas('success', 'Pengajuan bidang berhasil disetujui.');
        $this->assertDatabaseHas('pengajuan_jadwal', [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'status_pengajuan' => 'approved'
        ]);
    }

    #[Test]
    public function sekretaris_gagal_ajukan_ke_penatua_jika_aturan_min_pekerja_tidak_terpenuhi()
    {
        $this->actingAs($this->sekretaris);

        $response = $this->post(route('sekretaris.pengajuan.approve'), [
            'id_jadwal' => $this->jadwal->id_jadwal
        ]);

        $response->assertSessionHas('error');

        $this->assertEquals('draft', $this->jadwal->fresh()->status);
    }

    #[Test]
    public function sekretaris_berhasil_decline_koordinator_dengan_alasan()
    {
        $this->actingAs($this->sekretaris);

        $koor = User::create([
            'nama' => 'Koor PW', 'email' => 'pw@t.com', 'password' => 'p', 
            'role' => 'koordinator_bidang', 'id_bidang' => 4
        ]);

        PengajuanJadwal::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_koordinator' => $koor->id_user,
            'id_bidang' => 4,
            'status_pengajuan' => 'pending'
        ]);

        $response = $this->post(route('sekretaris.pengajuan.declineBidang'), [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_bidang' => 4,
            'alasan_penolakan' => 'Data personil tidak lengkap'
        ]);

        $response->assertSessionHas('success', 'Pengajuan bidang berhasil ditolak.');
        $this->assertDatabaseHas('pengajuan_jadwal', [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'status_pengajuan' => 'declined',
            'alasan_penolakan' => 'Data personil tidak lengkap'
        ]);
    }

    #[Test]
    public function sekretaris_berhasil_menghapus_tugas_yang_masih_pending()
    {
        $this->actingAs($this->sekretaris);

        $tugas = Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Pendoa',
            'status_tugas' => 'pending'
        ]);

        $response = $this->delete(route('sekretaris.pengajuan.batal', $tugas->id_tugas));

        $response->assertSessionHas('success', 'Pengajuan pelayanan berhasil dibatalkan.');
        $this->assertDatabaseMissing('tugas', ['id_tugas' => $tugas->id_tugas]);
    }

    #[Test]
    public function cek_apakah_sekretaris_bisa_menolak_satu_pengajuan_koordinator_saja()
    {
        $this->actingAs($this->sekretaris);

        $pengajuan = PengajuanJadwal::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_koordinator' => $this->koordinator->id_user,
            'id_bidang' => 1,
            'status_pengajuan' => 'pending'
        ]);

        $response = $this->post(route('sekretaris.pengajuan.declinePengajuan'), [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'alasan_penolakan' => 'Personil kurang lengkap'
        ]);

        $response->assertSessionHas('success', 'Pengajuan berhasil ditolak.');

        $this->assertDatabaseHas('pengajuan_jadwal', [
            'id_pengajuan' => $pengajuan->id_pengajuan,
            'status_pengajuan' => 'declined',
            'alasan_penolakan' => 'Personil kurang lengkap'
        ]);

        $this->assertDatabaseHas('notifikasi', [
            'id_user' => $this->koordinator->id_user,
            'pesan' => 'Pengajuan jadwal pelayanan ditolak. Alasan: Personil kurang lengkap',
            'tipe' => 'pengajuan_ditolak'
        ]);
    }
}