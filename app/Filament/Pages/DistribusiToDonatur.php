<?php

namespace App\Filament\Pages;

use App\Models;
use App\Filament\Components\ScannerQrCode;

use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Components;
use Filament\Forms\Set;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

class DistribusiToDonatur extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.pages.distribusi-to-donatur';

    protected static ?string $modelLabel        = 'Ke Donatur';
    protected static ?string $title             = 'Ke Donatur';
    protected static ?string $slug              = 'distribusi/donatur';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 3;

    protected static ?string $navigationLabel   = 'Ke Donatur';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Split::make([
                    ScannerQrCode::make('scanner')
                    ->hiddenLabel()
                    ->live()
                    ->afterStateUpdated(
                        function (Set $set, $state) {
                            $kencleng = Models\Kencleng::where('no_kencleng', $state)->first();

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

                    Components\Fieldset::make('Data Kencleng')
                    ->schema([
                        Components\Select::make('kencleng_id')
                            ->label('No. Kencleng')
                            ->relationship('kencleng', 'no_kencleng')
                            ->options(Models\Kencleng::all()->pluck('no_kencleng', 'id'))
                            ->searchable()
                            ->required()
                            ->optionsLimit(10),

                        Components\Select::make('donatur_id')
                            ->label('Donatur')
                            ->options(Models\Profile::get()->pluck('nama', 'id'))
                            ->searchable()
                            ->required()
                            ->optionsLimit(10),
                    ])
                    ->columns(1),

                ])
            ])
            ->statePath('data')
            ->columns(1);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Konfirmasi')
                ->submit('save'),
        ];
    }


    public function checkNoKencleng($noKencleng): bool
    {
        $kencleng = Models\Kencleng::where('no_kencleng', $noKencleng)->first();

        if ( !$kencleng ) {
            Notification::make()
                ->title('Kencleng ' . $noKencleng  . ' tidak terdaftar')
                ->danger()
                ->send();

            return false;
        }

        return true;
    }

    public function save()
    {
        try 
        {
            // Inisialisasi data distribusi kencleng
            // Distribusi ke donatur dan status jadi distribusi
            $data = [
                'kencleng_id' => $this->data['kencleng_id'],
                'donatur_id' => $this->data['donatur_id'],
                'tgl_distribusi' => now(),
                'status' => 'distribusi'
            ];

            // Simpan data distribusi kencleng
            $query = Models\DistribusiKencleng::create($data);

            if (!$query) throw new Halt('Gagal menyimpan data');

            Notification::make()
                ->title('Berhasil melakukan distribusi kencleng ke donatur')
                ->success()
                ->send();
        } 
        catch (Halt $e) 
        {
            return;
        }
    }
}
