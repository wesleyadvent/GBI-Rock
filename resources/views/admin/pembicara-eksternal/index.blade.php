@extends('layouts.index')

@section('content')
<main>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Manajemen Pembicara Eksternal</h2>
                <p class="text-muted">
                    Admin dapat melihat seluruh jadwal kebaktian dan mengatur pembicara eksternal.
                </p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Legend --}}
        <div class="card border-0 shadow-sm rounded-3 mb-3">
            <div class="card-body py-2">
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <small class="text-muted fw-bold">STATUS :</small>
                    <span class="badge bg-secondary">
                        <i class="bi bi-circle-fill"></i> Belum Ada
                    </span>
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock-fill"></i> Pending
                    </span>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle-fill"></i> Approved
                    </span>
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle-fill"></i> Declined
                    </span>
                    <span class="badge bg-primary">
                        <i class="bi bi-rocket-fill"></i> Published
                    </span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.pembicara-eksternal', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}"
                   class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-chevron-left"></i> Prev
                </a>

                <h5 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-calendar3"></i>
                    {{ $currentDate->translatedFormat('F Y') }}
                </h5>

                <a href="{{ route('admin.pembicara-eksternal', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}"
                   class="btn btn-sm btn-outline-primary">
                    Next <i class="bi bi-chevron-right"></i>
                </a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center" style="table-layout: fixed; min-width: 1100px;">
                        <thead class="bg-light text-uppercase">
                            <tr>
                                <th class="py-3 text-danger fw-bold">Minggu</th>
                                <th class="py-3 fw-bold">Senin</th>
                                <th class="py-3 fw-bold">Selasa</th>
                                <th class="py-3 fw-bold">Rabu</th>
                                <th class="py-3 fw-bold">Kamis</th>
                                <th class="py-3 fw-bold">Jumat</th>
                                <th class="py-3 fw-bold">Sabtu</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($calendarGrid as $week)
                            <tr>
                            @foreach($week as $day)
                                <td class="calendar-cell {{ !$day['isCurrentMonth'] ? 'bg-light opacity-50' : '' }}">
                                    <div class="text-end fw-bold mb-2 {{ $day['date']->isToday() ? 'text-primary' : '' }}">
                                        @if($day['date']->isToday())
                                            <span class="badge bg-primary">{{ $day['date']->day }}</span>
                                        @else
                                            {{ $day['date']->day }}
                                        @endif
                                    </div>

                                    @foreach($day['jadwal'] as $item)
                                        @php
                                            // Cek tugas pembicara (id_bidang = 2)
                                            $tugasPembicara = $item->tugas->filter(function($tugas) {
                                                return $tugas->user && $tugas->user->id_bidang == 2;
                                            });
                                            
                                            // Status pengajuan pembicara (dari tugas)
                                            $statusPembicara = 'draft';
                                            if ($tugasPembicara->isNotEmpty()) {
                                                $statusPembicara = $tugasPembicara->first()->status_tugas ?? 'draft';
                                            }

                                            // Gunakan status jadwal jika tersedia, fallback ke status pembicara
                                            $effectiveStatus = $item->status ?? $statusPembicara;

                                            // Admin terkunci jika effective status adalah pending/approved/published/declined
                                            $locked = in_array($effectiveStatus, ['pending', 'approved', 'published', 'declined']);

                                            // Tentukan warna border dan badge berdasarkan effective status
                                            $borderColor = 'secondary';
                                            $badgeClass = 'bg-secondary';
                                            $statusIcon = 'circle-fill';
                                            $statusText = 'Belum Ada Pembicara';

                                            switch($effectiveStatus) {
                                                case 'pending':
                                                    $borderColor = 'warning';
                                                    $badgeClass = 'bg-warning text-dark';
                                                    $statusIcon = 'clock-fill';
                                                    $statusText = 'Pembicara Pending';
                                                    break;
                                                case 'approved':
                                                    $borderColor = 'success';
                                                    $badgeClass = 'bg-success';
                                                    $statusIcon = 'check-circle-fill';
                                                    $statusText = 'Pembicara Approved';
                                                    break;
                                                case 'declined':
                                                    $borderColor = 'danger';
                                                    $badgeClass = 'bg-danger';
                                                    $statusIcon = 'x-circle-fill';
                                                    $statusText = 'Pembicara Declined';
                                                    break;
                                                case 'published':
                                                    $borderColor = 'primary';
                                                    $badgeClass = 'bg-primary';
                                                    $statusIcon = 'rocket-fill';
                                                    $statusText = 'Pembicara Published';
                                                    break;
                                            }
                                        @endphp

                                        <div class="card border-0 shadow-sm mb-2 border-start border-4 border-{{ $borderColor }} item-jadwal"
                                             data-bs-toggle="modal"
                                             data-bs-target="#modalDetail{{ $item->id_jadwal }}">
                                            <div class="card-body p-2 text-start">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <div class="fw-bold text-{{ $borderColor }} small text-truncate flex-grow-1">
                                                        {{ $item->jenis_kebaktian }}
                                                    </div>
                                                    <span class="badge {{ $badgeClass }} ms-1" style="font-size: 0.5rem;">
                                                        <i class="bi bi-{{ $statusIcon }}"></i>
                                                    </span>
                                                </div>
                                                
                                                <div class="text-muted mb-1" style="font-size: 0.65rem;">
                                                    <i class="bi bi-clock"></i> {{ $item->waktu_mulai }}
                                                </div>

                                                @if($item->pembicaraEksternal)
                                                    <div class="badge bg-warning text-dark w-100 text-start" style="font-size: 0.6rem;">
                                                        <i class="bi bi-mic-fill"></i>
                                                        <span class="text-truncate d-inline-block" style="max-width: 100px;">
                                                            {{ $item->pembicaraEksternal->nama_pembicara }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- MODAL DETAIL --}}
                                        <div class="modal fade" id="modalDetail{{ $item->id_jadwal }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content border-0 shadow-lg">

                                                    <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                                        <div class="flex-grow-1">
                                                            <h5 class="modal-title fw-bold mb-1">
                                                                {{ $item->jenis_kebaktian }}
                                                            </h5>
                                                            <small class="opacity-90">
                                                                <i class="bi bi-calendar3"></i>
                                                                {{ $item->tanggal_pelayanan->format('d F Y') }}
                                                                •
                                                                <i class="bi bi-clock"></i>
                                                                {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}
                                                            </small>
                                                        </div>
                                                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>

                                                    <div class="modal-body p-4">

                                                        {{-- STATUS PENGAJUAN PEMBICARA --}}
                                                        <div class="alert alert-{{ $borderColor }} border-0 mb-4">
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-{{ $statusIcon }} fs-4 me-3"></i>
                                                                <div>
                                                                    <strong>Status Pembicara: {{ $statusText }}</strong>
                                                                    <p class="mb-0 small">
                                                                        @if($locked)
                                                                            <i class="bi bi-lock-fill"></i> Pembicara sudah diajukan - Edit pembicara eksternal dikunci
                                                                        @else
                                                                            <i class="bi bi-unlock-fill"></i> Dapat mengelola pembicara eksternal
                                                                        @endif
                                                                    </p>
                                                                    @if($tugasPembicara->isNotEmpty())
                                                                        <div class="mt-2 small">
                                                                            <strong>Pembicara Internal:</strong>
                                                                            @foreach($tugasPembicara as $tp)
                                                                                <span class="badge bg-info text-dark ms-1">
                                                                                    {{ $tp->user->nama ?? 'N/A' }}
                                                                                </span>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- PEMBICARA EKSTERNAL --}}
                                                        <h6 class="fw-bold mb-3 text-uppercase border-bottom pb-2">
                                                            <i class="bi bi-mic-fill text-warning me-2"></i>
                                                            Pembicara Eksternal
                                                        </h6>

                                                        @if($item->pembicaraEksternal)
                                                            <div class="card border-0 bg-light mb-4 shadow-sm">
                                                                <div class="card-body p-3">
                                                                    <div class="row g-3">
                                                                        <div class="col-12">
                                                                            <label class="text-muted small mb-1">
                                                                                <i class="bi bi-person-fill"></i> Nama Pembicara
                                                                            </label>
                                                                            <div class="fw-bold text-dark">
                                                                                {{ $item->pembicaraEksternal->nama_pembicara }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="text-muted small mb-1">
                                                                                <i class="bi bi-building"></i> Asal Gereja
                                                                            </label>
                                                                            <div class="text-dark">
                                                                                {{ $item->pembicaraEksternal->asal_gereja ?? '-' }}
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <label class="text-muted small mb-1">
                                                                                <i class="bi bi-telephone-fill"></i> Kontak
                                                                            </label>
                                                                            <div class="text-dark">
                                                                                {{ $item->pembicaraEksternal->kontak ?? '-' }}
                                                                            </div>
                                                                        </div>
                                                                        @if($item->pembicaraEksternal->keterangan)
                                                                        <div class="col-12">
                                                                            <label class="text-muted small mb-1">
                                                                                <i class="bi bi-chat-left-text-fill"></i> Keterangan
                                                                            </label>
                                                                            <div class="text-dark">
                                                                                {{ $item->pembicaraEksternal->keterangan }}
                                                                            </div>
                                                                        </div>
                                                                        @endif
                                                                    </div>

                                                                    <div class="d-flex gap-2 mt-3 pt-3 border-top">
                                                                        <button class="btn btn-sm btn-primary"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#modalEditPembicara{{ $item->id_jadwal }}"
                                                                            {{ $locked ? 'disabled' : '' }}>
                                                                            <i class="bi bi-pencil-square"></i> Edit Data
                                                                        </button>

                                                                        <form method="POST"
                                                                              action="{{ route('pembicara-eksternal.destroy', $item->pembicaraEksternal->id_pembicara) }}"
                                                                              onsubmit="return confirm('Yakin ingin menghapus pembicara eksternal ini?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button class="btn btn-sm btn-danger" {{ $locked ? 'disabled' : '' }}>
                                                                                <i class="bi bi-trash3-fill"></i> Hapus
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="card border-warning border-2 mb-4">
                                                                <div class="card-body p-3">
                                                                    <form method="POST" action="{{ route('pembicara-eksternal.store') }}">
                                                                        @csrf
                                                                        <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">

                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">
                                                                                <i class="bi bi-person-fill text-primary"></i> Nama Pembicara <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="text" name="nama_pembicara"
                                                                                   class="form-control"
                                                                                   placeholder="Masukkan nama pembicara"
                                                                                   required {{ $locked ? 'disabled' : '' }}>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">
                                                                                <i class="bi bi-building text-primary"></i> Asal Gereja
                                                                            </label>
                                                                            <input type="text" name="asal_gereja"
                                                                                   class="form-control"
                                                                                   placeholder="Masukkan asal gereja"
                                                                                   {{ $locked ? 'disabled' : '' }}>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">
                                                                                <i class="bi bi-telephone-fill text-primary"></i> Kontak
                                                                            </label>
                                                                            <input type="text" name="kontak"
                                                                                   class="form-control"
                                                                                   placeholder="Nomor telepon atau email"
                                                                                   {{ $locked ? 'disabled' : '' }}>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">
                                                                                <i class="bi bi-chat-left-text-fill text-primary"></i> Keterangan
                                                                            </label>
                                                                            <textarea name="keterangan"
                                                                                      class="form-control"
                                                                                      rows="3"
                                                                                      placeholder="Catatan tambahan (opsional)"
                                                                                      {{ $locked ? 'disabled' : '' }}></textarea>
                                                                        </div>

                                                                        <button class="btn btn-warning w-100 fw-bold"
                                                                                {{ $locked ? 'disabled' : '' }}>
                                                                            <i class="bi bi-plus-circle-fill"></i>
                                                                            Tambah Pembicara Eksternal
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <hr class="my-4">

                                                        {{-- READ ONLY TIM --}}
                                                        <h6 class="fw-bold text-uppercase border-bottom pb-2 mb-3">
                                                            <i class="bi bi-people-fill text-primary me-2"></i>
                                                            Tim Pelayanan (Read Only)
                                                        </h6>

                                                        @if($item->tugas->isNotEmpty())
                                                            <div class="row g-3">
                                                                @foreach($item->tugas as $t)
                                                                    <div class="col-md-6">
                                                                        <div class="card border-0 bg-light h-100">
                                                                            <div class="card-body p-3">
                                                                                <div class="d-flex align-items-start">
                                                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                                                         style="width: 40px; height: 40px; min-width: 40px;">
                                                                                        <i class="bi bi-person-fill text-white"></i>
                                                                                    </div>
                                                                                    <div class="flex-grow-1">
                                                                                        <div class="fw-bold text-dark mb-1">
                                                                                            {{ $t->user->nama ?? 'User Terhapus' }}
                                                                                        </div>
                                                                                        <div class="small text-muted mb-1">
                                                                                            <i class="bi bi-briefcase-fill"></i> {{ $t->peran_tugas }}
                                                                                        </div>
                                                                                        @php
                                                                                            $statusClass = 'secondary';
                                                                                            $statusIcon = 'circle';
                                                                                            switch($t->status_tugas) {
                                                                                                case 'approved':
                                                                                                    $statusClass = 'success';
                                                                                                    $statusIcon = 'check-circle-fill';
                                                                                                    break;
                                                                                                case 'pending':
                                                                                                    $statusClass = 'warning';
                                                                                                    $statusIcon = 'clock-fill';
                                                                                                    break;
                                                                                                case 'declined':
                                                                                                    $statusClass = 'danger';
                                                                                                    $statusIcon = 'x-circle-fill';
                                                                                                    break;
                                                                                            }
                                                                                        @endphp
                                                                                        <span class="badge bg-{{ $statusClass }} text-white">
                                                                                            <i class="bi bi-{{ $statusIcon }}"></i> 
                                                                                            {{ ucfirst($t->status_tugas) }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="text-center py-4">
                                                                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                                                <p class="text-muted mt-2 mb-0">Belum ada personil yang ditugaskan</p>
                                                            </div>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                        {{-- MODAL EDIT --}}
                                        @if($item->pembicaraEksternal)
                                        <div class="modal fade" id="modalEditPembicara{{ $item->id_jadwal }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form method="POST"
                                                      action="{{ route('pembicara-eksternal.update', $item->pembicaraEksternal->id_pembicara) }}">
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                                            <h5 class="modal-title fw-bold">
                                                                <i class="bi bi-pencil-square"></i>
                                                                Edit Pembicara Eksternal
                                                            </h5>
                                                            <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    <i class="bi bi-person-fill text-primary"></i> Nama Pembicara <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="text" name="nama_pembicara"
                                                                       class="form-control"
                                                                       value="{{ $item->pembicaraEksternal->nama_pembicara }}" 
                                                                       required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    <i class="bi bi-building text-primary"></i> Asal Gereja
                                                                </label>
                                                                <input type="text" name="asal_gereja"
                                                                       class="form-control"
                                                                       value="{{ $item->pembicaraEksternal->asal_gereja }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    <i class="bi bi-telephone-fill text-primary"></i> Kontak
                                                                </label>
                                                                <input type="text" name="kontak"
                                                                       class="form-control"
                                                                       value="{{ $item->pembicaraEksternal->kontak }}">
                                                            </div>

                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">
                                                                    <i class="bi bi-chat-left-text-fill text-primary"></i> Keterangan
                                                                </label>
                                                                <textarea name="keterangan"
                                                                          class="form-control"
                                                                          rows="3">{{ $item->pembicaraEksternal->keterangan }}</textarea>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-primary fw-bold">
                                                                <i class="bi bi-check-circle-fill"></i> Simpan Perubahan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        @endif

                                    @endforeach
                                </td>
                            @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.calendar-cell { 
    height: 180px; 
    vertical-align: top; 
    padding: 8px;
    background-color: #fff;
}

.item-jadwal { 
    cursor: pointer; 
    transition: all .2s ease; 
    font-size: .75rem;
}

.item-jadwal:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modal-header {
    border-bottom: 3px solid rgba(255,255,255,0.2);
}

.form-label {
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.form-control {
    border-radius: 8px;
    border: 1px solid #dee2e6;
    padding: 0.625rem 0.875rem;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.btn {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.card {
    border-radius: 12px;
    overflow: hidden;
}

.badge {
    padding: 0.35em 0.65em;
    font-weight: 600;
}

.alert {
    border-radius: 12px;
}

@media (max-width: 768px) {
    .calendar-cell {
        height: 200px;
    }
}
</style>
@endsection