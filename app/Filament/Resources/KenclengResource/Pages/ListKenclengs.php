<?php

namespace App\Filament\Resources\KenclengResource\Pages;

use App\Filament\Resources\KenclengResource;
use App\Enums\StatusKencleng;

use App\Models\Kencleng;

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
                                        
            StatusKencleng::TERSEDIA->getLabel()
                => Tab::make()
                    ->badgeColor(StatusKencleng::TERSEDIA->getColor())
                    ->modifyQueryUsing(fn () => Kencleng::query()->where('status', 0))
                    ->badge(fn () => Kencleng::query()->where('status', 0)->count()),

            StatusKencleng::SEDANGDISTRIBUSI->getLabel()
                => Tab::make()
                    ->badgeColor(StatusKencleng::SEDANGDISTRIBUSI->getColor())
                    ->modifyQueryUsing(fn () => Kencleng::query()->where('status', 1))
                    ->badge(fn () => Kencleng::query()->where('status', 1)->count()),
        ];
    }
}
