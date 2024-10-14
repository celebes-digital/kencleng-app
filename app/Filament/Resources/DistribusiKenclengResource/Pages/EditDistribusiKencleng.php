<?php

namespace App\Filament\Resources\DistribusiKenclengResource\Pages;

use App\Filament\Resources\DistribusiKenclengResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistribusiKencleng extends EditRecord
{
    protected static string $resource = DistribusiKenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
