<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminLaporanController;
use App\Http\Controllers\AdminPerumahanController;
use App\Http\Controllers\AdminRekomendasiPerumahanController;
use App\Http\Controllers\AdminRisikoController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/perumahan/{perumahan:slug}', [PublicController::class, 'show'])->name('perumahan.show');

Route::get('/login', [AdminAuthController::class, 'showLogin'])->middleware('guest')->name('login');
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->middleware('guest')->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware('guest')->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->middleware('auth')->name('admin.logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/perumahans/rekomendasi', [AdminRekomendasiPerumahanController::class, 'index'])->name('perumahans.rekomendasi');
    Route::get('/perumahans/rekomendasi/export', [AdminRekomendasiPerumahanController::class, 'export'])->name('perumahans.rekomendasi.export');
    Route::resource('perumahans', AdminPerumahanController::class)->except(['show']);
    Route::patch('/perumahans/{perumahan}/toggle-rekomendasi', [AdminPerumahanController::class, 'toggleRecommended'])->name('perumahans.toggleRecommended');
    Route::resource('risikos', AdminRisikoController::class)->except(['show']);
    Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export', [AdminLaporanController::class, 'exportCsv'])->name('laporan.export');
});
