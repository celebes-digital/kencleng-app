<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
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

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->spa()
            ->spaUrlExceptions(fn(): array => [
                url('/dashboard/distribusi-kencleng/create'),
            ])
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->sidebarWidth(250)
            ->sidebarCollapsibleOnDesktop()
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->brandName('Kencleng Jariyah')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->passwordReset()
            ->profile(isSimple: false)
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Kencleng')
                    ->icon('heroicon-o-cube')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Distribusi')
                    ->icon('heroicon-o-truck')
                    ->collapsible(false),
                NavigationGroup::make()
                    ->label('Jadwal')
                    ->icon('heroicon-o-calendar')
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}