<?php

namespace App\Filament\Widgets;

use App\Enums\StatusDistribusi;
use App\Models\DistribusiKencleng;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TableDeadlineWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Daftar kencleng yang harus diambil';

    public static function canView(): bool
    {
        return !Auth::user()->is_admin;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DistribusiKencleng::where('distributor_id', Auth::user()->profile->id)
                    ->where('status', '==', 'diisi')
                    ->orderBy('tgl_distribusi', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('kencleng.no_kencleng')
                    ->label('No. Kencleng')
                    ->sortable(),
                Tables\Columns\TextColumn::make('donatur.nama')
                    ->label('Donatur')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kolektor.nama')
                    ->label('Kolektor')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('distributor.nama')
                    ->label('Distributor')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('geo_lat')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('geo_long')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('tgl_distribusi')
                    ->date()
                    ->sortable(),
                TextColumn::make('tgl_distribusi')
                    ->label('Deadline')
                    ->icon('heroicon-o-calendar')
                    ->badge()
                    ->color(
                        function ($record) {
                            return $this->getColorByStatus($record);
                        }
                    )
                    ->formatStateUsing(
                        function ($state, $record) {
                            $deadline = Carbon::parse($state)
                                ->addMonthWithoutOverflow()
                                ->addDay(1);

                            if ($record->status->value === 'diterima') {
                                return 'Selesai';
                            }

                            if ($deadline->isPast()) {
                                return 'Lewat deadline';
                            }

                            return $deadline->diffForHumans();
                        }
                    ),
                // Tables\Columns\TextColumn::make('tgl_pengambilan')
                //     ->date()
                //     ->sortable(),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->label('Terakhir diubah')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('status')
                //     ->badge(StatusDistribusi::class)
                //     ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Action::make('buka_maps')
                    ->label('Buka Maps')
                    ->button()
                    ->requiresConfirmation()
                    ->url(
                        fn($record)
                        => 'https://www.google.com/maps/search/?api=1&query='
                            . $record->geo_lat
                            . ','
                            . $record->geo_long,
                    )
                    ->openUrlInNewTab()
            ]);
    }


    private function getColorByStatus(DistribusiKencleng $event, bool $isHex = false): string
    {
        $deadline = Carbon::parse($event->tgl_distribusi)
            ->addMonthWithoutOverflow()
            ->addDay(1);

        $color = $isHex
            ? ['', '#dc2626', '#f59e0b', '#3b82f6']
            : ['primary', 'danger', 'warning', 'info'];

        if ($event->status->value === 'diterima') {
            return $color[0];
        }

        if ($deadline->isPast()) {
            return $color[1];
        }

        if ($deadline->subDays(7)->isPast()) {
            return $color[2];
        }

        return $color[3];
    }
}
