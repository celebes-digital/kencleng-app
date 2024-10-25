<?php

namespace App\Filament\Pages;

use App\Models\Kencleng;
use App\Models\DistribusiKencleng;

use App\Filament\Components\ScannerQrCode;
use App\Models\Profile;
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
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Auth;

/**
 * Kalau halaman ini nanti mau di kasih aktif untuk admin
 * maka toggle untuk is_kolektor dihilangkan ketika yang akses admin
 */

class TagLokasi extends Page implements HasForms
{
    use InteractsWithForms;

    public static function canAccess(): bool
    {
        $user = Auth::user()->load('profile');
        return
            (!$user->is_admin && $user->is_active) 
                && ($user->profile->group === 'kolektor' || $user->profile->group === 'distributor');
    }

    public ?array $data = [];

    protected static string  $view              = 'filament.pages.tag-lokasi';
    protected static ?string $modelLabel        = 'Tag Lokasi';
    protected static ?string $label             = 'Tag Lokasi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $slug              = 'tag-lokasi';
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?int    $navigationSort    = 3;

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
                        ->afterStateUpdated(
                            function (Set $set, $state) 
                            {
                                $kencleng = Kencleng::where('no_kencleng', $state)->first();

                                Notification::make()
                                    ->title('Kencleng ' . $kencleng->no_kencleng  . ' ditemukan')
                                    ->success()
                                    ->send();

                                $set('kencleng_id', $kencleng->id);
                            }
                        ),

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

                            
                            Select::make('donatur_id')
                                ->label('Donator')
                                ->relationship('donatur', 'nama')
                                ->options(
                                    Profile::where('group', 'donatur')
                                        ->pluck('nama', 'id')
                                )
                                ->searchable()
                                ->columnSpanFull()
                                ->required()
                                ->optionsLimit(10),

                            TextInput::make('latitude')
                                ->disabled()
                                ->required(),

                            TextInput::make('longitude')
                                ->disabled()
                                ->required(),

                            Toggle::make('is_kolektor')
                                ->label('Aktifkan jika sebagai kolektor')
                                ->columnSpanFull(),
                        ])
                ])->from('md'),
            ])
            ->statePath('data')
            ->columns(1);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
        ];
    }


    public function checkNoKencleng($noKencleng): bool
    {
        $kencleng = Kencleng::where('no_kencleng', $noKencleng)->first();

        if (!$kencleng) 
        {
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
        try {
            $distribusiKencleng = DistribusiKencleng::where('kencleng_id', $this->data['kencleng_id'])
                ->whereNull('tgl_distribusi')
                ->first();

            if (empty($distribusiKencleng)) {
                Notification::make()
                    ->title('Kencleng tidak tersedia, hubungi admin untuk informasi lebih lanjut')
                    ->danger()
                    ->send();
                return;
            }

            $distribusiKencleng->tgl_distribusi = now();
            $distribusiKencleng->geo_lat        = $this->data['latitude'];
            $distribusiKencleng->geo_long       = $this->data['longitude'];
            $distribusiKencleng->donatur_id     = $this->data['donatur_id'];

            if($this->data['is_kolektor']) {
                $distribusiKencleng->kolektor_id = Auth::user()->profile->id;
            }

            $distribusiKencleng->save();

        } catch (Halt $e) {
            return;
        }

        Notification::make()
            ->success()
            ->title('Lokasi Kencleng berhasil ditandai')
            ->send();
    }
}
