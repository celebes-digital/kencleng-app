<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Exceptions\Halt;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class JadwalKoleksi 
    extends Page 
    implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $view = 'filament.pages.koleksi.jadwal-koleksi';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return !$user->is_admin && $user->profile->group !== 'donatur';
    }

    protected static ?int    $navigationSort    = 3;
    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?string $navigationIcon    = 'heroicon-o-cube';
    protected static ?string $modelLabel        = 'Jadwal Koleksi';
    protected static ?string $label             = 'Jadwal Koleksi';
    protected static ?string $slug              = 'koleksi/jadwal';

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $user = Auth::user()->profile;

                return DistribusiKencleng::query()
                    ->where('kolektor_id', $user->id)
                    ->where('status', StatusDistribusi::DIISI);
            })
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('ID Kencleng'),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur'),
                Tables\Columns\TextColumn::make('area.nama_area')
                    ->label('Area'),
                Tables\Columns\TextColumn::make('tgl_batas_pengambilan')
                    ->label('Jadwal')
                    ->date('d F Y'),
                Tables\Columns\TextColumn::make('donatur.no_hp')
                    ->label('HP Donatur'),
                Tables\Columns\TextColumn::make('donatur.no_wa')
                    ->label('WA Donatur'),
                Tables\Columns\TextColumn::make('donatur.alamat')
                    ->label('Alamat'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('lokasi')
                ->iconButton()
                ->tooltip('Lihat Lokasi')
                ->icon('heroicon-o-map-pin')
                ->color(
                    fn($record)
                    => $record->geo_lat !== null
                        ? 'info'
                        : 'gray'
                )
                ->disabled(
                    fn($record)
                    => $record->geo_lat === null
                )
                ->url(
                    fn($record)
                    => "https://www.google.com/maps/search/?api=1&query="
                        . $record->geo_lat
                        . ","
                        . $record->geo_long,
                    true
                ),
                Action::make('koleksi')
                ->label('Koleksi')
                ->button()
                ->icon('heroicon-o-shopping-cart')
                ->iconPosition(IconPosition::After)
                ->modalSubmitActionLabel('Konfirmasi')
                ->modalDescription('Koleksi kencleng dari donatur')
                ->modal()
                ->form(
                    fn ($record) => [
                        Forms\Components\TextInput::make('donatur_id')
                        ->disabled()
                        ->default($record->donatur->nama)
                        ->label('Donatur'),
                        Forms\Components\TextInput::make('alamat')
                        ->disabled()
                        ->default($record->donatur->alamat)
                        ->label('Alamat'),
                        Forms\Components\TextInput::make('jumlah')
                        ->label('Jumlah')
                        ->prefix('IDR')
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
                    ]
                )
                ->action(
                    function ($record, $data) {
                        $this->konfirmasiDonasiAction($record, $data);
                    }
                ),
            ]);
    }

    public function konfirmasiDonasiAction($record, $data)
    {
        try {
            $record->update([
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
        } catch (Halt $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage() ?? 'Gagal mengkonfirmasi pengambilan kencleng')
                ->send();

            return;
        }
    }
}
