<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiResource extends Resource
{
    protected static ?string $model             = Transaksi::class;
    protected static ?string $navigationIcon    = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup   = 'Keuangan';
    protected static ?string $modelLabel        = 'Transaksi Keluar';
    protected static ?string $slug              = 'transaksi-keluar';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tgl_transaksi')
                    ->label('Tanggal Transaksi')
                    ->placeholder('Pilih tanggal transaksi')
                    ->native(false)
                    ->displayFormat('d F Y')
                    ->required(),
                Forms\Components\Select::make('sumber_dana')
                    ->label('Sumber Dana')
                    ->placeholder('Pilih sumber dana')
                    ->native(false)
                    ->options([
                        'Kencleng'  => 'Kencleng',
                        'Donasi'    => 'Donasi',
                    ]),
                Forms\Components\TextInput::make('jumlah')
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
                Forms\Components\Textarea::make('uraian')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tgl_transaksi')
                    ->label('Tanggal Transaksi')
                    ->date('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->prefix('Rp.')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uraian')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sumber_dana')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('created_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('updated_at')
                //     ->dateTime()
                //     ->sortable()
                //     ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index'     => Pages\ListTransaksis::route('/'),
            'create'    => Pages\CreateTransaksi::route('/create'),
            'edit'      => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
