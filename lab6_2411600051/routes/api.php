<?php

use App\Http\Controllers\Api\DashboardApiController;
use Illuminate\Support\Facades\Route;

Route::get('/dashboard/stats', [DashboardApiController::class, 'stats']);
Route::get('/products/low-stock', [DashboardApiController::class, 'lowStockProducts']);
Route::get('/transactions/recent', [DashboardApiController::class, 'recentTransactions']);