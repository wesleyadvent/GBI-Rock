<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\JadwalKebaktian;

class Tugas extends Model
{
    protected $table = 'tugas';
    public $timestamps = false;
    protected $primaryKey = 'id_tugas';

    protected $fillable = [
        'id_jadwal',
        'id_user',
        'peran_tugas',
        'status_tugas',
        'alasan_penolakan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function jadwalKebaktian()
    {
        return $this->belongsTo(JadwalKebaktian::class, 'id_jadwal', 'id_jadwal');
    }
}
