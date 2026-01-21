@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4 mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">User</a></li>
                <li class="breadcrumb-item active">Buat User Baru</li>
            </ol>
        </nav>
        <h2 class="mb-1 fw-bold">Buat Akun Baru</h2>
        <p class="text-muted mb-0">Tambahkan pengguna baru ke sistem</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Alert Success -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Alert Errors -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Terjadi kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Form Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Informasi User</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.user.store') }}" method="POST" id="createUserForm">
                        @csrf

                        <!-- Nama -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" 
                                       name="nama" 
                                       class="form-control border-start-0 @error('nama') is-invalid @enderror" 
                                       placeholder="Masukkan nama lengkap"
                                       value="{{ old('nama') }}"
                                       required>
                            </div>
                            @error('nama')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Email <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-envelope"></i>
                                </span>
                                <input type="email" 
                                       name="email" 
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       placeholder="contoh@email.com"
                                       value="{{ old('email') }}"
                                       required>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                                       placeholder="Minimal 6 karakter"
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Password minimal 6 karakter</div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Role <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <select name="role" 
                                        id="roleSelect" 
                                        class="form-select border-start-0 @error('role') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Pilih Role --</option>
                                    @foreach ($roles as $key => $value)
                                        <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bidang (conditional) -->
                        <div class="mb-4" id="bidangWrapper" style="display: none;">
                            <label class="form-label fw-medium">
                                Pilih Bidang <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-briefcase"></i>
                                </span>
                                <select name="id_bidang" class="form-select border-start-0 @error('id_bidang') is-invalid @enderror">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach ($bidang as $id => $namaBidang)
                                        <option value="{{ $id }}" {{ old('id_bidang') == $id ? 'selected' : '' }}>
                                            {{ $namaBidang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-text">Bidang wajib diisi untuk role Pekerja dan Koordinator Bidang</div>
                            @error('id_bidang')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Simpan User
                            </button>
                            <a href="{{ route('admin.user.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>Informasi
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Penjelasan Role:</h6>
                    <div class="mb-3">
                        <span class="badge bg-danger-subtle text-danger mb-2">Admin</span>
                        <p class="small text-muted mb-0">Akses penuh ke seluruh sistem</p>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-primary-subtle text-primary mb-2">Sekretaris</span>
                        <p class="small text-muted mb-0">Mengelola administrasi dan jadwal</p>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-warning-subtle text-warning mb-2">Koordinator Bidang</span>
                        <p class="small text-muted mb-0">Mengelola bidang pelayanan tertentu</p>
                    </div>
                    <div class="mb-3">
                        <span class="badge bg-info-subtle text-info mb-2">Penatua</span>
                        <p class="small text-muted mb-0">Melihat dan menyetujui jadwal</p>
                    </div>
                    <div class="mb-0">
                        <span class="badge bg-secondary-subtle text-secondary mb-2">Pekerja</span>
                        <p class="small text-muted mb-0">Melakukan pelayanan sesuai bidang</p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-lightbulb text-warning me-2"></i>Tips
                    </h6>
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Gunakan email yang valid dan aktif</li>
                        <li class="mb-2">Password akan di-hash secara otomatis</li>
                        <li class="mb-2">User baru akan langsung dalam status aktif</li>
                        <li class="mb-0">Bidang hanya untuk Pekerja dan Koordinator</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ExtraCSS')
<style>
    .card {
        border-radius: 12px;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    
    .input-group-text {
        border: 1px solid #dee2e6;
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
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('bi-eye');
            eyeIcon.classList.add('bi-eye-slash');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('bi-eye-slash');
            eyeIcon.classList.add('bi-eye');
        }
    });

    // Show/hide bidang based on role
    const roleSelect = document.getElementById('roleSelect');
    const bidangWrapper = document.getElementById('bidangWrapper');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'pekerja' || this.value === 'koordinator_bidang') {
            bidangWrapper.style.display = 'block';
            bidangWrapper.querySelector('select').required = true;
        } else {
            bidangWrapper.style.display = 'none';
            bidangWrapper.querySelector('select').required = false;
        }
    });

    // Trigger on page load if old value exists
    if (roleSelect.value === 'pekerja' || roleSelect.value === 'koordinator_bidang') {
        bidangWrapper.style.display = 'block';
        bidangWrapper.querySelector('select').required = true;
    }
</script>
@endsection