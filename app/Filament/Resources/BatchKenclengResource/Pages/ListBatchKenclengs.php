<?php

namespace App\Filament\Resources\BatchKenclengResource\Pages;

use App\Filament\Resources\BatchKenclengResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBatchKenclengs extends ListRecords
{
    protected static string $resource = BatchKenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->createAnother(false),
        ];
    }
}
