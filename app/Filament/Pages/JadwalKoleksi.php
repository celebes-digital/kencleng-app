<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class JadwalKoleksi extends Page
{
    protected static string $view               = 'filament.pages.jadwal-koleksi';
    protected static ?string $modelLabel        = 'Jadwal Koleksi';
    protected static ?string $label             = 'Jadwal Koleksi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'jadwal-koleksi';
    protected static ?string $navigationGroup   = 'Jadwal';
    protected static ?int    $navigationSort    = 1;

}
