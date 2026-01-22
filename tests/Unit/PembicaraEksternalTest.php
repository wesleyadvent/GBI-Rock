<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\JadwalKebaktian;
use App\Models\Tugas;
use App\Models\PembicaraEksternal;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class PembicaraEksternalTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
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
                $table->string('jenis_kebaktian');
                $table->time('waktu_mulai');
                $table->time('waktu_selesai');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('tugas')) {
            Schema::create('tugas', function (Blueprint $table) {
                $table->id('id_tugas');
                $table->unsignedBigInteger('id_jadwal');
                $table->unsignedBigInteger('id_user');
                $table->string('status_tugas')->default('pending');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pembicara_eksternal')) {
            Schema::create('pembicara_eksternal', function (Blueprint $table) {
                $table->id('id_pembicara');
                $table->unsignedBigInteger('id_jadwal');
                $table->string('nama_pembicara');
                $table->string('asal_gereja')->nullable();
                $table->string('kontak')->nullable();
                $table->text('keterangan')->nullable();
                $table->timestamps();
            });
        }


        $this->admin = User::create([
            'nama' => 'Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);

        $this->jadwal = JadwalKebaktian::create([
            'tanggal_pelayanan' => now()->addDays(5)->format('Y-m-d'),
            'jenis_kebaktian' => 'Ibadah Raya',
            'waktu_mulai' => '08:00:00',
            'waktu_selesai' => '10:00:00'
        ]);
    }


    #[Test]
    public function cek_apakah_bisa_tambah_pembicara_eksternal_dengan_sukses()
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('pembicara-eksternal.store'), [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'nama_pembicara' => 'Pdt. Budi',
            'asal_gereja' => 'GBI Sukses',
            'kontak' => '0812345678',
            'keterangan' => 'Tamu khusus'
        ]);

        $response->assertSessionHas('success', 'Pembicara eksternal berhasil ditambahkan');
        $this->assertDatabaseHas('pembicara_eksternal', ['nama_pembicara' => 'Pdt. Budi']);
    }

    #[Test]
    public function pastikan_gagal_tambah_pembicara_luar_kalau_sudah_ada_pembicara_dalam_gereja()
    {
        $this->actingAs($this->admin);

        $pembicaraInternal = User::create([
            'nama' => 'Pembicara Internal',
            'email' => 'internal@test.com',
            'password' => 'p',
            'role' => 'pekerja',
            'id_bidang' => 2
        ]);

        Tugas::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'id_user' => $pembicaraInternal->id_user,
            'status_tugas' => 'approved'
        ]);

        $response = $this->post(route('pembicara-eksternal.store'), [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'nama_pembicara' => 'Pdt. Luar',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('pembicara_eksternal', ['nama_pembicara' => 'Pdt. Luar']);
    }

    #[Test]
    public function cek_apakah_bisa_update_data_pembicara_eksternal()
    {
        $this->actingAs($this->admin);

        $pembicara = new PembicaraEksternal();
        $pembicara->id_jadwal = $this->jadwal->id_jadwal;
        $pembicara->nama_pembicara = 'Nama Lama';
        $pembicara->save();

        $response = $this->put(route('pembicara-eksternal.update', $pembicara->id_pembicara), [
            'nama_pembicara' => 'Nama Baru',
            'asal_gereja' => 'Gereja Baru'
        ]);

        $response->assertSessionHas('success', 'Data pembicara berhasil diperbarui');
        $this->assertEquals('Nama Baru', $pembicara->fresh()->nama_pembicara);
    }

    #[Test]
    public function pastikan_pembicara_eksternal_bisa_dihapus()
    {
        $this->actingAs($this->admin);

        $pembicara = new PembicaraEksternal();
        $pembicara->id_jadwal = $this->jadwal->id_jadwal;
        $pembicara->nama_pembicara = 'Mau Dihapus';
        $pembicara->save();

        $response = $this->delete(route('pembicara-eksternal.destroy', $pembicara->id_pembicara));

        $response->assertSessionHas('success', 'Pembicara eksternal berhasil dihapus');
        $this->assertDatabaseMissing('pembicara_eksternal', ['id_pembicara' => $pembicara->id_pembicara]);
    }

    #[Test]
    public function pastikan_gagal_tambah_kalau_jadwal_ini_sudah_punya_pembicara_luar_lain()
    {
        $this->actingAs($this->admin);

        PembicaraEksternal::create([
            'id_jadwal' => $this->jadwal->id_jadwal,
            'nama_pembicara' => 'Pembicara 1'
        ]);

        $response = $this->post(route('pembicara-eksternal.store'), [
            'id_jadwal' => $this->jadwal->id_jadwal,
            'nama_pembicara' => 'Pembicara 2'
        ]);

        $response->assertSessionHas('error', 'Jadwal ini sudah memiliki pembicara eksternal.');
    }
}