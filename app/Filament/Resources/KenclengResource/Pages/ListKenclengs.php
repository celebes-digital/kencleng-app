<?php

namespace App\Filament\Resources\KenclengResource\Pages;

use App\Filament\Resources\KenclengResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKenclengs extends ListRecords
{
    protected static string $resource = KenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
