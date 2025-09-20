<?php

namespace App\Filament\Resources;

use App\Models\Professor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProfessorResource extends Resource
{
    protected static ?string $model = Professor::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Academico';
    protected static ?int $navigationSort = 20;
    protected static ?string $label = 'Professor';
    protected static ?string $pluralLabel = 'Professores';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')->required()->maxLength(255),
            Forms\Components\TextInput::make('cpf')->mask('999.999.999-99')->required()->unique(ignoreRecord: true),
            Forms\Components\Select::make('materia_id')->relationship('materia', 'nome')->required()->label('Matéria'),
            Forms\Components\TextInput::make('senha')
                ->password()
                ->dehydrateStateUsing(fn ($state) => $state ?: null)
                ->dehydrated(fn ($state) => filled($state))
                ->label('Senha (deixe vazio para manter)'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('cpf'),
                Tables\Columns\TextColumn::make('materia.nome')->label('Matéria')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => ProfessorResource\Pages\ManageProfessors::route('/'),
        ];
    }
}

namespace App\Filament\Resources\ProfessorResource\Pages;

use App\Filament\Resources\ProfessorResource;
use Filament\Resources\Pages\ManageRecords;

class ManageProfessors extends ManageRecords
{
    protected static string $resource = ProfessorResource::class;
}

