@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Daftar Pekerja</h1>
            <p class="text-muted mb-0">Kelola pekerja bidang {{ Auth::user()->bidang->nama_bidang ?? '-' }}</p>
        </div>
        <a href="{{ route('koordinator.pekerja.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i>Tambah Pekerja
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Pekerja</p>
                            <h4 class="mb-0 fw-bold">{{ $pekerja->count() }}</h4>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people-fill text-primary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pekerja Aktif</p>
                            <h4 class="mb-0 fw-bold">{{ $pekerja->where('status_aktif', 1)->count() }}</h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-check-fill text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pekerja Nonaktif</p>
                            <h4 class="mb-0 fw-bold">{{ $pekerja->where('status_aktif', 0)->count() }}</h4>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-x-fill text-secondary fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Bidang</p>
                            <h6 class="mb-0 fw-bold text-truncate">{{ Auth::user()->bidang->nama_bidang ?? '-' }}</h6>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-briefcase-fill text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-0">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-list-ul me-2 text-primary"></i>Data Pekerja
                    </h5>
                </div>
                <div class="col-md-6">
                    <!-- Search & Filter -->
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search"></i>
                        </span>
                        <input 
                            type="text" 
                            class="form-control border-start-0" 
                            id="searchInput"
                            placeholder="Cari nama atau email pekerja..."
                        >
                        <select class="form-select" id="filterStatus" style="max-width: 150px;">
                            <option value="">Semua Status</option>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($pekerja->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="tablePekerja">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0" width="5%">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th class="border-0" width="35%">Nama & Email</th>
                                <th class="border-0" width="20%">Bidang</th>
                                <th class="border-0" width="15%">Status</th>
                                <th class="border-0" width="15%">Bergabung</th>
                                <th class="border-0 text-center" width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pekerja as $p)
                                <tr class="pekerja-row" data-status="{{ $p->status_aktif ? 'aktif' : 'nonaktif' }}">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input row-checkbox" type="checkbox" value="{{ $p->id_user }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                                                <span class="fw-bold text-primary">
                                                    {{ strtoupper(substr($p->nama, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-semibold">{{ $p->nama }}</h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-envelope me-1"></i>{{ $p->email }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-75">
                                            <i class="bi bi-briefcase me-1"></i>
                                            {{ $p->bidang ? $p->bidang->nama_bidang : '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($p->status_aktif)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle me-1"></i>Nonaktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $p->created_at ? $p->created_at->format('d M Y') : '-' }}
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a 
                                                href="{{ route('koordinator.pekerja.edit', $p->id_user) }}" 
                                                class="btn btn-sm btn-warning"
                                                data-bs-toggle="tooltip"
                                                title="Edit Pekerja"
                                            >
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button 
                                                type="button"
                                                class="btn btn-sm btn-danger btn-delete"
                                                data-id="{{ $p->id_user }}"
                                                data-name="{{ $p->nama }}"
                                                data-bs-toggle="tooltip"
                                                title="Hapus Pekerja"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Bulk Actions -->
                <div class="p-3 bg-light border-top" id="bulkActions" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">
                            <strong id="selectedCount">0</strong> pekerja dipilih
                        </span>
                        <div>
                            <button class="btn btn-sm btn-danger" id="bulkDelete">
                                <i class="bi bi-trash me-1"></i>Hapus Terpilih
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-person-x text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">Belum Ada Pekerja</h5>
                    <p class="text-muted mb-4">Mulai tambahkan pekerja untuk bidang Anda</p>
                    <a href="{{ route('koordinator.pekerja.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Tambah Pekerja Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun pekerja:</p>
                <div class="alert alert-warning mb-0">
                    <strong id="deleteName"></strong>
                </div>
                <p class="text-muted small mt-2 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Tindakan ini tidak dapat dibatalkan
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ExtraCSS')
<style>
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    .card {
        border-radius: 10px;
    }
    .btn-group .btn {
        padding: 0.375rem 0.75rem;
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    .badge {
        padding: 0.5em 0.75em;
        font-weight: 500;
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('keyup', function() {
        filterTable();
    });

    // Filter by status
    document.getElementById('filterStatus').addEventListener('change', function() {
        filterTable();
    });

    function filterTable() {
        const searchValue = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('filterStatus').value.toLowerCase();
        const rows = document.querySelectorAll('.pekerja-row');

        rows.forEach(row => {
            const name = row.querySelector('h6').textContent.toLowerCase();
            const email = row.querySelector('small').textContent.toLowerCase();
            const status = row.getAttribute('data-status');

            const matchSearch = name.includes(searchValue) || email.includes(searchValue);
            const matchStatus = !statusFilter || status === statusFilter;

            if (matchSearch && matchStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Select all checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox
    document.querySelectorAll('.row-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkActions = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');

        if (checkedBoxes.length > 0) {
            bulkActions.style.display = 'block';
            selectedCount.textContent = checkedBoxes.length;
        } else {
            bulkActions.style.display = 'none';
        }
    }

    // Delete single pekerja
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('deleteName').textContent = name;
            document.getElementById('deleteForm').action = '{{ url("koordinator/pekerja") }}/' + id;
            
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        });
    });

    // Bulk delete
    document.getElementById('bulkDelete').addEventListener('click', function() {
        const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
        
        if (checkedBoxes.length === 0) {
            alert('Pilih minimal 1 pekerja untuk dihapus');
            return;
        }

        if (confirm(`Apakah Anda yakin ingin menghapus ${checkedBoxes.length} pekerja?`)) {
            // Implement bulk delete logic here
            console.log('Deleting:', Array.from(checkedBoxes).map(cb => cb.value));
            // You would need to add a bulk delete route in your controller
        }
    });
</script>
@endsection