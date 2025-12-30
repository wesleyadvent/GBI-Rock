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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_jadwal', 'id_jadwal');
    }
}