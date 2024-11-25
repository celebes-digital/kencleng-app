<?php

namespace App\Models;

use App\Enums\StatusDistribusi;
use App\Models\Scopes\CabangScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

#[ScopedBy(CabangScope::class)]
class DistribusiKencleng extends Model
{
    use HasFactory;

    protected $fillable = [
        'kencleng_id',
        'donatur_id',
        'kolektor_id',
        'distributor_id',
        'geo_lat',
        'geo_long',
        'tgl_distribusi',
        'tgl_aktivasi',
        'tgl_batas_pengambilan',
        'tgl_pengambilan',
        'jumlah',
        'status',
        'area_id',
        'cabang_id',
        'wilayah_id'
    ];

    protected $casts = [
        'status' => StatusDistribusi::class
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if ($model->donatur_id) {
                $donatur            = Profile::find($model->donatur_id);
                $model->area_id     = $donatur->area_id;
                $model->cabang_id   = $donatur->cabang_id;
                $model->wilayah_id  = $donatur->wilayah_id;
            }

            if ($model->distributor_id) {
                $distributor        = Profile::find($model->distributor_id);
                $model->area_id     = $distributor->area_id;
                $model->cabang_id   = $distributor->cabang_id;
                $model->wilayah_id  = $distributor->wilayah_id;
            }
        });
    }

    public function kencleng()
    {
        return $this->belongsTo(Kencleng::class, 'kencleng_id');
    }

    public function donatur()
    {
        return $this->belongsTo(Profile::class, 'donatur_id');
    }

    public function kolektor()
    {
        return $this->belongsTo(Profile::class, 'kolektor_id');
    }

    public function distributor()
    {
        return $this->belongsTo(Profile::class, 'distributor_id');
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
