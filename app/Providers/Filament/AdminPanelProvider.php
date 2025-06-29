<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Admin\Auth\Login;
use App\Filament\Pages\Admin\Profile;
use App\Http\Middleware\AdminPermission;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Livewire\Livewire;
use function App\Utils\getConfigurationData;
use function App\Utils\getImageOrPlaceholder;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->default()
            ->id('admin')
            ->authGuard('admin')
            ->path('admin')
            ->login(Login::class)
//            ->brandLogo(
//                getImageOrPlaceholder(
//                    path: getConfigurationData('web_header_logo'),
//                    storageType: 'storage',
//                    type: 'placeholder-basic'
//                )
//            )
//            ->brandLogoHeight('50px')
//            ->colors([
//                'primary' => getConfigurationData('panel_primary_color') ?? Color::Amber,
//            ])
//            ->favicon(getImageOrPlaceholder(path: getConfigurationData('web_fav_icon'),storageType: 'storage',type: 'placeholder-basic'))
            ->registration(false)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Pages\Dashboard::class,
                Profile::class,
            ])
            ->userMenuItems([
                'profile' => MenuItem::make()->url(fn (): string => Profile::getUrl())
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->breadcrumbs(false)
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
                AdminPermission::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
    public function boot():void
    {
        Livewire::addPersistentMiddleware([
            AdminPermission::class,
        ]);
    }
}
