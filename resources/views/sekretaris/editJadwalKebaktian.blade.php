@extends('layouts.index')

@section('content')
<div class="container">

    <h3 class="mb-4">Edit Jadwal Kebaktian</h3>

    {{-- Form Update --}}
    <form action="{{ route('sekretaris.jadwal.update', $jadwal->id_jadwal) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label">Tanggal Pelayanan</label>
            <input 
                type="date" 
                name="tanggal_pelayanan" 
                class="form-control" 
                value="{{ old('tanggal_pelayanan', $jadwal->tanggal_pelayanan) }}"
                required>
        </div>

        {{-- Dropdown Jenis Kebaktian --}}
        <div class="mb-3">
            <label class="form-label">Jenis Kebaktian</label>
            <select name="jenis_kebaktian" class="form-control" required>
                <option value="">-- Pilih Jenis Kebaktian --</option>

                @php
                    $jenisIbadah = [
                        'Kebaktian Umum 1',
                        'Kebaktian Umum 2',
                        'Kebaktian Umum 3',
                        'Ibadah Natal',
                        'Ibadah Tahun Baru',
                        'Ibadah Paskah',
                        'Ibadah Remaja',
                        'Ibadah Youth',
                        'Ibadah Kids',
                        'Ibadah Umum Khusus',
                        'Retreat',
                        'Kebaktian Lainnya'
                    ];
                @endphp

                @foreach($jenisIbadah as $jenis)
                    <option value="{{ $jenis }}"
                        {{ $jenis == $jadwal->jenis_kebaktian ? 'selected' : '' }}>
                        {{ $jenis }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Waktu Mulai --}}
        <div class="mb-3">
            <label class="form-label">Waktu Mulai</label>
            <input 
                type="time" 
                name="waktu_mulai" 
                class="form-control" 
                value="{{ old('waktu_mulai', $jadwal->waktu_mulai) }}"
                required>
        </div>

        {{-- Waktu Selesai --}}
        <div class="mb-3">
            <label class="form-label">Waktu Selesai</label>
            <input 
                type="time" 
                name="waktu_selesai" 
                class="form-control" 
                value="{{ old('waktu_selesai', $jadwal->waktu_selesai) }}"
                required>
        </div>

        {{-- Lokasi --}}
        <div class="mb-3">
            <label class="form-label">Lokasi</label>
            <input 
                type="text" 
                name="lokasi" 
                class="form-control" 
                value="{{ old('lokasi', $jadwal->lokasi) }}"
                placeholder="Aula Utama, Youth Room...">
        </div>

        {{-- Tema --}}
        <div class="mb-3">
            <label class="form-label">Tema</label>
            <input 
                type="text" 
                name="tema" 
                class="form-control" 
                value="{{ old('tema', $jadwal->tema) }}"
                placeholder="Opsional">
        </div>

        {{-- Tombol --}}
        <button class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('sekretaris.jadwal.index') }}" class="btn btn-secondary ms-2">Batal</a>

    </form>

</div>
@endsection
