<?php

use App\Http\Controllers\CollectivFormController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\KtpController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FormController::class, 'index'])->name('home');

Route::get('/form-ktp', [FormController::class, 'index'])->name('form-ktp.index');
Route::get('/form-ktp/check-nik', [FormController::class, 'checkNik'])->name('form-ktp.check-nik');
Route::post('/form-ktp', [FormController::class, 'store'])->name('form-ktp.store');
Route::get('/form-collectiv', [CollectivFormController::class, 'index'])->name('form-collectiv.index');
Route::post('/form-collectiv', [CollectivFormController::class, 'store'])->name('form-collectiv.store');

Route::middleware('auth')->get('/admin/ktp/{applicant}', [KtpController::class, 'show'])->name('admin.ktp.show');
Route::middleware('auth')->get('/admin/collectiv/ktp/{collectiv}', [KtpController::class, 'showCollectiv'])->name('admin.collectiv.ktp.show');
Route::middleware('auth')->get('/admin/export/applicants', [ExportController::class, 'applicants'])->name('admin.export.applicants');
Route::middleware('auth')->get('/admin/export/collectivs', [ExportController::class, 'collectivs'])->name('admin.export.collectivs');
