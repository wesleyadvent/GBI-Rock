@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h2 class="fw-bold mb-1">Dashboard Sekretaris</h2>
            <p class="text-muted mb-0">Kelola jadwal pelayanan kebaktian gereja</p>
        </div>
        <div>
            <span class="badge bg-primary px-3 py-2">
                <i class="bi bi-calendar-event me-2"></i>{{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row g-3 mb-4">
        <!-- Draft Jadwal -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-file-earmark-text text-warning fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-normal">Draft Jadwal</h6>
                            <h3 class="mb-0 fw-bold">
                                {{ App\Models\JadwalKebaktian::where('status', 'draft')->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-clock-history text-info fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-normal">Menunggu Approval</h6>
                            <h3 class="mb-0 fw-bold">
                                {{ App\Models\JadwalKebaktian::where('status', 'pending')->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-check-circle text-success fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-normal">Disetujui</h6>
                            <h3 class="mb-0 fw-bold">
                                {{ App\Models\JadwalKebaktian::where('status', 'approved')->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Published -->
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="bi bi-globe text-primary fs-3"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1 fw-normal">Published</h6>
                            <h3 class="mb-0 fw-bold">
                                {{ App\Models\JadwalKebaktian::where('status', 'published')->count() }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row g-4">
        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-lightning-charge text-primary me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('sekretaris.jadwal.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Buat Jadwal Baru
                        </a>
                        <a href="{{ route('sekretaris.jadwal.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-calendar3 me-2"></i>Lihat Semua Jadwal
                        </a>
                        <a href="{{ route('sekretaris.pengajuan.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-clipboard-check me-2"></i>Kelola Pengajuan
                        </a>
                    </div>

                    <hr class="my-4">

                    <div class="small text-muted mb-2">
                        <i class="bi bi-info-circle me-1"></i>Menu Lainnya
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action border-0 py-2">
                            <i class="bi bi-file-earmark-pdf text-danger me-2"></i>Export PDF
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 py-2">
                            <i class="bi bi-file-earmark-excel text-success me-2"></i>Export Excel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Schedule -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-calendar-week text-primary me-2"></i>Jadwal Mendatang
                        </h5>
                        <a href="{{ route('sekretaris.jadwal.index') }}" class="btn btn-sm btn-outline-primary">
                            Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $upcomingJadwal = App\Models\JadwalKebaktian::with(['tugas.user'])
                            ->whereIn('status', ['approved', 'published'])
                            ->where('tanggal_pelayanan', '>=', now())
                            ->orderBy('tanggal_pelayanan')
                            ->take(5)
                            ->get();
                    @endphp

                    @if($upcomingJadwal->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Tanggal</th>
                                        <th class="border-0">Jenis Kebaktian</th>
                                        <th class="border-0">Waktu</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 text-center">Pekerja</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingJadwal as $jadwal)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded p-2 me-2">
                                                    <i class="bi bi-calendar-date text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ \Carbon\Carbon::parse($jadwal->tanggal_pelayanan)->format('d M Y') }}</div>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($jadwal->tanggal_pelayanan)->diffForHumans() }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $jadwal->jenis_kebaktian }}</span>
                                            @if($jadwal->tema)
                                                <br><small class="text-muted">{{ Str::limit($jadwal->tema, 30) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="bi bi-clock me-1"></i>
                                            <span class="small">{{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}</span>
                                        </td>
                                        <td>
                                            @if($jadwal->status == 'published')
                                                <span class="badge bg-primary">
                                                    <i class="bi bi-globe me-1"></i>Published
                                                </span>
                                            @elseif($jadwal->status == 'approved')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Approved
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                {{ $jadwal->tugas->count() }} orang
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Belum ada jadwal yang akan datang</p>
                            <a href="{{ route('sekretaris.jadwal.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Buat Jadwal Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-activity text-primary me-2"></i>Aktivitas Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $recentJadwal = App\Models\JadwalKebaktian::with('pembuat')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @if($recentJadwal->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentJadwal as $jadwal)
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        @if($jadwal->status == 'draft')
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2">
                                                <i class="bi bi-file-earmark-text text-warning"></i>
                                            </div>
                                        @elseif($jadwal->status == 'pending')
                                            <div class="bg-info bg-opacity-10 rounded-circle p-2">
                                                <i class="bi bi-clock-history text-info"></i>
                                            </div>
                                        @elseif($jadwal->status == 'approved')
                                            <div class="bg-success bg-opacity-10 rounded-circle p-2">
                                                <i class="bi bi-check-circle text-success"></i>
                                            </div>
                                        @else
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2">
                                                <i class="bi bi-globe text-primary"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">{{ $jadwal->jenis_kebaktian }}</h6>
                                                <p class="text-muted small mb-0">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ \Carbon\Carbon::parse($jadwal->tanggal_pelayanan)->format('d M Y') }}
                                                    <span class="mx-2">•</span>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }}
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <small class="text-muted">{{ $jadwal->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2">Belum ada aktivitas</p>
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
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.btn {
    transition: all 0.2s ease-in-out;
}

.list-group-item:hover {
    background-color: rgba(var(--bs-primary-rgb), 0.05);
}

.table > :not(caption) > * > * {
    padding: 1rem 0.75rem;
}
</style>
@endsection

@section('ExtraJS')
<script>
// Optional: Add any interactive features here
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard Sekretaris loaded');
});
</script>
@endsection