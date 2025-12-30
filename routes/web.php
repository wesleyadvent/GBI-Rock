<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunPekerjaController;
use App\Http\Controllers\JadwalKebaktianController;
use App\Http\Controllers\KoordinatorBidangController;
use App\Http\Controllers\PekerjaController;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:koordinator_bidang'])->group(function () {
    Route::get('/koordinator/pekerja', [AkunPekerjaController::class, 'index'])->name('koordinator.pekerja.index');
    Route::get('/koordinator/pekerja/create', [AkunPekerjaController::class, 'create'])->name('koordinator.pekerja.create');
    Route::post('/koordinator/pekerja', [AkunPekerjaController::class, 'store'])->name('koordinator.pekerja.store');

    Route::get('/koordinator/pekerja/{id}/edit', [AkunPekerjaController::class, 'edit'])->name('koordinator.pekerja.edit');
    Route::put('/koordinator/pekerja/{id}', [AkunPekerjaController::class, 'update'])->name('koordinator.pekerja.update');

    Route::delete('/koordinator/pekerja/{id}', [AkunPekerjaController::class, 'destroy'])->name('koordinator.pekerja.destroy');

    // Koordinator mencari tim pelayanan pelayanan
    Route::get('/timPelayanan/index', [KoordinatorBidangController::class, 'index'])->name('timPelayanan.index');
    Route::get('/timPelayanan/index/detail/{id}', [KoordinatorBidangController::class, 'showDetail']);
    Route::post('/timPelayanan/index/assign', [KoordinatorBidangController::class, 'assignPekerja'])->name('timPelayanan.assign');
    Route::delete('/tim-pelayanan/batal/{id}', [KoordinatorBidangController::class, 'destroy'])->name('timPelayanan.batal');
});

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

// Grup Sekretaris
Route::middleware(['auth', 'role:sekretaris'])->group(function () {
    Route::get('/sekretaris/jadwal/create', [JadwalKebaktianController::class, 'create'])
        ->name('sekretaris.jadwal.create');

    Route::post('/sekretaris/jadwal/store', [JadwalKebaktianController::class, 'store'])
        ->name('sekretaris.jadwal.store');

    Route::get('/sekretaris/jadwal', [JadwalKebaktianController::class, 'index'])
        ->name('sekretaris.jadwal.index');

    Route::get('/sekretaris/jadwal/detail/{id}', [JadwalKebaktianController::class, 'show']);

    Route::get('/sekretaris/jadwal/{id}/edit', [JadwalKebaktianController::class, 'edit'])
        ->name('sekretaris.jadwal.edit');

    Route::put('/sekretaris/jadwal/{id}', [JadwalKebaktianController::class, 'update'])
        ->name('sekretaris.jadwal.update');

    Route::delete('/sekretaris/jadwal/{id}', [JadwalKebaktianController::class, 'destroy'])
        ->name('sekretaris.jadwal.delete');
});

Route::middleware(['auth', 'role:pekerja'])->prefix('pekerja')->group(function () {
    Route::get('/pekerja/index', [PekerjaController::class, 'index'])->name('pekerja.index');
    Route::post('/pekerja/index/konfirmasi/{id_tugas}', [PekerjaController::class, 'konfirmasi'])->name('pekerja.konfirmasi');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
