@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('koordinator.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('koordinator.pekerja.index') }}">Daftar Pekerja</a></li>
            <li class="breadcrumb-item active">Buat Akun Pekerja</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>Buat Akun Pekerja Baru
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading mb-2">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi Kesalahan!
                            </h6>
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('koordinator.pekerja.store') }}" method="POST" id="formPekerja">
                        @csrf

                        <!-- Info Box -->
                        <div class="alert alert-info border-0 mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Informasi:</strong> Pekerja yang dibuat akan otomatis terdaftar dalam bidang 
                            <strong>{{ $bidang }}</strong>
                        </div>

                        <div class="row g-3">
                            <!-- Nama Pekerja -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Nama Lengkap Pekerja <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        name="nama" 
                                        class="form-control @error('nama') is-invalid @enderror" 
                                        placeholder="Masukkan nama lengkap pekerja"
                                        value="{{ old('nama') }}"
                                        required
                                    >
                                </div>
                                <small class="text-muted">Contoh: John Doe</small>
                                @error('nama')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Email Pekerja <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-envelope"></i>
                                    </span>
                                    <input 
                                        type="email" 
                                        name="email" 
                                        class="form-control @error('email') is-invalid @enderror" 
                                        placeholder="contoh@email.com"
                                        value="{{ old('email') }}"
                                        required
                                    >
                                </div>
                                <small class="text-muted">Email akan digunakan untuk login</small>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input 
                                        type="password" 
                                        name="password" 
                                        id="password"
                                        class="form-control @error('password') is-invalid @enderror" 
                                        placeholder="Minimal 6 karakter"
                                        required
                                    >
                                    <button 
                                        class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                    >
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Minimal 6 karakter, disarankan kombinasi huruf dan angka</small>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bidang (Readonly) -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Bidang Pelayanan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="bi bi-briefcase"></i>
                                    </span>
                                    <input 
                                        type="text" 
                                        class="form-control bg-light" 
                                        value="{{ $bidang }}" 
                                        readonly
                                    >
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-lock me-1"></i>Bidang otomatis mengikuti bidang koordinator
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('koordinator.pekerja.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Buat Akun Pekerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-question-circle text-info me-2"></i>Bantuan
                    </h6>
                    <ul class="small text-muted mb-0">
                        <li class="mb-2">Pastikan email yang dimasukkan valid dan belum terdaftar</li>
                        <li class="mb-2">Password minimal 6 karakter untuk keamanan akun</li>
                        <li class="mb-2">Pekerja baru akan otomatis masuk dalam bidang Anda</li>
                        <li>Pekerja dapat mengubah password mereka setelah login pertama kali</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ExtraCSS')
<style>
    .form-control:focus,
    .input-group-text {
        border-color: #0d6efd;
    }
    .input-group-text {
        min-width: 45px;
        justify-content: center;
    }
    .card {
        border-radius: 10px;
    }
    .form-label {
        margin-bottom: 0.5rem;
        color: #344767;
    }
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const password = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (password.type === 'password') {
            password.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            password.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Form validation
    document.getElementById('formPekerja').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter!');
            return false;
        }
    });
</script>
@endsection