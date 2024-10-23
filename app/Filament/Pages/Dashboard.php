<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\StatsDashbordOverview;
use App\Filament\Widgets\StatsTopDonaturOverview;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $title = 'Dashboard';
    protected static ?string $modelLabel        = 'Dashboard';
    protected static ?string $label             = 'Dashboard';
    protected static ?string $navigationIcon    = 'heroicon-o-home';



    public function getWidgets(): array
    {
        return [
            StatsDashbordOverview::class,
            StatsTopDonaturOverview::class,
        ];
    }
}
