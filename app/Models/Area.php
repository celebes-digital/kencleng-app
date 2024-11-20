<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'cabang_id',
        'nama_area',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }
}
