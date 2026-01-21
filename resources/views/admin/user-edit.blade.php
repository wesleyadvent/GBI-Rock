@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4 mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.user.index') }}">User</a></li>
                <li class="breadcrumb-item active">Edit User</li>
            </ol>
        </nav>
        <h2 class="mb-1 fw-bold">Edit User</h2>
        <p class="text-muted mb-0">Update informasi pengguna: <strong>{{ $user->nama }}</strong></p>
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
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Informasi User</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.user.update', $user->id_user) }}" method="POST" id="editUserForm">
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
                                       value="{{ old('nama', $user->nama) }}"
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
                                       value="{{ old('email', $user->email) }}"
                                       required>
                            </div>
                            @error('email')
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
                                    @foreach ($roles as $key => $label)
                                        <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bidang (conditional) -->
                        <div class="mb-4" id="bidangWrapper" style="{{ in_array(old('role', $user->role), ['pekerja','koordinator_bidang']) ? '' : 'display:none;' }}">
                            <label class="form-label fw-medium">
                                Bidang <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-briefcase"></i>
                                </span>
                                <select name="id_bidang" class="form-select border-start-0 @error('id_bidang') is-invalid @enderror">
                                    <option value="">-- Pilih Bidang --</option>
                                    @foreach ($bidang as $id => $namaB)
                                        <option value="{{ $id }}" {{ old('id_bidang', $user->id_bidang) == $id ? 'selected' : '' }}>
                                            {{ $namaB }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @error('id_bidang')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.user.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Update Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-key me-2"></i>Ubah Password
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.user.updatePassword', $user->id_user) }}" method="POST" id="passwordForm">
                        @csrf

                        <div class="alert alert-info border-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <small>Password baru akan langsung mengganti password lama. Kosongkan jika tidak ingin mengubah password.</small>
                        </div>

                        <!-- New Password -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Password Baru
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       id="newPassword"
                                       class="form-control border-start-0 border-end-0 @error('password') is-invalid @enderror" 
                                       placeholder="Masukkan password baru">
                                <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                    <i class="bi bi-eye" id="eyeIconNew"></i>
                                </button>
                            </div>
                            <div class="form-text">Minimal 6 karakter</div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Konfirmasi Password Baru
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="confirmPassword"
                                       class="form-control border-start-0 border-end-0" 
                                       placeholder="Konfirmasi password baru">
                                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                    <i class="bi bi-eye" id="eyeIconConfirm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Button -->
                        <div class="pt-3 border-top">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-key me-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- User Info Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-person-circle me-2"></i>Info User
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3 pb-3 border-bottom">
                        <div class="avatar-large bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                            {{ strtoupper(substr($user->nama, 0, 2)) }}
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $user->nama }}</h5>
                        <p class="text-muted small mb-2">{{ $user->email }}</p>
                        @if($user->status_aktif)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-danger">Nonaktif</span>
                        @endif
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">ID User:</small>
                        <div class="fw-medium">#{{ $user->id_user }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Role:</small>
                        <div class="fw-medium">{{ ucfirst(str_replace('_', ' ', $user->role)) }}</div>
                    </div>
                    
                    @if($user->id_bidang)
                    <div class="mb-2">
                        <small class="text-muted">Bidang:</small>
                        <div class="fw-medium">{{ $bidang[$user->id_bidang] }}</div>
                    </div>
                    @endif
                    
                    <div class="mb-0">
                        <small class="text-muted">Dibuat:</small>
                        <div class="fw-medium">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-gear me-2"></i>Aksi Lainnya
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.user.toggle', $user->id_user) }}" 
                           class="btn btn-outline-{{ $user->status_aktif ? 'warning' : 'success' }}">
                            <i class="bi bi-toggle-{{ $user->status_aktif ? 'on' : 'off' }} me-2"></i>
                            {{ $user->status_aktif ? 'Nonaktifkan User' : 'Aktifkan User' }}
                        </a>
                        
                        <a href="{{ route('admin.user.delete', $user->id_user) }}"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $user->nama }}? Tindakan ini tidak dapat dibatalkan!')"
                           class="btn btn-outline-danger">
                            <i class="bi bi-trash me-2"></i>Hapus User
                        </a>
                    </div>
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
    
    .avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 32px;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Toggle new password visibility
    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('newPassword');
        const eyeIcon = document.getElementById('eyeIconNew');
        
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

    // Toggle confirm password visibility
    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('confirmPassword');
        const eyeIcon = document.getElementById('eyeIconConfirm');
        
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
        } else {
            bidangWrapper.style.display = 'none';
        }
    });
</script>
@endsection