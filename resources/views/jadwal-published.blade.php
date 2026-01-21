@extends('layouts.index')
@section('content')
<main>
    <div class="py-4">
        <!-- Header -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold mb-2 d-flex align-items-center">
                        <i class="bi bi-calendar-check text-primary me-2"></i>
                        Jadwal Pelayanan
                    </h2>
                    <p class="text-muted mb-0">Lihat jadwal pelayanan yang telah terpublish untuk semua pekerja</p>
                </div>
                
                <!-- Export Buttons -->
                <div class="btn-group" role="group">
                    <a href="{{ route('jadwal.published.pdf', ['month' => $currentDate->month, 'year' => $currentDate->year]) }}" 
                       class="btn btn-outline-danger">
                        <i class="bi bi-file-pdf me-1"></i> PDF
                    </a>
                    <a href="{{ route('jadwal.published.excel', ['month' => $currentDate->month, 'year' => $currentDate->year]) }}" 
                       class="btn btn-outline-success">
                        <i class="bi bi-file-excel me-1"></i> Excel
                    </a>
                </div>
            </div>
        </div>

        <!-- Month Navigator -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <a href="{{ route('jadwal.published', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
                           class="btn btn-light">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </div>
                    
                    <div class="col text-center">
                        <h4 class="mb-0 fw-bold text-dark">
                            {{ $currentDate->translatedFormat('F Y') }}
                        </h4>
                    </div>
                    
                    <div class="col-auto">
                        <a href="{{ route('jadwal.published', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" 
                           class="btn btn-light">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($jadwals->isEmpty())
            <!-- Empty State -->
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                    </div>
                    <h5 class="fw-semibold mb-2">Belum Ada Jadwal Terpublish</h5>
                    <p class="text-muted mb-0">Jadwal pelayanan untuk bulan ini belum dipublish.</p>
                </div>
            </div>
        @else
            <!-- Jadwal List -->
            <div class="row g-4">
                @foreach($jadwals as $jadwal)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm jadwal-card">
                            <!-- Card Header -->
                            <div class="card-header bg-primary text-white border-0 py-3">
                                <div class="row align-items-center">
                                    <div class="col-lg-9">
                                        <h5 class="mb-2 fw-bold d-flex align-items-center">
                                            <i class="bi bi-calendar-event me-2"></i>
                                            {{ $jadwal->jenis_kebaktian }}
                                        </h5>
                                        <div class="d-flex flex-wrap gap-3 text-white-50">
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $jadwal->tanggal_pelayanan->translatedFormat('l, d F Y') }}
                                            </span>
                                            <span class="d-flex align-items-center">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $jadwal->waktu_mulai }} - {{ $jadwal->waktu_selesai }}
                                            </span>
                                            @if($jadwal->lokasi)
                                                <span class="d-flex align-items-center">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    {{ $jadwal->lokasi }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-3 text-lg-end mt-2 mt-lg-0">
                                        <span class="badge bg-white text-primary px-3 py-2 fw-semibold">
                                            <i class="bi bi-megaphone-fill me-1"></i>
                                            Published
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Card Body -->
                            <div class="card-body p-4">
                                <!-- Tema Section -->
                                @if($jadwal->tema)
                                    <div class="tema-box mb-4">
                                        <div class="d-flex align-items-start">
                                            <div class="tema-icon">
                                                <i class="bi bi-lightbulb-fill"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <strong class="d-block mb-1">Tema Kebaktian</strong>
                                                <p class="mb-0 text-muted">{{ $jadwal->tema }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Pekerja Section -->
                                <div class="section-title mb-3">
                                    <i class="bi bi-people-fill me-2"></i>
                                    <span class="fw-bold text-uppercase">Pekerja yang Bertugas</span>
                                </div>

                                <div class="row g-3">
                                    @php
                                        $bidangList = [
                                            1 => ['nama' => 'Usher', 'icon' => 'person', 'color' => 'info'],
                                            2 => ['nama' => 'Pembicara', 'icon' => 'mic-fill', 'color' => 'danger'],
                                            3 => ['nama' => 'Pendoa', 'icon' => 'hand-thumbs-up-fill', 'color' => 'primary'],
                                            4 => ['nama' => 'Praise & Worship', 'icon' => 'music-note-beamed', 'color' => 'warning'],
                                            5 => ['nama' => 'Multimedia', 'icon' => 'camera-video-fill', 'color' => 'success'],
                                        ];

                                        $tugasPerBidang = $jadwal->tugas
                                            ->where('status_tugas', 'approved')
                                            ->filter(fn($t) => $t->user && $t->user->id_bidang)
                                            ->groupBy(fn($t) => $t->user->id_bidang);
                                    @endphp

                                    @foreach($bidangList as $idBidang => $info)
                                        @php
                                            $pekerja = $tugasPerBidang[$idBidang] ?? collect();
                                        @endphp

                                        @if($pekerja->isNotEmpty())
                                            <div class="col-md-6 col-lg-4">
                                                <div class="bidang-card h-100">
                                                    <div class="bidang-header bg-{{ $info['color'] }}">
                                                        <i class="bi bi-{{ $info['icon'] }} me-2"></i>
                                                        <span class="fw-semibold">{{ $info['nama'] }}</span>
                                                    </div>
                                                    <div class="bidang-body">
                                                        <ul class="list-unstyled mb-0">
                                                            @foreach($pekerja as $tugas)
                                                                <li class="pekerja-item">
                                                                    <i class="bi bi-check-circle-fill text-success"></i>
                                                                    <div class="pekerja-info">
                                                                        <div class="fw-semibold text-dark">{{ $tugas->user->nama }}</div>
                                                                        @if($tugas->peran_tugas)
                                                                            <small class="text-muted">{{ $tugas->peran_tugas }}</small>
                                                                        @endif
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @if($tugasPerBidang->isEmpty())
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Belum ada pekerja yang ditugaskan
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>

<style>
/* Card Styles */
.jadwal-card {
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.jadwal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important;
}

.jadwal-card .card-header {
    border-radius: 0;
}

/* Tema Box */
.tema-box {
    background-color: #e7f3ff;
    border-left: 4px solid #0d6efd;
    padding: 1rem 1.25rem;
    border-radius: 8px;
}

.tema-icon {
    width: 36px;
    height: 36px;
    background-color: #0d6efd;
    color: white;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

/* Section Title */
.section-title {
    color: #495057;
    font-size: 0.875rem;
    letter-spacing: 0.5px;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e9ecef;
}

/* Bidang Card */
.bidang-card {
    border: 1px solid #e9ecef;
    border-radius: 10px;
    overflow: hidden;
    background-color: #fff;
    transition: all 0.2s ease;
}

.bidang-card:hover {
    border-color: #dee2e6;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.bidang-header {
    color: white;
    padding: 0.875rem 1rem;
    font-size: 0.9rem;
}

.bidang-body {
    padding: 1rem;
}

/* Pekerja Item */
.pekerja-item {
    display: flex;
    align-items: start;
    gap: 0.5rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.pekerja-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.pekerja-item i {
    margin-top: 2px;
    font-size: 0.875rem;
}

.pekerja-info {
    flex: 1;
}

/* Empty State */
.empty-state-icon {
    opacity: 0.5;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .jadwal-card .card-header h5 {
        font-size: 1.1rem;
    }
    
    .jadwal-card .card-header .text-white-50 {
        font-size: 0.85rem;
    }
}

/* Month Navigator */
.btn-light {
    background-color: #f8f9fa;
    border-color: #e9ecef;
}

.btn-light:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
}
</style>
@endsection