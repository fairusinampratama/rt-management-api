<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rumah extends Model
{
    protected $table = 'rumah';

    protected $fillable = [
        'nomor_rumah',
        'status',
    ];

    public function rumahPenghunis()
    {
        return $this->hasMany(RumahPenghuni::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
