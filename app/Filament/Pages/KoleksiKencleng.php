<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class KoleksiKencleng extends Page
{
    protected static string $view = 'filament.pages.koleksi-kencleng';
    protected static ?string $modelLabel        = 'Koleksi Kencleng';
    protected static ?string $label             = 'Koleksi Kencleng';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'koleksi-kencleng';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 2;
}
