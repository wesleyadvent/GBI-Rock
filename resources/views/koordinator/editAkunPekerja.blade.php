@extends('layouts.index')

@section('content')
<div class="container">
    <h3>{{ isset($isEdit) ? 'Edit Akun Pekerja' : 'Buat Akun Pekerja' }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form 
        action="{{ isset($isEdit) ? route('koordinator.pekerja.update', $user->id_user) : route('koordinator.pekerja.store') }}" 
        method="POST"
    >
        @csrf
        @if(isset($isEdit))
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Nama Pekerja</label>
            <input type="text" name="nama" 
                class="form-control" 
                value="{{ $user->nama ?? '' }}" 
                required>
        </div>

        <div class="mb-3">
            <label>Email Pekerja</label>
            <input type="email" name="email" 
                class="form-control" 
                value="{{ $user->email ?? '' }}" 
                required>
        </div>

        @if(!isset($isEdit))
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        @endif

        <div class="mb-3">
            <label>Bidang</label>
            <input 
                type="text" 
                class="form-control" 
                value="{{ $bidang }}" 
                readonly
            >
        </div>

        <button class="btn btn-primary">
            {{ isset($isEdit) ? 'Update' : 'Buat Akun' }}
        </button>

        <a href="{{ route('koordinator.pekerja.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
