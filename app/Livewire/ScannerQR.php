<?php

namespace App\Livewire;

use App\Models\DistribusiKencleng;
use App\Models\Profile;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Livewire\Component;

class ScannerQR extends Component implements HasActions, HasForms
{

    use InteractsWithActions;
    use InteractsWithForms;

    public ?array $data = [];

    // public DistribusiKencleng $distribusiKencleng;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                    Fieldset::make('Data Kencleng')
                    ->schema([
                        // TextInput::make('kencleng_id')
                        //     ->label('No. Kencleng')
                        //     ->placeholder(fn($get) => $get('scanner'))
                        //     ->live(),
                            // ->afterStateUpdated('scanQr'),

                        // Toggle::make('tag_lokasi')
                        //     ->label('Tag Lokasi')
                        //     ->inline(false)
                        //     ->extraAttributes(['class' => 'mt-2'])
                        //     ->helperText('Aktifkan untuk menandai lokasi saat ini'),
                        // Select::make('donatur_id')
                        // ->label('Donator')
                        // ->options(Profile::where('group', 'donatur')->pluck('nama', 'id'))
                        // ->searchable(),
                        // Select::make('distributor_id')
                        // ->label('Distributor')
                        // ->options(Profile::where('group', 'distributor')->pluck('nama', 'id'))
                        // ->searchable(),
                        // Select::make('kolektor_id')
                        // ->label('Kolektor')
                        // ->options(Profile::where('group', 'kolektor')->pluck('nama', 'id'))
                        // ->searchable(),
                    ])
                        ->columns(1),
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function scanQr(string $data, Set $set): void
    {
        $data = $data;
        $set(['data' => ['1' => $data]]);
        
        dd($this->form->getState('data'));
    }

    public function render()
    {
        return view('livewire.scanner-q-r');
    }
}
