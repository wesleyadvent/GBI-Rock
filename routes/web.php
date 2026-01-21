<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AkunPekerjaController;
use App\Http\Controllers\JadwalKebaktianController;
use App\Http\Controllers\BidangController;
use App\Http\Controllers\KoordinatorBidangController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PekerjaController;
use App\Http\Controllers\SekretarisController;
use App\Http\Controllers\SekretarisPengajuanController;
use App\Http\Controllers\PenatuaController;
use App\Http\Controllers\PembicaraEksternalController;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/jadwal-published', [JadwalKebaktianController::class, 'publishedSchedules'])
    ->name('jadwal.published');

Route::get('/jadwal-published/export-pdf', [JadwalKebaktianController::class, 'exportPDF'])
    ->name('jadwal.published.pdf');
        
Route::get('/jadwal-published/export-excel', [JadwalKebaktianController::class, 'exportExcel'])
    ->name('jadwal.published.excel');


Route::middleware(['auth', 'role:koordinator_bidang'])->group(function () {
    Route::get('/koordinator/dashboard', [KoordinatorBidangController::class, 'dashboard'])
        ->name('koordinator.dashboard');

    // Manajemen Pekerja
    Route::get('/koordinator/pekerja', [AkunPekerjaController::class, 'index'])->name('koordinator.pekerja.index');
    Route::get('/koordinator/pekerja/create', [AkunPekerjaController::class, 'create'])->name('koordinator.pekerja.create');
    Route::post('/koordinator/pekerja', [AkunPekerjaController::class, 'store'])->name('koordinator.pekerja.store');
    Route::get('/koordinator/pekerja/{id}/edit', [AkunPekerjaController::class, 'edit'])->name('koordinator.pekerja.edit');
    Route::put('/koordinator/pekerja/{id}', [AkunPekerjaController::class, 'update'])->name('koordinator.pekerja.update');
    Route::delete('/koordinator/pekerja/{id}', [AkunPekerjaController::class, 'destroy'])->name('koordinator.pekerja.destroy');

    // Tim Pelayanan
    Route::get('/timPelayanan/index', [KoordinatorBidangController::class, 'index'])->name('timPelayanan.index');
    
    // Assign pekerja (kirim permintaan ke pekerja)
    Route::post('/timPelayanan/assign', [KoordinatorBidangController::class, 'assignPekerja'])->name('timPelayanan.assign');
    
    // Edit peran tugas
    Route::put('/timPelayanan/edit-peran/{id}', [KoordinatorBidangController::class, 'editPeranTugas'])->name('timPelayanan.editPeran');
    
    // Hapus tugas pekerja
    Route::delete('/timPelayanan/batal/{id}', [KoordinatorBidangController::class, 'destroy'])->name('timPelayanan.batal');
    
    // Ajukan ke sekretaris
    Route::post('/timPelayanan/ajukan-sekretaris', [KoordinatorBidangController::class, 'ajukanKeSekretaris'])->name('timPelayanan.ajukanSekretaris');
    
    // Batalkan pengajuan ke sekretaris
    Route::post('/timPelayanan/batalkan-pengajuan', [KoordinatorBidangController::class, 'batalkanPengajuan'])->name('timPelayanan.batalkanPengajuan');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route::get('/admin/dashboard', function () {
    //     return view('dashboard.admin');
    // })->name('admin.dashboard')->middleware('role:admin');

    // Route::get('/koordinator/dashboard', function () {
    //     return view('dashboard.koordinator');
    // })->name('koordinator.dashboard')->middleware('role:koordinator_bidang');

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
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

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

    Route::post('/user/update-password/{id}', [AdminController::class, 'updatePassword'])
        ->name('admin.user.updatePassword');

    Route::get('/admin/user/delete/{id}', [AdminController::class, 'delete'])
        ->name('admin.user.delete');

    Route::get('/admin/user/toggle-status/{id}', [AdminController::class, 'toggleStatus'])
        ->name('admin.user.toggle');

    Route::get('/admin/bidang', [BidangController::class, 'index'])
        ->name('admin.bidang.index');

    Route::get('/admin/bidang/create', [BidangController::class, 'create'])
        ->name('admin.bidang.create');

    Route::post('/admin/bidang/store', [BidangController::class, 'store'])
        ->name('admin.bidang.store');

    Route::get('/admin/bidang/edit/{id}', [BidangController::class, 'edit'])
        ->name('admin.bidang.edit');

    Route::post('/admin/bidang/update/{id}', [BidangController::class, 'update'])
        ->name('admin.bidang.update');

    Route::get('/admin/bidang/delete/{id}', [BidangController::class, 'delete'])
        ->name('admin.bidang.delete');

    Route::get('/admin/pembicara-eksternal', [AdminController::class, 'pembicaraEksternal'])
        ->name('admin.pembicara-eksternal');
    
    Route::post('/pembicara-eksternal/store', [PembicaraEksternalController::class, 'store'])
        ->name('pembicara-eksternal.store');
    
    Route::put('/pembicara-eksternal/{id}', [PembicaraEksternalController::class, 'update'])
        ->name('pembicara-eksternal.update');
    
    Route::delete('/pembicara-eksternal/{id}', [PembicaraEksternalController::class, 'destroy'])
        ->name('pembicara-eksternal.destroy');

    Route::get('/admin-only', function () {
        return 'Halaman khusus admin';
    })->name('admin.only');
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

    Route::post('/sekretaris/jadwal/{id}/ajukan-ulang', [SekretarisController::class, 'ajukanUlang'])
        ->name('sekretaris.jadwal.ajukanUlang');

    Route::post('/sekretaris/jadwal/{id}/duplicate-with-pending', [SekretarisController::class, 'duplicateWithPending'])
        ->name('sekretaris.jadwal.duplicateWithPending');

    Route::get('/pengajuan', [SekretarisController::class, 'index'])
        ->name('sekretaris.pengajuan.index');

    Route::post('/pengajuan/assign', [SekretarisController::class, 'assignPekerja'])
        ->name('sekretaris.pengajuan.assign');

    Route::post('/pengajuan/approve', [SekretarisController::class, 'approve'])
        ->name('sekretaris.pengajuan.approve');

    Route::post('/pengajuan/decline', [SekretarisController::class, 'decline'])
        ->name('sekretaris.pengajuan.decline');

    // Tolak pengajuan individual (per koordinator)
    Route::post('/pengajuan/decline-pengajuan', [SekretarisPengajuanController::class, 'decline'])
        ->name('sekretaris.pengajuan.declinePengajuan');

    // Tolak semua pengajuan untuk bidang tertentu pada satu jadwal (Usher/PW)
    Route::post('/pengajuan/decline-bidang', [SekretarisController::class, 'declineBidang'])
        ->name('sekretaris.pengajuan.declineBidang');

    // Setujui semua pengajuan untuk bidang tertentu pada satu jadwal (Usher/PW)
    Route::post('/pengajuan/approve-bidang', [SekretarisController::class, 'approveBidang'])
        ->name('sekretaris.pengajuan.approveBidang');

    Route::delete('/pengajuan/batal/{id}', [SekretarisController::class, 'destroy'])
        ->name('sekretaris.pengajuan.batal');

    Route::post('/jadwal/publish/{id}', [JadwalKebaktianController::class, 'publish'])
        ->name('sekretaris.jadwal.publish');
});

Route::middleware(['auth', 'role:pekerja'])->prefix('pekerja')->group(function () {
    Route::get('/pekerja/index', [PekerjaController::class, 'index'])->name('pekerja.index');
    Route::post('/pekerja/index/konfirmasi/{id_tugas}', [PekerjaController::class, 'konfirmasi'])->name('pekerja.konfirmasi');
});

Route::middleware(['auth', 'role:penatua'])->group(function () {
    Route::get('/penatua/jadwal', [PenatuaController::class, 'index'])
        ->name('penatua.jadwal');

    Route::post('/penatua/jadwal/{id}/approve', [PenatuaController::class, 'approve'])
        ->name('penatua.jadwal.approve');

    Route::post('/penatua/jadwal/{id}/reject', [PenatuaController::class, 'reject'])
        ->name('penatua.jadwal.reject');
});


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{id}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.readAll');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'delete'])->name('notifikasi.delete');

});

require __DIR__ . '/auth.php';
