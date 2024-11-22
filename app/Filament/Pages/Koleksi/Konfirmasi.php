<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Enums\StatusKencleng;
use App\Models\DistribusiKencleng;
use App\Models\Infaq;

use Filament\Pages\Page;
use Filament\Forms;

use Filament\Tables;
use Filament\Tables\Table;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;

class Konfirmasi 
    extends Page 
    implements Tables\Contracts\HasTable, Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;
    use Tables\Concerns\InteractsWithTable;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->is_admin && $user->admin->level === 'admin';
    }
    
    protected static string $view               = 'filament.pages.koleksi.konfirmasi';
    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?int    $navigationSort    = 2;
    protected static ?string $navigationLabel   = 'Konfirmasi';
    protected static ?string $modelLabel        = 'Konfirmasi';
    protected static ?string $title             = 'Konfirmasi';
    protected static ?string $slug              = 'koleksi/konfirmasi';
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DistribusiKencleng::where('status', StatusDistribusi::KEMBALI)
                ->orWhere('status', StatusDistribusi::DIISI))
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('No. Kencleng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->placeholder('Tidak ada')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_pengambilan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('konfirmasi')
                ->button()
                ->color('primary')
                ->icon('heroicon-o-finger-print')
                ->modalSubmitActionLabel('Konfirmasi')
                ->form(fn($record) => [
                    Forms\Components\TextInput::make('kolektor')
                        ->hidden($record->status === StatusDistribusi::KEMBALI ? false : true)
                        ->default($record?->kolektor?->nama)
                        ->disabled(),
                    Forms\Components\TextInput::make('donatur')
                        ->default($record?->donatur->nama)
                        ->disabled(),
                    Forms\Components\TextInput::make('donasi')
                        ->hidden($record->status === StatusDistribusi::KEMBALI ? false : true)
                        ->default(Number::currency($record->jumlah ?? 0, 'IDR', 'id'))
                        ->placeholder('Tidak ada')
                        ->prefixIcon('heroicon-o-banknotes')
                        ->disabled(),
                    Forms\Components\TextInput::make('jumlah_donasi')
                        ->label('Jumlah Diterima')
                        ->prefix('Rp')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    Forms\Components\Textarea::make('uraian')
                        ->label('Keterangan Tambahan')
                        ->autosize()
                        ->rows(3),
                    Forms\Components\Radio::make('status')
                        ->label('Status')
                        ->hidden($record?->status === StatusDistribusi::KEMBALI ? true : false)
                        ->default('lanjut_tetap')
                        ->options([
                            'berhenti'          => 'Berhenti',
                            'lanjut_tetap'      => 'Lanjut Tetap',
                            'lanjut_pindah'     => 'Lanjut Pindah',
                        ])
                        ->inline()
                        ->inlineLabel(false)
                        ->required(),
                ])
                ->action(
                    function (DistribusiKencleng $record, $data) 
                    {
                        Infaq::create([
                            'cabang_id'     => Auth::user()->admin->cabang_id,
                            'distribusi_id' => $record->id,
                            'tgl_transaksi' => now(),
                            'jumlah_donasi' => $data['jumlah_donasi'],
                            'uraian'        => $data['uraian'],
                        ]);

                        $record->update([
                            'status'            => 'diterima',
                            'tgl_pengambilan'   => $record->tgl_pengambilan ?? now(),
                            'jumlah'            => $record->jumlah ?? $data['jumlah_donasi'],
                        ]);

                        // Jika donatur kolek langsung ke aqtif
                        if ($record?->status !== StatusDistribusi::KEMBALI) {
                            switch ($data['status']) {
                                // Jika donatur masih lanjut dan ditempat yang sama
                                case 'lanjut_tetap':
                                    DistribusiKencleng::create([
                                        'kencleng_id'           => $record['kencleng_id'],
                                        'donatur_id'            => $record['donatur_id'],
                                        'distributor_id'        => $record['distibutor_id'],
                                        'cabang_id'             => $record['cabang_id'],
                                        'tgl_distribusi'        => now(),
                                        'tgl_aktivasi'          => now(),
                                        'geo_lat'               => $record['latitude'],
                                        'geo_long'              => $record['longitude'],
                                        'status'                => 'diisi',
                                        'tgl_batas_pengambilan' => now()->addMonth(),
                                    ]);
                                    break;
                                // Jika donatur masih lanjut dan pindah tempat
                                case 'lanjut_pindah':
                                    DistribusiKencleng::create([
                                        'kencleng_id'           => $record['kencleng_id'],
                                        'donatur_id'            => $record['donatur_id'],
                                        'distributor_id'        => $record['distibutor_id'],
                                        'cabang_id'             => $record['cabang_id'],
                                        'tgl_distribusi'        => now(),
                                        'status'                => 'distribusi',
                                        'tgl_batas_pengambilan' => now()->addMonth(),
                                    ]);
                                    break;
                                // Jika donatur berhenti
                                case 'berhenti':
                                    $record->kencleng()->update([
                                        'status' => StatusKencleng::TERSEDIA,
                                    ]);
                                    break;
                            }
                        }
                    }
                ),
            ]);
    }
}
