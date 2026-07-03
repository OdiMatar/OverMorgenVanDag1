<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BehandelingController;
use App\Http\Controllers\KlantController;
use App\Http\Controllers\ProductController;
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
    Route::match(['get', 'post'], '/klanten', [KlantController::class, 'index'])->name('klanten.index')->can('view-owner-pages');
    Route::get('/klanten/{klantId}', [KlantController::class, 'show'])->whereNumber('klantId')->name('klanten.show')->can('view-owner-pages');
    Route::get('/klanten/{klantId}/wijzigen', [KlantController::class, 'edit'])->whereNumber('klantId')->name('klanten.edit')->can('view-owner-pages');
    Route::put('/klanten/{klantId}', [KlantController::class, 'update'])->whereNumber('klantId')->name('klanten.update')->can('view-owner-pages');

    Route::get('/medewerkers', [MedewerkerController::class, 'index'])->name('medewerkers.index')->can('view-owner-pages');
    Route::get('/medewerkers/{id}', [MedewerkerController::class, 'show'])->name('medewerkers.show')->can('view-owner-pages');
    Route::get('/medewerkers/{id}/edit', [MedewerkerController::class, 'edit'])->name('medewerkers.edit')->can('view-owner-pages');
    Route::put('/medewerkers/{id}', [MedewerkerController::class, 'update'])->name('medewerkers.update')->can('view-owner-pages');

    Route::get('/behandelingen', [BehandelingController::class, 'index'])->name('behandelingen.index')->can('view-owner-pages');
    Route::get('/behandelingen/{behandeling}/producten', [BehandelingController::class, 'producten'])->name('behandelingen.producten.index')->can('view-owner-pages');
    Route::get('/behandelingen/{behandeling}/producten/{product}', [BehandelingController::class, 'productDetail'])->name('behandelingen.producten.show')->can('view-owner-pages');
    Route::get('/behandelingen/{behandeling}/producten/{product}/wijzigen', [BehandelingController::class, 'productWijzigen'])->name('behandelingen.producten.edit')->can('view-owner-pages');
    Route::put('/behandelingen/{behandeling}/producten/{product}', [BehandelingController::class, 'productOpslaan'])->name('behandelingen.producten.update')->can('view-owner-pages');

    Route::get('/producten', [ProductController::class, 'index'])->name('producten.index')->can('view-owner-pages');
    Route::get('/producten/{id}', [ProductController::class, 'show'])->name('producten.show')->can('view-owner-pages');
    Route::get('/producten/{id}/edit', [ProductController::class, 'edit'])->name('producten.edit')->can('view-owner-pages');
    Route::put('/producten/{id}', [ProductController::class, 'update'])->name('producten.update')->can('view-owner-pages');
});
