<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Saade\FilamentFullCalendar\FilamentFullCalendarPlugin;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('')
            ->path('')
            ->login()
            ->spa()
            ->spaUrlExceptions(fn(): array => [
                url('/dashboard/distribusi-kencleng/create'),
            ])
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->favicon(asset('logo-aqtif.png'))
            ->sidebarWidth(250)
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->brandName('Kencleng Jariyah')
            ->defaultThemeMode(ThemeMode::Light)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([])
            ->passwordReset()
            ->profile(EditProfile::class, isSimple: false)
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Setting')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Kencleng')
                    ->icon('heroicon-o-cube')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Distribusi')
                    ->icon('heroicon-o-truck')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Koleksi')
                    ->icon('heroicon-o-swatch')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Keuangan')
                    ->icon('heroicon-o-currency-dollar')
                    ->collapsible(false),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentFullCalendarPlugin::make()
                    ->selectable(false)
                    ->editable(false)
                    ->timezone(config('app.timezone'))
                    ->locale(config('app.locale'))
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
