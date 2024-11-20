<?php

namespace App\Models;

use App\Enums\StatusDistribusi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'cabang_id',
        'area_id',
    ];

    protected $casts = [
        'status' => StatusDistribusi::class
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            $user = Auth::user();
            $model->cabang_id = $user->is_admin ? $user->admin->cabang_id : null;
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

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
