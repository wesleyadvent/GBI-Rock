<?php

namespace App\Http\Controllers;

use App\Models\JadwalKebaktian;
use App\Models\JadwalKebaktianHistory;
use App\Models\KategoriPenolakan;
use App\Models\PengajuanJadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenatuaController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;

        $currentDate = Carbon::createFromDate($year, $month, 1);

        $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar   = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $jadwal = JadwalKebaktian::with([
                'tugas.user.bidang',
                'histories.oleh',
                'pembicaraEksternal'
            ])
            ->whereIn('status', ['pending', 'approved', 'declined', 'published'])
            ->whereBetween('tanggal_pelayanan', [
                $startOfCalendar->toDateString(),
                $endOfCalendar->toDateString()
            ])
            ->orderBy('tanggal_pelayanan')
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy(fn ($item) => $item->tanggal_pelayanan->format('Y-m-d'));

        $calendarGrid = [];
        $dateCursor = $startOfCalendar->copy();

        while ($dateCursor <= $endOfCalendar) {
            $week = [];
            for ($i = 0; $i < 7; $i++) {
                $dateKey = $dateCursor->toDateString();
                $week[] = [
                    'date' => $dateCursor->copy(),
                    'isCurrentMonth' => $dateCursor->month == $month,
                    'jadwal' => $jadwal[$dateKey] ?? collect()
                ];
                $dateCursor->addDay();
            }
            $calendarGrid[] = $week;
        }

        $kategoriPenolakan = KategoriPenolakan::orderBy('id')->get();

        return view('penatua.index', compact(
            'calendarGrid',
            'currentDate',
            'kategoriPenolakan'
        ));
    }

    public function approve($id)
    {
        $jadwal = JadwalKebaktian::findOrFail($id);

        if ($jadwal->status !== 'pending') {
            return back()->with('error', 'Jadwal tidak dapat diproses.');
        }

        DB::transaction(function () use ($jadwal) {

            $jadwal->update([
                'status' => 'approved',
                'disetujui_oleh' => Auth::id(),
                'alasan_penolakan' => null,
                'kategori_penolakan_id' => null
            ]);

            JadwalKebaktianHistory::create([
                'id_jadwal' => $jadwal->id_jadwal,
                'status' => 'approved',
                'oleh_user' => Auth::id()
            ]);
        });

        return back()->with('success', 'Jadwal berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'kategori_penolakan_id' => 'required|exists:kategori_penolakan,id',
            'alasan_penolakan' => 'required'
        ]);

        $jadwal = JadwalKebaktian::with(['tugas', 'pengajuan'])->findOrFail($id);

        if ($jadwal->status !== 'pending') {
            return back()->with('error', 'Jadwal tidak dapat diproses.');
        }

        $kategori = KategoriPenolakan::findOrFail($request->kategori_penolakan_id);
        $dampak = $kategori->dampak;

        DB::transaction(function () use ($jadwal, $request, $kategori, $dampak) {
            $jadwal->update([
                'status' => 'declined',
                'disetujui_oleh' => Auth::id(),
                'alasan_penolakan' => $request->alasan_penolakan,
                'kategori_penolakan_id' => $kategori->id
            ]);

            JadwalKebaktianHistory::create([
                'id_jadwal' => $jadwal->id_jadwal,
                'status' => 'declined',
                'alasan' => $request->alasan_penolakan,
                'oleh_user' => Auth::id()
            ]);

            $tugasDampak = $dampak['tugas'] ?? null;
            
            if ($tugasDampak === 'reset') {
                $jadwal->tugas()->delete();
                
            } elseif ($tugasDampak === 'pending') {
                $jadwal->tugas()
                    ->where('status_tugas', 'approved')
                    ->update([
                        'status_tugas' => 'declined'
                    ]);
            }

            if ($tugasDampak === 'pending' || $tugasDampak === 'reset') {
                PengajuanJadwal::where('id_jadwal', $jadwal->id_jadwal)
                    ->where('status_pengajuan', 'approved')
                    ->update([
                        'status_pengajuan' => 'declined',
                        'alasan_penolakan' => 'Jadwal ditolak oleh Penatua: ' . $request->alasan_penolakan
                    ]);
            }

            if (($dampak['jadwal'] ?? null) === 'recreate') {

                $jadwalBaru = $jadwal->replicate([
                    'status',
                    'kategori_penolakan_id',
                    'alasan_penolakan',
                    'disetujui_oleh'
                ]);

                $jadwalBaru->status = 'draft';
                $jadwalBaru->asal_jadwal = $jadwal->id_jadwal;
                $jadwalBaru->save();

                JadwalKebaktianHistory::create([
                    'id_jadwal' => $jadwalBaru->id_jadwal,
                    'status' => 'draft',
                    'alasan' => 'Jadwal otomatis dibuat ulang dari jadwal yang ditolak (ID: ' . $jadwal->id_jadwal . ')',
                    'oleh_user' => Auth::id()
                ]);
            }
        });

        return back()->with('success', 'Jadwal berhasil ditolak.');
    }
}