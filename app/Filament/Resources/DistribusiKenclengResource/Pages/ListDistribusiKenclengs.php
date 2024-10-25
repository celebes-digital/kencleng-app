<?php

namespace App\Filament\Resources\DistribusiKenclengResource\Pages;

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
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All'           => Tab::make()
                ->badgeColor('info')
                ->badge(fn() => DistribusiKencleng::query()->count()),
            'Konfirmasi'    => Tab::make()
                ->badgeColor('warning')
                ->modifyQueryUsing(fn($query) => $query->whereNotNull('tgl_pengambilan')->where('diterima', 0))
                ->badge(fn() => DistribusiKencleng::query()->whereNotNull('tgl_pengambilan')->where('diterima', 0)->count()),
            'Diterima'    => Tab::make()
                ->badgeColor('primary')
                ->modifyQueryUsing(fn($query) => $query->where('diterima', 1))
                ->badge(fn() => DistribusiKencleng::query()->where('diterima', 1)->count()),
        ];
    }
}
