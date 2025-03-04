<?php

use Illuminate\Support\Facades\{Route, Auth};

// disable register, reset password
Auth::routes(['register' => false, 'reset' => false]);

// jika ke /, redirect ke /login
Route::redirect('/', '/login');

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::view('kendaraan', 'kendaraan')->name('kendaraan');
    Route::view('dashboard', 'dashboard')->name('home');
    Route::view('manajemen-user', 'manajemen-user')->name('admin.manajemen-user');
    Route::view('profil', 'profil')->name('profil');
});