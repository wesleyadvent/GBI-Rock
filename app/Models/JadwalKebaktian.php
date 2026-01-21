<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class JadwalKebaktian extends Model
{
    protected $table = 'jadwal_kebaktian';
    protected $primaryKey = 'id_jadwal';
    protected $casts = [
        'tanggal_pelayanan' => 'date',
    ];

    protected $fillable = [
        'tanggal_pelayanan',
        'jenis_kebaktian',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'tema',
        'status',
        'pending_action',
        'dibuat_oleh',
        'disetujui_oleh',
        'asal_jadwal',
        'alasan_penolakan',
        'kategori_penolakan_id',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function penatua()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_jadwal', 'id_jadwal');
    }

    public function pengajuan()
    {
        return $this->hasMany(PengajuanJadwal::class, 'id_jadwal', 'id_jadwal');
    }

    public function histories()
    {
        return $this->hasMany(
            JadwalKebaktianHistory::class,
            'id_jadwal',
            'id_jadwal'
        )->orderBy('created_at', 'desc');
    }

    public function kategoriPenolakan()
    {
        return $this->belongsTo(KategoriPenolakan::class, 'kategori_penolakan_id');
    }

    public function pembicaraEksternal()
    {
        return $this->hasOne(PembicaraEksternal::class, 'id_jadwal');
    }
}