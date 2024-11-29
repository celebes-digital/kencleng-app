<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[
    ScopedBy(Scopes\CabangScope::class), 
    ScopedBy(Scopes\WilayahScope::class), 
    ScopedBy(Scopes\AreaScope::class)
]
class Infaq extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribusi_id',
        'tgl_transaksi',
        'jumlah_donasi',
        'uraian',
        'sumber_dana',
        'area_id',
        'cabang_id',
        'wilayah_id',
        'nama_donatur',
        'metode_donasi',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $distribusi         = DistribusiKencleng::find($model->distribusi_id);

            if($distribusi) {
                $model->nama_donatur    = $distribusi->donatur->nama ?? 'Hamba Allah';
                $model->area_id         = $distribusi->area_id ?? null;
                $model->cabang_id       = $distribusi->cabang_id ?? null;
                $model->wilayah_id      = $distribusi->wilayah_id ?? null;
            }
        });
    }

    public function distribusi()
    {
        return $this->belongsTo(DistribusiKencleng::class, 'distribusi_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
