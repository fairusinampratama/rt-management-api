<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghuni extends Model
{
    protected $table = 'penghuni';

    protected $fillable = [
        'nama_lengkap',
        'foto_ktp',
        'status_penghuni',
        'nomor_telepon',
        'status_menikah',
    ];

    public function rumahPenghuni()
    {
        return $this->hasMany(RumahPenghuni::class);
    }

    public function activeRumahPenghuni()
    {
        return $this->hasMany(RumahPenghuni::class)->whereNull('tanggal_keluar');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
