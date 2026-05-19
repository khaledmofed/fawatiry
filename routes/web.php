<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceEditorController;
use App\Http\Controllers\InvoicePdfController;
use App\Http\Controllers\InvoiceTemplateController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::resource('clients', ClientController::class)->except(['show']);
    Route::resource('products', ProductController::class)->except(['show']);

    Route::get('/media', [MediaController::class, 'index'])->name('media.index');
    Route::post('/media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('/media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');

    Route::get('/settings/company', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('/settings/company', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('/invoice-templates', [InvoiceTemplateController::class, 'index'])->name('invoice-templates.index');

    Route::get('/invoices/{invoice}/editor', [InvoiceEditorController::class, 'edit'])->name('invoices.editor');
    Route::patch('/invoices/{invoice}/editor-meta', [InvoiceEditorController::class, 'updateMeta'])
        ->middleware('throttle:120,1')
        ->name('invoices.editor.meta');
    Route::patch('/invoices/{invoice}/editor-client', [InvoiceEditorController::class, 'updateClient'])
        ->middleware('throttle:120,1')
        ->name('invoices.editor.client');
    Route::patch('/invoices/{invoice}/editor-template', [InvoiceEditorController::class, 'updateTemplate'])
        ->middleware('throttle:60,1')
        ->name('invoices.editor.template');
    Route::post('/invoices/{invoice}/editor-logo', [InvoiceEditorController::class, 'uploadLogo'])
        ->middleware('throttle:30,1')
        ->name('invoices.editor.logo');
    Route::post('/invoices/{invoice}/editor-stamp', [InvoiceEditorController::class, 'uploadStamp'])
        ->middleware('throttle:30,1')
        ->name('invoices.editor.stamp');
    Route::post('/invoices/{invoice}/editor-stamp/remove', [InvoiceEditorController::class, 'removeStamp'])
        ->middleware('throttle:30,1')
        ->name('invoices.editor.stamp.remove');
    Route::patch('/invoices/{invoice}/design', [InvoiceEditorController::class, 'updateDesign'])
        ->middleware('throttle:120,1')
        ->name('invoices.design.update');
    Route::put('/invoices/{invoice}/items', [InvoiceEditorController::class, 'updateItems'])->name('invoices.items.update');
    Route::get('/invoices/{invoice}/dompdf', [InvoicePdfController::class, 'dompdf'])->name('invoices.pdf.dompdf');

    Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::resource('invoices', InvoiceController::class)->except(['store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
