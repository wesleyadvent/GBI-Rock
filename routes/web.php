<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->name('admin.dashboard')->middleware('role:admin');

    Route::get('/koordinator/dashboard', function () {
        return view('dashboard.koordinator');
    })->name('koordinator.dashboard')->middleware('role:koordinator_bidang');

    Route::get('/sekretaris/dashboard', function () {
        return view('dashboard.sekretaris');
    })->name('sekretaris.dashboard')->middleware('role:sekretaris');

    Route::get('/pekerja/dashboard', function () {
        return view('dashboard.pekerja');
    })->name('pekerja.dashboard')->middleware('role:pekerja');

    Route::get('/penatua/dashboard', function () {
        return view('dashboard.penatua');
    })->name('penatua.dashboard')->middleware('role:penatua');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
