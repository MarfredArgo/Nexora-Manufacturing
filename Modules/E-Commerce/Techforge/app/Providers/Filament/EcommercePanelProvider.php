<?php

namespace Modules\Ecommerce\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Ecommerce\Http\Middleware\ResolveEcommerceAdminClient;

class EcommercePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('ecommerce')
            ->path('ecommerce-admin')
            ->login()
            ->authGuard('ecommerce_admin')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandLogo(fn () => view('ecommerce::filament.brand-logo'))
            ->brandLogoHeight('2rem')
            ->brandName(function () {
                $admin = auth('ecommerce_admin')->user();
                $company = $admin?->getCompany();
                return $company?->company_name ?? 'E-Commerce Admin';
            })
            // The standalone project exposed raw component tables. The ERP
            // panel deliberately exposes only client-safe operational areas.
            ->resources([
                \Modules\Ecommerce\Filament\Resources\StorefrontListings\StorefrontListingResource::class,
                \Modules\Ecommerce\Filament\Resources\Orders\OrderResource::class,
            ])
            ->discoverPages(in: __DIR__ . '/../../Filament/Pages', for: 'Modules\\Ecommerce\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: __DIR__ . '/../../Filament/Widgets', for: 'Modules\\Ecommerce\\Filament\\Widgets')
            ->widgets([
                \Modules\Ecommerce\Filament\Widgets\StatsOverview::class,
                \Modules\Ecommerce\Filament\Widgets\LatestOrders::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                ResolveEcommerceAdminClient::class,
            ]);
    }
}
