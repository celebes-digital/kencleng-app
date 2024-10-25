<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusKencleng: string implements HasLabel, HasColor
{
    case BelumDistribusi    = '0';
    case DalamDistribusi    = '1';
    case SelesaiDistribusi  = '2';

    public function getLabel(): string
    {
        return match ($this) {
            self::BelumDistribusi   => 'Belum Distribusi',
            self::DalamDistribusi   => 'Sedang Diisi',
            self::SelesaiDistribusi => 'Tersedia',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::BelumDistribusi   => 'danger',
            self::DalamDistribusi   => 'warning',
            self::SelesaiDistribusi => 'success',
        };
    }
}