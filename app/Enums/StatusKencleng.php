<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusKencleng: int implements HasLabel, HasColor
{
    case TERSEDIA           = 0;
    case SEDANGDISTRIBUSI   = 1;

    public function getLabel(): string
    {
        return match ($this) {
            self::TERSEDIA          => 'Tersedia',
            self::SEDANGDISTRIBUSI  => 'Sedang Distribusi',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::TERSEDIA          => 'primary',
            self::SEDANGDISTRIBUSI  => 'warning',
        };
    }
}