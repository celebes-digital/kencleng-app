<?php

namespace App\Filament\Pages\Distribusi\Distributor;

use App\Enums\StatusKencleng;
use App\Filament\Components\ScannerQrCode;
use App\Models\DistribusiKencleng;
use App\Models\Kencleng;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;

class AmbilKencleng extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    public static function canAccess(): bool
    {
        return !Auth::user()->is_admin && Auth::user()->profile->group === 'distributor';
    }
    
    public ?string $heading                         = 'Ambil Kencleng';
    protected static ?string $slug                  = 'distribusi/ambil-kencleng';
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $navigationIcon        = 'heroicon-o-document-text';
    protected static string $view                   = 'filament.pages.distribusi.distributor.ambil-kencleng';

    public ?array $data = [];

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                ScannerQrCode::make('scanner')
                    ->label('')
                    ->live()
                    ->registerActions([
                        Action::make('reset')
                            ->label('Reset')
                            ->icon('heroicon-o-arrow-path')
                            ->color('gray')
                            ->button()
                            ->action(function () {
                                $this->dispatch('component-mounted');
                                $this->form->fill([]);
                            }),
                    ])
                    ->afterStateUpdated(
                        function (Forms\Set $set, $state) {
                            $kencleng = Kencleng::where('no_kencleng', $state)->first();

                            Notification::make()
                                ->title('Kencleng ' . $kencleng->no_kencleng  . ' ditemukan')
                                ->success()
                                ->send();

                            $set('no_kencleng', $kencleng->no_kencleng);
                            $set('kencleng_id', $kencleng->id);
                        }
                    ),

                    Fieldset::make('Data Kencleng')
                    ->schema([
                        Forms\Components\Hidden::make('kencleng_id'),
                        Forms\Components\TextInput::make('no_kencleng')
                            ->label('ID. Kencleng')
                            ->readOnly()
                            ->required()
                            ->hint('Scan QR Code Kencleng')
                            ->columnSpanFull(),

                        Actions::make([
                            Action::make('saveAction')
                                ->label('Ambil Kencleng')
                                ->icon('heroicon-o-arrows-pointing-in')
                                ->action(function () {
                                    $this->save();
                                })
                            ])
                            ->fullWidth(),
                    ])
                    ->columns(1),
                ])
            ])
            ->columns(1)
            ->statePath('data');
    }

    public function checkNoKencleng($noKencleng): bool
    {
        $kencleng = Kencleng::where('no_kencleng', $noKencleng)->where('status', StatusKencleng::TERSEDIA)->first();

        if (!$kencleng) {
            Notification::make()
            ->title('Status kencleng sedang distribusi atau tidak ditemukan')
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

            // Inisialisasi data distribusi kencleng
            // DIstributor ID dan status jadi distribusi
            $query = DistribusiKencleng::create([
                'kencleng_id'       => $data['kencleng_id'],
                'distributor_id'    => Auth::user()->profile?->id,
                'tgl_distribusi'    => now(),
                'status'            => 'distribusi',
            ]);

            $query = $query->kencleng()->update(['status' => StatusKencleng::SEDANGDISTRIBUSI]);

            if (!$query) throw new Halt('Gagal menyimpan data');

            Notification::make()
                ->title('Berhasil melakukan pengambilan kencleng di aqtif')
                ->success()
                ->send();

            $this->dispatch('component-mounted');
            $this->form->fill([]);
        } catch (Halt $e) {
            Notification::make()
                ->title($e->getMessage() ?? 'Gagal menyimpan data')
                ->danger()
                ->send();
            return;
        }
    }
}
