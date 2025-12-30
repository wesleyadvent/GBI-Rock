@extends('layouts.index')

@section('content')
    <div class="container">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <h3 class="mb-4">Jadwal Kebaktian</h3>

        <a href="{{ route('sekretaris.jadwal.create') }}" class="btn btn-primary mb-3">
            + Tambah Jadwal
        </a>

        <div id="calendar"></div>

    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="jadwalModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jadwal Kebaktian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Tanggal:</strong> <span id="modalTanggal"></span></p>
                    <p><strong>Jenis Kebaktian:</strong> <span id="modalJenis"></span></p>
                    <p><strong>Waktu:</strong> <span id="modalWaktu"></span></p>
                    <p><strong>Lokasi:</strong> <span id="modalLokasi"></span></p>
                    <p><strong>Tema:</strong> <span id="modalTema"></span></p>
                    <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                </div>
                <div class="modal-footer">
                    <a id="btnEdit" class="btn btn-warning">Edit</a>

                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Delete</button>
                    </form>

                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('ExtraJS')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            var calendarEl = document.getElementById('calendar');

            var events = @json($events);

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: events,
                eventClick: function(info) {

                    let id = info.event.id;

                    fetch('/sekretaris/jadwal/detail/' + id)
                        .then(response => response.json())
                        .then(data => {

                            document.getElementById('modalTanggal').innerText = data
                                .tanggal_pelayanan;
                            document.getElementById('modalJenis').innerText = data.jenis_kebaktian;
                            document.getElementById('modalWaktu').innerText = data.waktu_mulai +
                                ' - ' + data.waktu_selesai;
                            document.getElementById('modalLokasi').innerText = data.lokasi ?? '-';
                            document.getElementById('modalTema').innerText = data.tema ?? '-';
                            document.getElementById('modalStatus').innerText = data.status;

                            document.getElementById('btnEdit').href = '/sekretaris/jadwal/' + id +
                                '/edit';

                            document.getElementById('deleteForm').action = '/sekretaris/jadwal/' +
                                id;

                            var modal = new bootstrap.Modal(document.getElementById('jadwalModal'));
                            modal.show();

                        });
                }
            });

            calendar.render();
        });
    </script>
@endsection
