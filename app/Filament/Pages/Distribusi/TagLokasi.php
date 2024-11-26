<?php

namespace App\Filament\Pages\Distribusi;

use App\Enums\StatusDistribusi;

use App\Models\Profile;
use App\Models\Kencleng;
use App\Models\DistribusiKencleng;

use App\Filament\Components\ScannerQrCode;

use Filament\Pages\Page;
use Filament\Actions\Action;

use Filament\Forms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

use Illuminate\Support\Facades\Auth;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Filament\Support\Enums\VerticalAlignment;

class TagLokasi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string  $view  = 'filament.pages.distribusi.tag-lokasi';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return
            (!$user->is_admin) 
                && ($user->profile->group === 'donatur' || $user->profile->group === 'distributor');
    }

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Distribusi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $modelLabel        = 'Tag Lokasi';
    protected static ?string $label             = 'Tag Lokasi';
    protected static ?string $slug              = 'distribusi/tag-lokasi';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->data);
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Split::make([
                    ScannerQrCode::make('scanner')
                        ->label('')
                        ->live()
                        ->afterStateUpdated(
                            function (Forms\Set $set, $state) 
                            {
                                $kencleng = Kencleng::where('no_kencleng', $state)->first();

                                Notification::make()
                                    ->title('Kencleng ' . $kencleng->no_kencleng  . ' ditemukan')
                                    ->success()
                                    ->send();

                                $set('no_kencleng', $kencleng->no_kencleng);
                                $set('kencleng_id', $kencleng->id);
                            }
                        ),

                    Forms\Components\Fieldset::make('')
                        ->schema([
                            Forms\Components\Hidden::make('kencleng_id'),
                            Forms\Components\TextInput::make('no_kencleng')
                                ->label('ID. Kencleng')
                                ->readOnly()
                                ->required()
                                ->hint('Scan QR Code Kencleng')
                                ->columnSpanFull(),

                            Forms\Components\Select::make('donatur_id')
                                ->label('Donatur')
                                ->hidden(fn() => Auth::user()->profile->group === 'donatur')
                                ->options(Profile::all()->pluck('nama', 'id'))
                                ->searchable()
                                ->columnSpanFull()
                                ->required()
                                ->optionsLimit(10),

                            Forms\Components\Group::make([
                                Forms\Components\TextInput::make('latitude')
                                    ->readOnly()
                                    ->columnSpan(3)
                                    ->required()
                                    ->live(),
    
                                Forms\Components\TextInput::make('longitude')
                                    ->readOnly()
                                    ->columnSpan(3)
                                    ->required()
                                    ->live(),
    
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('getLocation')
                                        ->icon('heroicon-o-map-pin')
                                        ->button()
                                        ->hiddenLabel()
                                        ->action(function () {
                                            $this->dispatch('getLocation');
                                        })
                                    ])
                                    ->verticalAlignment(VerticalAlignment::End)
                            ])
                            ->dehydrated(true)
                            ->columnSpanFull()
                            ->columns(7)
                        ])
                ])->from('md'),
            ])
            ->columns(1)
            ->statePath('data');
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
            $data = $this->form->getState();

            $distribusiKencleng = DistribusiKencleng::where('kencleng_id', $data['kencleng_id'])
                ->where('status', StatusDistribusi::DISTRIBUSI)
                ->orderBy('created_at', 'desc')
                ->first();

            if (empty($distribusiKencleng)) {
                Notification::make()
                    ->title('Kencleng tidak tersedia, hubungi admin untuk informasi lebih lanjut')
                    ->danger()
                    ->send();
                    
                return;
            }

            $distribusiKencleng->tgl_aktivasi           = now();
            $distribusiKencleng->tgl_batas_pengambilan  = now()->addMonth();
            $distribusiKencleng->geo_lat                = $this->data['latitude'];
            $distribusiKencleng->geo_long               = $this->data['longitude'];
            $distribusiKencleng->status                 = StatusDistribusi::DIISI;
            $distribusiKencleng->donatur_id             = $distribusiKencleng->donatur_id 
                                                            ?? $this->data['donatur_id'];

            if (Auth::user()->profile->group === 'donatur') {
                $distribusiKencleng->donatur_id = Auth::user()->profile->id;
            }

            $distribusiKencleng->save();

            $this->form->fill([]);

            Notification::make()
            ->success()
            ->title('Lokasi Kencleng berhasil ditandai')
            ->send();
        } catch (Halt $e) {
            return;
        }
    }
}
