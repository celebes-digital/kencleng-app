<?php

use App\Http\Controllers\DownloadQRCodePdf;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

Route::get('dashbord/qr-pdf/{id}', [DownloadQRCodePdf::class, 'download'])
    ->middleware(['auth'])
    ->name('qr-pdf');

Route::get('daftar-donatur', App\Livewire\Forms\DaftarDonatur::class);

Route::get('daftar/donatur', App\Livewire\Pages\Daftar\Donatur::class);

require __DIR__.'/auth.php';
