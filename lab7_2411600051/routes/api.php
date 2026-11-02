<?php

use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardWidgetController;

Route::get('/widgets/pending-orders', [DashboardWidgetController::class, 'pendingOrders']);
Route::get('/widgets/recent-orders', [DashboardWidgetController::class, 'recentOrders']);
Route::get('/widgets/supplier-performance', [DashboardWidgetController::class, 'supplierPerformance']);
Route::get('/widgets/reorder-alerts', [DashboardWidgetController::class, 'reorderAlerts']);
Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
Route::get('/products/low-stock', [DashboardApiController::class, 'lowStockProducts']);
Route::get('/transactions/recent', [DashboardApiController::class, 'recentTransactions']);