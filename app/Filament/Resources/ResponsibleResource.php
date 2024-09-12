<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ResponsibleResource\Pages;
use App\Filament\Resources\ResponsibleResource\RelationManagers;
use App\Models\Responsible;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResponsibleResource extends Resource
{
    protected static ?string $model = Responsible::class;

    public static function getModelLabel(): string
    {
        return __('Responsible');
    }
    public static function getPluralModelLabel(): string
    {
        return __('Responsibles');
    }

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(191),
                Forms\Components\TextInput::make('matriculation')
                    ->required()
                    ->maxLength(191),
                Radio::make('category')
                    ->options([
                        'docente' => 'Docente',
                        'tecnico' => 'Técnico Administrativo',
                    ])
                    ->inline()
                    ->inlineLabel(false)
                    ->filled()
                    ->required(),
                Forms\Components\TextInput::make('sector')
                    ->required()
                    ->maxLength(191),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->size(TextColumnSize::Large)
                    ->weight(FontWeight::Bold)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Contatos')
                    ->description(fn(Responsible $record): string => $record->phone)
                    ->copyable()
                    ->searchable(),
                // Tables\Columns\TextColumn::make('email')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('matriculation')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'docente' => 'info',
                        'tecnico' => 'warning'
                    })
                    ->searchable(),
                // SelectFilter::make('category')
                //     ->options([
                //         'docente' => 'Docente',
                //         'tecnico' => 'Técnico'
                //     ]),
                Tables\Columns\TextColumn::make('sector')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResponsibles::route('/'),
            'create' => Pages\CreateResponsible::route('/create'),
            'edit' => Pages\EditResponsible::route('/{record}/edit'),
        ];
    }
}
