<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfaqResource\Pages;
use App\Filament\Resources\InfaqResource\RelationManagers;
use App\Models\Infaq;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
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
                Forms\Components\Select::make('nama_donatur')
                    ->label('Nama Donatur')
                    ->placeholder('Pilih nama donatur')
                    ->native(false)
                    ->searchable()
                    ->options(function () {
                        $options = Profile::all()->pluck('nama', 'nama')->toArray();

                        $options = [
                            'Hamba Allah' => 'Hamba Allah',
                        ] + $options;

                        return $options;
                    }),
                Forms\Components\DatePicker::make('tgl_transaksi')
                    ->label('Tanggal Diterima')
                    ->native(false)
                    ->placeholder('Tentukan tanggal diterima')
                    ->displayFormat('d F Y')
                    ->default(now())
                    ->prefixIcon('heroicon-o-calendar')
                    ->required(),
                Forms\Components\Select::make('sumber_dana')
                    ->label('Sumber Dana')
                    ->placeholder('Pilih sumber dana')
                    ->native(false)
                    ->options([
                        'Kencleng'  => 'Kencleng',
                        'Donasi'    => 'Donasi',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('jumlah_donasi')
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
                Forms\Components\ToggleButtons::make('metode_donasi')
                    ->label('Metode Donasi')
                    ->default('Tunai')
                    ->options([
                        'Tunai'     => 'Tunai',
                        'Transfer'  => 'Transfer',
                    ])
                    ->icons([
                        'Tunai'     => 'heroicon-o-banknotes',
                        'Transfer'  => 'heroicon-o-credit-card',
                    ])
                    ->colors([
                        'Tunai'     => 'warning',
                        'Transfer'  => 'warning',
                    ])
                    ->inline()
                    ->required(),
                Forms\Components\TextArea::make('uraian')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ])
            ->columns([
                'sm' => 2,
                'md' => 2,
                'lg' => 3,
                'xl' => 3,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_donatur')
                    ->label('Nama Donatur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_transaksi')
                    ->label('Tanggal Diterima')
                    ->dateTime('d F Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_donasi')
                    ->numeric()
                    ->prefix('Rp')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_donasi')
                    ->label('Metode Donasi')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfaqs::route('/'),
            'create' => Pages\CreateInfaq::route('/create'),
            // 'edit' => Pages\EditInfaq::route('/{record}/edit'),
        ];
    }
}
