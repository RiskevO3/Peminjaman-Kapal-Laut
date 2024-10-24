<?php

use App\Http\Middleware\AdminAuth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (FacadesAuth::check()) {
        return redirect()->route('dashboard');
    } else {
        return redirect()->route('login');
    }
});

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->middleware('verified')
        ->name('dashboard');

    Route::view('peminjaman-kapal-laut','peminjaman-kapal-laut')
    ->middleware('verified')
    ->name('peminjaman-kapal-laut');

    Route::view('kapal-laut-borrowed','kapal-laut-borrowed')
    ->middleware('verified')
    ->name('kapal-laut-borrowed');

    Route::view('profile', 'profile')
        ->name('profile');
});

Route::middleware(AdminAuth::class)->group(function () {
    Route::view('admin', 'admin')
        ->name('admin');
    Route::get('/admin/ship/{id}',function($id){
        return view('ship-detail',[
            'shipId' => $id
        ]);
    })->name('admin-ship');
});

require __DIR__.'/auth.php';
