@extends('layouts.index')

@section('content')
<div class="container">
    <h3>Edit User</h3>

    <form action="{{ route('admin.user.update', $user->id_user) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ $user->nama }}" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" id="roleSelect" class="form-control" required>
                @foreach ($roles as $key => $label)
                    <option value="{{ $key }}" {{ $user->role == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3" id="bidangWrapper" style="{{ in_array($user->role, ['pekerja','koordinator_bidang']) ? '' : 'display:none;' }}">
            <label>Bidang</label>
            <select name="id_bidang" class="form-control">
                <option value="">-- Pilih Bidang --</option>

                @foreach ($bidang as $id => $namaB)
                    <option value="{{ $id }}" {{ $user->id_bidang == $id ? 'selected' : '' }}>
                        {{ $namaB }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Simpan Perubahan</button>
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
