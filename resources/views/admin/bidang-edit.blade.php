@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4 mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.bidang.index') }}">Bidang</a></li>
                <li class="breadcrumb-item active">Edit Bidang</li>
            </ol>
        </nav>
        <h2 class="mb-1 fw-bold">Edit Bidang</h2>
        <p class="text-muted mb-0">Update informasi bidang: <strong>{{ $bidang->nama_bidang }}</strong></p>
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
                    <h5 class="mb-0 fw-bold">Informasi Bidang</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.bidang.update', $bidang->id_bidang) }}" method="POST">
                        @csrf

                        <!-- Nama Bidang -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                Nama Bidang <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="bi bi-briefcase"></i>
                                </span>
                                <input type="text" 
                                       name="nama_bidang" 
                                       class="form-control border-start-0 @error('nama_bidang') is-invalid @enderror" 
                                       value="{{ old('nama_bidang', $bidang->nama_bidang) }}"
                                       required>
                            </div>
                            @error('nama_bidang')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">Deskripsi</label>
                            <textarea name="deskripsi" 
                                      class="form-control @error('deskripsi') is-invalid @enderror" 
                                      rows="5" 
                                      placeholder="Jelaskan tentang bidang ini...">{{ old('deskripsi', $bidang->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.bidang.index') }}" class="btn btn-light px-4">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <!-- Bidang Info Card -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-info-circle me-2"></i>Info Bidang
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3 pb-3 border-bottom">
                        <div class="bidang-icon-large bg-primary bg-opacity-10 text-primary mx-auto mb-3">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h5 class="mb-1 fw-bold">{{ $bidang->nama_bidang }}</h5>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">ID Bidang:</small>
                        <div class="fw-medium">#{{ $bidang->id_bidang }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Total Pekerja:</small>
                        <div class="fw-medium">
                            <i class="bi bi-people me-1"></i>{{ $bidang->users_count }} orang
                        </div>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted">Dibuat:</small>
                        <div class="fw-medium">{{ $bidang->created_at ? $bidang->created_at->format('d M Y') : '-' }}</div>
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
                    @if($bidang->users_count > 0)
                    <div class="alert alert-warning mb-0 small">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Bidang ini memiliki <strong>{{ $bidang->users_count }} pekerja</strong> dan tidak dapat dihapus
                    </div>
                    @else
                    <div class="d-grid">
                        <a href="{{ route('admin.bidang.delete', $bidang->id_bidang) }}"
                           onclick="return confirm('Apakah Anda yakin ingin menghapus bidang {{ $bidang->nama_bidang }}? Tindakan ini tidak dapat dibatalkan!')"
                           class="btn btn-outline-danger">
                            <i class="bi bi-trash me-2"></i>Hapus Bidang
                        </a>
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
    .card {
        border-radius: 12px;
    }
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
    }
    
    .bidang-icon-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
</style>
@endsection

@section('ExtraJS')

@endsection