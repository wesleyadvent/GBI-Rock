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
                                                    <div class="card border-0 shadow-sm mb-1 border-start border-3 border-primary item-jadwal" 
                                                        data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id_jadwal }}">
                                                        <div class="card-body p-2 text-start">
                                                            <div class="fw-bold text-primary small text-truncate">{{ $item->jenis_kebaktian }}</div>
                                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                                <i class="bi bi-clock"></i> {{ $item->waktu_mulai }}
                                                            </div>
                                                            <div class="mt-1">
                                                                <span class="badge bg-info p-1" style="font-size: 0.6rem;">
                                                                    {{ $item->tugas->count() }} Personil
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade text-start" id="modalDetail{{ $item->id_jadwal }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title fw-bold">Atur Tim: {{ $item->jenis_kebaktian }}</h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                
                                                                <div class="modal-body text-start">
                                                                    <div class="row mb-3">
                                                                        <div class="col-6">
                                                                            <label
                                                                                class="text-muted small d-block">Tanggal</label>
                                                                            <span class="fw-bold">{{ $item->tanggal_pelayanan->format('d F Y') }}</span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <label
                                                                                class="text-muted small d-block">Waktu</label>
                                                                            <span
                                                                                class="fw-bold">{{ $item->waktu_mulai }}
                                                                                -
                                                                                {{ $item->waktu_selesai }}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <hr style="margin-top: -20px; margin-bottom:-10px">

                                                                <div class="modal-body p-4">
                                                                    <div class="row g-4">
                                                                        <div class="col-md-6 border-end">
                                                                            <h6 class="fw-bold text-muted small text-uppercase mb-3">Tim Terdaftar</h6>
                                                                            <ul class="list-group list-group-flush">
                                                                                @forelse($item->tugas as $t)
                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 border-0 border-bottom">
                                                                                        <div class="d-flex align-items-center">
                                                                                            @if($t->status_tugas == 'pending')
                                                                                                <form action="{{ route('timPelayanan.batal', $t->id_tugas) }}" method="POST" class="me-2" onsubmit="return confirm('Batalkan pengajuan untuk pekerja ini?')">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                    <button type="submit" class="btn btn-link text-danger p-0" title="Batalkan Pengajuan">
                                                                                                        <i class="bi bi-trash"></i>
                                                                                                    </button>
                                                                                                </form>
                                                                                            @endif
                                                                                            
                                                                                            <div>
                                                                                                <div class="fw-bold small">{{ $t->user->nama ?? 'User Terhapus' }}</div>
                                                                                                <small class="text-muted">{{ $t->peran_tugas }}</small>
                                                                                            </div>
                                                                                        </div>
                                            
                                                                                        @if($t->status_tugas == 'approved')
                                                                                            <span class="badge rounded-pill bg-success" style="font-size: 0.6rem;">{{ $t->status_tugas }}</span>
                                                                                        @elseif($t->status_tugas == 'pending')
                                                                                            <span class="badge rounded-pill bg-warning text-dark" style="font-size: 0.6rem;">{{ $t->status_tugas }}</span>
                                                                                        @else
                                                                                            <span class="badge rounded-pill bg-danger" style="font-size: 0.6rem;">{{ $t->status_tugas }}</span>
                                                                                        @endif
                                                                                    </li>
                                                                                @empty
                                                                                    <p class="text-muted small italic">Belum ada personil.</p>
                                                                                @endforelse
                                                                            </ul>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <h6 class="fw-bold text-muted small text-uppercase mb-3">Tambah Personil</h6>
                                                                            <form action="{{ route('timPelayanan.assign') }}" method="POST">
                                                                                @csrf
                                                                                <input type="hidden" name="id_jadwal" value="{{ $item->id_jadwal }}">                             
                                                                                <div class="mb-3">
                                                                                    <select name="id_user" class="form-select form-select-sm" required>
                                                                                        <option value="">-- Pilih Anggota --</option>
                                                                                        @foreach ($daftarPekerja as $p)
                                                                                            @if (! $item->tugas->contains('id_user', $p->id_user))
                                                                                                <option value="{{ $p->id_user }}">
                                                                                                    {{ $p->nama }}
                                                                                                </option>
                                                                                            @endif
                                                                                        @endforeach
                                                                                    </select>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <input type="text" name="peran_tugas" class="form-control form-control-sm" placeholder="Peran (Contoh: Singer)" required>
                                                                                </div>
                                                                                <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold">Kirim Permintaan</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
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

    <style>
        .calendar-cell { height: 160px; vertical-align: top; padding: 8px; }
        .item-jadwal { cursor: pointer; transition: transform 0.2s; font-size: 0.75rem; }
        .item-jadwal:hover { transform: translateY(-2px); background-color: #f8f9fa; }
    </style>
    @endsection