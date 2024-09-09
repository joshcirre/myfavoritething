<?php

use App\Models\Feed;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/feed/{feed}', function (Feed $feed) {
    return view('feed', ['feed' => $feed]);
})->name('feed.show');

require __DIR__.'/auth.php';
