<?php

namespace App\Http\Controllers;

use App\Models\JadwalKebaktian;
use App\Models\User;
use App\Models\Tugas;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class KoordinatorBidangController extends Controller
{
    public function index(Request $request)
    {
        $id_bidang_user = Auth::user()->id_bidang;

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $currentDate = \Carbon\Carbon::createFromDate($year, $month, 1);

        $jadwalRaw = JadwalKebaktian::where('status', 'draft')
            ->with(['tugas' => function ($query) use ($id_bidang_user) {
                $query->whereHas('user', function ($q) use ($id_bidang_user) {
                    $q->where('id_bidang', $id_bidang_user);
                })->with('user');
            }])
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->tanggal_pelayanan)->format('Y-m-d');
            });

        $daftarPekerja = User::where('id_bidang', $id_bidang_user)
            ->where('role', 'pekerja')
            ->get();

        $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(\Carbon\Carbon::SUNDAY);
        $endOfCalendar = $currentDate->copy()->endOfMonth()->endOfWeek(\Carbon\Carbon::SATURDAY);

        $calendarGrid = [];
        $iterDate = $startOfCalendar->copy();

        while ($iterDate <= $endOfCalendar) {
            $calendarGrid[] = [
                'date' => $iterDate->copy(),
                'isCurrentMonth' => $iterDate->month == $currentDate->month,
                'jadwal' => $jadwalRaw->get($iterDate->format('Y-m-d')) ?? collect([])
            ];
            $iterDate->addDay();
        }

        return view('koordinator.timPelayanan.index', [
            'currentDate' => $currentDate,
            'calendarGrid' => collect($calendarGrid)->chunk(7),
            'daftarPekerja' => $daftarPekerja
        ]);
    }

    public function assignPekerja(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal',
            'id_user' => 'required|exists:user,id_user',
            'peran_tugas' => 'required|string|max:100',
        ]);

        $cekPekerja = Tugas::where('id_jadwal', $request->id_jadwal)
            ->where('id_user', $request->id_user)
            ->exists();

        if ($cekPekerja) {
            return back()->with('error', 'Pekerja ini sudah diajukan pada jadwal ini.');
        }

        $tugas = Tugas::create([
            'id_jadwal' => $request->id_jadwal,
            'id_user' => $request->id_user,
            'peran_tugas' => $request->peran_tugas,
            'status_tugas' => 'pending',
        ]);

        $tugas->load('jadwalKebaktian');

        Notifikasi::create([
            'id_user' => $request->id_user,
            'pesan' => "Anda diminta melayani sebagai {$request->peran_tugas} pada " .
                \Carbon\Carbon::parse($tugas->jadwalKebaktian->tanggal_pelayanan)->format('d M Y'),
            'tipe' => 'permintaan_pelayanan',
            'status_baca' => 0,
        ]);

        return back()->with('success', 'Pekerja berhasil diajukan!');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);

        if ($tugas->status_tugas !== 'pending') {
            return back()->with('error', 'Tugas sudah dikonfirmasi oleh pekerja dan tidak bisa dibatalkan.');
        }

        $tugas->delete();

        return back()->with('success', 'Pengajuan pelayanan berhasil dibatalkan.');
    }
}
