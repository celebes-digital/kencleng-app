<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[
    ScopedBy(Scopes\CabangScope::class),
    ScopedBy(Scopes\WilayahScope::class),
    ScopedBy(Scopes\AreaScope::class)
]
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tgl_lahir',
        'nik',
        'kelamin',
        'pekerjaan',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'no_hp',
        'no_wa',
        'foto',
        'foto_ktp',
        'group',
        'user_id',
        'area_id',
        'cabang_id',
        'wilayah_id',
        'distributor_id',
    ];

    protected static function booted()
    {
        static::creating(function ($profile) {
            $profile->cabang_id = Area::find($profile->area_id)->cabang_id ?? null;
            $profile->wilayah_id = Cabang::find($profile->cabang_id)->wilayah_id ?? null;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function wilayah(): BelongsTo
    {
        return $this->belongsTo(Wilayah::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Profile::class, 'distributor_id', 'id');
    }

    public function donaturs()
    {
        return $this->hasMany(Profile::class, 'id', 'distibutor_id');
    }
}
