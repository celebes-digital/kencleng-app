<?php

namespace App\Filament\Pages\Koleksi;

use App\Models\Kencleng;
use App\Models\DistribusiKencleng;

use App\Filament\Components\ScannerQrCode;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Actions\Action;

use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;

use Illuminate\Support\Facades\Auth;

class KoleksiKencleng extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string  $view  = 'filament.pages.koleksi.koleksi-kencleng';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (!$user->is_admin);
    }

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $modelLabel        = 'Koleksi Kencleng ki';
    protected static ?string $label             = 'Koleksi Kencleng ki';
    protected static ?string $slug              = 'koleksi/kencleng';

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

                        dd($kencleng->distribusiKenclengs()->latest);

                        Notification::make()
                            ->title('Kencleng ' . $kencleng->no_kencleng  . ' ditemukan')
                            ->success()
                            ->send();

                        $set('kencleng_id', $kencleng->id);
                    }
                ),
                Forms\Components\Fieldset::make('')
                    ->schema([
                    Forms\Components\Hidden::make('kencleng_id'),
                    Forms\Components\TextInput::make('no_kencleng')
                        ->label('ID Kencleng')
                        ->readOnly()
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->numeric()
                        ->minValue(0)
                        ->prefix('Rp')
                        ->required(),
                    Forms\Components\Radio::make('status')
                        ->label('Status')
                        ->default('lanjut_tetap')
                        ->options([
                            'lanjut_tetap'      => 'Lanjut Tetap',
                            'lanjut_pindah'     => 'Lanjut Pindah',
                            'berhenti'          => 'Berhenti',
                        ])
                        ->inline()
                        ->inlineLabel(false)
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
            $data = $this->form->getState();

            $distribusiKencleng = DistribusiKencleng::where('kencleng_id', $data['kencleng_id'])
                                    ->where('status', 'diisi')
                                    ->latest();

            if ( empty($distribusiKencleng) ) {
                return throw new Halt('Status kencleng tidak valid');
            }

            dd($distribusiKencleng);

            $distribusiKencleng->update([
                'tgl_pengambilan'   => now(),
                'jumlah'            => $this->data['jumlah'],
                'status'            => 'kembali',
            ]);

            if( $data['status'] == 'lanjut_tetap' ) {
                DistribusiKencleng::create([
                    'kencleng_id'           => $distribusiKencleng['kencleng_id'],
                    'donatur_id'            => $distribusiKencleng['donatur_id'],
                    'cabang_id'             => $distribusiKencleng['cabang_id'],
                    'tgl_distribusi'        => now(),
                    'tgl_aktivasi'          => now(),
                    'geo_lat'               => $this->data['latitude'],
                    'geo_long'              => $this->data['longitude'],
                    'status'                => 'diisi',
                    'tgl_batas_pengambilan' => now()->addMonth(),
                ]);
            }

            if($data['status'] == 'lanjut_pindah') {
                DistribusiKencleng::create([
                    'kencleng_id'           => $distribusiKencleng['kencleng_id'],
                    'donatur_id'            => $distribusiKencleng['donatur_id'],
                    'donatur_id'            => $distribusiKencleng['cabang_id'],
                    'tgl_distribusi'        => now(),
                    'status'                => 'distribusi',
                    'tgl_batas_pengambilan' => now()->addMonth(),
                ]);
            }

            Notification::make()
                ->success()
                ->title('Berhasil mengkonfirmasi pengambilan kencleng')
                ->send();
        } 
        catch (Halt $e) 
        {
            Notification::make()
                ->success()
                ->title($e->getMessage() ?? 'Gagal mengkonfirmasi pengambilan kencleng')
                ->send();

            return;
        }
    }
}
