<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'rumah_id',
        'penghuni_id',
        'bulan',
        'tahun',
        'jenis_iuran',
        'jumlah',
        'status',
        'tanggal_bayar',
    ];

    public function rumah()
    {
        return $this->belongsTo(Rumah::class);
    }

    public function penghuni()
    {
        return $this->belongsTo(Penghuni::class);
    }
}
