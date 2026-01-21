<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PembicaraEksternal extends Model
{
    protected $table = 'pembicara_eksternal';
    protected $primaryKey = 'id_pembicara'; 

    protected $fillable = [
        'nama_pembicara',
        'asal_gereja',
        'kontak',
        'keterangan',
        'id_jadwal'
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKebaktian::class, 'id_jadwal');
    }
}
