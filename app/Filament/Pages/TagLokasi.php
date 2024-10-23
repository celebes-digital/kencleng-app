<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class TagLokasi extends Page
{
    protected static string $view = 'filament.pages.tag-lokasi';
    protected static ?string $modelLabel        = 'Tag Lokasi';
    protected static ?string $label             = 'Tag Lokasi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'tag-lokasi';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 3;
}
