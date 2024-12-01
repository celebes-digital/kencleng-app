<?php

namespace App\Filament\Pages\Koleksi;

use App\Models\Kencleng;
use App\Models\DistribusiKencleng;

use App\Filament\Components\ScannerQrCode;
use App\Libraries\WhatsappAPI;
use Filament\Forms;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Components\Actions\Action as ActionsAction;
use Filament\Support\Exceptions\Halt;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\Auth;

class KoleksiKencleng extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    
    public static function canAccess(): bool
    {
        $user = Auth::user();
        
        return (!$user->is_admin) && ($user->profile->group !== 'donatur');
    }
    
    protected static string  $view              = 'filament.pages.koleksi.koleksi-kencleng';
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
                    ->registerActions([
                        ActionsAction::make('reset')
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
                        ->mask(RawJs::make(
                            <<<'JS'
                                    $money($input, ',', '.', 0);
                                JS
                        ))
                        ->stripCharacters(['.'])
                        ->numeric()
                        ->minValue(0)
                        ->prefix('IDR')
                        ->required(),
                    Forms\Components\Radio::make('status')
                        ->label('Status')
                        ->default('lanjut_tetap')
                        ->options([
                            'lanjut_tetap'      => 'Lanjut Tetap',
                            // 'lanjut_pindah'     => 'Lanjut Pindah',
                            'tidak_lanjut'      => 'Tidak Lanjut',
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
                                    ->orderBy('tgl_distribusi', 'desc')
                                    ->first();

            if ( empty($distribusiKencleng) ) {
                return throw new Halt('Kencleng yang dapat dikoleksi adalah kencleng dengan status diisi');
            }

            if( $distribusiKencleng->kolektor_id !== Auth::user()->profile->id ) {
                return throw new Halt('Anda tidak/belum ditugaskan untuk mengambil kencleng ini oleh admin');
            }

            $distribusiKencleng->update([
                'tgl_pengambilan'   => now(),
                'jumlah'            => $data['jumlah'],
                'status'            => 'kembali',
                'status_kelanjutan' => $data['status'],
            ]);

            // if( $data['status'] == 'lanjut_tetap' ) {
            //     DistribusiKencleng::create([
            //         'kencleng_id'           => $distribusiKencleng['kencleng_id'],
            //         'donatur_id'            => $distribusiKencleng['donatur_id'],
            //         'distributor_id'        => $distribusiKencleng['distibutor_id'],
            //         'cabang_id'             => $distribusiKencleng['cabang_id'],
            //         'tgl_distribusi'        => now(),
            //         'tgl_aktivasi'          => now(),
            //         'geo_lat'               => $this->data['latitude'],
            //         'geo_long'              => $this->data['longitude'],
            //         'status'                => 'diisi',
            //         'tgl_batas_pengambilan' => now()->addMonth(),
            //     ]);
            // }

            // if($data['status'] == 'lanjut_pindah') {
            //     DistribusiKencleng::create([
            //         'kencleng_id'           => $distribusiKencleng['kencleng_id'],
            //         'donatur_id'            => $distribusiKencleng['donatur_id'],
            //         'distributor_id'        => $distribusiKencleng['distibutor_id'],
            //         'cabang_id'             => $distribusiKencleng['cabang_id'],
            //         'tgl_distribusi'        => now(),
            //         'status'                => 'distribusi',
            //         'tgl_batas_pengambilan' => now()->addMonth(),
            //     ]);
            // }

            Notification::make()
                ->success()
                ->title('Berhasil mengkonfirmasi pengambilan kencleng')
                ->send();

            $this->dispatch('component-mounted');
            $this->form->fill([]);

            $whatsapp = new WhatsappAPI($distribusiKencleng->donatur?->no_wa);

            $data = [
                'nama'      => $distribusiKencleng->donatur?->nama,
                'kelamin'   => $distribusiKencleng->donatur?->kelamin,
                'jumlah'    => $data['jumlah'],
            ];

            $whatsapp->getTemplateMessage('KonfirmasiKoleksiKeDonatur', $data);
            $whatsapp->send();
        } 
        catch (Halt $e) 
        {
            Notification::make()
                ->danger()
                ->title($e->getMessage() ?? 'Gagal mengkonfirmasi pengambilan kencleng')
                ->send();

            return;
        }
    }
}
