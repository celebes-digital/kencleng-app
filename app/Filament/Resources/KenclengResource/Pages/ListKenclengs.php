<?php

namespace App\Filament\Resources\KenclengResource\Pages;

use App\Enums\StatusKencleng;
use App\Filament\Resources\KenclengResource;
use App\Models\Kencleng;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListKenclengs extends ListRecords
{
    protected static string $resource = KenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All'
                 => Tab::make()
                    ->badgeColor('gray'),
                                        
            'Aqtif'  
                => Tab::make()
                    ->badgeColor(StatusKencleng::AQTIF->getColor())
                    ->modifyQueryUsing(
                        fn () => Kencleng::query()->where('status', 0)
                    )
                    ->badge(
                        fn () => Kencleng::query()->where('status', 0)->count()
                    ),

            'Distributor'
                => Tab::make()
                    ->badgeColor(StatusKencleng::DISTRIBUTOR->getColor())
                    ->modifyQueryUsing(
                        fn () => Kencleng::query()->where('status', 1)
                    )
                    ->badge(
                        fn () => Kencleng::query()->where('status', 1)->count()
                    ),
        ];
    }
}
