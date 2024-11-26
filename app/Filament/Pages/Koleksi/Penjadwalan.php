<?php

namespace App\Filament\Pages\Koleksi;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use App\Models\Profile;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class Penjadwalan extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user->is_admin && $user->admin->level === 'admin';
    }

    protected static string $view = 'filament.pages.koleksi.penjadwalan';

    protected static ?string $navigationGroup   = 'Koleksi';
    protected static ?int    $navigationSort    = 1;
    protected static ?string $navigationLabel   = 'Penentuan Kolektor';
    protected static ?string $modelLabel        = 'Penentuan Kolektor';
    protected static ?string $title             = 'Penetuan Kolektor';
    protected static ?string $slug              = 'koleksi/kolektor';
    protected static ?string $navigationIcon    = 'heroicon-o-document-text';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(
                DistribusiKencleng::where('status', '!=', StatusDistribusi::DISTRIBUSI)
                ->orderBy('tgl_batas_pengambilan', 'desc'))
                // ->where('tgl_batas_pengambilan', '<=', now()->addDays(7)))
            
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('ID Kencleng')
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.no_wa')
                    ->label('No. Whatsapp'),
                Tables\Columns\TextColumn::make('area.nama_area')
                    ->label('Area')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('donatur.alamat')
                    ->label('Alamat')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_batas_pengambilan')
                    ->label('Jadwal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_batas_pengambilan')
                    ->label('Jadwal')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        $daysRemaining = (int) now()->diffInDays($record->tgl_batas_pengambilan, false);

                        if ($daysRemaining > 10 || $record->kolektor_id !== null)
                        return 'success';
                        if ($daysRemaining <= 3) {
                            return 'danger';
                        } elseif ($daysRemaining <= 10) {
                            return 'warning';
                        }
                    })
                    ->formatStateUsing(function ($record) {
                        $daysRemaining = floor(now()->diffInDays($record->tgl_batas_pengambilan, false));
                        if ($daysRemaining > 0) {
                            return "{$daysRemaining} hari lagi";
                        } elseif ($daysRemaining == 0) {
                            return "Hari ini";
                        } else {
                            return date('d M Y', strtotime($record->tgl_batas_pengambilan));
                        }
                    }),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->placeholder('Belum ada')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kolektor_id')
                    ->label('Status')
                    ->badge()
                    ->default('Belum Ditentukan')
                    ->color(fn($record) => $record->kolektor_id ? 'success' : 'danger')
                    // ->formatStateUsing(fn($record) => match ($record->kolektor_id) {
                    //      => 'Sudah Ditentukan',
                    //     default => 'Belum Ditentukan',
                    // })
                    ->formatStateUsing(fn($record) => $record?->kolektor_id ? 'Sudah Ditentukan' : 'Belum Ditentukan')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('kolektor_id')
                ->native(false)
                ->label('Status Penentuan')
                ->placeholder('Semua')
                ->trueLabel('Sudah ditentukan')
                ->falseLabel('Belum ditentukan')
                ->queries(
                    true: fn(Builder $query) => $query->whereNotNull('kolektor_id'),
                    false: fn(Builder $query) => $query->whereNull('kolektor_id')->orderBy('tgl_batas_pengambilan', 'asc'),
                    blank: fn(Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                )
            ])
            ->actions([
                Tables\Actions\Action::make('pilihKolektor')
                ->button()
                ->disabled(fn($record) => $record->status !== StatusDistribusi::DIISI)
                ->icon('heroicon-o-forward')
                ->color(fn($record) => $record->status !== StatusDistribusi::DIISI ? 'gray' : 'primary')
                ->modalSubmitActionLabel('Jadwalkan')
                ->form(
                    fn() => [
                        Select::make('kolektor_id')
                        ->label('Kolektor')
                        ->native(false)
                        ->searchable()
                        ->options(function ($record) {
                            $listKolektor = Profile::where('group', '!=', 'donatur')->pluck('nama', 'id')->toArray();

                            $donatur = Profile::find($record->donatur_id);
                            $listKolektor = [$donatur->id => $donatur->nama . ' (Donatur)'] + $listKolektor;
                            
                            return $listKolektor;
                        })
                    ]
                )
                ->action(
                    function (DistribusiKencleng $record, $data) 
                    {
                        $record->update([
                            'kolektor_id' => $data['kolektor_id'],
                        ]);
                    }
                ),
                // Tables\Actions\Action::make('aturArea')
                // ->button()
                // ->icon('heroicon-o-map')
                // ->color('primary')
                // ->modalSubmitActionLabel('Atur Area')
                // ->form(
                //     fn() => [
                //         Select::make('area_id')
                //         ->label('Area')
                //         ->native(false)
                //         ->relationship('area')
                //         ->options(Area::all()->pluck('nama_area', 'id')->toArray())
                //     ]
                // )
                // ->action(
                //     function (DistribusiKencleng $record, $data) 
                //     {
                //         $record->update([
                //             'area_id' => $data['area_id'],
                //         ]);
                //     }
                // ),
            ]);
    }
}
