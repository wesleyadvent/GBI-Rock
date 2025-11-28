@extends('layouts.index')

@section('content')
<div class="container">
    <h3>Buat Akun Baru</h3>

    @if(session('success'))
        <div style="color: green; margin-bottom: 10px;">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf

        {{-- Nama --}}
        <div class="mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        {{-- Email --}}
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        {{-- Role --}}
        <div class="mb-3">
            <label>Role</label>
            <select name="role" id="roleSelect" class="form-control" required>
                <option value="">-- Pilih Role --</option>

                @foreach ($roles as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
        </div>

        {{-- Bidang (muncul jika role koordinator_bidang / pekerja) --}}
        <div class="mb-3" id="bidangWrapper" style="display: none;">
            <label>Pilih Bidang</label>
            <select name="id_bidang" class="form-control">
                <option value="">-- Pilih Bidang --</option>

                @foreach ($bidang as $id => $namaBidang)
                    <option value="{{ $id }}">{{ $namaBidang }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            Simpan
        </button>
         <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
    const roleSelect = document.getElementById('roleSelect');
    const bidangWrapper = document.getElementById('bidangWrapper');

    roleSelect.addEventListener('change', function() {
        if (this.value === 'pekerja' || this.value === 'koordinator_bidang') {
            bidangWrapper.style.display = 'block';
        } else {
            bidangWrapper.style.display = 'none';
        }
    });
</script>
@endsection

@section('ExtraCSS')

@endsection

@section('ExtraJS')

@endsection