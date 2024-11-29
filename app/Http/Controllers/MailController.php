<?php

namespace App\Http\Controllers;

use App\Mail\ReservationConfirmed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function reservationConfirmed()
    {
        $mailData = [
            'title' => 'Sua reserva da sala foi confirmada.',
            'body' => 'Olá, sua reserva da sala foi confirmada pelo responsável.',
            'dates' => null
        ];

        Mail::to('r4faelmf@gmail.com')->send(new ReservationConfirmed($mailData));
    }
}
