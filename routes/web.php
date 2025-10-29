<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;

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

    // Ticket management for events (organizer/admin)
    Route::post('events/{event}/tickets', [\App\Http\Controllers\TicketController::class, 'store'])
        ->name('events.tickets.store');

    // Booking routes
    Route::post('events/{event}/book', [\App\Http\Controllers\BookingController::class, 'store'])->name('events.book');
    Route::resource('bookings', \App\Http\Controllers\BookingController::class)->only(['index','show','destroy']);

    // Ticket download
    Route::get('bookings/{booking}/ticket', [\App\Http\Controllers\BookingController::class, 'downloadTicket'])
        ->name('tickets.download');

    // User transactions
    Route::get('transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('transactions.index');
    Route::get('transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'show'])->name('transactions.show');

    // Reviews for events
    Route::post('events/{event}/reviews', [ReviewController::class, 'store'])->name('events.reviews.store');
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Payment (mock) for a booking
    Route::post('bookings/{booking}/pay', [\App\Http\Controllers\BookingController::class, 'pay'])
        ->name('bookings.pay');

    // Stripe Checkout - create a session and redirect
    Route::post('payments/checkout/{booking}', [\App\Http\Controllers\PaymentController::class, 'checkout'])
        ->name('payments.checkout');

    // 👤 Profile User (bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 💳 Booking & Pembayaran Dummy
    Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');

    // 🎫 Halaman untuk melihat semua tiket yang dimiliki user
    Route::get('/my-tickets', [\App\Http\Controllers\TicketController::class, 'index'])->name('tickets.index');

    // 📄 Halaman untuk melihat detail satu tiket (termasuk QR Code)
    Route::get('/my-tickets/{booking}', [\App\Http\Controllers\TicketController::class, 'show'])->name('tickets.show');

    // ❌ Aksi untuk membatalkan booking
    Route::delete('/my-tickets/{booking}', [\App\Http\Controllers\TicketController::class, 'destroy'])->name('tickets.cancel');

    // ⭐ Aksi untuk menyimpan ulasan & rating
    Route::post('/events/{event}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

});

// 🧾 Route otentikasi (login, register, dll)
require __DIR__.'/auth.php';

// Admin routes example (protected by auth; controllers enforce role checks)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    // Admin user management
    Route::get('/admin/users', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])
        ->name('admin.users.index');
    Route::get('/admin/users/{user}/edit', [\App\Http\Controllers\Admin\UserManagementController::class, 'edit'])
        ->name('admin.users.edit');
    Route::patch('/admin/users/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'update'])
        ->name('admin.users.update');
    
    // Admin booking management (check-in)
    Route::post('/admin/bookings/{booking}/checkin', [\App\Http\Controllers\Admin\BookingManagementController::class, 'checkin'])
        ->name('admin.bookings.checkin');
    Route::get('/admin/bookings', [\App\Http\Controllers\Admin\BookingManagementController::class, 'index'])
        ->name('admin.bookings.index');
    // Admin bulk actions and export
    Route::post('/admin/bookings/bulk-checkin', [\App\Http\Controllers\Admin\BookingManagementController::class, 'bulkCheckin'])
        ->name('admin.bookings.bulk_checkin');
    Route::get('/admin/bookings/export', [\App\Http\Controllers\Admin\BookingManagementController::class, 'index'])
        ->name('admin.bookings.export');
    
    // Admin transactions
    Route::get('/admin/transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])
        ->name('admin.transactions.index');
    Route::get('/admin/transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])
        ->name('admin.transactions.show');
});