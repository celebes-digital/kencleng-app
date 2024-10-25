<?php

namespace App\Models;

use App\Enums\StatusKencleng;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kencleng extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kencleng',
        'qr_image'
    ];

    protected $casts = [
        'status' => StatusKencleng::class
    ];

    public function batchKenclengs()
    {
        return $this->belongsTo(BatchKencleng::class);
    }

    public function distribusiKenclengs()
    {
        return $this->hasMany(DistribusiKencleng::class);
    }
}
