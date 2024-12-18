<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy(Scopes\WilayahScope::class)]
class Cabang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_cabang',
        'wilayah_id',
    ];

    public function areas()
    {
        return $this->hasMany(Area::class);
    }

    public function wilayah()
    {
        return $this->belongsTo(Wilayah::class);
    }
}
