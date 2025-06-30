<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RumahPenghuni extends Model
{
    use HasFactory;

    protected $table = 'rumah_penghuni';

    protected $fillable = [
        'rumah_id',
        'penghuni_id',
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }

    public function rumah()
    {
        return $this->belongsTo(Rumah::class);
    }
}
