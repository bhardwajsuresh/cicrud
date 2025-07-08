<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

Route::get('/', [DownloadController::class, 'index'])->name('downloads.index');
Route::get('/downloads/create', [DownloadController::class, 'create'])->name('downloads.create');
Route::post('/downloads', [DownloadController::class, 'store'])->name('downloads.store');
Route::get('/downloads/{download}', [DownloadController::class, 'show'])->name('downloads.show');
Route::get('/downloads/{download}/download', [DownloadController::class, 'download'])->name('downloads.download');
Route::get('/downloads/{download}/status', [DownloadController::class, 'status'])->name('downloads.status');
