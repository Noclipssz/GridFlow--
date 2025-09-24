<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Pages;
use Filament\Widgets;
use App\Filament\Resources\MateriaResource;
use App\Filament\Resources\ProfessorResource;
use App\Filament\Resources\TurmaResource;
use App\Filament\Pages\GenerateGrade;
use App\Filament\Widgets\StatsOverviewWidget;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->maxContentWidth('full')
            ->login()
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->resources([
                MateriaResource::class,
                ProfessorResource::class,
                TurmaResource::class,
            ])
            ->pages([
                Pages\Dashboard::class,
                GenerateGrade::class,
            ])
            ->widgets([
                StatsOverviewWidget::class,
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ]);
    }
}
