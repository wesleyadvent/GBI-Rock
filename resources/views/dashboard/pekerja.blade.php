@extends('layouts.index')

@section('content')
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body p-4 text-white">
                        <h2 class="fw-bold mb-2">Selamat Datang, {{ Auth::user()->nama }}! 👋</h2>
                        <p class="mb-0 opacity-75">{{ now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Tugas -->
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Total Penugasan</p>
                                <h3 class="fw-bold mb-0">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->count() }}</h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-calendar-check text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Menunggu Konfirmasi</p>
                                <h3 class="fw-bold mb-0 text-warning">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->where('status_tugas', 'pending')->count() }}</h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-clock-history text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Disetujui</p>
                                <h3 class="fw-bold mb-0 text-success">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->where('status_tugas', 'approved')->count() }}</h3>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-check-circle text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Declined -->
            <div class="col-md-3 col-sm-6">
                <div class="card border-0 shadow-sm h-100 hover-lift">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <p class="text-muted mb-1 small">Ditolak</p>
                                <h3 class="fw-bold mb-0 text-danger">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->where('status_tugas', 'declined')->count() }}</h3>
                            </div>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <i class="bi bi-x-circle text-danger fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Penugasan Menunggu Konfirmasi -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold mb-0">
                                <i class="bi bi-bell text-warning me-2"></i>
                                Menunggu Konfirmasi Anda
                            </h5>
                            <a href="{{ route('pekerja.index') }}" class="btn btn-sm btn-outline-primary">
                                Lihat Semua
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $tugasPending = App\Models\Tugas::with('jadwalKebaktian')
                                ->where('id_user', Auth::user()->id_user)
                                ->where('status_tugas', 'pending')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp

                        @forelse($tugasPending as $tugas)
                            <div class="border-bottom p-3 hover-bg">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $tugas->jadwalKebaktian->jenis_kebaktian }}</h6>
                                        <div class="d-flex flex-wrap gap-2 mb-2">
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                <i class="bi bi-person-badge me-1"></i>{{ $tugas->peran_tugas }}
                                            </span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                <i class="bi bi-calendar-event me-1"></i>{{ $tugas->jadwalKebaktian->tanggal_pelayanan->format('d M Y') }}
                                            </span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                <i class="bi bi-clock me-1"></i>{{ $tugas->jadwalKebaktian->waktu_mulai }} - {{ $tugas->jadwalKebaktian->waktu_selesai }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <button class="btn btn-sm btn-success me-1" data-bs-toggle="modal" data-bs-target="#modalKonfirmasi{{ $tugas->id_tugas }}" data-action="terima">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalKonfirmasi{{ $tugas->id_tugas }}" data-action="tolak">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Konfirmasi -->
                            <div class="modal fade" id="modalKonfirmasi{{ $tugas->id_tugas }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header border-0 pb-0">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center px-4 pb-4">
                                            <div class="mb-3">
                                                <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                                    <i class="bi bi-exclamation-triangle text-warning fs-1"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Konfirmasi Keputusan</h5>
                                                <p class="text-muted mb-0">Keputusan yang Anda buat <strong>tidak dapat diubah</strong> setelah dikonfirmasi. Pastikan pilihan Anda sudah benar.</p>
                                            </div>

                                            <div class="alert alert-light text-start">
                                                <small class="text-muted d-block mb-1">Detail Penugasan:</small>
                                                <strong>{{ $tugas->jadwalKebaktian->jenis_kebaktian }}</strong><br>
                                                <small>{{ $tugas->peran_tugas }} • {{ $tugas->jadwalKebaktian->tanggal_pelayanan->format('d F Y') }}</small>
                                            </div>

                                            <!-- Form Terima -->
                                            <form action="{{ route('pekerja.konfirmasi', $tugas->id_tugas) }}" method="POST" id="formTerima{{ $tugas->id_tugas }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="aksi" value="terima">
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="bi bi-check-circle me-2"></i>Ya, Saya Setuju Menerima
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </form>

                                            <!-- Form Tolak -->
                                            <form action="{{ route('pekerja.konfirmasi', $tugas->id_tugas) }}" method="POST" id="formTolak{{ $tugas->id_tugas }}" style="display: none;">
                                                @csrf
                                                <input type="hidden" name="aksi" value="tolak">
                                                <div class="mb-3 text-start">
                                                    <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                                                    <textarea name="alasan" class="form-control" rows="4" placeholder="Tuliskan alasan Anda menolak penugasan ini..." required></textarea>
                                                </div>
                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-circle me-2"></i>Ya, Saya Tolak Penugasan Ini
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle text-success fs-1 mb-3 d-block"></i>
                                <p class="text-muted">Tidak ada penugasan yang menunggu konfirmasi</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Jadwal Terdekat -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-calendar-week text-primary me-2"></i>
                            Jadwal Terdekat
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $tugasTerdekat = App\Models\Tugas::with('jadwalKebaktian')
                                ->where('id_user', Auth::user()->id_user)
                                ->where('status_tugas', 'approved')
                                ->whereHas('jadwalKebaktian', function($q) {
                                    $q->where('tanggal_pelayanan', '>=', now());
                                })
                                ->orderBy(function($query) {
                                    $query->select('tanggal_pelayanan')
                                          ->from('jadwal_kebaktian')
                                          ->whereColumn('jadwal_kebaktian.id_jadwal', 'tugas.id_jadwal')
                                          ->limit(1);
                                })
                                ->limit(5)
                                ->get();
                        @endphp

                        @forelse($tugasTerdekat as $tugas)
                            <div class="border-bottom p-3">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary bg-opacity-10 rounded p-2 me-3 text-center" style="min-width: 60px;">
                                        <div class="text-primary fw-bold fs-5">{{ $tugas->jadwalKebaktian->tanggal_pelayanan->format('d') }}</div>
                                        <div class="text-primary small">{{ $tugas->jadwalKebaktian->tanggal_pelayanan->format('M') }}</div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">{{ $tugas->jadwalKebaktian->jenis_kebaktian }}</h6>
                                        <p class="mb-1 small text-muted">{{ $tugas->peran_tugas }}</p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>{{ $tugas->jadwalKebaktian->waktu_mulai }} - {{ $tugas->jadwalKebaktian->waktu_selesai }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x text-muted fs-1 mb-3 d-block"></i>
                                <p class="text-muted small">Belum ada jadwal yang disetujui</p>
                            </div>
                        @endforelse
                    </div>
                    @if($tugasTerdekat->count() > 0)
                        <div class="card-footer bg-white border-0 py-2">
                            <a href="{{ route('pekerja.index') }}" class="btn btn-sm btn-link text-decoration-none w-100">
                                Lihat Semua Jadwal <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0">
                            <i class="bi bi-lightning-charge text-warning me-2"></i>
                            Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('pekerja.index') }}" class="btn btn-outline-primary">
                                <i class="bi bi-calendar-check me-2"></i>Lihat Kalender
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('ExtraCSS')
<style>
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-lift:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .hover-bg {
        transition: background-color 0.2s ease;
    }

    .hover-bg:hover {
        background-color: #f8f9fa;
    }

    .bg-gradient {
        position: relative;
        overflow: hidden;
    }

    .bg-gradient::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        animation: pulse 15s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(-10%, -10%); }
    }
</style>
@endsection

@section('ExtraJS')
<script>
    // Handle modal untuk menampilkan form yang sesuai
    document.addEventListener('DOMContentLoaded', function() {
        const modals = document.querySelectorAll('[id^="modalKonfirmasi"]');
        
        modals.forEach(modal => {
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const action = button.getAttribute('data-action');
                const tugasId = modal.id.replace('modalKonfirmasi', '');
                
                const formTerima = modal.querySelector('#formTerima' + tugasId);
                const formTolak = modal.querySelector('#formTolak' + tugasId);
                
                if (action === 'terima') {
                    formTerima.style.display = 'block';
                    formTolak.style.display = 'none';
                } else {
                    formTerima.style.display = 'none';
                    formTolak.style.display = 'block';
                }
            });
        });
    });
</script>
@endsection