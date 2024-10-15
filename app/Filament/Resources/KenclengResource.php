<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KenclengResource\Pages;
use App\Filament\Resources\KenclengResource\RelationManagers;
use App\Models\Kencleng;
use App\Models\Profile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class KenclengResource extends Resource
{
    protected static ?string $model = Kencleng::class;

    protected static ?string $modelLabel = 'Kencleng';
    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('no_kencleng')
                    ->required()
                    ->maxLength(10),
                Forms\Components\FileUpload::make('qr_image')
                    ->image()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no_kencleng')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('qr_image')
                    ->label('QR Code')
                    ->size(50, 50),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('download')
                    ->button()
                    ->action(function (Kencleng $kencleng) {
                        $filePath = $kencleng->qr_image;
                        if (Storage::disk('public')->exists($filePath)) {
                            return response()->download(Storage::disk('public')->path($filePath), 'qr-code.png');
                        } 
                        Notification::make()
                            ->title('Error')
                            ->body('File not found.')
                            ->send();
                    })
                    ->successNotification(
                        Notification::make()
                            ->title('QR Berhasil Diunduh')
                            ->body('QR Code telah berhasil diunduh.')
                    ),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListKenclengs::route('/'),
            'create' => Pages\CreateKencleng::route('/create'),
            'edit' => Pages\EditKencleng::route('/{record}/edit'),
        ];
    }
}
