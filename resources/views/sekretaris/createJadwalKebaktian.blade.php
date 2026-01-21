@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-plus-circle text-primary me-2"></i>Buat Jadwal Kebaktian Baru
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('sekretaris.jadwal.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('sekretaris.jadwal.index') }}">Jadwal</a></li>
                    <li class="breadcrumb-item active">Buat Baru</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('sekretaris.jadwal.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-plus me-2"></i>Form Jadwal Kebaktian
                    </h5>
                </div>
                <div class="card-body p-4">
                    <!-- Alert Info -->
                    <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                        <i class="bi bi-info-circle-fill me-3 fs-5"></i>
                        <div>
                            <strong>Informasi:</strong> Jadwal yang dibuat akan berstatus <strong>Draft</strong>. Anda perlu menambahkan pekerja sebelum mengajukan ke Penatua.
                        </div>
                    </div>

                    <form action="{{ route('sekretaris.jadwal.store') }}" method="POST" id="formJadwal">
                        @csrf

                        <div class="row g-4">
                            <!-- Tanggal Pelayanan -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-calendar-event text-primary me-2"></i>Tanggal Pelayanan
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="date" 
                                    name="tanggal_pelayanan" 
                                    class="form-control form-control-lg @error('tanggal_pelayanan') is-invalid @enderror" 
                                    value="{{ old('tanggal_pelayanan') }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('tanggal_pelayanan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Pilih tanggal pelayanan kebaktian</small>
                            </div>

                            <!-- Jenis Kebaktian -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-bookmark text-primary me-2"></i>Jenis Kebaktian
                                    <span class="text-danger">*</span>
                                </label>
                                <select 
                                    name="jenis_kebaktian" 
                                    class="form-select form-select-lg @error('jenis_kebaktian') is-invalid @enderror" 
                                    required>
                                    <option value="">-- Pilih Jenis Kebaktian --</option>
                                    <optgroup label="Kebaktian Umum">
                                        <option value="Kebaktian Umum 1" {{ old('jenis_kebaktian') == 'Kebaktian Umum 1' ? 'selected' : '' }}>Kebaktian Umum 1</option>
                                        <option value="Kebaktian Umum 2" {{ old('jenis_kebaktian') == 'Kebaktian Umum 2' ? 'selected' : '' }}>Kebaktian Umum 2</option>
                                        <option value="Kebaktian Umum 3" {{ old('jenis_kebaktian') == 'Kebaktian Umum 3' ? 'selected' : '' }}>Kebaktian Umum 3</option>
                                    </optgroup>
                                    <optgroup label="Ibadah Khusus">
                                        <option value="Ibadah Natal" {{ old('jenis_kebaktian') == 'Ibadah Natal' ? 'selected' : '' }}>Ibadah Natal</option>
                                        <option value="Ibadah Tahun Baru" {{ old('jenis_kebaktian') == 'Ibadah Tahun Baru' ? 'selected' : '' }}>Ibadah Tahun Baru</option>
                                        <option value="Ibadah Paskah" {{ old('jenis_kebaktian') == 'Ibadah Paskah' ? 'selected' : '' }}>Ibadah Paskah</option>
                                    </optgroup>
                                    <optgroup label="Ibadah Kategori">
                                        <option value="Ibadah Remaja" {{ old('jenis_kebaktian') == 'Ibadah Remaja' ? 'selected' : '' }}>Ibadah Remaja</option>
                                        <option value="Ibadah Youth" {{ old('jenis_kebaktian') == 'Ibadah Youth' ? 'selected' : '' }}>Ibadah Youth</option>
                                        <option value="Ibadah Kids" {{ old('jenis_kebaktian') == 'Ibadah Kids' ? 'selected' : '' }}>Ibadah Kids</option>
                                        <option value="Ibadah Umum Khusus" {{ old('jenis_kebaktian') == 'Ibadah Umum Khusus' ? 'selected' : '' }}>Ibadah Umum Khusus</option>
                                    </optgroup>
                                    <optgroup label="Lainnya">
                                        <option value="Retreat" {{ old('jenis_kebaktian') == 'Retreat' ? 'selected' : '' }}>Retreat</option>
                                        <option value="Kebaktian Lainnya" {{ old('jenis_kebaktian') == 'Kebaktian Lainnya' ? 'selected' : '' }}>Kebaktian Lainnya</option>
                                    </optgroup>
                                </select>
                                @error('jenis_kebaktian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Waktu Mulai -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-clock text-primary me-2"></i>Waktu Mulai
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="time" 
                                    name="waktu_mulai" 
                                    class="form-control form-control-lg @error('waktu_mulai') is-invalid @enderror" 
                                    value="{{ old('waktu_mulai', '07:00') }}"
                                    required>
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Waktu Selesai -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-clock-history text-primary me-2"></i>Waktu Selesai
                                    <span class="text-danger">*</span>
                                </label>
                                <input 
                                    type="time" 
                                    name="waktu_selesai" 
                                    class="form-control form-control-lg @error('waktu_selesai') is-invalid @enderror" 
                                    value="{{ old('waktu_selesai', '09:00') }}"
                                    required>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Lokasi -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>Lokasi
                                </label>
                                <input 
                                    type="text" 
                                    name="lokasi" 
                                    class="form-control form-control-lg @error('lokasi') is-invalid @enderror" 
                                    placeholder="Contoh: Aula Utama, Youth Room, Gedung Utama..."
                                    value="{{ old('lokasi') }}">
                                @error('lokasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional - Lokasi pelaksanaan kebaktian</small>
                            </div>

                            <!-- Tema -->
                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-lightbulb text-primary me-2"></i>Tema Kebaktian
                                </label>
                                <textarea 
                                    name="tema" 
                                    class="form-control @error('tema') is-invalid @enderror" 
                                    rows="3" 
                                    placeholder="Contoh: Kasih Allah yang Sempurna, Hidup dalam Terang..."
                                >{{ old('tema') }}</textarea>
                                @error('tema')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Opsional - Tema atau topik kebaktian</small>
                            </div>
                        </div>

                        <!-- Divider -->
                        <hr class="my-4">

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('sekretaris.jadwal.index') }}" class="btn btn-lg btn-light px-4">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary px-4">
                                <i class="bi bi-save me-2"></i>Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border-0 shadow-sm mt-4 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-question-circle text-primary me-2"></i>Langkah Selanjutnya
                    </h6>
                    <ol class="mb-0 ps-3">
                        <li class="mb-2">Setelah menyimpan jadwal, status akan menjadi <span class="badge bg-warning">Draft</span></li>
                        <li class="mb-2">Tambahkan pekerja dari berbagai bidang (Usher, Pembicara, Pendoa, PW, Multimedia)</li>
                        <li class="mb-2">Pastikan semua bidang memenuhi kuota minimal sebelum mengajukan</li>
                        <li class="mb-0">Ajukan jadwal ke Penatua untuk mendapatkan persetujuan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('ExtraCSS')
<style>
.form-control:focus, .form-select:focus {
    border-color: var(--bs-primary);
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
}

.form-control-lg, .form-select-lg {
    border-radius: 0.5rem;
}

.card {
    border-radius: 1rem;
    overflow: hidden;
}

.breadcrumb-item + .breadcrumb-item::before {
    content: "›";
}

optgroup {
    font-weight: bold;
    color: var(--bs-primary);
}

optgroup option {
    font-weight: normal;
    color: var(--bs-body-color);
}
</style>
@endsection

@section('ExtraJS')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validasi waktu selesai harus lebih dari waktu mulai
    const waktuMulai = document.querySelector('input[name="waktu_mulai"]');
    const waktuSelesai = document.querySelector('input[name="waktu_selesai"]');
    
    function validateWaktu() {
        if (waktuMulai.value && waktuSelesai.value) {
            if (waktuSelesai.value <= waktuMulai.value) {
                waktuSelesai.setCustomValidity('Waktu selesai harus lebih dari waktu mulai');
            } else {
                waktuSelesai.setCustomValidity('');
            }
        }
    }
    
    waktuMulai.addEventListener('change', validateWaktu);
    waktuSelesai.addEventListener('change', validateWaktu);

    // Form submission confirmation
    document.getElementById('formJadwal').addEventListener('submit', function(e) {
        const tanggal = document.querySelector('input[name="tanggal_pelayanan"]').value;
        const jenis = document.querySelector('select[name="jenis_kebaktian"]').value;
        
        if (tanggal && jenis) {
            const confirmMsg = `Apakah Anda yakin ingin membuat jadwal "${jenis}" pada tanggal ${new Date(tanggal).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}?`;
            
            if (!confirm(confirmMsg)) {
                e.preventDefault();
            }
        }
    });
});
</script>
@endsection