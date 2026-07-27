<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\UserController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::resource('users', UserController::class);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');

// 404 Error page route for direct testing
Route::get('/404-error', function () {
    return response()->view('errors.404', [], 404);
})->name('error.404');
