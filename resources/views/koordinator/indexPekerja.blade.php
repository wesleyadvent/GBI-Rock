@extends('layouts.index')

@section('content')
<div class="container">
    <h3>Daftar Pekerja Bidang Saya</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('koordinator.pekerja.create') }}" class="btn btn-primary mb-3">+ Buat Akun Pekerja</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Bidang</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pekerja as $p)
                <tr>
                    <td>{{ $p->nama }}</td>
                    <td>{{ $p->email }}</td>
                    <td>{{ $p->bidang ? $p->bidang->nama_bidang : '-' }}</td>
                    <td>{{ $p->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
                    <td>
                        <a href="{{ route('koordinator.pekerja.edit', $p->id_user) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('koordinator.pekerja.destroy', $p->id_user) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus akun ini?')" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
