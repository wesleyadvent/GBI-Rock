@extends('layouts.index')

@section('content')
<div class="container">

    <h3>Daftar User</h3>

    @if(session('success'))
        <div style="color: green;">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.user.create') }}" class="btn btn-primary mb-3">+ Tambah User</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Bidang</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($users as $u)
                <tr>
                    <td>{{ $u->id_user }}</td>
                    <td>{{ $u->nama }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ ucfirst($u->role) }}</td>
                    <td>
                        @if($u->id_bidang)
                            {{ $bidang[$u->id_bidang] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($u->status_aktif)
                            <span style="color: green; font-weight: bold;">Aktif</span>
                        @else
                            <span style="color: red; font-weight: bold;">Nonaktif</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('admin.user.edit', $u->id_user) }}" class="btn btn-warning btn-sm">Edit</a>

                        <a href="{{ route('admin.user.delete', $u->id_user) }}"
                           onclick="return confirm('Hapus user ini?')"
                           class="btn btn-danger btn-sm">
                           Hapus
                        </a>

                        <a href="{{ route('admin.user.toggle', $u->id_user) }}"
                           class="btn btn-secondary btn-sm">
                           {{ $u->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>

    </table>
</div>
@endsection
@section('ExtraCSS')

@endsection

@section('ExtraJS')

@endsection
