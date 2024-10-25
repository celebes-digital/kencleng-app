<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusDistribusi: string implements HasLabel, HasIcon, HasColor
{
    case DISTRIBUSI = 'distribusi';
    case DIISI      = 'diisi';
    case KEMBALI    = 'kembali';
    case DITERIMA   = 'diterima';

    public function getLabel(): string
    {
        return match ($this) {
            self::DISTRIBUSI => 'Distribusi',
            self::DIISI      => 'Sedang Diisi',
            self::KEMBALI    => 'Kembali',
            self::DITERIMA   => 'Diterima',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DISTRIBUSI => 'info',
            self::DIISI      => 'amber',
            self::KEMBALI    => 'warning',
            self::DITERIMA   => 'primary',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::DISTRIBUSI => 'heroicon-o-cube',
            self::DIISI      => 'heroicon-o-clipboard-check',
            self::KEMBALI    => 'heroicon-o-arrow-left-circle',
            self::DITERIMA   => 'heroicon-o-check-badge',
        };
    }
}
