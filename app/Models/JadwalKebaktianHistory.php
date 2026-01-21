<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalKebaktianHistory extends Model
{
    use HasFactory;

    protected $table = 'jadwal_kebaktian_history';

    protected $primaryKey = 'id';

    public $timestamps = false; 

    protected $fillable = [
        'id_jadwal',
        'status',
        'alasan',
        'oleh_user',
        'created_at'
    ];

    public function jadwal()
    {
        return $this->belongsTo(JadwalKebaktian::class, 'id_jadwal', 'id_jadwal');
    }

    public function oleh()
    {
        return $this->belongsTo(User::class, 'oleh_user');
    }
}
