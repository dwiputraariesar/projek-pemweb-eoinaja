<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

// 🔒 Semua route di bawah ini hanya bisa diakses setelah login
Route::middleware(['auth'])->group(function () {

    // 🏠 Dashboard
    Route::get('/dashboard', function () {
        $events = \App\Models\Event::latest()->get();
        return view('dashboard', compact('events'));
    })->name('dashboard');

    // 🎟️ CRUD Event
    Route::resource('events', EventController::class);

    // 👤 Profile User (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';