<?php

namespace App\Livewire\Forms;

use App\Filament\Components\ScannerQrCode;
use App\Models\Kencleng;
use App\Models\Profile;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Component;

class DaftarDonatur extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static string $layout = 'layouts.app';

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    ScannerQrCode::make('scanner')
                        ->live()
                        ->afterStateUpdated(
                            function (callable $set, $state) {
                                $kencleng = Kencleng::where('no_kencleng', $state)->first();

                                Notification::make()
                                    ->title(
                                        'Kencleng '
                                        . $kencleng->no_kencleng
                                            . ' ditemukan'
                                    )
                                    ->success()
                                    ->send();

                                $set('kencleng_id', $kencleng->id);
                            }
                        ),

                    Fieldset::make('Data Kencleng')
                        ->schema([
                            Select::make('kencleng_id')
                                ->label('No. Kencleng')
                                ->relationship('kencleng', 'no_kencleng')
                                ->options(Kencleng::all()->pluck('no_kencleng', 'id'))
                                ->searchable()
                                ->required()
                                ->optionsLimit(10),


                            Select::make('distributor_id')
                                ->label('Distributor')
                                ->relationship('distributor', 'nama')
                                ->options(
                                    Profile::where('group', 'distributor')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->required()
                                ->optionsLimit(10),

                            Select::make('donatur_id')
                                ->label('Donator')
                                ->relationship('donatur', 'nama')
                                ->options(
                                    Profile::where('group', 'donatur')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->optionsLimit(10),

                            Select::make('kolektor_id')
                                ->label('Kolektor')
                                ->relationship('kolektor', 'nama')
                                ->options(
                                    Profile::where('group', 'kolektor')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->optionsLimit(10),
                        ])
                        ->columns(1),
                ])
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render()
    {
        return view('livewire.daftar-donatur');
    }
}
