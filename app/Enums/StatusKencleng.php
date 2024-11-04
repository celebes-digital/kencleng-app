<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum StatusKencleng: string implements HasLabel, HasColor
{
    case AQTIF          = '0';
    case DISTRIBUTOR    = '1';

    public function getLabel(): string
    {
        return match ($this) {
            self::AQTIF       => 'Aqtif',
            self::DISTRIBUTOR => 'Distributor',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AQTIF       => 'primary',
            self::DISTRIBUTOR => 'warning',
        };
    }
}