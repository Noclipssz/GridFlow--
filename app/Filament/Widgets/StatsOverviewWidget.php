<?php

namespace App\Filament\Widgets;

use App\Models\Professor;
use App\Models\Turma;
use App\Models\Materia;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total de Professores', Professor::count())
                ->description('Professores cadastrados')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
            Stat::make('Total de Turmas', Turma::count())
                ->description('Turmas cadastradas')
                ->descriptionIcon('heroicon-m-building-library')
                ->color('info'),
            Stat::make('Total de Matérias', Materia::count())
                ->description('Matérias cadastradas')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('warning'),
        ];
    }
}
