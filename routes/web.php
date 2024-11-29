<?php

use App\Filament\Pages\Search;
use App\Filament\Pages\Solicitation;
use App\Livewire\CreateReservation;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $sectors = User::withCount('rooms')->where('is_admin', false)->get();
    return view('welcome', ['sectors' => $sectors]);
});
Route::get('/consultar/{sector}', Search::class)->name('search');
Route::get('/solicitar/{sector}', Solicitation::class)->name('solicitation');
// Route::get('/solicitar', CreateReservation::class)->name('solicitation');
// Route::get('/my-page', MyPage::class)->name('my-page');
