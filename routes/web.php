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

Route::get('/feeds/{feed}', function (Feed $feed) {
    if (! $feed->canAccess(auth()->user())) {
        abort(403, 'This feed is no longer active.');
    }

    return view('feed', ['feed' => $feed]);
})->name('feed.show')->middleware('auth');

require __DIR__.'/auth.php';
