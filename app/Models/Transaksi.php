<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'tgl_transaksi',
        'jumlah',
        'uraian',
        'sumber_dana',
        'cabang_id',
    ];

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    protected static function booted()
    {
        static::creating(function ($transaksi) {
            $transaksi->cabang_id = Auth::user()->admin->cabang_id ?? null;
        });
    }
}
