<?php

namespace App\Filament\Pages\Distribusi;

use Filament\Pages\Page;
use Filament\Actions\Action;

use Illuminate\Support\Facades\Auth;

class DistribusiToDonatur extends Page
{
    protected static string $view   = 'filament.pages.distribusi.to-donatur';

    public static function canAccess(): bool
    {
        return Auth::user()->admin?->level === 'admin';
    }

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?string $navigationLabel   = 'Ke Donatur';
    protected static ?string $modelLabel        = 'Ke Donatur';
    protected static ?string $title             = 'Ke Donatur';
    protected static ?string $slug              = 'distribusi/donatur';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_qr')
            ->label('QR Pendaftaran')
            ->icon('heroicon-o-viewfinder-circle')
            ->modal()
        ];
    }
}
