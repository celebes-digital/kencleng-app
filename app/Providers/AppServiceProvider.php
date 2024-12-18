<?php

// GREAT CODER BUT BAD SOFWARE ENGINEER
namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentColor;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        FilamentView::registerRenderHook('panels::body.end', fn(): string => Blade::render("@vite('resources/js/app.js')"));
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn(): string => Blade::render('filament.components.jadwal-navigation-topbar'),
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_BEFORE,
            fn(): string => Blade::render('filament.components.user-info'),
        );

        FilamentIcon::register([
            'panels::sidebar.collapse-button' => view('icons.collapse'),
            'panels::sidebar.expand-button' => view('icons.expand'),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentColor::register([
            'danger'    => Color::Red,
            'gray'      => Color::Zinc,
            'info'      => Color::Blue,
            'primary'   => Color::Teal,
            'success'   => Color::Green,
            'warning'   => Color::Amber,
        ]);

        FilamentAsset::register([
            Js::make('qrcode-scanner', __DIR__ . '/../../resources/js/html5-qrcode.min.js'),
        ]);
    }
}
