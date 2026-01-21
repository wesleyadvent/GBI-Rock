@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <div>
            <h2 class="mb-1 fw-bold">Dashboard Admin</h2>
            <p class="text-muted mb-0">Selamat datang kembali, {{ Auth::user()->nama }}!</p>
        </div>
        <div class="text-muted">
            <i class="bi bi-calendar3"></i> {{ date('d F Y') }}
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Users -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 fw-medium">Total User</p>
                            <h3 class="mb-0 fw-bold">{{ $totalUsers ?? 0 }}</h3>
                            <small class="text-success">
                                <i class="bi bi-arrow-up"></i> Aktif: {{ $activeUsers ?? 0 }}
                            </small>
                        </div>
                        <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jadwal -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 fw-medium">Total Jadwal</p>
                            <h3 class="mb-0 fw-bold">{{ $totalJadwal ?? 0 }}</h3>
                            <small class="text-info">
                                <i class="bi bi-calendar-check"></i> Bulan ini: {{ $jadwalBulanIni ?? 0 }}
                            </small>
                        </div>
                        <div class="stats-icon bg-info bg-opacity-10 text-info">
                            <i class="bi bi-calendar3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pelayanan -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 fw-medium">Total Pelayanan</p>
                            <h3 class="mb-0 fw-bold">{{ $totalPelayanan ?? 0 }}</h3>
                            <small class="text-warning">
                                <i class="bi bi-clock-history"></i> Minggu ini: {{ $pelayananMingguIni ?? 0 }}
                            </small>
                        </div>
                        <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bidang Terbanyak -->
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted mb-2 fw-medium">Bidang Aktif</p>
                            <h3 class="mb-0 fw-bold">5</h3>
                            <small class="text-success">
                                <i class="bi bi-graph-up"></i> Semua bidang
                            </small>
                        </div>
                        <div class="stats-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-diagram-3-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Details -->
    <div class="row g-4">
        <!-- Pelayanan per Bidang -->
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Pelayanan per Bidang</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Bidang</th>
                                    <th class="border-0">Total Pekerja</th>
                                    <th class="border-0">Total Pelayanan</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $bidangList = [
                                    1 => ['name' => 'Usher', 'color' => 'primary', 'icon' => 'door-open'],
                                    2 => ['name' => 'Pembicara', 'color' => 'success', 'icon' => 'mic'],
                                    3 => ['name' => 'Pendoa', 'color' => 'info', 'icon' => 'book'],
                                    4 => ['name' => 'PW', 'color' => 'warning', 'icon' => 'music-note-beamed'],
                                    5 => ['name' => 'Multimedia', 'color' => 'danger', 'icon' => 'camera-video']
                                ];
                                @endphp
                                
                                @foreach($bidangList as $id => $data)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="badge bg-{{ $data['color'] }} bg-opacity-10 text-{{ $data['color'] }} p-2 me-2">
                                                <i class="bi bi-{{ $data['icon'] }}"></i>
                                            </div>
                                            <span class="fw-medium">{{ $data['name'] }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $pelayananPerBidang[$id]['pekerja'] ?? 0 }} orang
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $data['color'] }}" 
                                                     style="width: {{ ($pelayananPerBidang[$id]['pelayanan'] ?? 0) * 10 }}%"></div>
                                            </div>
                                            <span class="fw-medium">{{ $pelayananPerBidang[$id]['pelayanan'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success">Aktif</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Recent Activity -->
        <div class="col-xl-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.user.create') }}" class="btn btn-primary">
                            <i class="bi bi-person-plus me-2"></i>Tambah User Baru
                        </a>
                        <a href="{{ route('admin.user.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>Kelola User
                        </a>
                        <a href="{{ route('admin.pembicara-eksternal') }}" class="btn btn-outline-primary">
                            <i class="bi bi-calendar-plus me-2"></i>Tambah Pembicara Eksternal
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-bold">Informasi Sistem</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="text-muted mb-1 small">User Online</p>
                            <h6 class="mb-0">{{ $onlineUsers ?? 0 }} pengguna</h6>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div>
                            <p class="text-muted mb-1 small">Jadwal Hari Ini</p>
                            <h6 class="mb-0">{{ $jadwalHariIni ?? 0 }} jadwal</h6>
                        </div>
                        <i class="bi bi-calendar-day text-primary"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Last Update</p>
                            <h6 class="mb-0">{{ date('H:i') }}</h6>
                        </div>
                        <i class="bi bi-clock text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ExtraCSS')
<style>
    .stats-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stats-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 24px;
    }
    
    .card {
        border-radius: 12px;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .table > tbody > tr > td {
        padding: 1rem 0.75rem;
    }
</style>
@endsection

@section('ExtraJS')
@endsection