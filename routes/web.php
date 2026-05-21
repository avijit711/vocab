<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\Word\ImportController;

Route::view('/', 'welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('dashboard', 'pages.dashboard')
        ->name('dashboard');

    Route::get('words/import', [ImportController::class, 'index'])
        ->name('words.import');
    Route::post('words/import', [ImportController::class, 'store'])
        ->name('words.import.store');

    Volt::route('words/read', 'pages.words.read')
        ->name('words.read');

    Volt::route('practice', 'pages.practice.index')
        ->name('practice.index');

    Volt::route('words/favorites', 'pages.words.favorites')
        ->name('words.favorites');

    Volt::route('words/read-later', 'pages.words.read-later')
        ->name('words.read-later');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
