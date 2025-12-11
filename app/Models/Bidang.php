<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $table = 'bidang';

    protected $primaryKey = 'id_bidang';

    public $incrementing = true;

    protected $keyType = 'int';

    protected $fillable = [
        'nama_bidang',
        'deskripsi',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_bidang', 'id_bidang');
    }
}
