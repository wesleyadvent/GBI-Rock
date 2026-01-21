@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h2 class="mb-1 fw-bold">Manajemen User</h2>
            <p class="text-muted mb-0">Kelola data pengguna sistem</p>
        </div>
        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Tambah User Baru
        </a>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Stats Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 text-primary p-3 rounded-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Total User</p>
                            <h4 class="mb-0 fw-bold">{{ $users->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 text-success p-3 rounded-3">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">User Aktif</p>
                            <h4 class="mb-0 fw-bold">{{ $users->where('status_aktif', 1)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 text-warning p-3 rounded-3">
                            <i class="bi bi-person-x-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">User Nonaktif</p>
                            <h4 class="mb-0 fw-bold">{{ $users->where('status_aktif', 0)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 text-info p-3 rounded-3">
                            <i class="bi bi-briefcase-fill fs-4"></i>
                        </div>
                        <div class="ms-3">
                            <p class="text-muted mb-0 small">Pekerja</p>
                            <h4 class="mb-0 fw-bold">{{ $users->where('role', 'pekerja')->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <h5 class="mb-0 fw-bold">Daftar User</h5>
                </div>
                <div class="col-md-8">
                    <div class="row g-2">
                        <!-- Search Bar -->
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari nama, email, atau role...">
                            </div>
                        </div>
                        
                        <!-- Filter Role -->
                        <div class="col-md-3">
                            <select class="form-select" id="filterRole">
                                <option value="">Semua Role</option>
                                <option value="admin">Admin</option>
                                <option value="sekretaris">Sekretaris</option>
                                <option value="koordinator_bidang">Koordinator Bidang</option>
                                <option value="penatua">Penatua</option>
                                <option value="pekerja">Pekerja</option>
                            </select>
                        </div>
                        
                        <!-- Filter Status -->
                        <div class="col-md-3">
                            <select class="form-select" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="userTable">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 ps-4">ID</th>
                            <th class="border-0">Nama</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Role</th>
                            <th class="border-0">Bidang</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                        <tr data-role="{{ $u->role }}" data-status="{{ $u->status_aktif ? 'aktif' : 'nonaktif' }}">
                            <td class="ps-4">
                                <span class="badge bg-light text-dark">#{{ $u->id_user }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-primary bg-opacity-10 text-primary me-2">
                                        {{ strtoupper(substr($u->nama, 0, 1)) }}
                                    </div>
                                    <span class="fw-medium">{{ $u->nama }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $u->email }}</td>
                            <td>
                                @php
                                $roleColors = [
                                    'admin' => 'danger',
                                    'sekretaris' => 'primary',
                                    'koordinator_bidang' => 'warning',
                                    'penatua' => 'info',
                                    'pekerja' => 'secondary'
                                ];
                                $color = $roleColors[$u->role] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                    {{ ucfirst(str_replace('_', ' ', $u->role)) }}
                                </span>
                            </td>
                            <td>
                                @if($u->id_bidang)
                                    <span class="badge bg-light text-dark">
                                        <i class="bi bi-briefcase me-1"></i>{{ $bidang[$u->id_bidang] }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($u->status_aktif)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>Aktif
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger">
                                        <i class="bi bi-x-circle me-1"></i>Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.user.edit', $u->id_user) }}" 
                                       class="btn btn-outline-primary" 
                                       data-bs-toggle="tooltip"
                                       title="Edit User">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('admin.user.toggle', $u->id_user) }}" 
                                       class="btn btn-outline-secondary"
                                       data-bs-toggle="tooltip"
                                       title="{{ $u->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="bi bi-{{ $u->status_aktif ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </a>
                                    <a href="{{ route('admin.user.delete', $u->id_user) }}"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus user {{ $u->nama }}?')"
                                       class="btn btn-outline-danger"
                                       data-bs-toggle="tooltip"
                                       title="Hapus User">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    <p class="mb-0">Belum ada data user</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">Menampilkan <span id="visibleCount">{{ $users->count() }}</span> dari {{ $users->count() }} user</small>
                <div id="noResults" style="display: none;" class="text-danger">
                    <i class="bi bi-exclamation-circle me-1"></i>Tidak ada data yang cocok
                </div>
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
    
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
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
    
    .btn-group-sm > .btn {
        padding: 0.375rem 0.75rem;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    
    .table-hover tbody tr {
        transition: background-color 0.2s;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
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
    const filterRole = document.getElementById('filterRole');
    const filterStatus = document.getElementById('filterStatus');
    const tableRows = document.querySelectorAll('#userTable tbody tr:not(.no-data)');
    const visibleCount = document.getElementById('visibleCount');
    const noResults = document.getElementById('noResults');

    function filterTable() {
        const searchValue = searchInput.value.toLowerCase();
        const roleValue = filterRole.value.toLowerCase();
        const statusValue = filterStatus.value.toLowerCase();
        
        let visibleRowCount = 0;

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const rowRole = row.getAttribute('data-role');
            const rowStatus = row.getAttribute('data-status');
            
            const matchSearch = text.includes(searchValue);
            const matchRole = roleValue === '' || rowRole === roleValue;
            const matchStatus = statusValue === '' || rowStatus === statusValue;
            
            if (matchSearch && matchRole && matchStatus) {
                row.style.display = '';
                visibleRowCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update visible count
        visibleCount.textContent = visibleRowCount;
        
        // Show/hide no results message
        if (visibleRowCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    // Event listeners
    searchInput.addEventListener('keyup', filterTable);
    filterRole.addEventListener('change', filterTable);
    filterStatus.addEventListener('change', filterTable);

    // Reset filters button (optional)
    function resetFilters() {
        searchInput.value = '';
        filterRole.value = '';
        filterStatus.value = '';
        filterTable();
    }
</script>
@endsection