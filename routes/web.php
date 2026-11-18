<?php

use App\Http\Controllers\Admin\ComplaintController as AdminComplaintController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\LeaseController as AdminLeaseController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\RoomController as AdminRoomController;
use App\Http\Controllers\Penghuni\ComplaintController as PenghuniComplaintController;
use App\Http\Controllers\Penghuni\DashboardController as PenghuniDashboardController;
use App\Http\Controllers\Penghuni\PaymentController as PenghuniPaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login'));

// Rute "dashboard" bawaan Breeze dipertahankan (dipakai redirect setelah login),
// lalu diarahkan sesuai role.
Route::get('/dashboard', fn () => redirect()->route(
    auth()->user()->isAdmin() ? 'admin.dashboard' : 'penghuni.dashboard'
))->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin (pemilik kos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class.':admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('rooms', AdminRoomController::class)->except('show');

        Route::post('leases/{lease}/checkout', [AdminLeaseController::class, 'checkout'])->name('leases.checkout');
        Route::resource('leases', AdminLeaseController::class)->except('show');

        Route::post('payments/generate', [AdminPaymentController::class, 'generate'])->name('payments.generate');
        Route::patch('payments/{payment}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');
        Route::patch('payments/{payment}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
        Route::resource('payments', AdminPaymentController::class)->except('show');

        Route::resource('complaints', AdminComplaintController::class)->only(['index', 'show', 'update']);
    });

/*
|--------------------------------------------------------------------------
| Penghuni (anak kos)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', RoleMiddleware::class.':penghuni'])
    ->prefix('penghuni')
    ->name('penghuni.')
    ->group(function () {
        Route::get('/', [PenghuniDashboardController::class, 'index'])->name('dashboard');

        Route::get('payments', [PenghuniPaymentController::class, 'index'])->name('payments.index');
        Route::post('payments/{payment}/upload', [PenghuniPaymentController::class, 'upload'])->name('payments.upload');

        Route::resource('complaints', PenghuniComplaintController::class)->except('show');
    });

require __DIR__.'/auth.php';
