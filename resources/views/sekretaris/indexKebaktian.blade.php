@extends('layouts.index')
@section('content')
<main>
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold">Kelola Pengajuan Jadwal</h2>
                <p class="text-muted">Review pengajuan jadwal dari Koordinator Bidang dan ajukan jadwal pelayanan ke Penatua</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <a href="{{ route('sekretaris.jadwal.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}" 
                class="btn btn-sm btn-outline-primary"><i class="bi bi-chevron-left"></i></a>

                <h5 class="mb-0 fw-bold">{{ $currentDate->translatedFormat('F Y') }}</h5>

                <a href="{{ route('sekretaris.jadwal.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}" 
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
                                                    // HANYA hitung tugas yang sudah APPROVED dari koordinator bidang
                                                    $jumlahPengajuanBidang = $item->pengajuan?->count() ?? 0;

                                                    // Hitung tugas yang sudah APPROVED (bukan pending)
                                                    $jumlahTugasDiajukan = $item->tugas?->where('status_tugas', 'approved')->count() ?? 0;

                                                    // Hitung pengajuan dari PW dan Usher
                                                    $jumlahPengajuan = $item->pengajuan?->count() ?? 0;

                                                    // Tentukan status & warna berdasarkan status jadwal
                                                    $badgeIcon = 'bi-hourglass-split';
                                                    $warnaBorder = '#6c757d';
                                                    $warnaKalender = 'bg-secondary';
                                                    $statusText = 'Menunggu review';

                                                    // Jika jadwal sudah disetujui oleh Penatua
                                                    if ($item->status === 'approved') {
                                                        $statusText = 'Disetujui';
                                                        $warnaKalender = 'bg-success';
                                                        $badgeIcon = 'bi-check-circle-fill';
                                                        $warnaBorder = '#198754';
                                                    }
                                                    // Jika jadwal ditolak oleh Penatua
                                                    elseif ($item->status === 'declined') {
                                                        $statusText = 'Ditolak';
                                                        $warnaKalender = 'bg-danger';
                                                        $badgeIcon = 'bi-x-circle-fill';
                                                        $warnaBorder = '#dc3545';
                                                    }
                                                    // Jika sudah diajukan ke Penatua dan menunggu
                                                    elseif ($item->status === 'pending') {
                                                        $statusText = 'Menunggu persetujuan';
                                                        $warnaKalender = 'bg-warning';
                                                        $badgeIcon = 'bi-clock-history';
                                                        $warnaBorder = '#fd7e14';
                                                    }
                                                    // Jika belum ada pengajuan / tugas sama sekali
                                                    elseif ($jumlahPengajuanBidang === 0 && $jumlahTugasDiajukan === 0) {
                                                        $statusText = 'Belum ada pengajuan';
                                                        $warnaKalender = 'bg-primary';
                                                        $badgeIcon = 'bi-dash-circle';
                                                        $warnaBorder = '#0d6efd';
                                                    }
                                                    // Jika jadwal dipublish 
                                                    elseif ($item->status === 'published') {
                                                        $statusText = 'Published';
                                                        $warnaKalender = 'bg-success';
                                                        $badgeIcon = 'bi-check-circle-fill';
                                                        $warnaBorder = '#034225ff';
                                                    }
                                                @endphp

                                                <div class="card border-0 shadow-sm mb-1 border-start border-3 item-jadwal" 
                                                    style="border-color: {{ $warnaBorder }};" 
                                                    data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id_jadwal }}">
                                                    <div class="card-body p-2 text-start">
                                                        <div class="fw-bold small text-truncate"
                                                            style="color: {{ $warnaBorder }};">
                                                            {{ $item->jenis_kebaktian }}
                                                        </div>
                                                        <div class="text-muted" style="font-size: 0.65rem;">
                                                            <i class="bi bi-clock"></i> {{ $item->waktu_mulai }}
                                                        </div>
                                                        <div class="mt-1">
                                                            <span class="badge {{ $warnaKalender }} {{ in_array($warnaKalender, ['bg-primary', 'bg-success', 'bg-warning-alt']) ? 'text-white' : 'text-dark' }} p-1" style="font-size: 0.6rem;">
                                                                <i class="bi {{ $badgeIcon }}"></i> {{ $statusText }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>


                                                <!-- MODAL -->
                                                <div class="modal fade text-start" id="modalDetail{{ $item->id_jadwal }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            <!-- Header -->
                                                            <div class="modal-header {{ $warnaKalender }} {{ in_array($warnaKalender, ['bg-primary', 'bg-success']) ? 'text-white' : 'text-dark' }}">
                                                                <div>
                                                                    <h5 class="modal-title fw-bold mb-1 d-flex align-items-center flex-wrap gap-2">
                                                                        {{ $item->jenis_kebaktian }}

                                                                        <span class="badge {{ $warnaKalender }} {{ in_array($warnaKalender, ['bg-primary', 'bg-success']) ? 'text-white' : 'text-dark' }}">
                                                                            <i class="bi {{ $badgeIcon }}"></i> {{ $statusText }}
                                                                        </span>
                                                                    </h5>

                                                                    <small>
                                                                        <i class="bi bi-calendar3"></i>
                                                                        {{ $item->tanggal_pelayanan->format('d F Y') }}
                                                                        <span class="mx-2">•</span>
                                                                        <i class="bi bi-clock"></i>
                                                                        {{ $item->waktu_mulai }} - {{ $item->waktu_selesai }}
                                                                    </small>
                                                                </div>
                                                                
                                                                <!-- ACTION BUTTON -->
                                                                <div class="d-flex align-items-center gap-2">
                                                                    @if ($item->status === 'draft' && $item->tugas->count() === 0)

                                                                        {{-- Edit --}}
                                                                        <a href="{{ route('sekretaris.jadwal.edit', $item->id_jadwal) }}"
                                                                        class="btn btn-sm btn-light border"
                                                                        title="Edit Jadwal">
                                                                            <i class="bi bi-pencil-square"></i>
                                                                        </a>

                                                                        {{-- Delete --}}
                                                                        <form action="{{ route('sekretaris.jadwal.delete', $item->id_jadwal) }}"
                                                                            method="POST"
                                                                            onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit"
                                                                                    class="btn btn-sm btn-danger"
                                                                                    title="Hapus Jadwal">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        </form>

                                                                    @endif

                                                                    {{-- Close Modal --}}
                                                                    <button type="button" class="btn-close ms-1" data-bs-dismiss="modal"></button>
                                                                </div>

                                                            </div>

                                                            
                                                            <!-- Body -->
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">
                                                                    <!-- Kolom 1: Pengajuan PW -->
                                                                    <div class="col-md-4">
                                                                        <div class="card border-0 bg-light h-100">
                                                                            <div class="card-body">
                                                                                <h6 class="fw-bold text-uppercase mb-3 text-primary" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                                                                    <i class="bi bi-music-note-beamed me-2"></i>PW (Praise & Worship)
                                                                                </h6>

                                                                                @php
                                                                                    // FILTER: hanya yang approved
                                                                                    $tugasPW = $item->tugas->filter(fn($t) => 
                                                                                        $t->user && 
                                                                                        $t->user->id_bidang == 4 && 
                                                                                        $t->status_tugas == 'approved'
                                                                                    );
                                                                                    $approvedPW = $tugasPW->count();
                                                                                    $minPW = $aturanBidang[4]['min'];
                                                                                @endphp

                                                                                <div class="mb-3">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <small class="text-muted">Status:</small>
                                                                                        @if($approvedPW >= $minPW)
                                                                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terpenuhi</span>
                                                                                        @else
                                                                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Belum Terpenuhi</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="progress mt-2" style="height: 8px;">
                                                                                        <div class="progress-bar {{ $approvedPW >= $minPW ? 'bg-success' : 'bg-warning' }}" 
                                                                                            style="width: {{ min(($approvedPW / $minPW) * 100, 100) }}%"></div>
                                                                                    </div>
                                                                                    <small class="text-muted">{{ $approvedPW }}/{{ $minPW }} approved</small>
                                                                                </div>

                                                                                <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                                                                                    @forelse($tugasPW as $t)
                                                                                        <div class="list-group-item px-0 py-2 border-bottom bg-transparent">
                                                                                            <div class="d-flex justify-content-between align-items-start">
                                                                                                <div>
                                                                                                    <div class="fw-bold small">{{ $t->user->nama ?? 'User Terhapus' }}</div>
                                                                                                    <small class="text-muted d-block"><i class="bi bi-briefcase"></i> {{ $t->peran_tugas }}</small>
                                                                                                </div>
                                                                                                <span class="badge bg-success small">
                                                                                                    Approved
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    @empty
                                                                                        <p class="text-muted small text-center py-3">Belum ada personil</p>
                                                                                    @endforelse
                                                                                </div>

                                                                                <!-- Tombol Terima & Tolak Pengajuan PW -->
                                                                                @php
                                                                                    $statusPW = optional($item->pengajuan)
                                                                                        ->where('id_bidang', 4)
                                                                                        ->first()
                                                                                        ?->status_pengajuan;
                                                                                    $noPersonilPW = $tugasPW->count() === 0;
                                                                                @endphp
                                                                                <div class="mt-3">
                                                                                    @if($statusPW === 'approved')
                                                                                        <div class="alert alert-success py-2 small text-center">
                                                                                            <i class="bi bi-check-circle-fill"></i>
                                                                                            Pengajuan PW telah <b>DITERIMA</b>
                                                                                        </div>

                                                                                    @elseif($statusPW === 'declined')
                                                                                        <div class="alert alert-danger py-2 small text-center">
                                                                                            <i class="bi bi-x-circle-fill"></i>
                                                                                            Pengajuan PW telah <b>DITOLAK</b>
                                                                                        </div>

                                                                                    @else
                                                                                        {{-- BARU tampil tombol kalau masih pending --}}
                                                                                        <div class="d-grid gap-2">
                                                                                            <form action="{{ route('sekretaris.pengajuan.approveBidang') }}" method="POST">
                                                                                                @csrf
                                                                                                <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                                <input type="hidden" name="id_bidang" value="4">
                                                                                                <button class="btn btn-sm btn-success w-100" {{ $noPersonilPW ? 'disabled' : '' }}>
                                                                                                    <i class="bi bi-check-circle"></i> Terima Pengajuan PW
                                                                                                </button>
                                                                                            </form>

                                                                                            @if($noPersonilPW)
                                                                                                <button class="btn btn-sm btn-danger w-100" disabled>
                                                                                                    <i class="bi bi-x-circle"></i> Tolak Pengajuan PW
                                                                                                </button>
                                                                                            @else
                                                                                                <button class="btn btn-sm btn-danger w-100"
                                                                                                    data-bs-toggle="modal"
                                                                                                    data-bs-target="#modalTolakBidang{{ $item->id_jadwal }}-4">
                                                                                                    <i class="bi bi-x-circle"></i> Tolak Pengajuan PW
                                                                                                </button>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Kolom 2: Pengajuan Usher -->
                                                                    <div class="col-md-4">
                                                                        <div class="card border-0 bg-light h-100">
                                                                            <div class="card-body">
                                                                                <h6 class="fw-bold text-uppercase mb-3 text-info" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                                                                    <i class="bi bi-person-raised-hand me-2"></i>Usher
                                                                                </h6>

                                                                                @php
                                                                                    // FILTER: hanya yang approved
                                                                                    $tugasUsher = $item->tugas->filter(fn($t) => 
                                                                                        $t->user && 
                                                                                        $t->user->id_bidang == 1 && 
                                                                                        $t->status_tugas == 'approved'
                                                                                    );
                                                                                    $approvedUsher = $tugasUsher->count();
                                                                                    $minUsher = $aturanBidang[1]['min'];
                                                                                @endphp

                                                                                <div class="mb-3">
                                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                                        <small class="text-muted">Status:</small>
                                                                                        @if($approvedUsher >= $minUsher)
                                                                                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Terpenuhi</span>
                                                                                        @else
                                                                                            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Belum Terpenuhi</span>
                                                                                        @endif
                                                                                    </div>
                                                                                    <div class="progress mt-2" style="height: 8px;">
                                                                                        <div class="progress-bar {{ $approvedUsher >= $minUsher ? 'bg-success' : 'bg-warning' }}" 
                                                                                            style="width: {{ min(($approvedUsher / $minUsher) * 100, 100) }}%"></div>
                                                                                    </div>
                                                                                    <small class="text-muted">{{ $approvedUsher }}/{{ $minUsher }} approved</small>
                                                                                </div>

                                                                                <div class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                                                                                    @forelse($tugasUsher as $t)
                                                                                        <div class="list-group-item px-0 py-2 border-bottom bg-transparent">
                                                                                            <div class="d-flex justify-content-between align-items-start">
                                                                                                <div>
                                                                                                    <div class="fw-bold small">{{ $t->user->nama ?? 'User Terhapus' }}</div>
                                                                                                    <small class="text-muted d-block"><i class="bi bi-briefcase"></i> {{ $t->peran_tugas }}</small>
                                                                                                </div>
                                                                                                <span class="badge bg-success small">
                                                                                                    Approved
                                                                                                </span>
                                                                                            </div>
                                                                                        </div>
                                                                                    @empty
                                                                                        <p class="text-muted small text-center py-3">Belum ada personil</p>
                                                                                    @endforelse
                                                                                </div>

                                                                                <!-- Tombol Terima & Tolak Pengajuan Usher -->
                                                                                @php
                                                                                    $statusUsher = optional($item->pengajuan)
                                                                                        ->where('id_bidang', 1)
                                                                                        ->first()
                                                                                        ?->status_pengajuan;
                                                                                    $noPersonilUsher = $tugasUsher->count() === 0;
                                                                                @endphp
                                                                                <div class="mt-3">
                                                                                    @if($statusUsher === 'approved')
                                                                                        <div class="alert alert-success py-2 small text-center">
                                                                                            <i class="bi bi-check-circle-fill"></i>
                                                                                            Pengajuan Usher telah <b>DITERIMA</b>
                                                                                        </div>
                                                                                    @elseif($statusUsher === 'declined')
                                                                                        <div class="alert alert-danger py-2 small text-center">
                                                                                            <i class="bi bi-x-circle-fill"></i>
                                                                                            Pengajuan Usher telah <b>DITOLAK</b>
                                                                                        </div>
                                                                                    @else
                                                                                        {{-- BARU tampil tombol kalau masih pending --}}
                                                                                        <div class="d-grid gap-2">
                                                                                            <form action="{{ route('sekretaris.pengajuan.approveBidang') }}" method="POST">
                                                                                                @csrf
                                                                                                <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                                <input type="hidden" name="id_bidang" value="1">
                                                                                                <button class="btn btn-sm btn-success w-100" {{ $noPersonilUsher ? 'disabled' : '' }}>
                                                                                                    <i class="bi bi-check-circle"></i> Terima Pengajuan Usher
                                                                                                </button>
                                                                                            </form>

                                                                                            @if($noPersonilUsher)
                                                                                                <button class="btn btn-sm btn-danger w-100" disabled>
                                                                                                    <i class="bi bi-x-circle"></i> Tolak Pengajuan Usher
                                                                                                </button>
                                                                                            @else
                                                                                                <button class="btn btn-sm btn-danger w-100"
                                                                                                    data-bs-toggle="modal"
                                                                                                    data-bs-target="#modalTolakBidang{{ $item->id_jadwal }}-1">
                                                                                                    <i class="bi bi-x-circle"></i> Tolak Pengajuan Usher
                                                                                                </button>
                                                                                            @endif
                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Kolom 3: Assign Sekretaris (Pembicara, Pendoa, Multimedia) -->
                                                                    <div class="col-md-4">
                                                                        <div class="card border-0 bg-light h-100">
                                                                            <div class="card-body">
                                                                                <h6 class="fw-bold text-uppercase mb-3 text-success" style="font-size: 0.875rem; letter-spacing: 0.5px;">
                                                                                    <i class="bi bi-person-plus-fill me-2"></i>Assign Personil Lain
                                                                                </h6>

                                                                                <!-- Pembicara -->
                                                                                @php
                                                                                    $tugasPembicara = $item->tugas->filter(fn($t) => $t->user && $t->user->id_bidang == 2);
                                                                                    $approvedPembicara = $tugasPembicara->where('status_tugas', 'approved')->count();
                                                                                    // Jika ada pembicara eksternal, hitung sebagai 1 approved untuk bidang pembicara
                                                                                    $hasPembicaraEksternal = !empty($item->pembicaraEksternal);
                                                                                    $approvedPembicaraTotal = $approvedPembicara + ($hasPembicaraEksternal ? 1 : 0);
                                                                                    $maxPembicara = $aturanBidang[2]['max'];
                                                                                @endphp
                                                                                <div class="mb-3 border-bottom pb-3">
                                                                                    <label class="form-label small fw-semibold text-muted mb-2">
                                                                                        <i class="bi bi-mic-fill text-danger"></i> Pembicara (Max {{ $maxPembicara }})
                                                                                            <span class="badge bg-secondary ms-1">{{ $approvedPembicaraTotal }}/{{ $maxPembicara }}</span>
                                                                                    </label>
                                                                                    
                                                                                    @if($approvedPembicaraTotal < $maxPembicara)
                                                                                        <form action="{{ route('sekretaris.pengajuan.assign') }}" method="POST" class="mb-2">
                                                                                            @csrf
                                                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                            <div class="input-group input-group-sm">
                                                                                                <select name="id_user" class="form-select form-select-sm" required>
                                                                                                    <option value="">-- Pilih --</option>
                                                                                                    @foreach ($daftarPembicara as $p)
                                                                                                            @if (! $item->tugas->contains('id_user', $p->id_user))
                                                                                                                <option value="{{ $p->id_user }}">{{ $p->nama }}</option>
                                                                                                            @endif
                                                                                                        @endforeach
                                                                                                </select>
                                                                                                <input type="text" name="peran_tugas" class="form-control form-control-sm" placeholder="Peran" value="Pembicara" required>
                                                                                                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-plus"></i></button>
                                                                                            </div>
                                                                                        </form>
                                                                                    @endif

                                                                                    <div class="small">
                                                                                        @forelse($tugasPembicara as $t)
                                                                                            <div class="d-flex justify-content-between align-items-center py-1">
                                                                                                <span>{{ $t->user->nama ?? 'N/A' }}</span>
                                                                                                <div>
                                                                                                    <span class="badge {{ $t->status_tugas == 'approved' ? 'bg-success' : 'bg-warning text-dark' }} me-1">
                                                                                                        {{ ucfirst($t->status_tugas) }}
                                                                                                    </span>
                                                                                                    @if($t->status_tugas == 'pending')
                                                                                                        <form action="{{ route('sekretaris.pengajuan.batal', $t->id_tugas) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan?')">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                                                                        </form>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @empty
                                                                                            @if(! $hasPembicaraEksternal)
                                                                                                <small class="text-muted fst-italic">Belum ada</small>
                                                                                            @endif
                                                                                        @endforelse

                                                                                        {{-- Tampilkan pembicara eksternal jika ada --}}
                                                                                        @if($hasPembicaraEksternal)
                                                                                            <div class="mt-2 p-2 border rounded bg-white">
                                                                                                <div class="d-flex justify-content-between align-items-start">
                                                                                                    <div>
                                                                                                        <div class="fw-bold">{{ $item->pembicaraEksternal->nama_pembicara }}</div>
                                                                                                        <div class="small text-muted">{{ $item->pembicaraEksternal->asal_gereja ?? '-' }}</div>
                                                                                                        <div class="small text-muted">{{ $item->pembicaraEksternal->kontak ?? '-' }}</div>
                                                                                                        @if($item->pembicaraEksternal->keterangan)
                                                                                                            <div class="small text-muted mt-1">{{ $item->pembicaraEksternal->keterangan }}</div>
                                                                                                        @endif
                                                                                                    </div>
                                                                                                    <span class="badge bg-primary small">Eksternal</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Pendoa -->
                                                                                @php
                                                                                    $tugasPendoa = $item->tugas->filter(fn($t) => $t->user && $t->user->id_bidang == 3);
                                                                                    $approvedPendoa = $tugasPendoa->where('status_tugas', 'approved')->count();
                                                                                    $maxPendoa = $aturanBidang[3]['max'];
                                                                                @endphp
                                                                                <div class="mb-3 border-bottom pb-3">
                                                                                    <label class="form-label small fw-semibold text-muted mb-2">
                                                                                        <i class="bi bi-hand-thumbs-up-fill text-primary"></i> Pendoa (Max {{ $maxPendoa }})
                                                                                        <span class="badge bg-secondary ms-1">{{ $approvedPendoa }}/{{ $maxPendoa }}</span>
                                                                                    </label>
                                                                                    
                                                                                    @if($approvedPendoa < $maxPendoa)
                                                                                        <form action="{{ route('sekretaris.pengajuan.assign') }}" method="POST" class="mb-2">
                                                                                            @csrf
                                                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                            <div class="input-group input-group-sm">
                                                                                                <select name="id_user" class="form-select form-select-sm" required>
                                                                                                    <option value="">-- Pilih --</option>
                                                                                                    @foreach ($daftarPendoa as $p)
                                                                                                        @if (! $item->tugas->contains('id_user', $p->id_user))
                                                                                                            <option value="{{ $p->id_user }}">{{ $p->nama }}</option>
                                                                                                        @endif
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                <input type="text" name="peran_tugas" class="form-control form-control-sm" placeholder="Peran" value="Pendoa" required>
                                                                                                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-plus"></i></button>
                                                                                            </div>
                                                                                        </form>
                                                                                    @endif

                                                                                    <div class="small">
                                                                                        @forelse($tugasPendoa as $t)
                                                                                            <div class="d-flex justify-content-between align-items-center py-1">
                                                                                                <span>{{ $t->user->nama ?? 'N/A' }}</span>
                                                                                                <div>
                                                                                                    <span class="badge {{ $t->status_tugas == 'approved' ? 'bg-success' : 'bg-warning text-dark' }} me-1">
                                                                                                        {{ ucfirst($t->status_tugas) }}
                                                                                                    </span>
                                                                                                    @if($t->status_tugas == 'pending')
                                                                                                        <form action="{{ route('sekretaris.pengajuan.batal', $t->id_tugas) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan?')">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                                                                        </form>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @empty
                                                                                            <small class="text-muted fst-italic">Belum ada</small>
                                                                                        @endforelse
                                                                                    </div>
                                                                                </div>

                                                                                <!-- Multimedia -->
                                                                                @php
                                                                                    $tugasMultimedia = $item->tugas->filter(fn($t) => $t->user && $t->user->id_bidang == 5);
                                                                                    $approvedMultimedia = $tugasMultimedia->where('status_tugas', 'approved')->count();
                                                                                    $minMultimedia = $aturanBidang[5]['min'];
                                                                                @endphp
                                                                                <div class="mb-3">
                                                                                    <label class="form-label small fw-semibold text-muted mb-2">
                                                                                        <i class="bi bi-camera-video-fill text-warning"></i> Multimedia (Min {{ $minMultimedia }})
                                                                                        <span class="badge bg-secondary ms-1">{{ $approvedMultimedia }}/{{ $minMultimedia }}</span>
                                                                                    </label>
                                                                                    
                                                                                    {{-- Tambahkan kondisi ini --}}
                                                                                    @if($item->status === 'draft' || $item->status === 'pending')
                                                                                        <form action="{{ route('sekretaris.pengajuan.assign') }}" method="POST" class="mb-2">
                                                                                            @csrf
                                                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                            <div class="input-group input-group-sm">
                                                                                                <select name="id_user" class="form-select form-select-sm" required>
                                                                                                    <option value="">-- Pilih --</option>
                                                                                                    @foreach ($daftarMultimedia as $p)
                                                                                                        @if (! $item->tugas->contains('id_user', $p->id_user))
                                                                                                            <option value="{{ $p->id_user }}">{{ $p->nama }}</option>
                                                                                                        @endif
                                                                                                    @endforeach
                                                                                                </select>
                                                                                                <input type="text" name="peran_tugas" class="form-control form-control-sm" placeholder="Peran" required>
                                                                                                <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-plus"></i></button>
                                                                                            </div>
                                                                                        </form>
                                                                                    @endif

                                                                                    <div class="small">
                                                                                        @forelse($tugasMultimedia as $t)
                                                                                            <div class="d-flex justify-content-between align-items-center py-1">
                                                                                                <span>{{ $t->user->nama ?? 'N/A' }}</span>
                                                                                                <div>
                                                                                                    <span class="badge {{ $t->status_tugas == 'approved' ? 'bg-success' : 'bg-warning text-dark' }} me-1">
                                                                                                        {{ ucfirst($t->status_tugas) }}
                                                                                                    </span>
                                                                                                    @if($t->status_tugas == 'pending')
                                                                                                        <form action="{{ route('sekretaris.pengajuan.batal', $t->id_tugas) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan?')">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                                                                        </form>
                                                                                                    @endif
                                                                                                </div>
                                                                                            </div>
                                                                                        @empty
                                                                                            <small class="text-muted fst-italic">Belum ada</small>
                                                                                        @endforelse
                                                                                    </div>
                                                                                </div>                   
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Footer -->
                                                            <div class="modal-footer bg-light border-top">
                                                                <div class="w-100">

                                                                    @php
                                                                        $statusJadwal = $item->status;
                                                                    @endphp

                                                                    {{-- JADWAL MASIH DRAFT → BOLEH AJUKAN --}}
                                                                    @if ($statusJadwal === 'draft')

                                                                        @php
                                                                            $semuaTerpenuhi = true;
                                                                            $pesanError = [];

                                                                            foreach($aturanBidang as $idB => $rule) {
                                                                                // Untuk bidang pembicara, hitung juga pembicara eksternal sebagai 1
                                                                                if ($idB == 2) {
                                                                                    $count = $item->tugas
                                                                                        ->where('status_tugas', 'approved')
                                                                                        ->filter(fn($t) => $t->user && $t->user->id_bidang == $idB)
                                                                                        ->count();

                                                                                    if (!empty($item->pembicaraEksternal)) {
                                                                                        $count += 1;
                                                                                    }
                                                                                } else {
                                                                                    $count = $item->tugas
                                                                                        ->where('status_tugas', 'approved')
                                                                                        ->filter(fn($t) => $t->user && $t->user->id_bidang == $idB)
                                                                                        ->count();
                                                                                }

                                                                                if($count < $rule['min']) {
                                                                                    $semuaTerpenuhi = false;
                                                                                    $pesanError[] = "{$rule['nama']} {$count}/{$rule['min']}";
                                                                                }
                                                                            }
                                                                        @endphp

                                                                        @if($semuaTerpenuhi)
                                                                            <form action="{{ route('sekretaris.pengajuan.approve') }}" method="POST">
                                                                                @csrf
                                                                                <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                                                <button class="btn btn-success w-100 fw-bold py-2"
                                                                                    onclick="return confirm('Ajukan jadwal ini ke Penatua?')">
                                                                                    <i class="bi bi-send-check-fill"></i>
                                                                                    Ajukan Jadwal ke Penatua
                                                                                </button>
                                                                            </form>
                                                                        @else
                                                                            <button class="btn btn-secondary w-100 fw-bold py-2" disabled>
                                                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                                                Belum Terpenuhi: {{ implode(', ', $pesanError) }}
                                                                            </button>
                                                                        @endif


                                                                    {{-- JADWAL SUDAH DIKIRIM KE PENATUA --}}
                                                                    @elseif ($statusJadwal === 'pending')
                                                                        <button class="btn btn-warning w-100 fw-bold py-2" disabled>
                                                                            <i class="bi bi-hourglass-split"></i>
                                                                            Jadwal sedang diajukan ke Penatua
                                                                        </button>

                                                                    {{-- SUDAH DISETUJUI PENATUA → BISA PUBLISH --}}
                                                                    @elseif ($statusJadwal === 'approved')
                                                                        <div class="mb-2">
                                                                            <div class="alert alert-success py-2 small text-center mb-3">
                                                                                <i class="bi bi-check-circle-fill"></i>
                                                                                Jadwal telah <b>DISETUJUI</b> oleh Penatua
                                                                            </div>
                                                                            <div class="d-grid">
                                                                                <button type="button" 
                                                                                        class="btn btn-primary w-100 fw-bold py-2"
                                                                                        data-bs-toggle="modal" 
                                                                                        data-bs-target="#modalPublish{{ $item->id_jadwal }}">
                                                                                    <i class="bi bi-megaphone-fill"></i> Publish Jadwal Pelayanan
                                                                                </button>
                                                                            </div>
                                                                        </div>

                                                                    {{-- SUDAH DIPUBLISH --}}
                                                                    @elseif ($statusJadwal === 'published')
                                                                        <div class="alert alert-info py-2 small text-center mb-0">
                                                                            <i class="bi bi-megaphone-fill"></i>
                                                                            Jadwal telah <b>DIPUBLISH</b> dan dapat dilihat oleh semua pekerja
                                                                        </div>

                                                                    {{-- DITOLAK PENATUA → AMBIL ACTION BERDASARKAN DAMPAK --}}
                                                                    @elseif ($statusJadwal === 'declined')
                                                                        @php
                                                                            $kategori = $item->kategoriPenolakan ?? null;
                                                                            $dampak = $kategori?->dampak ?? [];
                                                                            $tugasDampak = $dampak['tugas'] ?? null;
                                                                            $jadwalDampak = $dampak['jadwal'] ?? null;
                                                                        @endphp

                                                                        <div class="mb-2">
                                                                            <div class="alert alert-danger py-2 small text-center mb-3">
                                                                                <i class="bi bi-x-circle-fill"></i>
                                                                                Jadwal <b>DITOLAK</b> oleh Penatua
                                                                            </div>

                                                                            @if($kategori)
                                                                                <div class="alert alert-info small mb-2">
                                                                                    <strong>Kategori penolakan:</strong>
                                                                                    <div class="mt-1"><b>{{ $kategori->nama }}</b>@if(!empty($kategori->deskripsi)) - {{ $kategori->deskripsi }}@endif</div>
                                                                                </div>
                                                                            @endif

                                                                            @if(!empty($item->alasan_penolakan))
                                                                                <div class="alert alert-secondary small mb-3">
                                                                                    <strong>Alasan penolakan:</strong>
                                                                                    <div class="mt-1">{{ $item->alasan_penolakan }}</div>
                                                                                </div>
                                                                            @endif

                                                                            <div class="d-grid gap-2">
                                                                                {{-- CASE 1: jadwal="edit" --}}
                                                                                @if($jadwalDampak === 'edit')
                                                                                    
                                                                                    {{-- SUB-CASE: tugas="pending" → Harus edit jadwal dulu, tampilkan modal konfirmasi --}}
                                                                                    @if($tugasDampak === 'pending')
                                                                                        <div class="alert alert-warning small text-center py-2 mb-2">
                                                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                                                            Anda harus <b>mengedit jadwal pelayanan</b> dan semua pekerja akan diminta konfirmasi ulang
                                                                                        </div>
                                                                                        
                                                                                        <button type="button" 
                                                                                                class="btn btn-primary w-100 fw-bold py-2"
                                                                                                data-bs-toggle="modal" 
                                                                                                data-bs-target="#modalKonfirmasiPending{{ $item->id_jadwal }}">
                                                                                            <i class="bi bi-pencil-square"></i> Edit Jadwal Pelayanan
                                                                                        </button>

                                                                                    {{-- SUB-CASE: tugas="keep" → Langsung edit saja --}}
                                                                                    @elseif($tugasDampak === 'keep')
                                                                                        <a href="{{ route('sekretaris.jadwal.edit', $item->id_jadwal) }}" 
                                                                                        class="btn btn-outline-primary w-100 fw-bold py-2">
                                                                                            <i class="bi bi-pencil-square"></i> Edit Jadwal Kebaktian
                                                                                        </a>

                                                                                        <div class="alert alert-success small text-center py-2 mb-0">
                                                                                            <i class="bi bi-info-circle"></i> Personil tetap; tidak perlu konfirmasi ulang.
                                                                                        </div>

                                                                                    {{-- SUB-CASE: tugas="reset" --}}
                                                                                    @elseif($tugasDampak === 'reset')
                                                                                        <a href="{{ route('sekretaris.jadwal.create', ['from' => $item->id_jadwal]) }}" 
                                                                                        class="btn btn-warning w-100 fw-bold py-2">
                                                                                            <i class="bi bi-arrow-clockwise"></i> Buat Ulang Jadwal Kebaktian
                                                                                        </a>
                                                                                    @else
                                                                                        {{-- Fallback --}}
                                                                                        <a href="{{ route('sekretaris.jadwal.create', ['from' => $item->id_jadwal]) }}" 
                                                                                        class="btn btn-warning w-100 fw-bold py-2">
                                                                                            <i class="bi bi-arrow-clockwise"></i> Buat Ulang Jadwal Kebaktian
                                                                                        </a>
                                                                                    @endif

                                                                                {{-- CASE 2: jadwal="recreate" (sudah otomatis dibuat draft baru) --}}
                                                                                @elseif($jadwalDampak === 'recreate')
                                                                                    @php
                                                                                        // Cari jadwal draft yang baru dibuat dari yang ditolak ini
                                                                                        $jadwalBaru = \App\Models\JadwalKebaktian::where('status', 'draft')
                                                                                            ->where('tanggal_pelayanan', $item->tanggal_pelayanan)
                                                                                            ->where('waktu_mulai', $item->waktu_mulai)
                                                                                            ->where('jenis_kebaktian', $item->jenis_kebaktian)
                                                                                            ->where('id_jadwal', '!=', $item->id_jadwal)
                                                                                            ->first();
                                                                                    @endphp

                                                                                    @if($jadwalBaru)
                                                                                        <div class="alert alert-info small text-center py-2 mb-2">
                                                                                            <i class="bi bi-info-circle-fill"></i>
                                                                                            Jadwal baru telah dibuat otomatis dengan status <b>DRAFT</b>
                                                                                        </div>
                                                                                        <a href="{{ route('sekretaris.pengajuan.index') }}" class="btn btn-primary w-100 fw-bold py-2">
                                                                                            <i class="bi bi-eye"></i> Lihat Jadwal Baru
                                                                                        </a>
                                                                                    @else
                                                                                        {{-- Jika belum ada, tampilkan tombol buat baru --}}
                                                                                        <a href="{{ route('sekretaris.jadwal.create', ['from' => $item->id_jadwal]) }}" 
                                                                                        class="btn btn-warning w-100 fw-bold py-2">
                                                                                            <i class="bi bi-arrow-clockwise"></i> Buat Ulang Jadwal Kebaktian
                                                                                        </a>
                                                                                    @endif

                                                                                {{-- CASE 3: Fallback / tidak ada kategori --}}
                                                                                @else
                                                                                    <a href="{{ route('sekretaris.jadwal.create', ['from' => $item->id_jadwal]) }}" 
                                                                                    class="btn btn-warning w-100 fw-bold py-2">
                                                                                        <i class="bi bi-arrow-clockwise"></i> Buat Ulang Jadwal Kebaktian
                                                                                    </a>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                            <!-- External modals for Tolak Bidang (placed outside nested modal to avoid nesting issues) -->
                                            <div class="modal fade" id="modalTolakBidang{{ $item->id_jadwal }}-4" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title fw-bold">Tolak Pengajuan PW</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('sekretaris.pengajuan.declineBidang') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                            <input type="hidden" name="id_bidang" value="4">
                                                            <div class="modal-body">
                                                                <label class="form-label fw-semibold">Alasan Penolakan:</label>
                                                                <textarea name="alasan_penolakan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menolak pengajuan PW?')">
                                                                    <i class="bi bi-x-circle"></i> Tolak
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="modalTolakBidang{{ $item->id_jadwal }}-1" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title fw-bold">Tolak Pengajuan Usher</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('sekretaris.pengajuan.declineBidang') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">
                                                            <input type="hidden" name="id_bidang" value="1">
                                                            <div class="modal-body">
                                                                <label class="form-label fw-semibold">Alasan Penolakan:</label>
                                                                <textarea name="alasan_penolakan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menolak pengajuan Usher?')">
                                                                    <i class="bi bi-x-circle"></i> Tolak
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal Konfirmasi Edit Jadwal + Pending Pekerja --}}
                                            <div class="modal fade" id="modalKonfirmasiPending{{ $item->id_jadwal }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header bg-warning text-dark">
                                                            <h5 class="modal-title fw-bold">
                                                                <i class="bi bi-exclamation-triangle-fill"></i> Perhatian: Edit Jadwal Pelayanan
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="alert alert-warning border-0 mb-3">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                                                    <div>
                                                                        <h6 class="fw-bold mb-2">Apa yang akan terjadi?</h6>
                                                                        <p class="mb-0 small">
                                                                            Setelah Anda mengedit jadwal pelayanan, sistem akan secara otomatis:
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <ul class="list-group list-group-flush mb-3">
                                                                <li class="list-group-item border-0 ps-0">
                                                                    <i class="bi bi-1-circle-fill text-primary me-2"></i>
                                                                    Menduplikasi jadwal kebaktian dengan data yang baru
                                                                </li>
                                                                <li class="list-group-item border-0 ps-0">
                                                                    <i class="bi bi-2-circle-fill text-primary me-2"></i>
                                                                    Mengatur ulang status <b>semua pekerja</b> menjadi <span class="badge bg-warning text-dark">PENDING</span>
                                                                </li>
                                                                <li class="list-group-item border-0 ps-0">
                                                                    <i class="bi bi-3-circle-fill text-primary me-2"></i>
                                                                    Mengirim notifikasi konfirmasi ulang ke semua pekerja
                                                                </li>
                                                            </ul>

                                                            <div class="alert alert-danger border-0 small mb-0">
                                                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                                <strong>Penting:</strong> Semua pekerja (PW, Usher, Pembicara, Pendoa, Multimedia) harus mengkonfirmasi ulang ketersediaan mereka setelah jadwal diubah.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> Batal
                                                            </button>
                                                            
                                                            <a href="{{ route('sekretaris.jadwal.edit', $item->id_jadwal) }}" 
                                                            class="btn btn-primary fw-bold">
                                                                <i class="bi bi-pencil-square"></i> Lanjutkan Edit Jadwal
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Modal Konfirmasi Publish Jadwal --}}
                                            <div class="modal fade" id="modalPublish{{ $item->id_jadwal }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title fw-bold">
                                                                <i class="bi bi-megaphone-fill"></i> Publish Jadwal Pelayanan
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="alert alert-warning border-0 mb-3">
                                                                <div class="d-flex align-items-start">
                                                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                                                                    <div>
                                                                        <h6 class="fw-bold mb-2">Perhatian!</h6>
                                                                        <p class="mb-0 small">
                                                                            Setelah jadwal dipublish, hal berikut akan terjadi:
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <ul class="list-group list-group-flush mb-3">
                                                                <li class="list-group-item border-0 ps-0">
                                                                    <i class="bi bi-1-circle-fill text-primary me-2"></i>
                                                                    Jadwal akan <b>terlihat oleh semua pekerja</b> di dashboard mereka
                                                                </li>
                                                                <li class="list-group-item border-0 ps-0">
                                                                    <i class="bi bi-2-circle-fill text-primary me-2"></i>
                                                                    Jadwal yang sudah dipublish <b>tidak dapat diedit lagi</b>
                                                                </li>
                                                                <li class="list-group-item border-0 ps-0">
                                                                    <i class="bi bi-3-circle-fill text-primary me-2"></i>
                                                                    Semua pekerja akan menerima notifikasi
                                                                </li>
                                                            </ul>

                                                            <div class="alert alert-info border-0 small mb-0">
                                                                <i class="bi bi-info-circle-fill me-2"></i>
                                                                <strong>Info:</strong> Pastikan semua data sudah benar sebelum mempublish.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> Batal
                                                            </button>
                                                            
                                                            <form action="{{ route('sekretaris.jadwal.publish', $item->id_jadwal) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-primary fw-bold">
                                                                    <i class="bi bi-megaphone-fill"></i> Ya, Publish Sekarang
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
@endsection