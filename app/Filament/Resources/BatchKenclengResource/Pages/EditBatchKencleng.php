<?php

namespace App\Filament\Resources\BatchKenclengResource\Pages;

use App\Filament\Resources\BatchKenclengResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Livewire\Attributes\On;

class EditBatchKencleng extends EditRecord
{
    protected static string $resource   = BatchKenclengResource::class;
    protected static ?string $title         = 'Detail Batch Kencleng';
    protected static ?string $breadcrumb    = 'Detail';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    #[On('refreshForm')]
    public function refreshForm(): void 
    {
        $this->fillForm();
    }
}
