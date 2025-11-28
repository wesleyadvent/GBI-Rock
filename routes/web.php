<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/user', [AdminController::class, 'index'])
        ->name('admin.user.index');

    Route::get('/admin/user/create', [AdminController::class, 'create'])
        ->name('admin.user.create');

    Route::post('/admin/user/store', [AdminController::class, 'store'])
        ->name('admin.user.store');

    Route::get('/admin/user/edit/{id}', [AdminController::class, 'edit'])
        ->name('admin.user.edit');

    Route::post('/admin/user/update/{id}', [AdminController::class, 'update'])
        ->name('admin.user.update');

    Route::get('/admin/user/delete/{id}', [AdminController::class, 'delete'])
        ->name('admin.user.delete');

    Route::get('/admin/user/toggle-status/{id}', [AdminController::class, 'toggleStatus'])
        ->name('admin.user.toggle');

    Route::get('/admin-only', function () {
        return 'Halaman khusus admin';
    })->name('admin.only');

    Route::get('/admin/user/create', [AdminController::class, 'create'])
        ->name('admin.user.create');

    Route::post('/admin/user/store', [AdminController::class, 'store'])
        ->name('admin.user.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
