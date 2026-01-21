@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('koordinator.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('koordinator.pekerja.index') }}">Daftar Pekerja</a></li>
            <li class="breadcrumb-item active">Edit Akun Pekerja</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-person-fill-gear me-2"></i>Edit Akun Pekerja
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

                    <form 
                        action="{{ route('koordinator.pekerja.update', $user->id_user) }}" 
                        method="POST"
                        id="formEditPekerja"
                    >
                        @csrf
                        @method('PUT')

                        <!-- Info Pekerja -->
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                    <i class="bi bi-person-fill text-primary fs-1"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $user->nama }}</h6>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
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
                                        value="{{ old('nama', $user->nama) }}"
                                        required
                                    >
                                </div>
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
                                        value="{{ old('email', $user->email) }}"
                                        required
                                    >
                                </div>
                                <small class="text-muted">Pastikan email valid dan belum digunakan akun lain</small>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password (Optional for edit) -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">
                                    Password Baru <span class="text-muted">(Opsional)</span>
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
                                        placeholder="Kosongkan jika tidak ingin mengubah password"
                                    >
                                    <button 
                                        class="btn btn-outline-secondary" 
                                        type="button" 
                                        id="togglePassword"
                                    >
                                        <i class="bi bi-eye" id="eyeIcon"></i>
                                    </button>
                                </div>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Kosongkan jika tidak ingin mengubah password. Minimal 6 karakter jika diisi.
                                </small>
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
                                    <i class="bi bi-lock me-1"></i>Bidang tidak dapat diubah
                                </small>
                            </div>

                            <!-- Status Aktif -->
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Status Pekerja</label>
                                <div class="form-check form-switch">
                                    <input 
                                        class="form-check-input" 
                                        type="checkbox" 
                                        name="status_aktif" 
                                        id="statusAktif"
                                        value="1"
                                        {{ old('status_aktif', $user->status_aktif) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="statusAktif">
                                        <span class="badge bg-success" id="statusLabel">
                                            <i class="bi bi-check-circle me-1"></i>Aktif
                                        </span>
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Nonaktifkan jika pekerja tidak lagi melayani
                                </small>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('koordinator.pekerja.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <button type="submit" class="btn btn-warning px-4 text-white">
                                <i class="bi bi-save me-2"></i>Update Data Pekerja
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Riwayat Pelayanan -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history text-primary me-2"></i>Riwayat Pelayanan (5 Terakhir)
                    </h6>
                </div>
                <div class="card-body">
                    @if(isset($riwayatPelayanan) && $riwayatPelayanan->count() > 0)
                        <div class="timeline">
                            @foreach($riwayatPelayanan->take(5) as $riwayat)
                                <div class="timeline-item mb-3">
                                    <div class="d-flex">
                                        <div class="timeline-marker bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; min-width: 35px;">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="mb-1">
                                                <strong>{{ $riwayat->jadwalKebaktian->jenis_kebaktian }}</strong>
                                                <span class="badge bg-{{ $riwayat->status_tugas == 'approved' ? 'success' : 'warning' }} ms-2">
                                                    {{ ucfirst($riwayat->status_tugas) }}
                                                </span>
                                            </p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ \Carbon\Carbon::parse($riwayat->jadwalKebaktian->tanggal_pelayanan)->format('d M Y') }}
                                                | Peran: {{ $riwayat->peran_tugas }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mb-0 mt-3">Belum ada riwayat pelayanan</p>
                        </div>
                    @endif
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
        border-color: #ffc107;
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
    .form-check-input:checked {
        background-color: #198754;
        border-color: #198754;
    }
    .timeline-marker {
        font-size: 0.875rem;
    }
    .timeline-item:not(:last-child) .timeline-marker::after {
        content: '';
        position: absolute;
        left: 17px;
        top: 35px;
        width: 2px;
        height: calc(100% + 10px);
        background: #dee2e6;
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

    // Toggle status label
    const statusCheckbox = document.getElementById('statusAktif');
    const statusLabel = document.getElementById('statusLabel');
    
    statusCheckbox.addEventListener('change', function() {
        if (this.checked) {
            statusLabel.className = 'badge bg-success';
            statusLabel.innerHTML = '<i class="bi bi-check-circle me-1"></i>Aktif';
        } else {
            statusLabel.className = 'badge bg-secondary';
            statusLabel.innerHTML = '<i class="bi bi-x-circle me-1"></i>Nonaktif';
        }
    });

    // Form validation
    document.getElementById('formEditPekerja').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        
        if (password && password.length < 6) {
            e.preventDefault();
            alert('Password minimal 6 karakter!');
            return false;
        }
    });
</script>
@endsection