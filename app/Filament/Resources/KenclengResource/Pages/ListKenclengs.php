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
            'All'               => Tab::make()
                                        ->badgeColor('gray'),
                                        
            'Belum Distribusi'  => Tab::make()
                                        ->badgeColor(StatusKencleng::BelumDistribusi->getColor())
                                        ->modifyQueryUsing(
                                            fn () => Kencleng::query()->where('status', 0)
                                        )
                                        ->badge(
                                            fn () => Kencleng::query()->where('status', 0)->count()
                                        ),

            'Sedang Diisi'      => Tab::make()
                                        ->badgeColor(StatusKencleng::DalamDistribusi->getColor())
                                        ->modifyQueryUsing(
                                            fn () => Kencleng::query()->where('status', 1)
                                        )
                                        ->badge(
                                            fn () => Kencleng::query()->where('status', 1)->count()
                                        ),

            'Tersedia'          => Tab::make()
                                        ->badgeColor(StatusKencleng::SelesaiDistribusi->getColor())
                                        ->modifyQueryUsing(
                                            fn () => Kencleng::query()->where('status', 2)
                                        )
                                        ->badge(
                                            fn () => Kencleng::query()->where('status', 2)->count()
                                        ),
        ];
    }
}
