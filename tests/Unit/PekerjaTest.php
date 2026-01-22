<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\JadwalKebaktian;
use App\Models\Tugas;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;

class PekerjaTest extends TestCase
{
    use RefreshDatabase;

    protected $pekerja;
    protected $pekerjaLain;
    protected $jadwal;

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
                $table->unsignedBigInteger('id_bidang')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('jadwal_kebaktian')) {
            Schema::create('jadwal_kebaktian', function (Blueprint $table) {
                $table->id('id_jadwal');
                $table->date('tanggal_pelayanan');
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
                $table->text('alasan_penolakan')->nullable();
                $table->timestamps();
            });
        }

        $this->pekerja = User::create([
            'nama' => 'Pekerja Aktif',
            'email' => 'pekerja@test.com',
            'password' => Hash::make('password'),
            'role' => 'pekerja'
        ]);

        $this->pekerjaLain = User::create([
            'nama' => 'Pekerja Lain',
            'email' => 'lain@test.com',
            'password' => Hash::make('password'),
            'role' => 'pekerja'
        ]);

        $this->jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->addDays(2)->format('Y-m-d'),
            'status' => 'draft'
        ]);
    }

    #[Test]
    public function pekerja_berhasil_menerima_permintaan_pelayanan()
    {
        $this->actingAs($this->pekerja);

        $tugas = Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Usher',
            'status_tugas' => 'pending'
        ]);

        $response = $this->post(route('pekerja.konfirmasi', $tugas->id_tugas), [
            'aksi' => 'terima'
        ]);

        $response->assertSessionHas('success', 'Anda telah menyetujui pelayanan.');
        $this->assertEquals('approved', $tugas->fresh()->status_tugas);
    }

    #[Test]
    public function pekerja_berhasil_menolak_permintaan_pelayanan_dengan_alasan()
    {
        $this->actingAs($this->pekerja);

        $tugas = Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Multimedia',
            'status_tugas' => 'pending'
        ]);

        $response = $this->post(route('pekerja.konfirmasi', $tugas->id_tugas), [
            'aksi' => 'tolak',
            'alasan' => 'Ada urusan keluarga'
        ]);

        $response->assertSessionHas('success', 'Anda telah menolak pelayanan.');
        $this->assertEquals('declined', $tugas->fresh()->status_tugas);
        $this->assertEquals('Ada urusan keluarga', $tugas->fresh()->alasan_penolakan);
    }

    #[Test]
    public function pekerja_gagal_menolak_tanpa_mengisi_alasan()
    {
        $this->actingAs($this->pekerja);

        $tugas = Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $this->pekerja->id_user,
            'peran_tugas' => 'Singer',
            'status_tugas' => 'pending'
        ]);

        // Mencoba menolak tanpa field 'alasan'
        $response = $this->post(route('pekerja.konfirmasi', $tugas->id_tugas), [
            'aksi' => 'tolak',
            'alasan' => '' 
        ]);

        $response->assertSessionHasErrors(['alasan']);
        // Status tugas tidak boleh berubah jadi declined
        $this->assertEquals('pending', $tugas->fresh()->status_tugas);
    }
}