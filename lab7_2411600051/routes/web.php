<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InventoryTransactionController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SupplierPerformanceController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\ExportController;

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

Route::resource('suppliers', SupplierController::class);
    Route::get('/suppliers-performance', [SupplierPerformanceController::class, 'index'])->name('suppliers.performance');

    Route::resource('purchase-orders', PurchaseOrderController::class);
    Route::post('/purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('/purchase-orders/{purchaseOrder}/reject', [PurchaseOrderController::class, 'reject'])->name('purchase-orders.reject');
    Route::post('/purchase-orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'send'])->name('purchase-orders.send');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive');

    Route::get('/export/products', [ExportController::class, 'productsCsv'])->name('export.products');
    Route::get('/export/suppliers', [ExportController::class, 'suppliersCsv'])->name('export.suppliers');
    Route::get('/export/purchase-orders/{purchaseOrder}/pdf', [ExportController::class, 'purchaseOrderPdf'])->name('export.purchase-order-pdf');Route::resource('suppliers', SupplierController::class);

Route::get('/adjustments', [StockAdjustmentController::class, 'create'])->name('adjustments.create');
Route::post('/adjustments', [StockAdjustmentController::class, 'store'])->name('adjustments.store');
Route::get('/adjustments/{product}/quantity', [StockAdjustmentController::class, 'currentQuantity'])->name('adjustments.quantity');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/stock-movement', [ReportController::class, 'stockMovement'])->name('reports.stock-movement');
Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
Route::get('/reports/valuation', [ReportController::class, 'valuation'])->name('reports.valuation');
});

require __DIR__.'/auth.php';
