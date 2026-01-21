<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanJadwal extends Model
{
    protected $table = 'pengajuan_jadwal';
    protected $primaryKey = 'id_pengajuan';

    public $timestamps = false;

    protected $fillable = [
        'id_koordinator',
        'id_jadwal',
        'id_bidang',
        'status_pengajuan',
        'alasan_penolakan',
        'tanggal_pengajuan',
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKebaktian::class, 'id_jadwal');
    }

    public function koordinator()
    {
        return $this->belongsTo(User::class, 'id_koordinator', 'id_user');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'id_bidang');
    }
}
