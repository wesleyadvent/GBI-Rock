<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PekerjaController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $currentDate = Carbon::createFromDate($year, $month, 1);

        $daftarTugas = Tugas::with('jadwalKebaktian')
            ->where('id_user', Auth::user()->id_user)
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->jadwalKebaktian->tanggal_pelayanan)->format('Y-m-d');
            });

        $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $calendarGrid = [];
        $iterDate = $startOfCalendar->copy();

        while ($iterDate <= $endOfCalendar) {
            $calendarGrid[] = [
                'date' => $iterDate->copy(),
                'isCurrentMonth' => $iterDate->month == $currentDate->month,
                'tugas' => $daftarTugas->get($iterDate->format('Y-m-d')) ?? collect([])
            ];
            $iterDate->addDay();
        }

        return view('pekerja.index', [
            'currentDate'  => $currentDate,
            'calendarGrid' => collect($calendarGrid)->chunk(7),
            'month'        => $month,
            'year'         => $year
        ]);
    }

    public function konfirmasi(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);

        if ($tugas->id_user != Auth::user()->id_user) {
            return back()->with('error', 'Akses ditolak.');
        }

        if ($request->aksi == 'terima') {
            $tugas->update(['status_tugas' => 'approved']);
            $pesan = "Anda telah menyetujui pelayanan.";
        } else {
            $request->validate(['alasan' => 'required|string']);
            $tugas->update([
                'status_tugas' => 'declined',
                'alasan_penolakan' => $request->alasan
            ]);
            $pesan = "Anda telah menolak pelayanan.";
        }

        return back()->with('success', $pesan);
    }
}
