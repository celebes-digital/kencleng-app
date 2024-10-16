<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kencleng extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_kencleng',
        'qr_image'
    ];

    public function batchKenclengs()
    {
        return $this->belongsTo(BatchKencleng::class);
    }
}
