<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Bidang;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\Attributes\Test;

class LoginValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $password = 'password';
    protected $bidangId; 

    protected $roles = [
        'admin',
        'penatua',
        'koordinator_bidang',
        'sekretaris',
        'pekerja',

    ];

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
        
        $bidang = Bidang::create(['nama_bidang' => 'Bidang Umum']);
        $this->bidangId = $bidang->id_bidang;

        foreach ($this->roles as $role) {
            User::create([
                'nama' => ucfirst($role),
                'email' => $role . '@test.com',
                'password' => Hash::make($this->password),
                'role' => ($role === 'default_user') ? 'other' : $role,
                'status_aktif' => true,
                'id_bidang' => $this->bidangId,
            ]);
        }
    }


    // LOGIKA VALIDASI
    
    #[Test]
    public function tes_pengguna_gagal_login_dengan_password_salah()
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => 'salah_dong',
        ]);

        $response->assertInvalid(['email']);
        $this->assertGuest();
    }

    #[Test]
    public function tes_pengguna_gagal_login_dengan_email_tidak_terdaftar()
    {
        $response = $this->post('/login', [
            'email' => 'tidakada@test.com',
            'password' => $this->password,
        ]);

        $response->assertInvalid(['email']);
        $this->assertGuest();
    }

    
    // ROLE-BASED REDIRECT

    #[Test]
    public function tes_admin_diarahkan_ke_dashboard_admin()
    {
        $response = $this->post('/login', [
            'email' => 'admin@test.com',
            'password' => $this->password,
        ]);
        
        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function tes_penatua_diarahkan_ke_dashboard_penatua()
    {
        $response = $this->post('/login', [
            'email' => 'penatua@test.com',
            'password' => $this->password,
        ]);
        
        $response->assertRedirect(route('penatua.dashboard'));
        $this->assertAuthenticated();
    }
    
    #[Test]
    public function tes_koordinator_diarahkan_ke_dashboard_koordinator()
    {
        $response = $this->post('/login', [
            'email' => 'koordinator_bidang@test.com',
            'password' => $this->password,
        ]);
        
        $response->assertRedirect(route('koordinator.dashboard'));
        $this->assertAuthenticated();
    }

    #[Test]
    public function tes_sekretaris_diarahkan_ke_dashboard_sekretaris()
    {
        $response = $this->post('/login', [
            'email' => 'sekretaris@test.com',
            'password' => $this->password,
        ]);
        
        $response->assertRedirect(route('sekretaris.dashboard'));
        $this->assertAuthenticated();
    }
    
    #[Test]
    public function tes_pekerja_diarahkan_ke_dashboard_pekerja()
    {
        $response = $this->post('/login', [
            'email' => 'pekerja@test.com',
            'password' => $this->password,
        ]);
        
        $response->assertRedirect(route('pekerja.dashboard'));
        $this->assertAuthenticated();
    }
}