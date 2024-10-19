<?php

namespace App\Filament\Components;

use Filament\Forms\Components\Field;
use Filament\Forms\Set;

class ScannerQrCode extends Field
{
    protected string $view = 'filament.forms.components.scanner-qr-code';

    public function mount(): void
    {
        $this->dispatchBrowserEvent('component-mounted');
    }

    public function dehydrate(): void
    {
        $this->dispatchBrowserEvent('component-unmounted');
    }
}
