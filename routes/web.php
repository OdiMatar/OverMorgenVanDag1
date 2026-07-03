<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\MedewerkerController;
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
    
    Route::get('/medewerkers', [MedewerkerController::class, 'index'])->name('medewerkers.index')->can('view-owner-pages');
    Route::get('/medewerkers/{id}', [MedewerkerController::class, 'show'])->name('medewerkers.show')->can('view-owner-pages');
    Route::get('/medewerkers/{id}/edit', [MedewerkerController::class, 'edit'])->name('medewerkers.edit')->can('view-owner-pages');
    Route::put('/medewerkers/{id}', [MedewerkerController::class, 'update'])->name('medewerkers.update')->can('view-owner-pages');
    
    Route::view('/behandelingen', 'behandelingen.index')->name('behandelingen.index')->can('view-owner-pages');
    Route::view('/producten', 'producten.index')->name('producten.index')->can('view-owner-pages');
});
