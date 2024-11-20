<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;

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
        return Auth::user()->admin->level === 'admin';
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
                ->orWhere('status', StatusDistribusi::DIISI)
                ->whereNull('kolektor_id'))
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
                        ->hidden($record->kolektor ? false : true)
                        ->default($record->kolektor?->nama)
                        ->disabled(),
                    Forms\Components\TextInput::make('donatur')
                        ->default($record->donatur->nama)
                        ->disabled(),
                    Forms\Components\TextInput::make('donasi')
                        ->hidden($record->kolektor ? false : true)
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
                        ->rows(3)
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
                            'status' => 'diterima',
                        ]);
                    }
                ),
            ]);
    }
}
