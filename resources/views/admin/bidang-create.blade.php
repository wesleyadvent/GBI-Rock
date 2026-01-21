@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="mb-4 mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.bidang.index') }}">Bidang</a></li>
                <li class="breadcrumb-item active">Tambah Bidang</li>
            </ol>
        </nav>
        <h2 class="mb-1 fw-bold">Tambah Bidang Baru</h2>
        <p class="text-muted mb-0">Buat bidang pelayanan baru</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
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
                    <form action="{{ route('admin.bidang.store') }}" method="POST">
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
                                       placeholder="Contoh: Usher, Multimedia, dll"
                                       value="{{ old('nama_bidang') }}"
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
                                      placeholder="Jelaskan tentang bidang ini...">{{ old('deskripsi') }}</textarea>
                            <div class="form-text">Opsional - Penjelasan singkat tentang bidang ini</div>
                            @error('deskripsi')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Simpan Bidang
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
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white border-0 py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-lightbulb me-2"></i>Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Gunakan nama yang jelas dan mudah dipahami</li>
                        <li class="mb-2">Deskripsi membantu pekerja memahami tanggung jawab</li>
                        <li class="mb-2">Pastikan nama bidang unik</li>
                        <li class="mb-0">Bidang yang sudah dibuat bisa diedit kapan saja</li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-info-circle text-info me-2"></i>Contoh Bidang
                    </h6>
                    <div class="mb-3">
                        <strong>Usher</strong>
                        <p class="small text-muted mb-0">Melayani penyambutan jemaat dan pengaturan tempat duduk</p>
                    </div>
                    <div class="mb-3">
                        <strong>Pembicara</strong>
                        <p class="small text-muted mb-0">Melayani dalam penyampaian firman Tuhan</p>
                    </div>
                    <div class="mb-3">
                        <strong>Multimedia</strong>
                        <p class="small text-muted mb-0">Melayani dalam teknis sound system dan proyektor</p>
                    </div>
                    <div class="mb-0">
                        <strong>Pujian & Penyembahan</strong>
                        <p class="small text-muted mb-0">Melayani dalam musik dan pujian</p>
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
    
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
</style>
@endsection

@section('ExtraJS')

@endsection