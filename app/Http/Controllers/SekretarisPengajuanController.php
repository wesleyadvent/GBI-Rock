<?php

namespace App\Http\Controllers;

use App\Models\PengajuanJadwal;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SekretarisPengajuanController extends Controller
{
    public function approve(Request $request)
    {
        $pengajuan = PengajuanJadwal::with('jadwal')->findOrFail($request->id_pengajuan);

        $pengajuan->update([
            'status_pengajuan' => 'approved'
        ]);

        Notifikasi::create([
            'id_user' => $pengajuan->id_koordinator,
            'pesan' => 'Pengajuan jadwal pelayanan Anda telah disetujui oleh Sekretaris.',
            'tipe' => 'pengajuan_disetujui',
            'status_baca' => 0
        ]);

        return back()->with('success', 'Pengajuan berhasil disetujui.');
    }

    public function decline(Request $request)
    {
        $request->validate([
            'alasan_penolakan' => 'required'
        ]);

        $pengajuan = PengajuanJadwal::findOrFail($request->id_pengajuan);

        $pengajuan->update([
            'status_pengajuan' => 'declined',
            'alasan_penolakan' => $request->alasan_penolakan
        ]);

        Notifikasi::create([
            'id_user' => $pengajuan->id_koordinator,
            'pesan' => 'Pengajuan jadwal pelayanan ditolak. Alasan: ' . $request->alasan_penolakan,
            'tipe' => 'pengajuan_ditolak',
            'status_baca' => 0
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
}
