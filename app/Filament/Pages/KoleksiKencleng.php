<?php

namespace App\Filament\Pages;

use App\Models\Kencleng;
use App\Models\DistribusiKencleng;

use App\Filament\Components\ScannerQrCode;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;


class KoleksiKencleng extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    protected static string  $view              = 'filament.pages.koleksi-kencleng';
    protected static ?string $modelLabel        = 'Koleksi Kencleng ki';
    protected static ?string $label             = 'Koleksi Kencleng ki';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'koleksi-kencleng';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 2;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    ScannerQrCode::make('scanner')
                    ->label('')
                        ->live()
                        ->afterStateUpdated(function (Set $set, $state) {
                            $kencleng = Kencleng::where('no_kencleng', $state)->first();

                            Notification::make()
                                ->title('Kencleng ' . $kencleng->no_kencleng  . ' ditemukan')
                                ->success()
                                ->send();

                            $set('kencleng_id', $kencleng->id);
                        }),
                    Fieldset::make('')
                        ->schema([
                            Select::make('kencleng_id')
                                ->label('No. Kencleng')
                                ->options(
                                    Kencleng::all()->pluck('no_kencleng', 'id')
                                )
                                ->disabled()
                                ->required()
                                ->hint('Scan QR Code Kencleng')
                                ->columnSpanFull(),
                            TextInput::make('jumlah')
                                ->label('Jumlah')
                                ->numeric()
                                ->minValue(0)
                                ->prefix('Rp')
                                ->required(),
                        ])->columns(1),
                ])->from('md'),
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
        $kencleng = Kencleng::where('no_kencleng', $noKencleng)->first();

        if (!$kencleng) {
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
            $distribusiKencleng = DistribusiKencleng::where('kencleng_id', $this->data['kencleng_id'])
                                        ->where('status', 'diisi')
                                        ->latest();

            if (empty($distribusiKencleng)) {
                Notification::make()
                    ->title('Status kencleng tidak valid, hubungi admin untuk informasi lebih lanjut')
                    ->danger()
                    ->send();
                return;
            }

            $distribusiKencleng->update([
                'tgl_pengambilan'   => now(),
                'jumlah'            => $this->data['jumlah'],
            ]);
        } 
        catch (Halt $e) 
        {
            return;
        }

        Notification::make()
            ->success()
            ->title('Berhasil mengkonfirmasi pengambilan kencleng')
            ->send();
    }
}
