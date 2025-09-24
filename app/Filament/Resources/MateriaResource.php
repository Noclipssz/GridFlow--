<?php

namespace App\Filament\Resources;

use App\Models\Materia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MateriaResource extends Resource
{
    protected static ?string $model = Materia::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Academico';
    protected static ?int $navigationSort = 10;
    protected static ?string $label = 'Matéria';
    protected static ?string $pluralLabel = 'Matérias';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nome')->required()->maxLength(255),
            Forms\Components\TextInput::make('quant_aulas')->numeric()->minValue(0)->maxValue(10)->required()->label('Aulas/semana'),
            Forms\Components\Toggle::make('check')->label('Ativa'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('nome')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('quant_aulas')->label('Aulas')->sortable(),
                Tables\Columns\IconColumn::make('check')->boolean()->label('Ativa'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => MateriaResource\Pages\ManageMaterias::route('/'),
        ];
    }
}

namespace App\Filament\Resources\MateriaResource\Pages;

use App\Filament\Resources\MateriaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMaterias extends ManageRecords
{
    protected static string $resource = MateriaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

