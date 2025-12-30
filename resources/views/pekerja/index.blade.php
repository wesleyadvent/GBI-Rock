@extends('layouts.index')

@section('content')
    <main>
        <div class="py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold">Jadwal Pelayanan Saya</h2>
                    <p class="text-muted">Halo {{ Auth::user()->nama }}, klik pada kartu jadwal untuk melihat detail.</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <a href="{{ route('pekerja.index', ['month' => $currentDate->copy()->subMonth()->month, 'year' => $currentDate->copy()->subMonth()->year]) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-chevron-left"></i>
                    </a>

                    <h5 class="mb-0 fw-bold">{{ $currentDate->translatedFormat('F Y') }}</h5>

                    <a href="{{ route('pekerja.index', ['month' => $currentDate->copy()->addMonth()->month, 'year' => $currentDate->copy()->addMonth()->year]) }}"
                        class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0 text-center"
                            style="table-layout: fixed; min-width: 1100px;">
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
                                @foreach ($calendarGrid as $week)
                                    <tr>
                                        @foreach ($week as $day)
                                            <td class="p-2 {{ !$day['isCurrentMonth'] ? 'bg-light opacity-50' : '' }}"
                                                style="height: 150px; vertical-align: top;">

                                                <div class="text-end fw-bold mb-1">{{ $day['date']->day }}</div>

                                                @foreach ($day['tugas'] as $t)
                                                    <div class="card border-0 shadow-sm mb-1 border-start border-3 {{ $t->status_tugas == 'pending' ? 'border-warning' : ($t->status_tugas == 'approved' ? 'border-success' : 'border-danger') }}"
                                                        style="font-size: 0.75rem; cursor: pointer;" data-bs-toggle="modal"
                                                        data-bs-target="#modalDetail{{ $t->id_tugas }}">
                                                        <div class="card-body p-2 text-start">
                                                            <div class="fw-bold text-primary">{{ $t->peran_tugas }}</div>
                                                            <div class="text-truncate">
                                                                {{ $t->jadwalKebaktian->jenis_kebaktian }}</div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="modalDetail{{ $t->id_tugas }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title fw-bold">Detail Pelayanan</h5>
                                                                    <button type="button" class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body text-start">
                                                                    <div class="mb-3">
                                                                        <label class="text-muted small d-block">Jenis
                                                                            Kebaktian</label>
                                                                        <span
                                                                            class="fw-bold fs-5">{{ $t->jadwalKebaktian->jenis_kebaktian }}</span>
                                                                    </div>
                                                                    <div class="row mb-3">
                                                                        <div class="col-6">
                                                                            <label
                                                                                class="text-muted small d-block">Tanggal</label>
                                                                            <span class="fw-bold">{{ $t->jadwalKebaktian->tanggal_pelayanan->format('d F Y') }}</span>
                                                                        </div>
                                                                        <div class="col-6">
                                                                            <label
                                                                                class="text-muted small d-block">Waktu</label>
                                                                            <span
                                                                                class="fw-bold">{{ $t->jadwalKebaktian->waktu_mulai }}
                                                                                -
                                                                                {{ $t->jadwalKebaktian->waktu_selesai }}</span>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="text-muted small d-block">Peran
                                                                            Anda</label>
                                                                        <span class="">{{ $t->peran_tugas }}</span>
                                                                    </div>
                                                                    @if ($t->status_tugas == 'declined')
                                                                        <div class="mb-3">
                                                                            <label class="text-muted small d-block">Alasan Penolakan</label>
                                                                            <span class="">{{ $t->alasan_penolakan }}</span>
                                                                        </div>
                                                                    @endif
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="text-muted small d-block">Status</label>
                                                                        <span
                                                                            class="badge {{ $t->status_tugas == 'approved' ? 'bg-success' : ($t->status_tugas == 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                                                            {{ ucfirst($t->status_tugas) }}
                                                                        </span>
                                                                    </div>

                                                                    @if ($t->status_tugas == 'pending')
                                                                        <hr>
                                                                        <div class="d-flex gap-2">
                                                                            <form
                                                                                action="{{ route('pekerja.konfirmasi', $t->id_tugas) }}"
                                                                                method="POST" class="flex-grow-1">
                                                                                @csrf
                                                                                <input type="hidden" name="aksi"
                                                                                    value="terima">
                                                                                <button type="submit"
                                                                                    class="btn btn-success w-100">Terima</button>
                                                                            </form>
                                                                            <button
                                                                                class="btn btn-outline-danger flex-grow-1"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#modalTolak{{ $t->id_tugas }}">Tolak</button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal fade" id="modalTolak{{ $t->id_tugas }}"
                                                        tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title">Alasan Penolakan</h5>
                                                                    <button type="button" class="btn-close btn-close-white"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form
                                                                    action="{{ route('pekerja.konfirmasi', $t->id_tugas) }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="aksi" value="tolak">
                                                                    <div class="modal-body">
                                                                        <textarea name="alasan" class="form-control" rows="3" placeholder="Alasan menolak..." required></textarea>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="submit"
                                                                            class="btn btn-danger">Kirim</button>
                                                                    </div>
                                                                </form>
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
        .card[data-bs-toggle="modal"]:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            transition: all 0.2s ease;
        }
    </style>
@endsection
