<?php

namespace App\Filament\Widgets;

use App\Models\Infaq;
use App\Models\Scopes\AreaScope;
use App\Models\Scopes\CabangScope;
use App\Models\Scopes\WilayahScope;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatsTopDonaturOverview extends ChartWidget
{
    protected static ?string $heading = '';

    public static function canView(): bool
    {
        return Auth::user()->is_admin;
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'indexAxis' => 'y',
            'plugins'   => 
            [
                'title' => 
                [
                    'display'   => true,
                    'text'      => 'Top 10 Donatur',
                    'font'      => 
                    [
                        'size'  => 16,
                    ],
                ]
            ],
        ];
    }

    protected function getData(): array
    {
        $admin = Auth::user()?->admin;

        $query = Infaq::withoutGlobalScope(WilayahScope::class)
            ->withoutGlobalScope(CabangScope::class)
            ->withoutGlobalScope(AreaScope::class)
            ->whereNotNull('distribusi_id')
            ->join('distribusi_kenclengs', 'infaqs.distribusi_id', '=', 'distribusi_kenclengs.id')
            ->join('profiles', 'distribusi_kenclengs.donatur_id', '=', 'profiles.id')
            ->groupBy('profiles.nama')
            ->select('profiles.nama', DB::raw('SUM(infaqs.jumlah_donasi) as total_donasi'));
        
        if ( $admin->level === 'supervisor' ) {
            $query->where('distribusi_kenclengs.area_id', $admin->area_id);
        }

        if (in_array($admin->level, ['admin', 'manajer'])) {
            $query->where('distribusi_kenclengs.cabang_id', $admin->cabang_id);
        }

        if (in_array($admin->level, ['direktur', 'admin_wilayah'])) {
            $query->where('distribusi_kenclengs.wilayah_id', $admin->wilayah_id);
        }

        $data = $query->orderBy('total_donasi', 'desc')->limit(10)->get();

        return
        [
            'datasets' => [
                [
                    'axis'  => 'y',
                    'label' => 'Top 10 Donatur',
                    'data'  => $data->map(fn($data) => $data->total_donasi)->toArray(),
                    'backgroundColor'   
                            => [
                                '#36A2EB', // Original color
                                '#5AB8F0', // Original color
                                '#7FCDF5', // Original color
                                '#3B82F6', // Info color
                                '#60A5FA', // Info color
                                '#F59E0B', // Warning color
                                '#FBBF24', // Warning color
                            ],
                    'borderColor'       
                            => '#9BD0F5',
                ],
            ],
            'labels' => $data->map(fn ($data) => $data->nama)->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
