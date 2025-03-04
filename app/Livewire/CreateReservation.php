<?php

namespace App\Livewire;

use App\Mail\ReservationConfirmed;
use App\Models\Reservation;
use App\Models\ReservationDate;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class CreateReservation extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];
    public Reservation $reservation;

    public function mount($sector): void
    {
        $sectorId = User::where('slug', $sector)->first()->id;
        $this->form->fill(
            [
                'user_id' => $sectorId,
                'dates' => [
                    'start_at' => '',
                ]
            ]
        );
    }

    public function form(Form $form): Form
    {

        return $form
            ->columns(4)
            ->schema([
                Section::make('Responsável')
                    ->description('Dados do Responsável pela Reserva')
                    ->columns(3)
                    ->schema([
                        TextInput::make('responsible')
                            ->label('Responsável pela Reserva')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('matriculation')
                            ->label('Matrícula')
                            ->maxLength(191),
                        TextInput::make('sector')
                            ->label('Setor do Responsável')
                            ->maxLength(191),
                        TextInput::make('phone')
                            ->label('Telefone')
                            ->required()
                            ->maxLength(191),
                        TextInput::make('email')
                            ->type('email')
                            ->required()
                            ->maxLength(191),
                        Select::make('category')
                            ->label('Categoria')
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
                    ->description('Dados do Evento')
                    ->columns(4)
                    ->schema([
                        Hidden::make('user_id'),
                        // ->label('Setor')
                        // ->options(User::where('is_admin', false)->pluck('name', 'id'))
                        // ->native(false),
                        Select::make('room_id')
                            ->label(__('Room'))
                            ->helperText(new HtmlString('<span style="color: #6666dd; font-size: 12px">Selecione o setor para carregar as salas</span>'))
                            ->options(function (Get $get): array {
                                return Room::where('user_id', $get('user_id'))->pluck('name', 'id')->all();
                            })
                            ->native(false),
                        TextInput::make('description')
                            ->columnSpan(3)
                            ->label('Descrição do evento')
                            ->required()
                            ->maxLength(191),
                        Repeater::make('dates')
                            ->label('Datas')
                            ->relationship()
                            ->columnSpan(4)
                            ->schema([
                                DateTimePicker::make('start_at')
                                    ->minDate(now()->format('Y-m-d'))
                                    ->label('Data Inicial')
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
                                    ->label('Data Final')
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
                            ->addActionLabel('Adicionar nova data')
                            ->grid(4)
                            ->minItems(1),

                    ])
            ])
            ->statePath('data')
            ->model(Reservation::class);
    }

    public function create(): void
    {
        $reservation = Reservation::create($this->form->getState());

        $this->form->model($reservation)->saveRelationships();

        $this->redirect(route('search', ['sector' => $reservation->room->user->slug]));


        try {
            $roomName = $reservation->room->name;
            $body = "Olá $reservation->responsible, sua reserva da sala $roomName foi enviada para aprovação.";
            $mailData = [
                'title' => 'Sua reserva da sala ' . $reservation->room->name . ' foi enviada e aguarda confirmação do setor responsável.',
                'body' => $body,
                'subject' => 'Reserva enviada para aprovação.',
                'description' => $reservation->description,
                'dates' => $reservation->dates
            ];
            Mail::to($reservation->email)->send(new ReservationConfirmed($mailData));

            Notification::make()
                ->title('Solicitação enviada para aprovação.')
                ->success()
                ->send();
        } catch (\Throwable $th) {
            Notification::make()
                ->title('Houve um erro no envio do email de confirmação.')
                ->danger()
                ->send();
        }
    }

    public function render()
    {
        return view('livewire.create-reservation');
    }
}
