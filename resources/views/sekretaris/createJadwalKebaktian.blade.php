@extends('layouts.index')

@section('content')
<div class="container">

    <h3 class="mb-4">Buat Jadwal Kebaktian</h3>

    <form action="{{ route('sekretaris.jadwal.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tanggal Pelayanan</label>
            <input type="date" name="tanggal_pelayanan" class="form-control" required>
        </div>

        <div class="mb-3">
        <label class="form-label">Jenis Kebaktian</label>
        <select name="jenis_kebaktian" class="form-control" required>
            <option value="">-- Pilih Jenis Kebaktian --</option>

            <option value="Kebaktian Umum 1">Kebaktian Umum 1</option>
            <option value="Kebaktian Umum 2">Kebaktian Umum 2</option>
            <option value="Kebaktian Umum 3">Kebaktian Umum 3</option>

            <option value="Ibadah Natal">Ibadah Natal</option>
            <option value="Ibadah Tahun Baru">Ibadah Tahun Baru</option>
            <option value="Ibadah Paskah">Ibadah Paskah</option>

            <option value="Ibadah Remaja">Ibadah Remaja</option>
            <option value="Ibadah Youth">Ibadah Youth</option>
            <option value="Ibadah Kids">Ibadah Kids</option>
            <option value="Ibadah Umum Khusus">Ibadah Umum Khusus</option>

            <option value="Retreat">Retreat</option>
            <option value="Kebaktian Lainnya">Kebaktian Lainnya</option>
        </select>
    </div>


        <div class="mb-3">
            <label class="form-label">Waktu Mulai</label>
            <input type="time" name="waktu_mulai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Waktu Selesai</label>
            <input type="time" name="waktu_selesai" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input type="text" name="lokasi" class="form-control" placeholder="Aula Utama, Youth Room...">
        </div>

        <div class="mb-3">
            <label class="form-label">Tema</label>
            <input type="text" name="tema" class="form-control" placeholder="Opsional">
        </div>

        <button class="btn btn-primary">Simpan Jadwal</button>
    </form>

</div>
@endsection
