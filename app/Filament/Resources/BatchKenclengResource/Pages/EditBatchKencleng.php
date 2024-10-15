<?php

namespace App\Filament\Resources\BatchKenclengResource\Pages;

use App\Filament\Resources\BatchKenclengResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBatchKencleng extends EditRecord
{
    protected static string $resource = BatchKenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
