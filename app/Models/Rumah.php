<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rumah extends Model
{
    use HasFactory;

    protected $table = 'rumah';

    protected $fillable = [
        'nomor_rumah',
        'status',
    ];

    public function currentResidents()
    {
        return $this->hasMany(RumahPenghuni::class)
            ->whereNull('tanggal_keluar')
            ->with('penghuni');
    }

    public function rumahPenghunis()
    {
        return $this->hasMany(RumahPenghuni::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
