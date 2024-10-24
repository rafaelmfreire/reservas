<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Filament\Resources\ReservationResource\RelationManagers\DatesRelationManager;
use App\Models\Reservation;
use App\Models\ReservationDate;
use App\Models\Responsible;
use App\Models\Room;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;

    public static function getModelLabel(): string
    {
        return __('Reservation');
    }

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(4)
            ->schema([
                Select::make('room_id')
                    ->label(__('Room'))
                    ->options(Room::all()->pluck('name', 'id'))
                    ->native(false)
                    ->searchable(),
                Select::make('responsible_id')
                    ->label(__('Responsible'))
                    ->options(Responsible::all()->pluck('name', 'id'))
                    ->native(false)
                    ->columnSpan(2)
                    ->searchable(),
                TextInput::make('description')
                    ->columnSpan(4)
                    ->required()
                    ->maxLength(191),
                Toggle::make('is_confirmed')->inline()->columnSpan(4),
                Repeater::make('dates')
                    ->relationship()
                    ->columnSpan(4)
                    ->schema([
                        DateTimePicker::make('start_at')
                            ->seconds(false)
                            ->rules([
                                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get, $form) {
                                    $reservation = ReservationDate::join('reservations', 'reservations.id', 'reservation_dates.reservation_id')
                                        ->where('room_id', $get('../../room_id'))
                                        ->where('start_at', '<', Carbon::parse($get('end_at'))->format('Y-m-d H:i'))
                                        ->where('end_at', '>', Carbon::parse($get('start_at'))->format('Y-m-d H:i'))
                                        ->first();

                                    if ($reservation?->count() > 0 && $reservation->id !== $form->getRecord()->id) {
                                        $fail('A sala já está reservada para esta data.');
                                    }
                                },
                            ])
                            ->required(),
                        DateTimePicker::make('end_at')
                            ->after('start_at')
                            ->seconds(false)
                            ->required(),
                    ])->addActionLabel('Adicionar nova data')
                    ->grid(4)
                    ->minItems(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->size(TextColumnSize::Large)
                    ->weight(FontWeight::Bold)
                    ->limit(65)
                    ->searchable(),
                TextColumn::make('room.name')
                    ->description(fn(Reservation $record): string => $record->room->capacity . ' pessoas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('responsible.name')
                    ->description(fn(Reservation $record): string => $record->responsible->phone)
                    ->searchable()
                    ->sortable(),
                // TextColumn::make('start_at')
                //     ->dateTime('d/m/Y H:i')
                //     ->sortable(),
                // TextColumn::make('end_at')
                //     ->dateTime('d/m/Y H:i')
                //     ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_confirmed')
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $rooms = Room::where('user_id', auth()->id())->get();
                $query->whereBelongsTo($rooms);
            })
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
            DatesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit' => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
