<?php

namespace App\Filament\Resources\ProfileResource\Pages;

use App\Filament\Resources\ProfileResource;
use App\Models\Profile;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListProfiles extends ListRecords
{
    protected static string $resource = ProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All'           => Tab::make(),
            'Distributor'   => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('group', 'distributor'))
                ->badge(fn () => Profile::query()->where('group', 'distributor')->count()),
            'Kolektor'      => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('group', 'kolektor'))
                ->badge(fn () => Profile::query()->where('group', 'kolektor')->count()),
            'Donatur'      => Tab::make()
                ->modifyQueryUsing(fn ($query) => $query->where('group', 'donatur'))
                ->badge(fn () => Profile::query()->where('group', 'donatur')->count()),
        ];
    }
}
