<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKebaktian extends Model
{
    protected $table = 'jadwal_kebaktian';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'tanggal_pelayanan',
        'jenis_kebaktian',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'tema',
        'status',
        'dibuat_oleh',
        'disetujui_oleh',
    ];

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function penatua()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}