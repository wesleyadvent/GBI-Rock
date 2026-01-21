<?php

namespace App\Http\Controllers;

use App\Models\JadwalKebaktian;
use App\Models\User;
use App\Models\Tugas;
use App\Models\PengajuanJadwal;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class KoordinatorBidangController extends Controller
{
    private function minimalPekerjaPerBidang()
    {
        return [
            1 => 2, // usher
            2 => 1, // pembicara
            3 => 2, // pendoa
            4 => 2, // pw
            5 => 2, // multimedia
        ];
    }

    private function statusPengajuanKoordinator($idJadwal)
    {
        return PengajuanJadwal::where('id_jadwal', $idJadwal)
            ->where('id_koordinator', Auth::id())
            ->value('status_pengajuan');
    }

    public function dashboard()
    {
        $idBidang = Auth::user()->id_bidang;
        $idKoordinator = Auth::id();

        $totalPekerja = User::where('id_bidang', $idBidang)
            ->where('role', 'pekerja')
            ->count();

        $pekerjaAktif = User::where('id_bidang', $idBidang)
            ->where('role', 'pekerja')
            ->where('status_aktif', 1)
            ->count();

        $jadwalPending = PengajuanJadwal::where('id_koordinator', $idKoordinator)
            ->where('status_pengajuan', 'pending')
            ->count();

        $jadwalApproved = PengajuanJadwal::where('id_koordinator', $idKoordinator)
            ->where('status_pengajuan', 'approved')
            ->count();

        $jadwalMendatang = JadwalKebaktian::whereHas('pengajuan', function($q) use ($idKoordinator) {
                $q->where('id_koordinator', $idKoordinator);
            })
            ->where('tanggal_pelayanan', '>=', now())
            ->with([
                'tugas' => function($q) use ($idBidang) {
                    $q->whereHas('user', function($u) use ($idBidang) {
                        $u->where('id_bidang', $idBidang);
                    });
                },
                'pengajuan' => function($q) use ($idKoordinator) {
                    $q->where('id_koordinator', $idKoordinator);
                }
            ])
            ->orderBy('tanggal_pelayanan', 'asc')
            ->take(5)
            ->get();

        $pekerjaTerbaru = User::where('id_bidang', $idBidang)
            ->where('role', 'pekerja')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        return view('dashboard.koordinator', compact(
            'totalPekerja',
            'pekerjaAktif',
            'jadwalPending',
            'jadwalApproved',
            'jadwalMendatang',
            'pekerjaTerbaru'
        ));
    }


    public function index(Request $request)
    {
        $id_bidang_user = Auth::user()->id_bidang;

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $currentDate = Carbon::createFromDate($year, $month, 1);

        $jadwalRaw = JadwalKebaktian::whereIn('status', ['draft', 'pending', 'declined' , 'published'])
        ->with([
            'tugas' => function ($query) use ($id_bidang_user) {
                $query->whereHas('user', function ($q) use ($id_bidang_user) {
                    $q->where('id_bidang', $id_bidang_user);
                })->with('user');
            },
            'pengajuan' => function ($q) {
                $q->where('id_koordinator', Auth::id());
            }
        ])
        ->orderBy('tanggal_pelayanan')
        ->get()
        ->groupBy(fn ($item) =>
            Carbon::parse($item->tanggal_pelayanan)->format('Y-m-d')
        );

        $daftarPekerja = User::where('id_bidang', $id_bidang_user)
            ->where('role', 'pekerja')
            ->get();

        $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar   = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $calendarGrid = [];
        $iterDate = $startOfCalendar->copy();

        while ($iterDate <= $endOfCalendar) {
            $calendarGrid[] = [
                'date' => $iterDate->copy(),
                'isCurrentMonth' => $iterDate->month == $currentDate->month,
                'jadwal' => $jadwalRaw->get($iterDate->format('Y-m-d')) ?? collect()
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
            'id_jadwal'   => 'required|exists:jadwal_kebaktian,id_jadwal',
            'id_user'     => 'required|exists:user,id_user',
            'peran_tugas' => 'required|string|max:100',
        ]);

        $pengajuanStatus = PengajuanJadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_koordinator', Auth::id())
            ->value('status_pengajuan');

        if ($pengajuanStatus && in_array($pengajuanStatus, ['approved', 'declined'])) {
            return back()->with('error', 'Tidak dapat menambah personil karena pengajuan sudah ' . 
                ($pengajuanStatus === 'approved' ? 'disetujui' : 'ditolak') . '.');
        }

        $pengajuan = PengajuanJadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_koordinator', Auth::id())
            ->first();

        // if (!$pengajuan) {
        //     return back()->with(
        //         'error',
        //         'Jadwal ini belum diajukan ke sekretaris. Silakan ajukan terlebih dahulu.'
        //     );
        // }

        if (Tugas::where('id_jadwal', $request->id_jadwal)
                ->where('id_user', $request->id_user)
                ->exists()) {
            return back()->with('error', 'Pekerja ini sudah diajukan pada jadwal ini.');
        }

        $tugas = Tugas::create([
            'id_jadwal'   => $request->id_jadwal,
            'id_user'     => $request->id_user,
            'peran_tugas' => $request->peran_tugas,
            'status_tugas'=> 'pending',
        ]);

        $tugas->load('jadwalKebaktian');

        Notifikasi::create([
            'id_user' => $request->id_user,
            'pesan'   => "Anda diminta melayani sebagai {$request->peran_tugas} pada " .
                Carbon::parse($tugas->jadwalKebaktian->tanggal_pelayanan)->format('d M Y'),
            'tipe' => 'permintaan_pelayanan',
            'status_baca' => 0,
        ]);

        return back()->with('success', 'Permintaan berhasil dikirim ke pekerja!');
    }

    public function editPeranTugas(Request $request, $id)
    {
        $request->validate([
            'peran_tugas' => 'required|string|max:100',
        ]);

        $tugas = Tugas::findOrFail($id);

        $pengajuanStatus = PengajuanJadwal::where('id_jadwal', $tugas->id_jadwal)
            ->where('id_koordinator', Auth::id())
            ->value('status_pengajuan');

        if ($pengajuanStatus && $pengajuanStatus !== 'pending') {
            return back()->with('error', 'Tidak dapat mengedit karena pengajuan sudah ' . $pengajuanStatus);
        }

        $tugas->update(['peran_tugas' => $request->peran_tugas]);

        return back()->with('success', 'Peran tugas berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);

        $pengajuanStatus = PengajuanJadwal::where('id_jadwal', $tugas->id_jadwal)
            ->where('id_koordinator', Auth::id())
            ->value('status_pengajuan');

        if ($pengajuanStatus && $pengajuanStatus !== 'pending') {
            return back()->with('error', 'Tidak dapat menghapus karena pengajuan sudah ' . $pengajuanStatus);
        }

        $tugas->delete();

        return back()->with('success', 'Pengajuan pelayanan berhasil dibatalkan.');
    }

    public function ajukanKeSekretaris(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal',
        ]);

        $jadwal = JadwalKebaktian::with(['tugas.user'])
            ->findOrFail($request->id_jadwal);

        $id_bidang = Auth::user()->id_bidang;
        $minimal   = $this->minimalPekerjaPerBidang()[$id_bidang] ?? 1;

        $approvedCount = $jadwal->tugas
            ->where('status_tugas', 'approved')
            ->filter(fn ($t) => $t->user && $t->user->id_bidang == $id_bidang)
            ->count();

        if ($approvedCount < $minimal) {
            return back()->with('error', 'Personil approved belum memenuhi minimal (' . $approvedCount . '/' . $minimal . ')');
        }

        $existingPengajuan = PengajuanJadwal::where('id_jadwal', $jadwal->id_jadwal)
            ->where('id_koordinator', Auth::id())
            ->first();

        if ($existingPengajuan && $existingPengajuan->status_pengajuan === 'pending') {
            return back()->with('error', 'Jadwal ini sudah diajukan dan menunggu persetujuan.');
        }

        if ($existingPengajuan) {
            $existingPengajuan->update([
                'status_pengajuan' => 'pending',
                'tanggal_pengajuan' => now(),
                'alasan_penolakan' => null,
            ]);
        } else {
            PengajuanJadwal::create([
                'id_koordinator'   => Auth::id(),
                'id_jadwal'        => $jadwal->id_jadwal,
                'id_bidang'        => Auth::user()->id_bidang,
                'status_pengajuan' => 'pending',
                'tanggal_pengajuan'=> now(),
            ]);
        }

        $sekretaris = User::where('role', 'sekretaris')->get();
        foreach ($sekretaris as $s) {
            Notifikasi::create([
                'id_user' => $s->id_user,
                'pesan'   => "Pengajuan jadwal {$jadwal->jenis_kebaktian} (" .
                    Carbon::parse($jadwal->tanggal_pelayanan)->format('d M Y') .
                    ") menunggu persetujuan.",
                'tipe' => 'pengajuan_jadwal',
                'status_baca' => 0,
            ]);
        }

        return back()->with('success', 'Jadwal berhasil diajukan ke sekretaris.');
    }

    public function batalkanPengajuan(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal',
        ]);

        $pengajuan = PengajuanJadwal::where('id_jadwal', $request->id_jadwal)
            ->where('id_koordinator', Auth::id())
            ->where('status_pengajuan', 'pending')
            ->first();

        if (!$pengajuan) {
            return back()->with('error', 'Tidak ada pengajuan pending untuk dibatalkan.');
        }

        $pengajuan->delete();

        return back()->with('success', 'Pengajuan ke sekretaris berhasil dibatalkan.');
    }

    public function jadwalBelumDiajukan(Request $request)
    {
        $idBidang = Auth::user()->id_bidang;
        $idKoordinator = Auth::id();

        $jadwal = JadwalKebaktian::where('status', 'draft')
            ->whereDoesntHave('pengajuan', function ($q) use ($idKoordinator) {
                $q->where('id_koordinator', $idKoordinator);
            })
            ->with([
                'tugas' => function ($q) use ($idBidang) {
                    $q->whereHas('user', fn ($u) => $u->where('id_bidang', $idBidang))
                    ->with('user');
                }
            ])
            ->orderBy('tanggal_pelayanan')
            ->get();

        return view('koordinator.timPelayanan.belum_diajukan', compact('jadwal'));
    }

}