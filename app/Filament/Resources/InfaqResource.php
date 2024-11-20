<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfaqResource\Pages;
use App\Filament\Resources\InfaqResource\RelationManagers;
use App\Models\Infaq;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InfaqResource extends Resource
{
    protected static ?string $model             = Infaq::class;
    protected static ?string $navigationIcon    = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup   = 'Keuangan';
    protected static ?string $modelLabel        = 'Penerimaan Donasi';
    protected static ?string $slug              = 'penerimaaan-donasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tgl_transaksi')
                    ->native(false)
                    ->required(),
                Forms\Components\TextInput::make('jumlah_donasi')
                    ->required()
                    ->numeric(),
                Forms\Components\TextArea::make('uraian')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sumber_dana')
                    ->required()
                    ->maxLength(255)
                    ->default('Kencleng'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('distribusi.donatur.nama')
                    ->label('Nama Donatur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_transaksi')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_donasi')
                    ->numeric()
                    ->sortable(),
                // Tables\Columns\TextColumn::make('uraian')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('sumber_dana')
                    ->searchable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('tgl_transaksi')
                    ->form([
                        DatePicker::make('dari')
                            ->native(false)
                            ->default(now()->subMonth()),
                        DatePicker::make('sampai')
                            ->native(false)
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data) {
                        $query->whereBetween('tgl_transaksi', $data);
                    }),
                ], layout: FiltersLayout::Modal)
                ->hiddenFilterIndicators()
            ->actions([
                // Hanya untuk owner
                // Tables\Actions\EditAction::make()
                //     ->iconButton(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfaqs::route('/'),
            // 'create' => Pages\CreateInfaq::route('/create'),
            // 'edit' => Pages\EditInfaq::route('/{record}/edit'),
        ];
    }
}
