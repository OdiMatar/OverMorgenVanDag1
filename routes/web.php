<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('/inloggen', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/inloggen', [AuthenticatedSessionController::class, 'store'])->name('login.store');

    Route::get('/registreren', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/registreren', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::match(['get', 'post'], '/uitloggen', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::view('/klanten', 'klanten.index')->name('klanten.index')->can('view-owner-pages');
    Route::view('/medewerkers', 'medewerkers.index')->name('medewerkers.index')->can('view-owner-pages');
    Route::view('/behandelingen', 'behandelingen.index')->name('behandelingen.index')->can('view-owner-pages');
    Route::view('/producten', 'producten.index')->name('producten.index')->can('view-owner-pages');
});
