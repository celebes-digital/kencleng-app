<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Infaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribusi_id',
        'tgl_transaksi',
        'jumlah_donasi',
        'uraian',
        'sumber_dana'
    ];

    public function distribusi()
    {
        return $this->belongsTo(DistribusiKencleng::class, 'distribusi_id');
    }
}
