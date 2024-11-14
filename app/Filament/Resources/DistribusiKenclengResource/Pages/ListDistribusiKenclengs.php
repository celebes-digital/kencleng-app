<?php

namespace App\Filament\Resources\DistribusiKenclengResource\Pages;

use App\Enums\StatusDistribusi;
use App\Filament\Resources\DistribusiKenclengResource;
use App\Models\DistribusiKencleng;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListDistribusiKenclengs extends ListRecords
{
    protected static string $resource = DistribusiKenclengResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All'           => Tab::make()
                                    ->badgeColor('primary')
                                    ->badge(fn() => DistribusiKencleng::query()->count()),
            'Distribusi'    => Tab::make()
                                    ->badgeColor(StatusDistribusi::DISTRIBUSI->getColor())
                                    ->modifyQueryUsing(
                                        fn($query) => $query->where('status', 'distribusi')
                                    )
                                    ->badge(
                                        fn() => DistribusiKencleng::query()->where('status', 'distribusi')->count()
                                    ),
            'Sedang Diisi'  => Tab::make()
                                    ->badgeColor(StatusDistribusi::DIISI->getColor())
                                    ->modifyQueryUsing(
                                        fn($query) => $query->where('status', 'diisi')
                                    )
                                    ->badge(
                                        fn() => DistribusiKencleng::query()->where('status', 'diisi')->count()
                                    ),
            'Kembali'       => Tab::make()
                                    ->badgeColor(StatusDistribusi::KEMBALI->getColor())
                                    ->modifyQueryUsing(
                                        fn($query) => $query->where('status', 'kembali')
                                    )
                                    ->badge(
                                        fn() => DistribusiKencleng::query()->where('status', 'kembali')->count()
                                    ),
            'Diterima'      => Tab::make()
                                    ->badgeColor(StatusDistribusi::DITERIMA->getColor())
                                    ->modifyQueryUsing(
                                        fn($query) => $query->where('status', 'diterima')
                                    )
                                    ->badge(
                                        fn() => DistribusiKencleng::query()->where('status', 'diterima')->count()
                                    ),
        ];
    }
}
