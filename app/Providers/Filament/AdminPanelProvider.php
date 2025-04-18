<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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
use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Filament\Admin\Resources\AcademicManagementResource;
use App\Filament\Admin\Resources\DiscountResource;
use App\Filament\Admin\Resources\ExpenseResource;
use App\Filament\Admin\Resources\FeeTypeResource;
use App\Filament\Admin\Resources\FoundationRequestResource;
use App\Filament\Admin\Resources\FoundationResource;
use App\Filament\Admin\Resources\IncomeResource;
use App\Filament\Admin\Resources\PaymentResource;
use App\Filament\Admin\Resources\StudentResource;
use App\Filament\Admin\Resources\UserResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Illuminate\Support\Facades\Auth;


class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->brandName('Web-Spp')

            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Gray,
                'info' => Color::Blue,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    // Dashboard
                    NavigationGroup::make()
                        ->items([
                            NavigationItem::make('dashboard')
                                ->label(fn(): string => __('filament-panels::pages/dashboard.title'))
                                ->url(fn(): string => Dashboard::getUrl())
                                ->icon('heroicon-o-home')
                                ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard'))
                                ->visible(fn() => Auth::user()?->can('view-dashboard')),
                        ]),

                    // Akademik
                    NavigationGroup::make('Akademik')
                        ->items([
                            ...(Auth::user()?->can('manage-academic') ? AcademicManagementResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-students') ? StudentResource::getNavigationItems() : []),
                        ]),

                    // Keuangan
                    NavigationGroup::make('Keuangan')
                        ->items([
                            ...(Auth::user()?->can('manage-payments') ? PaymentResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-fee-types') ? FeeTypeResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-discounts') ? DiscountResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-incomes') ? IncomeResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-expenses') ? ExpenseResource::getNavigationItems() : []),
                        ]),

                    // Yayasan
                    NavigationGroup::make('Yayasan')
                        ->items([
                            ...(Auth::user()?->can('manage-foundations') ? FoundationRequestResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-foundations') ? FoundationResource::getNavigationItems() : []),
                            ...(Auth::user()?->can('manage-users') ? UserResource::getNavigationItems() : []),
                        ]),

                    // Akses
                    NavigationGroup::make('Akses')
                        ->items([
                            NavigationItem::make('Roles')
                                ->icon('heroicon-o-user-group')
                                ->url(fn(): string => route('filament.admin.resources.roles.index'))
                                ->isActiveWhen(fn() => request()->routeIs([
                                    'filament.admin.resources.roles.index',
                                    'filament.admin.resources.roles.create',
                                    'filament.admin.resources.roles.view',
                                    'filament.admin.resources.roles.edit',
                                ]))
                                ->visible(fn() => Auth::user()?->can('manage-roles')),

                            NavigationItem::make('Permission')
                                ->icon('heroicon-o-lock-closed')
                                ->url(fn(): string => route('filament.admin.resources.permissions.index'))
                                ->isActiveWhen(fn() => request()->routeIs([
                                    'filament.admin.resources.permissions.index',
                                    'filament.admin.resources.permissions.create',
                                    'filament.admin.resources.permissions.view',
                                    'filament.admin.resources.permissions.edit',
                                ]))
                                ->visible(fn() => Auth::user()?->can('manage-permissions')),
                        ]),
                ]);
            });
    }
}  
