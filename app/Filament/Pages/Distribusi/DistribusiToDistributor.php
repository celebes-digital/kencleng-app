<?php

namespace App\Filament\Pages\Distribusi;

use Filament\Pages\Page;

class DistribusiToDistributor extends Page
{
    protected static string $view = 'filament.pages.distribusi.to-distributor';

    protected static ?string $modelLabel        = 'Ke Distributor';
    protected static ?string $title             = 'Ke Distributor';
    protected static ?string $slug              = 'distribusi/distributor';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 3;

    protected static ?string $navigationLabel   = 'Ke Distributor';
}
