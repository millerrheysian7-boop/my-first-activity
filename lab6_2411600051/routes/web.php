<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\ReportController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', ProductController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::resource('transactions', InventoryTransactionController::class)
    ->only(['index', 'create', 'store', 'show', 'destroy']);

    Route::post('/notifications/mark-all-read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
})->name('notifications.markAllRead');

Route::get('/adjustments', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
Route::get('/adjustments/{product}/quantity', [StockAdjustmentController::class, 'currentQuantity'])->name('adjustments.quantity');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');
Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
Route::get('/reports/valuation', [ReportController::class, 'valuation'])->name('reports.valuation');
});

require __DIR__.'/auth.php';
