<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BatchKenclengResource\Pages;
use App\Filament\Resources\BatchKenclengResource\RelationManagers;
use App\Models\BatchKencleng;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BatchKenclengResource extends Resource
{
    protected static ?string $model = BatchKencleng::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_batch')
                    ->required()
                    ->default('Batch ke')
                    ->maxLength(255),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->disabledOn('edit')
                    ->numeric(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_batch')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Tanggal Dibuat')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->color('danger'),
                Tables\Actions\ViewAction::make()
                    ->color('success'),
                Tables\Actions\Action::make('make_pdf')
                    ->label('Download PDF')
                    ->link()
                    ->color('info')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->requiresConfirmation()
                    ->url(fn(BatchKencleng $batchKencleng): string => route('qr-pdf', $batchKencleng->id))
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListBatchKenclengs::route('/'),
            // 'create' => Pages\CreateBatchKencleng::route('/create'),
            'edit' => Pages\EditBatchKencleng::route('/{record}/edit'),
        ];
    }
}
