<?php

use Illuminate\Support\Facades\{Route, Auth};

// disable register, reset password
Auth::routes(['register' => false, 'reset' => false]);

// jika ke /, redirect ke /login
Route::redirect('/', '/login');

Route::middleware('auth')->group(function () {
    Route::view('dashboard', 'dashboard')->name('home');
    Route::view('manajemen-user', 'manajemen-user')->name('admin.manajemen-user');
    Route::view('profil', 'profil')->name('profil');
    // Purposes (Tujuan) management page
    Route::get('purposes', function () {
        return view('purposes.index');
    })->name('purposes.index');
    
    // Items management (Volt Livewire component)
    Route::get('/items', function(){
        return view('items.index');
    })->name('items.index');

    // Export items CSV
    Route::get('/items/export-csv', [\App\Http\Controllers\ItemExportController::class, 'exportCsv'])->name('items.export');
    // Import items CSV (form POST)
    Route::post('/items/import-csv', [\App\Http\Controllers\ItemExportController::class, 'importCsv'])->name('items.import');

    // Stock movements (audit)
    Route::get('/stock-movements', function(){
        return view('stock-movements.index');
    })->name('stock.movements.index');

    // Goods Inwards (Penerimaan)
    Route::get('/goods-inwards', function(){
        return view('goods-inwards.index');
    })->name('goods-inwards.index');
});