<?php

namespace App\Filament\Resources\DistribusiKenclengResource\Pages;

use App\Filament\Resources\DistribusiKenclengResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistribusiKenclengs extends ListRecords
{
    protected static string $resource = DistribusiKenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
