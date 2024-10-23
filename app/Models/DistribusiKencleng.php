<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'tgl_pengambilan',
    ];

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
}
