@extends('layouts.index')

@section('content')
<main>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Manajemen Tim Pelayanan</h2>
                <p class="text-muted">Klik jadwal pada kalender untuk mengatur personil bidang Anda.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <a href="{{ route('timPelayanan.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
                class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left"></i></a>

                <h5 class="mb-0 fw-bold">{{ $currentDate->translatedFormat('F Y') }}</h5>

                <a href="{{ route('timPelayanan.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" 
                class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-right"></i></a>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center" style="table-layout: fixed; min-width: 1100px;">
                        <thead class="bg-light text-uppercase">
                            <tr>
                                <th class="py-3 text-danger">Minggu</th>
                                <th class="py-3">Senin</th>
                                <th class="py-3">Selasa</th>
                                <th class="py-3">Rabu</th>
                                <th class="py-3">Kamis</th>
                                <th class="py-3">Jumat</th>
                                <th class="py-3">Sabtu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($calendarGrid as $week)
                                <tr>
                                    @foreach($week as $day)
                                        <td class="calendar-cell {{ !$day['isCurrentMonth'] ? 'bg-light opacity-50' : '' }}">
                                            <div class="text-end fw-bold mb-1">{{ $day['date']->day }}</div>

                                            @foreach($day['jadwal'] as $item)
                                                @php
                                                    $pengajuanStatus = $item->pengajuan->first();
                                                    $idBidang = auth()->user()->id_bidang;
                                                    
                                                    // ✅ HANYA HITUNG YANG APPROVED
                                                    $approvedCount = $item->tugas
                                                        ->where('status_tugas', 'approved')
                                                        ->filter(fn ($t) => $t->user && $t->user->id_bidang == $idBidang)
                                                        ->count();
                                                    
                                                    $minimalBidang = [1 => 2, 2 => 1, 3 => 2, 4 => 2, 5 => 2];
                                                    $minimal = $minimalBidang[$idBidang] ?? 1;
                                                    
                                                    $borderColor = 'border-primary';
                                                    $textColor = 'text-primary';
                                                    $badgeColor = 'bg-info';
                                                    
                                                    if ($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'approved') {
                                                        $borderColor = 'border-success';
                                                        $textColor = 'text-success';
                                                        $badgeColor = 'bg-success';
                                                    } elseif ($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'declined') {
                                                        $borderColor = 'border-danger';
                                                        $textColor = 'text-danger';
                                                        $badgeColor = 'bg-danger';
                                                    } elseif ($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'pending') {
                                                        $borderColor = 'border-warning';
                                                        $textColor = 'text-warning';
                                                        $badgeColor = 'bg-warning text-dark';
                                                    } elseif ($approvedCount < $minimal) {
                                                        $borderColor = 'border-warning';
                                                        $textColor = 'text-warning';
                                                        $badgeColor = 'bg-warning text-dark';
                                                    } 
                                                @endphp

                                                <div class="card border-0 shadow-sm mb-1 border-start border-3 {{ $borderColor }} item-jadwal" 
                                                    data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id_jadwal }}">
                                                    <div class="card-body p-2 text-start">
                                                        <div class="fw-bold {{ $textColor }} small text-truncate">
                                                            {{ $item->jenis_kebaktian }}
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">
                                                            <i class="bi bi-clock"></i> {{ $item->waktu_mulai }}
                                                        </div>
                                                        <div class="mt-1">
                                                            <span class="badge {{ $badgeColor }} p-1" style="font-size: 0.6rem;">
                                                                {{ $approvedCount }}/{{ $minimal }} Approved
                                                            </span>
                                                            @if($pengajuanStatus)
                                                                @if($pengajuanStatus->status_pengajuan === 'pending')
                                                                    <span class="badge bg-warning text-dark p-1 ms-1" style="font-size: 0.6rem;">
                                                                        <i class="bi bi-hourglass-split"></i> Pending
                                                                    </span>
                                                                @elseif($pengajuanStatus->status_pengajuan === 'approved')
                                                                    <span class="badge bg-success p-1 ms-1" style="font-size: 0.6rem;">
                                                                        <i class="bi bi-check-circle-fill"></i> Approved
                                                                    </span>
                                                                @elseif($pengajuanStatus->status_pengajuan === 'declined')
                                                                    <span class="badge bg-danger p-1 ms-1" style="font-size: 0.6rem;">
                                                                        <i class="bi bi-x-circle-fill"></i> Declined
                                                                    </span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- MODAL -->
                                                <div class="modal fade text-start" id="modalDetail{{ $item->id_jadwal }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            @php
                                                                $headerClass = 'bg-primary text-white';
                                                                $closeClass = 'btn-close-white';
                                                                $headerBadge = '';
                                                                
                                                                if ($pengajuanStatus) {
                                                                    if ($pengajuanStatus->status_pengajuan === 'approved') {
                                                                        $headerClass = 'bg-success text-white';
                                                                        $headerBadge = '<span class="badge bg-light text-success ms-2">Disetujui</span>';
                                                                    } elseif ($pengajuanStatus->status_pengajuan === 'declined') {
                                                                        $headerClass = 'bg-danger text-white';
                                                                        $headerBadge = '<span class="badge bg-light text-danger ms-2">Ditolak</span>';
                                                                    } elseif ($pengajuanStatus->status_pengajuan === 'pending') {
                                                                        $headerClass = 'bg-warning text-dark';
                                                                        $closeClass = '';
                                                                        $headerBadge = '<span class="badge bg-dark ms-2">Menunggu Persetujuan</span>';
                                                                    }
                                                                }
                                                            @endphp
                                                            
                                                            <div class="modal-header {{ $headerClass }}">
                                                                <div>
                                                                    <h5 class="modal-title fw-bold mb-1">
                                                                        {{ $item->jenis_kebaktian }}
                                                                        {!! $headerBadge !!}
                                                                    </h5>
                                                                    <small class="opacity-75">
                                                                        <i class="bi bi-calendar3"></i> {{ $item->tanggal_pelayanan->format('d F Y') }} 
                                                                        <span class="mx-2">•</span>
                                                                        <i class="bi bi-clock"></i> {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}
                                                                    </small>
                                                                </div>
                                                                <button type="button" class="btn-close {{ $closeClass }}" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            
                                                            <div class="modal-body p-4">
                                                                @if($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'declined' && $pengajuanStatus->alasan_penolakan)
                                                                    <div class="alert alert-danger mb-4" role="alert">
                                                                        <h6 class="alert-heading">
                                                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                            Alasan Penolakan dari Sekretaris:
                                                                        </h6>
                                                                        <p class="mb-0">{{ $pengajuanStatus->alasan_penolakan }}</p>
                                                                    </div>
                                                                @endif

                                                                <div class="row g-4">
                                                                    <!-- Kolom Kiri: Tim Terdaftar -->
                                                                    <div class="col-md-6">
                                                                        @php
                                                                            // ✅ Pisahkan berdasarkan status
                                                                            $tugasApproved = $item->tugas->where('status_tugas', 'approved');
                                                                            $tugasPending = $item->tugas->where('status_tugas', 'pending');
                                                                            $tugasDeclined = $item->tugas->where('status_tugas', 'declined');
                                                                        @endphp

                                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                                            <h6 class="fw-bold text-uppercase mb-0" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                                                                <i class="bi bi-people-fill text-primary me-2"></i>Tim Terdaftar
                                                                            </h6>
                                                                            <span class="badge bg-primary">{{ $tugasApproved->count() + $tugasPending->count() }} Orang</span>
                                                                        </div>

                                                                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                                                                            {{-- APPROVED --}}
                                                                            @forelse($tugasApproved as $t)
                                                                                <div class="list-group-item px-0 py-3 border-bottom">
                                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                                        <div class="flex-grow-1">
                                                                                            <div class="fw-bold mb-1">{{ $t->user->nama ?? 'User Terhapus' }}</div>
                                                                                            <small class="text-muted d-block">
                                                                                                <i class="bi bi-briefcase"></i> {{ $t->peran_tugas }}
                                                                                            </small>
                                                                                        </div>
                                                                                        <span class="badge bg-success">
                                                                                            <i class="bi bi-check-circle"></i> Approved
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            @empty
                                                                            @endforelse

                                                                            {{-- DECLINED --}}
                                                                            @forelse($tugasDeclined as $t)
                                                                                <div class="list-group-item px-0 py-3 border-bottom">
                                                                                    <div class="d-flex justify-content-between align-items-start">
                                                                                        <div class="flex-grow-1">
                                                                                            <div class="fw-bold mb-1">{{ $t->user->nama ?? 'User Terhapus' }}</div>
                                                                                            <small class="text-muted d-block">
                                                                                                <i class="bi bi-briefcase"></i> {{ $t->peran_tugas }}
                                                                                            </small>
                                                                                            @if($t->user && $t->user->bidang)
                                                                                                <small class="text-muted d-block mt-1">
                                                                                                    <i class="bi bi-building"></i> {{ $t->user->bidang->nama_bidang }}
                                                                                                </small>
                                                                                            @endif
                                                                                        </div>
                                                                                        <span class="badge bg-danger">
                                                                                            <i class="bi bi-x-circle"></i> Declined
                                                                                        </span>
                                                                                    </div>
                                                                                    
                                                                                    {{-- Tampilkan alasan jika ada --}}
                                                                                    @if($t->alasan_penolakan)
                                                                                        <div class="mt-2 p-2 bg-light rounded">
                                                                                            <small class="text-muted">
                                                                                                <i class="bi bi-chat-left-text"></i> <strong>Alasan:</strong><br>
                                                                                                {{ $t->alasan_penolakan }}
                                                                                            </small>
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            @empty
                                                                            @endforelse

                                                                            {{-- PENDING --}}
                                                                            @foreach($tugasPending as $t)
                                                                                <div class="list-group-item px-0 py-3 border-bottom bg-light">
                                                                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                                                                        <div class="flex-grow-1">
                                                                                            <div class="fw-bold mb-1">
                                                                                                {{ $t->user->nama ?? 'User Terhapus' }}
                                                                                            </div>
                                                                                            <small class="text-muted d-block mb-2">
                                                                                                <i class="bi bi-briefcase"></i> {{ $t->peran_tugas }}
                                                                                            </small>
                                                                                            <span class="badge bg-warning text-dark">
                                                                                                <i class="bi bi-hourglass-split"></i> Menunggu Konfirmasi
                                                                                            </span>
                                                                                        </div>

                                                                                        <div class="d-flex gap-2">
                                                                                            <!-- EDIT BUTTON -->
                                                                                                <button type="button" class="btn btn-sm btn-outline-primary btn-open-edit"
                                                                                                        onclick="openEditModal('#modalEditTugas{{ $t->id_tugas }}', this)"
                                                                                                        title="Edit Peran">
                                                                                                    <i class="bi bi-pencil-square"></i>
                                                                                                </button>

                                                                                            {{-- DELETE BUTTON --}}
                                                                                            <form action="{{ route('timPelayanan.batal', $t->id_tugas) }}"
                                                                                                method="POST"
                                                                                                class="d-inline"
                                                                                                onsubmit="return confirm('Yakin ingin menghapus pengajuan ini?')">
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                                                                    <i class="bi bi-trash3"></i>
                                                                                                </button>
                                                                                            </form>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                {{-- MODAL EDIT --}}
                                                                                <div class="modal fade" id="modalEditTugas{{ $t->id_tugas }}" tabindex="-1">
                                                                                    <div class="modal-dialog modal-dialog-centered">
                                                                                        <form action="{{ route('timPelayanan.editPeran', $t->id_tugas) }}" method="POST">
                                                                                            @csrf
                                                                                            @method('PUT')

                                                                                            <div class="modal-content border-0 shadow-lg">
                                                                                                <div class="modal-header bg-primary text-white">
                                                                                                    <h5 class="modal-title">
                                                                                                        <i class="bi bi-pencil-square me-2"></i>Edit Peran Tugas
                                                                                                    </h5>
                                                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                                                </div>

                                                                                                <div class="modal-body">
                                                                                                    <div class="mb-3">
                                                                                                        <label class="form-label fw-semibold">Nama Anggota</label>
                                                                                                        <input type="text" class="form-control" value="{{ $t->user->nama ?? 'User Terhapus' }}" disabled>
                                                                                                    </div>
                                                                                                    
                                                                                                    <div class="mb-3">
                                                                                                        <label class="form-label fw-semibold">Peran Tugas <span class="text-danger">*</span></label>
                                                                                                        <input type="text"
                                                                                                            name="peran_tugas"
                                                                                                            class="form-control"
                                                                                                            value="{{ $t->peran_tugas }}"
                                                                                                            placeholder="Contoh: Singer, Drummer, dll."
                                                                                                            required>
                                                                                                        <small class="text-muted">Masukkan peran/posisi untuk anggota ini</small>
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="modal-footer bg-light">
                                                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                                                        <i class="bi bi-x-circle"></i> Batal
                                                                                                    </button>
                                                                                                    <button type="submit" class="btn btn-primary">
                                                                                                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                                                                                                    </button>
                                                                                                </div>
                                                                                            </div>
                                                                                        </form>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach


                                                                            @if($tugasApproved->isEmpty() && $tugasPending->isEmpty() && $tugasDeclined->isEmpty())
                                                                                <div class="text-center py-4">
                                                                                    <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                                                                    <p class="text-muted small mt-2 mb-0">Belum ada personil terdaftar</p>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <!-- Kolom Kanan: Tambah Personil -->
                                                                    <div class="col-md-6">
                                                                        <h6 class="fw-bold text-uppercase mb-3" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                                                            <i class="bi bi-person-plus-fill text-success me-2"></i>Tambah Personil
                                                                        </h6>

                                                                        @php
                                                                            $formDisabled = $pengajuanStatus && in_array($pengajuanStatus->status_pengajuan, ['approved', 'declined']);
                                                                        @endphp

                                                                        @if(!$formDisabled)
                                                                            <form action="{{ route('timPelayanan.assign') }}" method="POST">
                                                                                @csrf
                                                                                <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                
                                                                                <div class="mb-3">
                                                                                    <label class="form-label small fw-semibold text-muted">Pilih Anggota</label>
                                                                                    <select name="id_user" class="form-select" required>
                                                                                        <option value="">-- Pilih Anggota --</option>
                                                                                        @foreach ($daftarPekerja as $p)
                                                                                            @if (!$item->tugas->contains('id_user', $p->id_user))
                                                                                                <option value="{{ $p->id_user }}">{{ $p->nama }}</option>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>

                                                                                <div class="mb-3">
                                                                                    <label class="form-label small fw-semibold text-muted">Peran/Posisi</label>
                                                                                    <input type="text" name="peran_tugas" class="form-control" 
                                                                                        placeholder="Contoh: Singer, Drummer, dll." required>
                                                                                </div>

                                                                                <button type="submit" class="btn btn-info text-white w-100 fw-bold">
                                                                                    <i class="bi bi-plus-circle-fill"></i> Kirim Permintaan ke Pekerja
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <div class="alert alert-{{ $pengajuanStatus->status_pengajuan === 'approved' ? 'success' : 'danger' }}" role="alert">
                                                                                <i class="bi bi-lock-fill me-2"></i>
                                                                                <strong>Pengajuan {{ $pengajuanStatus->status_pengajuan === 'approved' ? 'Disetujui' : 'Ditolak' }}</strong>
                                                                                <p class="mb-0 mt-2 small">
                                                                                    Anda tidak dapat menambah atau mengubah personil karena pengajuan telah 
                                                                                    {{ $pengajuanStatus->status_pengajuan === 'approved' ? 'disetujui' : 'ditolak' }} oleh sekretaris.
                                                                                </p>
                                                                            </div>
                                                                        @endif

                                                                        <!-- Info Box -->
                                                                        <div class="alert alert-info mt-3 py-2 px-3" style="font-size: 0.875rem;">
                                                                            <div class="d-flex align-items-center">
                                                                                <i class="bi bi-info-circle-fill me-2"></i>
                                                                                <div>
                                                                                    <strong>Minimal:</strong> {{ $minimal }} personil approved<br>
                                                                                    <strong>Saat ini:</strong> {{ $approvedCount }}/{{ $minimal }} approved
                                                                                    @if($tugasPending->count() > 0)
                                                                                        <br><strong>Menunggu:</strong> {{ $tugasPending->count() }} personil
                                                                                    @endif
                                                                                    @if($pengajuanStatus)
                                                                                        <div class="mt-2">
                                                                                            @if($pengajuanStatus->status_pengajuan === 'approved')
                                                                                                <span class="badge bg-success">Disetujui Sekretaris</span>
                                                                                            @elseif($pengajuanStatus->status_pengajuan === 'declined')
                                                                                                <span class="badge bg-danger">Ditolak Sekretaris</span>
                                                                                            @elseif($pengajuanStatus->status_pengajuan === 'pending')
                                                                                                <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Footer -->
                                                            <div class="modal-footer bg-light border-top">
                                                                @if($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'approved')
                                                                    <div class="w-100 text-center py-2">
                                                                        <div class="alert alert-success mb-0">
                                                                            <i class="bi bi-check-circle-fill me-2"></i>
                                                                            <strong>Pengajuan Disetujui Sekretaris</strong>
                                                                        </div>
                                                                    </div>
                                                                @elseif($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'declined')
                                                                    <div class="w-100">
                                                                        <div class="alert alert-danger mb-3">
                                                                            <i class="bi bi-x-circle-fill me-2"></i>
                                                                            <strong>Pengajuan Ditolak Sekretaris</strong>
                                                                        </div>
                                                                        <form action="{{ route('timPelayanan.ajukanSekretaris') }}" method="POST" class="w-100">
                                                                            @csrf
                                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                            @if($approvedCount >= $minimal)
                                                                                <button class="btn btn-primary w-100 fw-bold py-2" 
                                                                                    onclick="return confirm('Ajukan ulang jadwal ini ke sekretaris?')">
                                                                                    <i class="bi bi-arrow-repeat"></i> Ajukan Ulang ke Sekretaris
                                                                                </button>
                                                                            @else
                                                                                <button class="btn btn-secondary w-100 fw-bold py-2" disabled>
                                                                                    <i class="bi bi-exclamation-triangle-fill"></i>
                                                                                    Personil Approved: {{ $approvedCount }}/{{ $minimal }} (Belum Memenuhi Syarat)
                                                                                </button>
                                                                            @endif
                                                                        </form>
                                                                    </div>
                                                                @elseif($pengajuanStatus && $pengajuanStatus->status_pengajuan === 'pending')
                                                                    <div class="w-100">
                                                                        <div class="alert alert-warning mb-3">
                                                                            <i class="bi bi-hourglass-split me-2"></i>
                                                                            <strong>Pengajuan Menunggu Persetujuan Sekretaris</strong>
                                                                        </div>
                                                                        <form action="{{ route('timPelayanan.batalkanPengajuan') }}" method="POST" class="w-100">
                                                                            @csrf
                                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                            <button class="btn btn-danger w-100 fw-bold py-2" 
                                                                                onclick="return confirm('Batalkan pengajuan ke sekretaris?')">
                                                                                <i class="bi bi-x-circle-fill"></i> Batalkan Pengajuan
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                @else
                                                                    <div class="w-100">
                                                                        @if($approvedCount >= $minimal)
                                                                            <form action="{{ route('timPelayanan.ajukanSekretaris') }}" method="POST" class="w-100">
                                                                                @csrf
                                                                                <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                <button class="btn btn-primary w-100 fw-bold py-2" 
                                                                                    onclick="return confirm('Ajukan jadwal ini ke sekretaris?')">
                                                                                    <i class="bi bi-send-check-fill"></i> Ajukan ke Sekretaris
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <button class="btn btn-secondary w-100 fw-bold py-2" disabled>
                                                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                                                Personil Approved: {{ $approvedCount }}/{{ $minimal }} (Belum Memenuhi Syarat)
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

<!-- Script to reliably open edit modal after parent modal is hidden -->
<script>
    function openEditModal(targetSelector, btn) {
        try {
            const parentModalEl = btn.closest('.modal');
            const targetEl = document.querySelector(targetSelector);
            if (!targetEl) return;
            const targetModal = new bootstrap.Modal(targetEl);

            if (parentModalEl) {
                // Get Bootstrap instance for parent (or create)
                let parentInstance = bootstrap.Modal.getInstance(parentModalEl);
                if (!parentInstance) parentInstance = new bootstrap.Modal(parentModalEl);

                const onHidden = function () {
                    targetModal.show();
                    parentModalEl.removeEventListener('hidden.bs.modal', onHidden);
                };

                parentModalEl.addEventListener('hidden.bs.modal', onHidden);
                parentInstance.hide();
            } else {
                targetModal.show();
            }
        } catch (e) {
            console.error('openEditModal error', e);
        }
    }
</script>
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
    .calendar-cell { height: 160px; vertical-align: top; padding: 8px; }
    .item-jadwal { cursor: pointer; transition: transform 0.2s; font-size: 0.75rem; }
    .item-jadwal:hover { transform: translateY(-2px); background-color: #f8f9fa; }
    
    .list-group-flush::-webkit-scrollbar { width: 6px; }
    .list-group-flush::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .list-group-flush::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    .list-group-flush::-webkit-scrollbar-thumb:hover { background: #555; }
</style>
@endsection