<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\JadwalKebaktian;
use App\Models\Tugas;
use App\Models\KategoriPenolakan;
use App\Models\JadwalKebaktianHistory;
use App\Models\PengajuanJadwal;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;

class PenatuaTest extends TestCase
{
    use RefreshDatabase;

    protected $penatua;
    protected $jadwal;
    protected $kategori;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->id('id_user');
                $table->string('nama');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('jadwal_kebaktian')) {
            Schema::create('jadwal_kebaktian', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->date('tanggal_pelayanan');
                $table->string('status')->default('draft');
                $table->unsignedBigInteger('disetujui_oleh')->nullable();
                $table->text('alasan_penolakan')->nullable();
                $table->unsignedBigInteger('kategori_penolakan_id')->nullable();
                $table->unsignedBigInteger('asal_jadwal')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('kategori_penolakan')) {
            Schema::create('kategori_penolakan', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->json('dampak');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('jadwal_kebaktian_history')) {
            Schema::create('jadwal_kebaktian_history', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_jadwal');
                $table->string('status');
                $table->text('alasan')->nullable();
                $table->unsignedBigInteger('oleh_user');
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
                $table->string('status_pengajuan');
                $table->text('alasan_penolakan')->nullable();
                $table->timestamps();
            });
        }


        $this->penatua = new User();
        $this->penatua->nama = 'Penatua Utama';
        $this->penatua->email = 'penatua@test.com';
        $this->penatua->password = Hash::make('password');
        $this->penatua->role = 'penatua';
        $this->penatua->save();

        $this->jadwal = new JadwalKebaktian();
        $this->jadwal->tanggal_pelayanan = now()->addDays(3)->format('Y-m-d');
        $this->jadwal->status = 'pending';
        $this->jadwal->save();

        $this->kategori = new KategoriPenolakan();
        $this->kategori->nama = 'Ubah Waktu';
        $this->kategori->dampak = ['tugas' => 'pending', 'jadwal' => 'edit'];
        $this->kategori->save();
    }


    #[Test]
    public function penatua_berhasil_menyetujui_jadwal()
    {
        $this->actingAs($this->penatua);

        $response = $this->post(route('penatua.jadwal.approve', $this->jadwal->id_jadwal));

        $response->assertSessionHas('success', 'Jadwal berhasil disetujui.');
        
        $updatedJadwal = $this->jadwal->fresh();
        $this->assertEquals('approved', $updatedJadwal->status);
        $this->assertEquals($this->penatua->id_user, $updatedJadwal->disetujui_oleh);

        $this->assertDatabaseHas('jadwal_kebaktian_history', [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'status' => 'approved',
            'oleh_user' => $this->penatua->id_user
        ]);
    }

    #[Test]
    public function penatua_berhasil_menolak_jadwal_dan_pekerja_diminta_konfirmasi_ulang()
    {
        $this->actingAs($this->penatua);

        $tugas = new Tugas();
        $tugas->id_jadwal = $this->jadwal->id_jadwal;
        $tugas->id_user = 99;
        $tugas->peran_tugas = 'Usher';
        $tugas->status_tugas = 'approved';
        $tugas->save();

        $response = $this->post(route('penatua.jadwal.reject', $this->jadwal->id_jadwal), [
            'kategori_penolakan_id' => $this->kategori->id,
            'alasan_penolakan' => 'Waktu bentrok dengan rapat'
        ]);

        $response->assertSessionHas('success', 'Jadwal berhasil ditolak.');

        $this->assertEquals('declined', $this->jadwal->fresh()->status);

        $this->assertEquals('declined', $tugas->fresh()->status_tugas);
    }

    #[Test]
    public function penatua_berhasil_menolak_jadwal_dan_memberikan_alasan()
    {
        $this->actingAs($this->penatua);

        $kategoriRecreate = new KategoriPenolakan();
        $kategoriRecreate->nama = 'Alasan Lain';
        $kategoriRecreate->dampak = ['tugas' => 'reset', 'jadwal' => 'recreate'];
        $kategoriRecreate->save();

        $response = $this->post(route('penatua.jadwal.reject', $this->jadwal->id_jadwal), [
            'kategori_penolakan_id' => $kategoriRecreate->id,
            'alasan_penolakan' => 'Jadwal harus diulang total'
        ]);

        $this->assertDatabaseHas('jadwal_kebaktian', [
            'status' => 'draft',
            'asal_jadwal' => $this->jadwal->id_jadwal
        ]);
    }

    #[Test]
    public function penatua_gagal_memproses_jadwal_yang_statusnya_bukan_pending()
    {
        $this->actingAs($this->penatua);

        $this->jadwal->update(['status' => 'draft']);

        $response = $this->post(route('penatua.jadwal.approve', $this->jadwal->id_jadwal));

        $response->assertSessionHas('error', 'Jadwal tidak dapat diproses.');
        $this->assertEquals('draft', $this->jadwal->fresh()->status);
    }
}