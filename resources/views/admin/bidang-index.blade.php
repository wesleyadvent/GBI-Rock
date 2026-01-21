@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h2 class="mb-1 fw-bold">Manajemen Bidang</h2>
            <p class="text-muted mb-0">Kelola bidang pelayanan</p>
        </div>
        <a href="{{ route('admin.bidang.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah Bidang Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stats Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i class="bi bi-briefcase-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Total Bidang</p>
                            <h4 class="mb-0 fw-bold">{{ $bidangs->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 text-success p-3 rounded-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Total Pekerja</p>
                            <h4 class="mb-0 fw-bold">{{ $bidangs->sum('users_count') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 text-info p-3 rounded-3">
                            <i class="bi bi-bar-chart-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Rata-rata per Bidang</p>
                            <h4 class="mb-0 fw-bold">{{ $bidangs->count() > 0 ? round($bidangs->sum('users_count') / $bidangs->count(), 1) : 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bidang Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold">Daftar Bidang</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari bidang...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="bidangTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">ID</th>
                            <th class="border-0">Nama Bidang</th>
                            <th class="border-0">Deskripsi</th>
                            <th class="border-0 text-center">Total Pekerja</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bidangs as $bidang)
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark">#{{ $bidang->id_bidang }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="badge-circle bg-primary bg-opacity-10 text-primary me-2">
                                        <i class="bi bi-briefcase"></i>
                                    </div>
                                    <span class="fw-medium">{{ $bidang->nama_bidang }}</span>
                                </div>
                            </td>
                            <td class="text-muted">
                                {{ $bidang->deskripsi ?? '-' }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info">
                                    <i class="bi bi-people me-1"></i>{{ $bidang->users_count }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.bidang.edit', $bidang->id_bidang) }}" 
                                       class="btn btn-outline-primary"
                                       data-bs-toggle="tooltip"
                                       title="Edit Bidang">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.bidang.delete', $bidang->id_bidang) }}"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus bidang {{ $bidang->nama_bidang }}?{{ $bidang->users_count > 0 ? '\n\nBidang ini memiliki '.$bidang->users_count.' pekerja dan tidak dapat dihapus!' : '' }}')"
                                       class="btn btn-outline-danger {{ $bidang->users_count > 0 ? 'disabled' : '' }}"
                                       data-bs-toggle="tooltip"
                                       title="{{ $bidang->users_count > 0 ? 'Tidak dapat dihapus (ada pekerja)' : 'Hapus Bidang' }}">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-3">Belum ada bidang</p>
                                    <a href="{{ route('admin.bidang.create') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-plus-circle me-1"></i>Tambah Bidang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ExtraCSS')
<style>
    .stat-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .card {
        border-radius: 12px;
    }
    
    .badge-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    
    .table > tbody > tr > td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .table > thead > tr > th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('#bidangTable tbody tr:not(.no-data)');

    searchInput.addEventListener('keyup', function() {
        const searchValue = this.value.toLowerCase();
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchValue) ? '' : 'none';
        });
    });
</script>
@endsection