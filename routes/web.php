<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductOrderSummaryController;

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('reports/product-orders', [ProductOrderSummaryController::class, 'index'])
        ->name('reports.product-orders.index');

    Route::get('reports/product-orders/export', [ProductOrderSummaryController::class, 'export'])
        ->name('reports.product-orders.export');
});

Route::get('/', function () {
    return view('welcome');
})->name('home');