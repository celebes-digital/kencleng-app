<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DistribusiKenclengResource;
use App\Models\DistribusiKencleng;
use Filament\Widgets\Widget;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class JadwalKoleksiCalenderWidget extends FullCalendarWidget
{
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function config(): array
    {
        return [
            'firstDay' => 1,
            'headerToolbar' => [
                'left' => 'dayGridWeek',
                'center' => 'title',
                'right' => 'prev,next today',
            ],
        ];
    }

    public function fetchEvents(array $info): array
    {
        return DistribusiKencleng::query()
            ->whereNotNull('tgl_distribusi')
            ->whereNull('tgl_pengambilan')
            ->get()
            ->map(function ($distribusiKencleng) {
                return [
                    'title' => 
                        $distribusiKencleng->kencleng->no_kencleng 
                                . ($distribusiKencleng->donatur ? ' - ' 
                                . $distribusiKencleng->donatur->nama : ''),
                    'start' => 
                        \Carbon\Carbon::parse($distribusiKencleng->tgl_distribusi)
                                        ->subDays(5)
                                        ->addDays(30),
                    'end' => 
                        \Carbon\Carbon::parse($distribusiKencleng->tgl_distribusi)
                                        ->addDays(30),
                    'url' => 'https://www.google.com/maps/search/?api=1&query=' 
                                . $distribusiKencleng->geo_lat 
                                . ',' 
                                . $distribusiKencleng->geo_long,
                ];
            })
            ->toArray();
    }

    protected function headerActions(): array
    {
        return [
            // 
        ];
    }

    public function eventDidMount(): string
    {
        return <<<JS
        function({ event, timeText, isStart, isEnd, isMirror, isPast, isFuture, isToday, el, view }){
            el.setAttribute("x-tooltip", "tooltip");
            el.setAttribute("x-data", "{ tooltip: '"+event.title+"' }");
        }
    JS;
    }
}
