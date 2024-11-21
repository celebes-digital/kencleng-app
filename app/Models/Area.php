<?php

namespace App\Models;

use App\Models\Scopes\CabangScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy(CabangScope::class)]
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
