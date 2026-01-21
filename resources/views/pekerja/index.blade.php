@extends('layouts.index')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 bg-primary">
                    <div class="card-body p-4 text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="fw-bold mb-2">📅 Jadwal Pelayanan Saya</h2>
                                <p class="mb-0 opacity-75">Halo {{ Auth::user()->nama }}, klik pada kartu jadwal untuk melihat detail dan konfirmasi.</p>
                            </div>
                            <div class="text-end d-none d-md-block">
                                <div class="bg-white bg-opacity-20 rounded p-3">
                                    <h4 class="fw-bold mb-0" style="color: #000000ff">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->where('status_tugas', 'pending')->count() }}</h4>
                                    <small style="color: #4d4a4aff">Menunggu Konfirmasi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Berhasil!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Calendar Card -->
        <div class="card border-0 shadow-sm rounded-3">
            <!-- Calendar Header -->
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <a href="{{ route('pekerja.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}"
                        class="btn btn-outline-primary">
                        <i class="bi bi-chevron-left"></i>
                        <span class="d-none d-md-inline ms-1">Prev</span>
                    </a>

                    <div class="text-center">
                        <h4 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-calendar3 me-2"></i>{{ $currentDate->translatedFormat('F Y') }}
                        </h4>
                    </div>

                    <a href="{{ route('pekerja.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}"
                        class="btn btn-outline-primary">
                        <span class="d-none d-md-inline me-1">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>

            <!-- Legend -->
            <div class="card-body border-bottom py-2 bg-light">
                <div class="d-flex flex-wrap gap-3 justify-content-center align-items-center small">
                    <div class="d-flex align-items-center">
                        <div class="border-start border-3 border-warning me-2" style="height: 20px; width: 3px;"></div>
                        <span><i class="bi bi-clock text-warning me-1"></i>Menunggu</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="border-start border-3 border-success me-2" style="height: 20px; width: 3px;"></div>
                        <span><i class="bi bi-check-circle text-success me-1"></i>Disetujui</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="border-start border-3 border-danger me-2" style="height: 20px; width: 3px;"></div>
                        <span><i class="bi bi-x-circle text-danger me-1"></i>Ditolak</span>
                    </div>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center calendar-table" style="table-layout: fixed; min-width: 1100px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 text-danger fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Minggu
                                </th>
                                <th class="py-3 fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Senin
                                </th>
                                <th class="py-3 fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Selasa
                                </th>
                                <th class="py-3 fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Rabu
                                </th>
                                <th class="py-3 fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Kamis
                                </th>
                                <th class="py-3 fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Jumat
                                </th>
                                <th class="py-3 fw-bold">
                                    <i class="bi bi-calendar-day me-1"></i>Sabtu
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($calendarGrid as $week)
                                <tr>
                                    @foreach ($week as $day)
                                        <td class="p-2 position-relative {{ !$day['isCurrentMonth'] ? 'bg-light opacity-50' : '' }} {{ $day['date']->isToday() ? 'today-cell' : '' }}"
                                            style="height: 150px; vertical-align: top;">

                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <div class="fw-bold {{ $day['date']->isToday() ? 'badge bg-primary' : '' }}">
                                                    {{ $day['date']->day }}
                                                </div>
                                                @if($day['tugas']->count() > 0)
                                                    <span class="badge bg-secondary rounded-pill">{{ $day['tugas']->count() }}</span>
                                                @endif
                                            </div>

                                            <div class="task-container" style="max-height: 110px; overflow-y: auto;">
                                                @foreach ($day['tugas'] as $t)
                                                    <div class="card border-0 shadow-sm mb-2 task-card border-start border-3 {{ $t->status_tugas == 'pending' ? 'border-warning' : ($t->status_tugas == 'approved' ? 'border-success' : 'border-danger') }}"
                                                        style="font-size: 0.75rem; cursor: pointer;" 
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalDetail{{ $t->id_tugas }}">
                                                        <div class="card-body p-2 text-start">
                                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                                <div class="fw-bold text-primary text-truncate flex-grow-1">
                                                                    {{ $t->peran_tugas }}
                                                                </div>
                                                                @if($t->status_tugas == 'pending')
                                                                    <i class="bi bi-bell-fill text-warning ms-1"></i>
                                                                @endif
                                                            </div>
                                                            <div class="text-truncate text-muted small">
                                                                {{ $t->jadwalKebaktian->jenis_kebaktian }}
                                                            </div>
                                                            <div class="text-muted small mt-1">
                                                                <i class="bi bi-clock me-1"></i>{{ substr($t->jadwalKebaktian->waktu_mulai, 0, 5) }}
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Detail -->
                                                    <div class="modal fade" id="modalDetail{{ $t->id_tugas }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header {{ $t->status_tugas == 'pending' ? 'bg-warning' : ($t->status_tugas == 'approved' ? 'bg-success' : 'bg-danger') }} text-white">
                                                                    <h5 class="modal-title fw-bold">
                                                                        <i class="bi bi-info-circle me-2"></i>Detail Pelayanan
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start p-4">
                                                                    <!-- Jenis Kebaktian -->
                                                                    <div class="mb-3 pb-3 border-bottom">
                                                                        <label class="text-muted small d-block mb-1">
                                                                            <i class="bi bi-bookmark me-1"></i>Jenis Kebaktian
                                                                        </label>
                                                                        <span class="fw-bold fs-5">{{ $t->jadwalKebaktian->jenis_kebaktian }}</span>
                                                                    </div>

                                                                    <!-- Tanggal & Waktu -->
                                                                    <div class="row mb-3 pb-3 border-bottom">
                                                                        <div class="col-6">
                                                                            <label class="text-muted small d-block mb-1">
                                                                                <i class="bi bi-calendar-event me-1"></i>Tanggal
                                                                            </label>
                                                                            <span class="fw-bold">{{ $t->jadwalKebaktian->tanggal_pelayanan->format('d F Y') }}</span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <label class="text-muted small d-block mb-1">
                                                                                <i class="bi bi-clock me-1"></i>Waktu
                                                                            </label>
                                                                            <span class="fw-bold">{{ $t->jadwalKebaktian->waktu_mulai }} - {{ $t->jadwalKebaktian->waktu_selesai }}</span>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Peran -->
                                                                    <div class="mb-3 pb-3 border-bottom">
                                                                        <label class="text-muted small d-block mb-1">
                                                                            <i class="bi bi-person-badge me-1"></i>Peran Anda
                                                                        </label>
                                                                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">{{ $t->peran_tugas }}</span>
                                                                    </div>

                                                                    <!-- Status -->
                                                                    <div class="mb-3 {{ $t->status_tugas == 'declined' ? 'pb-3 border-bottom' : '' }}">
                                                                        <label class="text-muted small d-block mb-1">
                                                                            <i class="bi bi-flag me-1"></i>Status
                                                                        </label>
                                                                        <span class="badge {{ $t->status_tugas == 'approved' ? 'bg-success' : ($t->status_tugas == 'pending' ? 'bg-warning' : 'bg-danger') }} px-3 py-2">
                                                                            @if($t->status_tugas == 'approved')
                                                                                <i class="bi bi-check-circle me-1"></i>Disetujui
                                                                            @elseif($t->status_tugas == 'pending')
                                                                                <i class="bi bi-clock me-1"></i>Menunggu Konfirmasi
                                                                            @else
                                                                                <i class="bi bi-x-circle me-1"></i>Ditolak
                                                                            @endif
                                                                        </span>
                                                                    </div>

                                                                    <!-- Alasan Penolakan -->
                                                                    @if ($t->status_tugas == 'declined')
                                                                        <div class="mb-3">
                                                                            <label class="text-muted small d-block mb-1">
                                                                                <i class="bi bi-chat-left-text me-1"></i>Alasan Penolakan
                                                                            </label>
                                                                            <div class="alert alert-light border mb-0">
                                                                                {{ $t->alasan_penolakan }}
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                    <!-- Action Buttons -->
                                                                    @if ($t->status_tugas == 'pending')
                                                                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 mb-3">
                                                                            <i class="bi bi-info-circle me-2"></i>
                                                                            <small>Silakan konfirmasi ketersediaan Anda untuk pelayanan ini.</small>
                                                                        </div>
                                                                        <div class="d-flex gap-2">
                                                                            <button class="btn btn-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalKonfirmasi{{ $t->id_tugas }}" data-bs-dismiss="modal" data-action="terima">
                                                                                <i class="bi bi-check-circle me-2"></i>Terima
                                                                            </button>
                                                                            <button class="btn btn-outline-danger flex-grow-1" data-bs-toggle="modal" data-bs-target="#modalKonfirmasi{{ $t->id_tugas }}" data-bs-dismiss="modal" data-action="tolak">
                                                                                <i class="bi bi-x-circle me-2"></i>Tolak
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Konfirmasi Final -->
                                                    <div class="modal fade" id="modalKonfirmasi{{ $t->id_tugas }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header border-0 pb-0">
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-center px-4 pb-4">
                                                                    <div class="mb-4">
                                                                        <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                                                                            <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>
                                                                        </div>
                                                                        <h4 class="fw-bold mb-2">⚠️ Perhatian!</h4>
                                                                        <p class="text-muted mb-0">Keputusan yang Anda buat <strong class="text-danger">TIDAK DAPAT DIUBAH</strong> setelah dikonfirmasi. Pastikan pilihan Anda sudah benar.</p>
                                                                    </div>

                                                                    <div class="alert alert-light text-start border shadow-sm mb-4">
                                                                        <div class="d-flex align-items-start">
                                                                            <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
                                                                            <div>
                                                                                <small class="text-muted d-block mb-2">Detail Penugasan:</small>
                                                                                <strong class="d-block">{{ $t->jadwalKebaktian->jenis_kebaktian }}</strong>
                                                                                <small class="text-muted">
                                                                                    <i class="bi bi-person-badge me-1"></i>{{ $t->peran_tugas }} • 
                                                                                    <i class="bi bi-calendar me-1"></i>{{ $t->jadwalKebaktian->tanggal_pelayanan->format('d F Y') }} • 
                                                                                    <i class="bi bi-clock me-1"></i>{{ $t->jadwalKebaktian->waktu_mulai }}
                                                                                </small>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Form Terima -->
                                                                    <form action="{{ route('pekerja.konfirmasi', $t->id_tugas) }}" method="POST" id="formTerima{{ $t->id_tugas }}" style="display: none;">
                                                                        @csrf
                                                                        <input type="hidden" name="aksi" value="terima">
                                                                        <div class="d-grid gap-2">
                                                                            <button type="submit" class="btn btn-success btn-lg">
                                                                                <i class="bi bi-check-circle-fill me-2"></i>Ya, Saya Setuju Menerima
                                                                            </button>
                                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                                <i class="bi bi-arrow-left me-2"></i>Batal
                                                                            </button>
                                                                        </div>
                                                                    </form>

                                                                    <!-- Form Tolak -->
                                                                    <form action="{{ route('pekerja.konfirmasi', $t->id_tugas) }}" method="POST" id="formTolak{{ $t->id_tugas }}" style="display: none;">
                                                                        @csrf
                                                                        <input type="hidden" name="aksi" value="tolak">
                                                                        <div class="mb-3 text-start">
                                                                            <label class="form-label fw-bold">
                                                                                <i class="bi bi-chat-left-text me-2"></i>Alasan Penolakan 
                                                                                <span class="text-danger">*</span>
                                                                            </label>
                                                                            <textarea name="alasan" class="form-control form-control-lg" rows="4" placeholder="Contoh: Saya memiliki keperluan keluarga yang mendesak pada tanggal tersebut..." required></textarea>
                                                                            <small class="text-muted">Berikan alasan yang jelas agar koordinator dapat memahami situasi Anda.</small>
                                                                        </div>
                                                                        <div class="d-grid gap-2">
                                                                            <button type="submit" class="btn btn-danger btn-lg">
                                                                                <i class="bi bi-x-circle-fill me-2"></i>Ya, Saya Tolak Penugasan Ini
                                                                            </button>
                                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                                                <i class="bi bi-arrow-left me-2"></i>Batal
                                                                            </button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Calendar Footer -->
            <!-- <div class="card-footer bg-light border-0 py-3">
                <div class="row text-center g-3">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-calendar-check text-primary fs-4 me-2"></i>
                            <div class="text-start">
                                <small class="text-muted d-block">Total Penugasan</small>
                                <strong>{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->whereMonth('created_at', $month)->whereYear('created_at', $year)->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-check-circle text-success fs-4 me-2"></i>
                            <div class="text-start">
                                <small class="text-muted d-block">Disetujui</small>
                                <strong class="text-success">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->where('status_tugas', 'approved')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count() }}</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-center">
                            <i class="bi bi-clock text-warning fs-4 me-2"></i>
                            <div class="text-start">
                                <small class="text-muted d-block">Menunggu</small>
                                <strong class="text-warning">{{ App\Models\Tugas::where('id_user', Auth::user()->id_user)->where('status_tugas', 'pending')->whereMonth('created_at', $month)->whereYear('created_at', $year)->count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
@endsection

@section('ExtraCSS')
<style>
    .task-card {
        transition: all 0.2s ease;
    }
    
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    }

    .today-cell {
        background-color: #fff3cd !important;
        position: relative;
    }

    .today-cell::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background:  #0d6efd;
    }

    .calendar-table td {
        position: relative;
    }

    .task-container::-webkit-scrollbar {
        width: 4px;
    }

    .task-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .task-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .task-container::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    .modal-content {
        overflow: hidden;
    }

    .modal-header {
        position: relative;
    }

    .modal-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, rgba(255,255,255,0.3), rgba(255,255,255,0.1));
    }

    @media (max-width: 768px) {
        .calendar-table {
            font-size: 0.875rem;
        }
        
        .task-card {
            font-size: 0.7rem !important;
        }
    }
</style>
@endsection

@section('ExtraJS')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle modal konfirmasi untuk menampilkan form yang sesuai
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

        // Auto dismiss alerts after 5 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Add loading state to submit buttons
        const forms = document.querySelectorAll('form[action*="konfirmasi"]');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
            });
        });
    });
</script>
@endsection