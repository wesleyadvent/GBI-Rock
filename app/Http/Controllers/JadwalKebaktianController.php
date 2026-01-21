<?php

namespace App\Http\Controllers;

use App\Models\JadwalKebaktian;
use App\Models\User;
use App\Models\Tugas;
use App\Models\Bidang;
use App\Models\PengajuanJadwal;
use App\Models\KategoriPenolakan;
use App\Exports\JadwalExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class JadwalKebaktianController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);

        $currentDate = Carbon::createFromDate($year, $month, 1);

        $startDate = $currentDate->copy()
            ->startOfMonth()
            ->startOfWeek(Carbon::SUNDAY);

        $endDate = $currentDate->copy()
            ->endOfMonth()
            ->endOfWeek(Carbon::SATURDAY);

        $jadwal = JadwalKebaktian::whereBetween(
                'tanggal_pelayanan',
                [$startDate, $endDate]
            )
            ->get()
            ->groupBy(fn ($item) =>
                $item->tanggal_pelayanan->format('Y-m-d')
            );

        $calendarGrid = [];
        $date = $startDate->copy();

        while ($date <= $endDate) {
            $week = [];

            for ($i = 0; $i < 7; $i++) {
                $week[] = [
                    'date' => $date->copy(),
                    'isCurrentMonth' => $date->month == $currentDate->month,
                    'jadwal' => $jadwal[$date->format('Y-m-d')] ?? collect(),
                ];
                $date->addDay();
            }

            $calendarGrid[] = $week;
        }

        $aturanBidang = [
            1 => ['nama' => 'Usher', 'min' => 2, 'max' => null],
            2 => ['nama' => 'Pembicara', 'min' => 1, 'max' => 1],
            3 => ['nama' => 'Pendoa', 'min' => 1, 'max' => 2],
            4 => ['nama' => 'PW (Praise & Worship)', 'min' => 2, 'max' => null],
            5 => ['nama' => 'Multimedia', 'min' => 1, 'max' => null],
        ];

        $daftarPembicara = User::where('id_bidang', 2)->where('role', 'pekerja')->get();
        $daftarPendoa = User::where('id_bidang', 3)->where('role', 'pekerja')->get();
        $daftarMultimedia = User::where('id_bidang', 5)->where('role', 'pekerja')->get();

        return view('sekretaris.indexKebaktian', compact(
            'calendarGrid',
            'currentDate',
            'aturanBidang',
            'daftarPembicara',
            'daftarPendoa',
            'daftarMultimedia'
        ));
    }

    public function create()
    {
        return view('sekretaris.createJadwalKebaktian');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal_pelayanan' => 'required|date',
            'jenis_kebaktian'   => 'required|string|max:50',
            'waktu_mulai'       => 'required',
            'waktu_selesai'     => 'required|after:waktu_mulai',
            'lokasi'            => 'nullable|string|max:100',
            'tema'              => 'nullable|string|max:255',
        ]);

        JadwalKebaktian::create([
            'tanggal_pelayanan' => $request->tanggal_pelayanan,
            'jenis_kebaktian'   => $request->jenis_kebaktian,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'lokasi'            => $request->lokasi,
            'tema'              => $request->tema,
            'status'            => 'draft', // default
            'dibuat_oleh'       => Auth::id(),
            'disetujui_oleh'    => null,
        ]);

        return redirect()->route('sekretaris.jadwal.index')
            ->with('success', 'Jadwal kebaktian berhasil dibuat.');
    }

    public function show($id)
    {
        $jadwal = JadwalKebaktian::findOrFail($id);
        return response()->json($jadwal);
    }

    public function edit($id)
    {
        $jadwal = JadwalKebaktian::findOrFail($id);
        return view('sekretaris.editJadwalKebaktian', compact('jadwal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_pelayanan' => 'required|date',
            'jenis_kebaktian'   => 'required|string|max:50',
            'waktu_mulai'       => 'required',
            'waktu_selesai'     => 'required|after:waktu_mulai',
            'lokasi'            => 'nullable|string|max:100',
            'tema'              => 'nullable|string|max:255',
        ]);

        $jadwalLama = JadwalKebaktian::with('tugas.user.bidang')->findOrFail($id);
        $kategori = $jadwalLama->kategoriPenolakan;
        $dampak = $kategori->dampak;

        DB::transaction(function () use ($jadwalLama, $request, $dampak) {
            
            // ========================================
            // CEK TIPE AKSI: EDIT atau RECREATE
            // ========================================
            
            if ($dampak['jadwal'] === 'edit') {
                
                if ($dampak['tugas'] === 'pending') {
                    // =====================================
                    // CASE: UBAH_WAKTU
                    // → RECREATE jadwal baru
                    // → Pengajuan lama dibatalkan (declined)
                    // → Buat pengajuan baru (status: null/pending)
                    // → Tugas lama declined
                    // → Buat tugas baru (status: pending)
                    // =====================================
                    
                    // 1. Set tugas lama jadi declined
                    $jadwalLama->tugas()->update([
                        'status_tugas' => 'declined'
                    ]);
                    
                    // 2. Set pengajuan lama jadi declined
                    PengajuanJadwal::where('id_jadwal', $jadwalLama->id_jadwal)
                        ->update([
                            'status_pengajuan' => 'declined'
                        ]);
                    
                    // 3. Buat jadwal baru
                    $jadwalBaru = JadwalKebaktian::create([
                        'tanggal_pelayanan' => $request->tanggal_pelayanan,
                        'jenis_kebaktian'   => $request->jenis_kebaktian,
                        'waktu_mulai'       => $request->waktu_mulai,
                        'waktu_selesai'     => $request->waktu_selesai,
                        'lokasi'            => $request->lokasi,
                        'tema'              => $request->tema,
                        'status'            => 'draft',
                        'dibuat_oleh'       => Auth::id(),
                        'asal_jadwal'       => $jadwalLama->id_jadwal,
                        'kategori_penolakan_id' => null,
                        'alasan_penolakan' => null,
                        'disetujui_oleh' => null,
                    ]);
                    
                    // 4. Copy semua tugas dengan status pending
                    foreach ($jadwalLama->tugas as $tugas) {
                        Tugas::create([
                            'id_jadwal'    => $jadwalBaru->id_jadwal,
                            'id_user'      => $tugas->id_user,
                            'peran_tugas'  => $tugas->peran_tugas,
                            'status_tugas' => 'pending',
                        ]);
                    }
                    
                    // 5. Buat pengajuan baru dengan status null/pending
                    $bidangYangTerlibat = $jadwalLama->tugas
                        ->filter(fn($t) => $t->user && $t->user->id_bidang)
                        ->pluck('user.id_bidang')
                        ->unique();

                    foreach ($bidangYangTerlibat as $idBidang) {
                        $koordinator = User::where('role', 'koordinator_bidang')
                            ->where('id_bidang', $idBidang)
                            ->first();

                        if ($koordinator) {
                            PengajuanJadwal::create([
                                'id_jadwal'         => $jadwalBaru->id_jadwal,
                                'id_koordinator'    => $koordinator->id_user,
                                'id_bidang'         => $idBidang,
                                'status_pengajuan'  => null, // null untuk menunggu konfirmasi
                                'tanggal_pengajuan' => now(),
                            ]);
                        }
                    }
                    
                    // 6. Update status jadwal lama
                    $jadwalLama->update([
                        'status' => 'declined',
                    ]);
                    
                } else {
                    // =====================================
                    // CASE: UBAH_TEMA, UBAH_LOKASI, UBAH_JENIS
                    // → tugas: "keep" → Edit jadwal, tugas & pengajuan TETAP
                    // =====================================
                    
                    $jadwalLama->update([
                        'tanggal_pelayanan' => $request->tanggal_pelayanan,
                        'jenis_kebaktian'   => $request->jenis_kebaktian,
                        'waktu_mulai'       => $request->waktu_mulai,
                        'waktu_selesai'     => $request->waktu_selesai,
                        'lokasi'            => $request->lokasi,
                        'tema'              => $request->tema,
                        'status'            => 'draft', // kembali ke draft
                        'kategori_penolakan_id' => null,
                        'alasan_penolakan' => null,
                    ]);
                    
                    // Tugas dan pengajuan TIDAK BERUBAH sama sekali
                }
                
            } elseif ($dampak['jadwal'] === 'recreate') {
                // =====================================
                // CASE: ALASAN_LAIN
                // → Recreate jadwal baru KOSONG (tanpa tugas)
                // → Sekretaris harus assign tugas manual
                // =====================================
                
                $jadwalBaru = JadwalKebaktian::create([
                    'tanggal_pelayanan' => $request->tanggal_pelayanan,
                    'jenis_kebaktian'   => $request->jenis_kebaktian,
                    'waktu_mulai'       => $request->waktu_mulai,
                    'waktu_selesai'     => $request->waktu_selesai,
                    'lokasi'            => $request->lokasi,
                    'tema'              => $request->tema,
                    'status'            => 'draft',
                    'dibuat_oleh'       => Auth::id(),
                    'asal_jadwal'       => $jadwalLama->id_jadwal,
                    'kategori_penolakan_id' => null,
                    'alasan_penolakan' => null,
                    'disetujui_oleh' => null,
                ]);

                // TIDAK ada copy tugas (tugas: "reset")
                // TIDAK ada buat pengajuan
                // Sekretaris harus assign tugas baru secara manual

                // Tandai jadwal lama sebagai cancelled
                $jadwalLama->update([
                    'status' => 'cancelled'
                ]);
            }
        });

        $pesan = match(true) {
            $dampak['jadwal'] === 'edit' && $dampak['tugas'] === 'pending' 
                => 'Jadwal baru telah dibuat. Semua pekerja akan dikonfirmasi ulang.',
            $dampak['jadwal'] === 'edit' && $dampak['tugas'] === 'keep' 
                => 'Jadwal berhasil diperbarui.',
            $dampak['jadwal'] === 'recreate' 
                => 'Jadwal baru telah dibuat. Silakan assign tugas pekerja secara manual.',
            default => 'Jadwal telah diproses.',
        };

        return redirect()->route('sekretaris.jadwal.index')
            ->with('success', $pesan);
    }

    public function destroy($id)
    {
        JadwalKebaktian::findOrFail($id)->delete();

        return redirect()->route('sekretaris.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }

    public function publish($id)
    {
        $jadwal = JadwalKebaktian::findOrFail($id);
        
        // Validasi: hanya jadwal yang approved yang bisa dipublish
        if ($jadwal->status !== 'approved') {
            return redirect()->back()->with('error', 'Hanya jadwal yang telah disetujui yang dapat dipublish.');
        }
        
        $jadwal->update(['status' => 'published']);
        
        return redirect()->route('sekretaris.jadwal.index')
            ->with('success', 'Jadwal berhasil dipublish dan dapat dilihat oleh semua pekerja.');
    }

    public function publishedSchedules(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);
        
        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        $jadwals = JadwalKebaktian::with(['tugas.user.bidang'])
            ->where('status', 'published')
            ->whereYear('tanggal_pelayanan', $year)
            ->whereMonth('tanggal_pelayanan', $month)
            ->orderBy('tanggal_pelayanan')
            ->orderBy('waktu_mulai')
            ->get();
        
        return view('jadwal-published', compact('jadwals', 'currentDate'));
    }

    public function exportPDF(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year  = $request->get('year', now()->year);
        
        $jadwals = JadwalKebaktian::with(['tugas.user.bidang'])
            ->where('status', 'published')
            ->whereYear('tanggal_pelayanan', $year)
            ->whereMonth('tanggal_pelayanan', $month)
            ->orderBy('tanggal_pelayanan')
            ->orderBy('waktu_mulai')
            ->get();
        
        $pdf = \PDF::loadView('exports.jadwal-pdf', compact('jadwals', 'month', 'year'));
        
        return $pdf->download('Jadwal-Pelayanan-' . $month . '-' . $year . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        try {
            $month = $request->get('month', now()->month);
            $year  = $request->get('year', now()->year);
            
            return Excel::download(
                new JadwalExport($month, $year), 
                'Jadwal-Pelayanan-' . $month . '-' . $year . '.xlsx'
            );
        } catch (\Exception $e) {
            // Debug: lihat error sebenarnya
            dd([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        } 
    }
}
