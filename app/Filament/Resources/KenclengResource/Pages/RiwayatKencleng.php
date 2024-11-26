<?php

namespace App\Filament\Resources\KenclengResource\Pages;

use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;

use App\Filament\Resources\KenclengResource;
use App\Models\DistribusiKencleng;
use Filament\Resources\Pages\ViewRecord;
use JaOcero\ActivityTimeline\Components\ActivityDate;
use JaOcero\ActivityTimeline\Components\ActivityDescription;
use JaOcero\ActivityTimeline\Components\ActivityIcon;
use JaOcero\ActivityTimeline\Components\ActivitySection;
use JaOcero\ActivityTimeline\Components\ActivityTitle;

class RiwayatKencleng extends ViewRecord
{
    protected static string $resource = KenclengResource::class;
    protected $distribusiKencleng;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state([
                'activities' => DistribusiKencleng::query()
                    ->where('kencleng_id', $this->record->id)
                    ->orderBy('tgl_distribusi', 'desc')
                    ->get()
                    ->flatMap(function ($distribusi) {
                        $activities = [];

                        if ( $distribusi->tgl_pengambilan ) 
                        {
                            $activities[] = 
                            [
                                'title'         => 'Pengembalian dari donatur ' . $distribusi->donatur->nama,
                                'description'   => 'Dikolektor ' 
                                                    . ($distribusi->kolektor->nama ?? 'sendiri oleh donatur') 
                                                    . ' sejumlah ' 
                                                    . $distribusi->jumlah,
                                'created_at'    => $distribusi->tgl_pengambilan,
                                'status'        => 'pengembalian',
                            ];
                        }

                        if ( $distribusi->tgl_distribusi ) 
                        {
                            $activities[] = [
                                'title'         => 'Distribusi ke donatur ' . $distribusi->donatur->nama,
                                'description'   => $distribusi->distributor 
                                                    ? 'Didistribusikan oleh ' . $distribusi->distributor->nama 
                                                    : 'Diambil sendiri oleh donatur',
                                'created_at'    => $distribusi->tgl_distribusi,
                                'status'        => 'distribusi',
                            ];
                        }

                        return $activities;
                    })
                    ->toArray(),
            ])
            ->schema([
                ActivitySection::make('activities')
                    ->label('Aktivitas Kencleng')
                    ->description('Riwayat aktivitas distribusi kencleng.')
                    ->schema(
                    [
                        ActivityTitle::make('title')
                            ->placeholder('No title is set')
                            ->allowHtml(),
                        ActivityDescription::make('description')
                            ->placeholder('No description is set')
                            ->allowHtml(),
                        ActivityDate::make('created_at')
                            ->date('Y', 'Asia/Makassar')
                            ->placeholder('No date is set.'),
                        ActivityIcon::make('status')
                            ->icon(fn(string | null $state): string | null => match ($state) {
                                'distribusi' => 'heroicon-m-rectangle-group',
                                'pengembalian' => 'heroicon-m-sparkles',
                                default => null,
                            })
                            ->color(fn(string | null $state): string | null => match ($state) {
                                'distribusi' => 'info',
                                'pengembalian' => 'success',
                                default => 'gray',
                            }),
                    ])
                    ->emptyStateHeading('Belum ada riwayat kencleng.')
                    // ->emptyStateDescription('Tidak ada riwayat yang tersedia.')
                    ->emptyStateIcon('heroicon-o-eye-slash')
                    ->showItemsCount(5) // Show up to 2 items
                    ->showItemsLabel('View Old') // Show "View Old" as link label
                    ->showItemsIcon('heroicon-m-chevron-down') // Show button icon
                    ->showItemsColor('gray') // Show button color and it supports all colors
                    // ->aside(true)
                    ->headingVisible(true) // make heading visible or not
                    ->extraAttributes(['class' => 'my-new-class']) // add extra class
            ])
            ->columns(1);
    }
}
