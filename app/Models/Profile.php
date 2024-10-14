<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'poto',
        'poto_ktp',
        'group',
    ];
}
