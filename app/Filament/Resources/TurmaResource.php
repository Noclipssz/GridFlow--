<?php

namespace App\Filament\Resources;

use App\Models\Turma;
use App\Services\GradeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class TurmaResource extends Resource
{
    protected static ?string $model = Turma::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Academico';
    protected static ?int $navigationSort = 30;
    protected static ?string $label = 'Turma';
    protected static ?string $pluralLabel = 'Turmas';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')->required()->maxLength(255),
            Forms\Components\Select::make('periodo')
                ->options(['manha' => 'Manhã', 'tarde' => 'Tarde', 'noite' => 'Noite'])
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nome')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('periodo')->colors([
                    'primary',
                ])->label('Período'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('liberar')
                    ->label('Liberar')
                    ->requiresConfirmation()
                    ->visible(function (Turma $record) {
                        $grid = (new GradeService())->getTurmaGrid($record, (string) $record->periodo);
                        if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];
                        for ($a = 0; $a < 5; $a++) {
                            $row = $grid[$a] ?? [];
                            for ($d = 0; $d < 5; $d++) {
                                $cell = $row[$d] ?? null;
                                if (is_array($cell) && (!empty($cell['professor_id']) || !empty($cell['materia_id']))) {
                                    return true;
                                }
                            }
                        }
                        return false;
                    })
                    ->action(function (Turma $record) {
                        (new GradeService())->clearTurma($record, $record->periodo);
                        Notification::make()->title('Turma liberada')->success()->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => TurmaResource\Pages\ManageTurmas::route('/'),
        ];
    }
}

namespace App\Filament\Resources\TurmaResource\Pages;

use App\Filament\Resources\TurmaResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Actions;

class ManageTurmas extends ManageRecords
{
    protected static string $resource = TurmaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
