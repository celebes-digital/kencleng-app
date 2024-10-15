<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatchKencleng extends Model
{
    use HasFactory; 

    protected $fillable = ['nama_batch', 'jumlah'];

    public function kenclengs(): HasMany
    { 
        return $this->hasMany(Kencleng::class);
    }
}
