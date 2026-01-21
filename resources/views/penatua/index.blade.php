@extends('layouts.index')

@section('content')
<main class="py-4">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-calendar-check text-primary me-2"></i>
                Approval Jadwal Pelayanan
            </h2>
            <p class="text-muted mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Klik jadwal untuk melihat detail & menyetujui
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <span class="badge bg-warning text-dark px-3 py-2 shadow-sm">
                <i class="bi bi-clock me-1"></i>Pending
            </span>
            <span class="badge bg-success px-3 py-2 shadow-sm">
                <i class="bi bi-check-circle me-1"></i>Disetujui
            </span>
            <span class="badge bg-danger px-3 py-2 shadow-sm">
                <i class="bi bi-x-circle me-1"></i>Ditolak
            </span>
            <span class="badge bg-primary px-3 py-2 shadow-sm">
                <i class="bi bi-broadcast me-1"></i>Published
            </span>
        </div>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 border-start border-success border-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 border-start border-danger border-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Error!</strong> {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- STATISTIK BULAN INI --}}
    @php
        use App\Models\JadwalKebaktian;
        $statsMonth = JadwalKebaktian::whereMonth('tanggal_pelayanan', $currentDate->month)
            ->whereYear('tanggal_pelayanan', $currentDate->year)
            ->selectRaw("
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'declined' THEN 1 END) as declined,
                COUNT(CASE WHEN status = 'published' THEN 1 END) as published
            ")
            ->first();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-start border-warning border-4 hover-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1 text-uppercase">Pending</small>
                            <h3 class="fw-bold mb-0 text-warning">{{ $statsMonth->pending ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-clock-history fs-3 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-start border-success border-4 hover-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1 text-uppercase">Disetujui</small>
                            <h3 class="fw-bold mb-0 text-success">{{ $statsMonth->approved ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-check-circle fs-3 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-start border-danger border-4 hover-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1 text-uppercase">Ditolak</small>
                            <h3 class="fw-bold mb-0 text-danger">{{ $statsMonth->declined ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-x-circle fs-3 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-4 hover-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block mb-1 text-uppercase">Published</small>
                            <h3 class="fw-bold mb-0 text-primary">{{ $statsMonth->published ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="bi bi-broadcast fs-3 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <a href="{{ route('penatua.jadwal', ['month'=>$currentDate->copy()->subMonth()->month,'year'=>$currentDate->copy()->subMonth()->year]) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-chevron-left"></i>
            </a>

            <h5 class="fw-bold mb-0">{{ $currentDate->translatedFormat('F Y') }}</h5>

            <a href="{{ route('penatua.jadwal', ['month'=>$currentDate->copy()->addMonth()->month,'year'=>$currentDate->copy()->addMonth()->year]) }}"
               class="btn btn-sm btn-outline-primary">
                <i class="bi bi-chevron-right"></i>
            </a>
        </div>

        <div class="card-body p-0">
            <table class="table table-bordered mb-0 text-center" style="table-layout:fixed;">
                <thead class="bg-light">
                    <tr>
                        <th class="text-danger">Minggu</th>
                        <th>Senin</th>
                        <th>Selasa</th>
                        <th>Rabu</th>
                        <th>Kamis</th>
                        <th>Jumat</th>
                        <th>Sabtu</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($calendarGrid as $week)
                    <tr>
                        @foreach ($week as $day)
                        <td class="p-2 {{ !$day['isCurrentMonth'] ? 'bg-light opacity-50':'' }}" style="height:150px;vertical-align:top">
                            <div class="text-end fw-bold mb-2">{{ $day['date']->day }}</div>

                            @foreach ($day['jadwal'] as $j)
                                @php
                                    $statusColor = match($j->status) {
                                        'approved' => 'success',
                                        'declined' => 'danger',
                                        default => 'warning'
                                    };
                                @endphp

                                <div class="card border-start border-3 border-{{ $statusColor }} shadow-sm mb-1"
                                     style="font-size:0.75rem;cursor:pointer"
                                     data-bs-toggle="modal"
                                     data-bs-target="#modal{{ $j->id_jadwal }}">
                                    <div class="card-body p-2 text-start">
                                        <div class="fw-bold text-primary">
                                            {{ $j->jenis_kebaktian }}
                                        </div>
                                        <small class="text-muted d-block">{{ $j->waktu_mulai }}</small>
                                    </div>
                                </div>

                                {{-- MODAL DETAIL --}}
                                <div class="modal fade" id="modal{{ $j->id_jadwal }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <div>
                                                    <h5 class="modal-title mb-1">Detail Jadwal Pelayanan</h5>
                                                    <span class="badge bg-{{ $statusColor }}">
                                                        {{ strtoupper($j->status) }}
                                                    </span>
                                                </div>
                                                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body text-start">
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="mb-2"><i class="bi bi-calendar3 text-primary me-2"></i><b>Tanggal:</b> {{ $j->tanggal_pelayanan->format('d F Y') }}</p>
                                                        <p class="mb-2"><i class="bi bi-clock text-primary me-2"></i><b>Waktu:</b> {{ $j->waktu_mulai }} - {{ $j->waktu_selesai }}</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="mb-2"><i class="bi bi-geo-alt text-primary me-2"></i><b>Lokasi:</b> {{ $j->lokasi }}</p>
                                                        <p class="mb-2"><i class="bi bi-bookmark text-primary me-2"></i><b>Tema:</b> {{ $j->tema }}</p>
                                                    </div>
                                                </div>

                                                @if($j->status == 'declined' && $j->alasan_penolakan)
                                                    <div class="alert alert-danger">
                                                        <b>Alasan Penolakan:</b><br>
                                                        {{ $j->alasan_penolakan }}
                                                    </div>
                                                @endif

                                                <hr>

                                                <h6 class="fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i>Daftar Pelayan</h6>

                                                @php
                                                    // Group by bidang, bukan peran_tugas
                                                    $grouped = $j->tugas->groupBy(function($item) {
                                                        return $item->user->bidang->nama_bidang ?? 'Tidak Ada Bidang';
                                                    });
                                                @endphp

                                                @foreach ($grouped as $namaBidang => $list)
                                                    <div class="mb-4">
                                                        <div class="bg-primary bg-opacity-10 p-2 rounded mb-2">
                                                            <h6 class="mb-0 text-uppercase fw-bold text-primary">
                                                                <i class="bi bi-person-badge me-2"></i>{{ $namaBidang }}
                                                            </h6>
                                                        </div>
                                                        
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-hover align-middle mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th style="width: 50%">Nama Pelayan</th>
                                                                        <th style="width: 50%">Tugas</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($list as $t)
                                                                        <tr>
                                                                            <td>
                                                                                <i class="bi bi-person-circle text-muted me-2"></i>
                                                                                {{ $t->user->nama }}
                                                                            </td>
                                                                            <td>
                                                                                <span class="badge bg-light text-dark">
                                                                                    {{ $t->peran_tugas }}
                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                {{-- Pembicara Eksternal --}}
                                                @if(!empty($j->pembicaraEksternal))
                                                    <hr>
                                                    <h6 class="fw-bold mb-3"><i class="bi bi-mic-fill text-warning me-2"></i>Pembicara Eksternal</h6>
                                                    <div class="card border-0 bg-light mb-3">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <div>
                                                                    <div class="fw-bold">{{ $j->pembicaraEksternal->nama_pembicara }}</div>
                                                                    <div class="small text-muted">Asal: {{ $j->pembicaraEksternal->asal_gereja ?? '-' }}</div>
                                                                    <div class="small text-muted">Kontak: {{ $j->pembicaraEksternal->kontak ?? '-' }}</div>
                                                                    @if($j->pembicaraEksternal->keterangan)
                                                                        <div class="small text-muted mt-1">{{ $j->pembicaraEksternal->keterangan }}</div>
                                                                    @endif
                                                                </div>
                                                                <span class="badge bg-primary small">Eksternal</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($j->status == 'pending')
                                                <div class="modal-footer">
                                                    <form action="{{ route('penatua.jadwal.approve',$j->id_jadwal) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-success">
                                                            <i class="bi bi-check-circle me-1"></i>Setujui
                                                        </button>
                                                    </form>

                                                    <button class="btn btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#reject{{ $j->id_jadwal }}">
                                                        <i class="bi bi-x-circle me-1"></i>Tolak
                                                    </button>
                                                </div>
                                            @else
                                                <div class="modal-footer">
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- MODAL TOLAK --}}
                                @if($j->status == 'pending')
                                    <div class="modal fade" id="reject{{ $j->id_jadwal }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form method="POST"
                                                  action="{{ route('penatua.jadwal.reject',$j->id_jadwal) }}"
                                                  class="modal-content">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="form-label fw-bold">Kategori Penolakan:</label>
                                                    <select name="kategori_penolakan_id" class="form-select mb-3" required>
                                                        <option value="">Pilih kategori penolakan...</option>
                                                        @foreach($kategoriPenolakan as $kat)
                                                            <option value="{{ $kat->id }}">{{ $kat->nama ?? $kat->label ?? $kat->kategori ?? ('Kategori '.$kat->id) }}</option>
                                                        @endforeach
                                                    </select>

                                                    <label class="form-label fw-bold">Berikan alasan penolakan:</label>
                                                    <textarea name="alasan_penolakan"
                                                              class="form-control"
                                                              rows="4"
                                                              placeholder="Tuliskan alasan penolakan jadwal..."
                                                              required></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-send me-1"></i>Kirim Penolakan
                                                    </button>
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
</main>
@endsection