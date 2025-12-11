@extends('layouts.index')

@section('content')
<div class="container">
    <h3>Buat Akun Pekerja</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('koordinator.pekerja.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama Pekerja</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email Pekerja</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Bidang</label>
            <input type="text" class="form-control" value="{{ $bidang }}" readonly>
            <small class="text-muted">Bidang otomatis mengikuti bidang koordinator.</small>
        </div>

        <button class="btn btn-primary">Buat Akun</button>
        <a href="{{ route('koordinator.pekerja.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
