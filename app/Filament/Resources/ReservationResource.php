<?php

namespace App\Filament\Resources;

use Filament\Support\Enums\Alignment;
use App\Filament\Resources\ReservationResource\Pages;
use App\Filament\Resources\ReservationResource\RelationManagers;
use App\Filament\Resources\ReservationResource\RelationManagers\DatesRelationManager;
use App\Mail\ReservationConfirmed;
use App\Models\Reservation;
use App\Models\ReservationDate;
use App\Models\Room;
use Carbon\Carbon;
use Closure;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        $rooms = Auth::user()->is_admin ? Room::pluck('name', 'id') : Room::where('user_id', Auth::user()->id)->pluck('name', 'id');
        return $form
            ->columns(4)
            ->schema([
                Section::make('Responsável')
                    ->description('Dados do responsável pela reserva')
                    ->columns(3)
                    ->schema([
                        TextInput::make('responsible')
                            ->label('Responsável pela Reserva')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('matriculation')
                            ->maxLength(191),
                        TextInput::make('sector')
                            ->label('Setor do Responsável')
                            ->maxLength(191),
                        TextInput::make('phone')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('email')
                            ->type('email')
                            ->maxLength(191),
                        Select::make('category')
                            ->options([
                                'docente' => 'Docente',
                                'tecnico' => 'Técnico Administrativo',
                                'aluno' => 'Discente',
                                'outro' => 'Outro',
                            ])
                            ->native(false)
                            ->required(),

                    ]),
                Section::make('Evento')
                    ->description('Dados do evento')
                    ->columns(4)
                    ->schema([
                        Select::make('room_id')
                            ->required()
                            ->label(__('Room'))
                            ->options($rooms)
                            ->native(false),
                        TextInput::make('description')
                            ->columnSpan(3)
                            ->required()
                            ->maxLength(191),
                        Toggle::make('is_confirmed')->inline()->columnSpan(4),
                        Repeater::make('dates')
                            ->relationship()
                            ->columnSpan(4)
                            ->schema([
                                DateTimePicker::make('start_at')
                                    ->minDate(now()->format('Y-m-d'))
                                    ->seconds(false)
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get, $form) {
                                            $reservation = ReservationDate::join('reservations', 'reservations.id', 'reservation_dates.reservation_id')
                                                ->where('room_id', $get('../../room_id'))
                                                ->where('start_at', '<', Carbon::parse($get('end_at'))->format('Y-m-d H:i'))
                                                ->where('end_at', '>', Carbon::parse($get('start_at'))->format('Y-m-d H:i'))
                                                ->first();

                                            if ($reservation?->count() > 0 && $reservation->id !== $form->getRecord()?->id) {
                                                $fail('A sala já está reservada para esta data.');
                                            }

                                            $start_time = Carbon::parse($get('start_at'))->format('H');
                                            if ($start_time < 7) {
                                                $fail('O horário das reservas deve ser entre 7h e 22h');
                                            }
                                        },
                                    ])
                                    ->required(),
                                DateTimePicker::make('end_at')
                                    ->after('start_at')
                                    ->seconds(false)
                                    ->rules([
                                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get, $form) {
                                            $start_day = Carbon::parse($get('start_at'))->format('Y-m-d');
                                            $end_day = Carbon::parse($get('end_at'))->format('Y-m-d');
                                            if ($start_day != $end_day) {
                                                $fail('Os dias Inicial e Final devem ser os mesmos. Para reservar a sala para mais dias, clique no botão "Adicionar nova data"');
                                            }

                                            $end_hour = Carbon::parse($get('end_at'))->format('H');
                                            $end_minute = Carbon::parse($get('end_at'))->format('i');
                                            if ($end_hour > 22 || ($end_hour == 22 && $end_minute > 0)) {
                                                $fail('O horário das reservas deve ser entre 7h e 22h');
                                            }
                                        },
                                    ])
                                    ->required(),
                            ])
                            // ->addActionAlignment(Alignment::Start)
                            ->addActionLabel('Adicionar nova data')
                            ->grid(4)
                            ->minItems(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        $rooms = Auth::user()->is_admin ? Room::pluck('name', 'id') : Room::where('user_id', Auth::user()->id)->pluck('name', 'id');
        return $table
            ->columns([
                TextColumn::make('description')
                    ->size(TextColumnSize::Large)
                    ->weight(FontWeight::Bold)
                    ->limit(65)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('room.name')
                    ->description(fn(Reservation $record): string => $record->room->capacity . ' pessoas')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('responsible')
                    ->description(fn(Reservation $record): string => $record->phone)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('dates.start_at')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->date('d/m/Y'),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ToggleColumn::make('is_confirmed')
                    ->afterStateUpdated(function ($record, $state) {

                        if ($state) {
                            $room = $record->room->name;
                            $responsible = $record->responsible;
                            $sector = $record->room->user->name;
                            $body = "Olá $responsible, sua reserva da sala $room foi confirmada por $sector.";
                            $dates = $record->dates;
                            $mailData = [
                                'title' => 'Sua reserva da sala ' . $room . ' foi confirmada.',
                                'body' => $body,
                                'description' => $record->description,
                                'dates' => $dates
                            ];

                            try {
                                Mail::to($record->email)->send(new ReservationConfirmed($mailData));

                                Notification::make()
                                    ->title('Email de confirmação enviado para o solicitante.')
                                    ->success()
                                    ->send();
                            } catch (\Throwable $th) {
                                Notification::make()
                                    ->title('Houve um erro no envio do email de confirmação.')
                                    ->danger()
                                    ->send();
                            }
                        }
                    })
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $rooms = auth()->user()->is_admin ? Room::all() : Room::where('user_id', auth()->id())->get();
                $query->whereBelongsTo($rooms);
            })
            ->filters([
                Filter::make('is_confirmed')
                    ->label('Apenas Pendentes de Aprovação')
                    ->query(fn(Builder $query): Builder => $query->where('is_confirmed', false)),
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->options([
                        'docente' => 'Docente',
                        'tecnico' => 'Técnico Administrativo',
                        'aluno' => 'Discente',
                        'outro' => 'Outro',
                    ]),
                SelectFilter::make('room_id')
                    ->label('Sala')
                    ->options($rooms),
            ])
            ->filtersTriggerAction(
                fn(Action $action) => $action
                    ->button()
                    ->label("Filtros")
            )
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
