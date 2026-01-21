@extends('layouts.index')

@section('content')
<main class="py-4">
    <div class="mb-4">
        <h2 class="fw-bold">Dashboard Penatua</h2>
        <p class="text-muted">Selamat datang, {{ Auth::user()->nama }}! Kelola persetujuan jadwal pelayanan gereja.</p>
    </div>

    @php
        use App\Models\JadwalKebaktian;
        use Carbon\Carbon;
        
        // Statistik jadwal
        $pendingCount = JadwalKebaktian::where('status', 'pending')->count();
        $approvedThisMonth = JadwalKebaktian::where('status', 'approved')
            ->whereMonth('tanggal_pelayanan', now()->month)
            ->whereYear('tanggal_pelayanan', now()->year)
            ->count();
        $declinedThisMonth = JadwalKebaktian::where('status', 'declined')
            ->whereMonth('tanggal_pelayanan', now()->month)
            ->whereYear('tanggal_pelayanan', now()->year)
            ->count();
        $publishedCount = JadwalKebaktian::where('status', 'published')->count();
        
        // Jadwal pending terbaru
        $jadwalPending = JadwalKebaktian::with(['tugas.user.bidang'])
            ->where('status', 'pending')
            ->orderBy('tanggal_pelayanan')
            ->limit(5)
            ->get();
            
        // Jadwal yang baru disetujui/ditolak (7 hari terakhir)
        $recentActions = JadwalKebaktian::with(['histories.oleh'])
            ->whereIn('status', ['approved', 'declined'])
            ->where('updated_at', '>=', now()->subDays(7))
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
    @endphp

    {{-- STATISTIK CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Menunggu Persetujuan</p>
                            <h3 class="fw-bold mb-0 text-warning">{{ $pendingCount }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock-history fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Disetujui Bulan Ini</p>
                            <h3 class="fw-bold mb-0 text-success">{{ $approvedThisMonth }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Ditolak Bulan Ini</p>
                            <h3 class="fw-bold mb-0 text-danger">{{ $declinedThisMonth }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-x-circle fs-2 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small">Sudah Dipublikasi</p>
                            <h3 class="fw-bold mb-0 text-primary">{{ $publishedCount }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-calendar-check fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- JADWAL PENDING --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-hourglass-split text-warning me-2"></i>
                        Jadwal Menunggu Persetujuan
                    </h5>
                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark">{{ $pendingCount }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($jadwalPending->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-1 d-block mb-2"></i>
                            <p class="mb-0">Tidak ada jadwal yang menunggu persetujuan</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($jadwalPending as $j)
                                <div class="list-group-item px-0 border-start border-warning border-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold text-primary">{{ $j->jenis_kebaktian }}</h6>
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $j->tanggal_pelayanan->format('d M Y') }} - {{ $j->waktu_mulai }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-geo-alt me-1"></i>{{ $j->lokasi }}
                                            </small>
                                            @if($j->tugas->count() > 0)
                                                <small class="text-muted d-block mt-1">
                                                    <i class="bi bi-people me-1"></i>{{ $j->tugas->count() }} pelayan
                                                </small>
                                            @endif
                                        </div>
                                        <a href="{{ route('penatua.jadwal', ['month' => $j->tanggal_pelayanan->month, 'year' => $j->tanggal_pelayanan->year]) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($pendingCount > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('penatua.jadwal') }}" class="btn btn-sm btn-outline-warning">
                                    Lihat Semua ({{ $pendingCount }})
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- AKTIVITAS TERBARU --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        Aktivitas Terbaru (7 Hari)
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentActions->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            <p class="mb-0">Belum ada aktivitas terbaru</p>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($recentActions as $j)
                                @php
                                    $statusColor = $j->status === 'approved' ? 'success' : 'danger';
                                    $statusIcon = $j->status === 'approved' ? 'check-circle' : 'x-circle';
                                    $statusText = $j->status === 'approved' ? 'Disetujui' : 'Ditolak';
                                @endphp
                                <div class="list-group-item px-0 border-start border-{{ $statusColor }} border-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge bg-{{ $statusColor }} me-2">
                                                    <i class="bi bi-{{ $statusIcon }} me-1"></i>{{ $statusText }}
                                                </span>
                                                <h6 class="mb-0 fw-bold">{{ $j->jenis_kebaktian }}</h6>
                                            </div>
                                            <small class="text-muted d-block mb-1">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $j->tanggal_pelayanan->format('d M Y') }}
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $j->updated_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <a href="{{ route('penatua.jadwal', ['month' => $j->tanggal_pelayanan->month, 'year' => $j->tanggal_pelayanan->year]) }}" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- QUICK ACTION --}}
    <div class="card border-0 shadow-sm mt-4 bg-primary bg-opacity-10">
        <div class="card-body text-center py-4">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-calendar-event text-primary me-2"></i>
                Lihat Kalender Jadwal
            </h5>
            <p class="text-muted mb-3">Tinjau dan setujui jadwal pelayanan dalam tampilan kalender</p>
            <a href="{{ route('penatua.jadwal') }}" class="btn btn-primary">
                <i class="bi bi-calendar3 me-2"></i>Buka Kalender Jadwal
            </a>
        </div>
    </div>
</main>
@endsection

@section('ExtraCSS')
<style>
    .card {
        transition: transform 0.2s ease;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .list-group-item {
        transition: background-color 0.2s ease;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Auto refresh badge setiap 5 menit
    setInterval(function() {
        location.reload();
    }, 300000); // 5 menit
</script>
@endsection