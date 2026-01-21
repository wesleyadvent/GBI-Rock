<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPenolakan extends Model
{
    protected $table = 'kategori_penolakan';

    protected $casts = [
        'dampak' => 'array'
    ];

    public function jadwal()
    {
        return $this->hasMany(JadwalKebaktian::class, 'kategori_penolakan_id');
    }
}
