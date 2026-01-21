@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">Dashboard Koordinator Bidang</h1>
            <p class="text-muted mb-0">Selamat datang, {{ Auth::user()->nama }}</p>
        </div>
        <div class="text-end">
            <small class="text-muted d-block">Bidang: <strong>{{ Auth::user()->bidang->nama_bidang ?? '-' }}</strong></small>
            <small class="text-muted">{{ now()->format('d F Y') }}</small>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Pekerja -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Total Pekerja</p>
                            <h3 class="mb-0 fw-bold">{{ $totalPekerja ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people-fill text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Pending -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Jadwal Pending</p>
                            <h3 class="mb-0 fw-bold">{{ $jadwalPending ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-clock-history text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Approved -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Jadwal Disetujui</p>
                            <h3 class="mb-0 fw-bold">{{ $jadwalApproved ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle-fill text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pekerja Aktif -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Pekerja Aktif</p>
                            <h3 class="mb-0 fw-bold">{{ $pekerjaAktif ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-person-check-fill text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('timPelayanan.index') }}" class="btn btn-outline-primary text-start">
                            <i class="bi bi-calendar3 me-2"></i> Kelola Tim Pelayanan
                        </a>
                        <a href="{{ route('koordinator.pekerja.index') }}" class="btn btn-outline-success text-start">
                            <i class="bi bi-people me-2"></i> Kelola Pekerja
                        </a>
                        <a href="{{ route('koordinator.pekerja.create') }}" class="btn btn-outline-info text-start">
                            <i class="bi bi-person-plus me-2"></i> Tambah Pekerja Baru
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Jadwal Mendatang -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Jadwal Mendatang</h5>
                    <a href="{{ route('timPelayanan.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($jadwalMendatang) && $jadwalMendatang->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jenis Kebaktian</th>
                                        <th>Status Pengajuan</th>
                                        <th>Pekerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalMendatang->take(5) as $jadwal)
                                        <tr>
                                            <td>
                                                <strong>{{ \Carbon\Carbon::parse($jadwal->tanggal_pelayanan)->format('d M Y') }}</strong>
                                                <br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($jadwal->tanggal_pelayanan)->format('l') }}</small>
                                            </td>
                                            <td>{{ $jadwal->jenis_kebaktian }}</td>
                                            <td>
                                                @php
                                                    $status = $jadwal->pengajuan->first()->status_pengajuan ?? 'belum diajukan';
                                                    $badge = [
                                                        'pending' => 'warning',
                                                        'approved' => 'success',
                                                        'declined' => 'danger',
                                                        'belum diajukan' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $badge[$status] }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $approvedCount = $jadwal->tugas->where('status_tugas', 'approved')->count();
                                                    $totalCount = $jadwal->tugas->count();
                                                @endphp
                                                <span class="badge bg-info">{{ $approvedCount }}/{{ $totalCount }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada jadwal mendatang</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Pekerja Terbaru -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Pekerja Terbaru</h5>
                    <a href="{{ route('koordinator.pekerja.index') }}" class="btn btn-sm btn-outline-primary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($pekerjaTerbaru) && $pekerjaTerbaru->count() > 0)
                        <div class="row g-3">
                            @foreach($pekerjaTerbaru->take(4) as $pekerja)
                                <div class="col-md-3">
                                    <div class="border rounded p-3 text-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-2" style="width: 60px; height: 60px;">
                                            <i class="bi bi-person-fill text-primary"></i>
                                        </div>
                                        <h6 class="mb-1">{{ $pekerja->nama }}</h6>
                                        <small class="text-muted">{{ $pekerja->email }}</small>
                                        <br>
                                        <span class="badge bg-{{ $pekerja->status_aktif ? 'success' : 'secondary' }} mt-2">
                                            {{ $pekerja->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3 mb-3">Belum ada pekerja terdaftar</p>
                            <a href="{{ route('koordinator.pekerja.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Tambah Pekerja
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
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .table th {
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .btn-outline-primary:hover,
    .btn-outline-success:hover,
    .btn-outline-info:hover,
    .btn-outline-warning:hover {
        transform: translateX(5px);
        transition: all 0.3s;
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Auto-refresh stats setiap 5 menit (opsional)
    // setTimeout(() => location.reload(), 300000);
</script>
@endsection