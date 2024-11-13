<?php

namespace App\Filament\Pages;

use App\Models;
use App\Filament\Components\ScannerQrCode;

use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Components;
use Filament\Forms\Set;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

class DistribusiToDistributor extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $view = 'filament.pages.distribusi-to-distributor';

    protected static ?string $modelLabel        = 'Ke Distributor';
    protected static ?string $title             = 'Ke Distributor';
    protected static ?string $slug              = 'distribusi/distributor';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 3;

    protected static ?string $navigationLabel   = 'Ke Distributor';

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
                    // Inputan untuk scanner
                    ScannerQrCode::make('scanner')
                    ->hiddenLabel()
                    ->live()
                    ->afterStateUpdated(
                        function (Set $set, $state) 
                        {
                            $kencleng = Models\Kencleng::where('no_kencleng', $state)->first();

                            Notification::make()
                                ->title('Kencleng ' . $kencleng->no_kencleng . ' ditemukan')
                                ->success()
                                ->send();

                            $set('kencleng_id', $kencleng->id);
                        }
                    ),

                    // Data distribusi
                    Components\Fieldset::make('Data Distribusi')
                    ->schema([
                        Components\Select::make('kencleng_id')
                            ->label('ID Kencleng')
                            ->options(Models\Kencleng::all()->pluck('no_kencleng', 'id'))
                            ->searchable()
                            ->required()
                            ->optionsLimit(10),

                        Components\Select::make('distributor_id')
                            ->label('Distributor')
                            ->relationship('distributor', 'nama')
                            ->options(
                                Models\Profile::where('group', 'distributor')
                                ->pluck('nama', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->optionsLimit(10),
                    ])
                    ->columns(1),
                ])
                ->from('md')
            ])
            ->statePath('data')
            ->columns(1);
    }

    protected function getFormActions(): array
    {
        return 
        [
            Action::make('save')
                ->label('Konfirmasi')
                ->submit('save'),
        ];
    }

    public function checkNoKencleng($noKencleng): bool
    {
        $kencleng = Models\Kencleng::where('no_kencleng', $noKencleng)->first();

        // Jika qr bukan dari nomor kencleng
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
            // DIstributor ID dan status jadi distribusi
            $query = Models\DistribusiKencleng::create([
                'kencleng_id'       => $this->data['kencleng_id'],
                'distributor_id'    => $this->data['distributor_id'],
                'tgl_distribusi'    => now(),
                'status'            => 'distribusi',
            ]);

            if ( !$query ) throw new Halt('Gagal menyimpan data');

            Notification::make()
                ->title('Berhasil melakukan distribusi kencleng ke distributor')
                ->success()
                ->send();
        } 
        catch (Halt $e) 
        {
            return;
        }
    }
}
