<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey = 'id_notifikasi';
    public $timestamps = false;

    protected $fillable = [
        'id_user',
        'pesan',
        'tipe',
        'status_baca',
        'created_at'
    ];

    protected $casts = [
        'status_baca' => 'boolean',
        'created_at' => 'datetime'
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // Scope untuk notifikasi belum dibaca
    public function scopeUnread($query)
    {
        return $query->where('status_baca', 0);
    }

    // Scope untuk user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->where('id_user', $userId);
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'id_user', 'id_user');
    }
}