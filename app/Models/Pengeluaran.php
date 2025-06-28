<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';

    protected $fillable = [
        'nama_pengeluaran',
        'jumlah',
        'bulan',
        'tahun',
        'tanggal_pengeluaran',
        'keterangan',
    ];
}
