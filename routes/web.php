<?php

use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShortUrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return redirect()->route('short-urls.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::prefix('profile')->name('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('/', 'edit')->name('edit');
        Route::patch('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    Route::prefix('short-urls')->name('short-urls.')->controller(ShortUrlController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
    });

    Route::post('/invitations', [InvitationController::class, 'store'])->name('invitations.store');
});

Route::get('/s/{code}', [ShortUrlController::class, 'resolve'])
    ->name('short-urls.resolve');

Route::prefix('invitations')->name('invitations.')->controller(InvitationController::class)->group(function () {
    Route::get('{token}/accept', 'acceptForm')->name('accept.form');
    Route::post('{token}/accept', 'accept')->name('accept');
});

require __DIR__.'/auth.php';
