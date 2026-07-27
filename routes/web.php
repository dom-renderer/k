<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SectorController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\CoatingCaseController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\SettingController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
Route::post('/products', [ProductController::class, 'store'])->name('products.store');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

Route::resource('users', UserController::class);
Route::resource('roles', RoleController::class);
Route::resource('sectors', SectorController::class);
Route::resource('equipment', EquipmentController::class);

// Coating Cases & Files Routes
Route::get('/cases/check-oa', [CoatingCaseController::class, 'checkOaNumber'])->name('cases.check-oa');
Route::post('/cases/upload-file', [CoatingCaseController::class, 'uploadFile'])->name('cases.upload-file');
Route::delete('/cases/file/{file}', [CoatingCaseController::class, 'deleteFile'])->name('cases.delete-file');
Route::get('/cases/file/{file}/download', [CoatingCaseController::class, 'downloadFile'])->name('cases.download-file');
Route::post('/cases/{case}/review-level', [CoatingCaseController::class, 'reviewLevel'])->name('cases.review-level');
Route::resource('cases', CoatingCaseController::class);

// Activity Logs Route
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');

// 404 Error page route for direct testing
Route::get('/404-error', function () {
    return response()->view('errors.404', [], 404);
})->name('error.404');
