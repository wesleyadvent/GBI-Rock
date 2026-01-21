<?php

namespace App\Http\Controllers;

use App\Models\JadwalKebaktian;
use App\Models\PengajuanJadwal;
use App\Models\User;
use App\Models\Tugas;
use App\Models\Notifikasi;
use App\Models\JadwalKebaktianHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SekretarisController extends Controller
{
    private function aturanPekerjaPerBidang()
    {
        return [
            1 => ['min' => 2, 'max' => null, 'nama' => 'Usher'],        // usher min 2
            2 => ['min' => 1, 'max' => 1, 'nama' => 'Pembicara'],       // pembicara max 1
            3 => ['min' => 2, 'max' => 2, 'nama' => 'Pendoa'],          // pendoa max 2
            4 => ['min' => 2, 'max' => null, 'nama' => 'PW'],           // pw min 2
            5 => ['min' => 2, 'max' => null, 'nama' => 'Multimedia'],   // multimedia min 2
        ];
    }

    public function index(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $currentDate = Carbon::createFromDate($year, $month, 1);

        $jadwalRaw = JadwalKebaktian::where('status', 'draft')
            ->with([
                'tugas' => function($q) {
                    $q->where('status_tugas', 'approved')
                    ->with('user.bidang');
                },
                'pengajuan' => function ($q) {
                    $q->where('status_pengajuan', 'pending')
                        ->whereHas('koordinator', function ($q2) {
                            $q2->whereIn('id_bidang', [1, 4]);
                        });
                },
                'pembicaraEksternal'
            ])
            ->whereHas('pengajuan', function ($q) {
                $q->where('status_pengajuan', 'pending')
                    ->whereHas('koordinator', function ($q2) {
                        $q2->whereIn('id_bidang', [1, 4]);
                    });
            })
            ->get()
            ->groupBy(fn ($item) =>
                Carbon::parse($item->tanggal_pelayanan)->format('Y-m-d')
            );

        $daftarPembicara = User::where('id_bidang', 2)->where('role', 'pekerja')->get();
        $daftarPendoa = User::where('id_bidang', 3)->where('role', 'pekerja')->get();
        $daftarMultimedia = User::where('id_bidang', 5)->where('role', 'pekerja')->get();

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

        return view('sekretaris.indexKebaktian', [
            'currentDate' => $currentDate,
            'calendarGrid' => collect($calendarGrid)->chunk(7),
            'daftarPembicara' => $daftarPembicara,
            'daftarPendoa' => $daftarPendoa,
            'daftarMultimedia' => $daftarMultimedia,
            'aturanBidang' => $this->aturanPekerjaPerBidang()
        ]);
    }

    public function assignPekerja(Request $request)
    {
        $request->validate([
            'id_jadwal'   => 'required|exists:jadwal_kebaktian,id_jadwal',
            'id_user'     => 'required|exists:user,id_user',
            'peran_tugas' => 'required|string|max:100',
        ]);

        $user = User::findOrFail($request->id_user);
        $id_bidang = $user->id_bidang;
        $aturan = $this->aturanPekerjaPerBidang()[$id_bidang] ?? null;

        if (!$aturan) {
            return back()->with('error', 'Bidang tidak valid.');
        }

        if (
            Tugas::where('id_jadwal', $request->id_jadwal)
                ->where('id_user', $request->id_user)
                ->exists()
        ) {
            return back()->with('error', 'Pekerja ini sudah diajukan pada jadwal ini.');
        }

        if ($aturan['max'] !== null) {
            $jumlahSaatIni = Tugas::where('id_jadwal', $request->id_jadwal)
                ->whereHas('user', fn ($q) => $q->where('id_bidang', $id_bidang))
                ->count();

            if ($jumlahSaatIni >= $aturan['max']) {
                return back()->with('error', "Maksimal {$aturan['max']} pekerja untuk bidang {$aturan['nama']}.");
            }
        }

        $jadwalBaru = JadwalKebaktian::findOrFail($request->id_jadwal);

        $jadwalBentrok = Tugas::where('id_user', $request->id_user)
            ->whereHas('jadwalKebaktian', function ($q) use ($jadwalBaru) {
                $q->where('tanggal_pelayanan', $jadwalBaru->tanggal_pelayanan)
                ->where(function ($time) use ($jadwalBaru) {
                    $time->where('waktu_mulai', '<', $jadwalBaru->waktu_selesai)
                        ->where('waktu_selesai', '>', $jadwalBaru->waktu_mulai);
                });
            })
            ->exists();

        if ($jadwalBentrok) {
            return back()->with(
                'error',
                'Pekerja ini sudah terdaftar pada kebaktian lain dengan waktu yang bentrok.'
            );
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

        return back()->with('success', 'Pekerja berhasil diajukan!');
    }

    public function declineBidang(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal',
            'id_bidang' => 'required|integer',
            'alasan_penolakan' => 'required|string'
        ]);

        $id_jadwal = $request->id_jadwal;
        $id_bidang = $request->id_bidang;

        PengajuanJadwal::where('id_jadwal', $id_jadwal)
            ->where('status_pengajuan', 'pending')
            ->whereHas('koordinator', fn($q) => $q->where('id_bidang', $id_bidang))
            ->update([
                'status_pengajuan' => 'declined',
                'alasan_penolakan' => $request->alasan_penolakan
            ]);

        JadwalKebaktian::where('id_jadwal', $id_jadwal)
            ->update(['status' => 'draft']);

        return back()->with('success', 'Pengajuan bidang berhasil ditolak.');
    }

    public function approveBidang(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal',
            'id_bidang' => 'required|integer'
        ]);

        $id_jadwal = $request->id_jadwal;
        $id_bidang = $request->id_bidang;

        PengajuanJadwal::where('id_jadwal', $id_jadwal)
            ->where('status_pengajuan', 'pending')
            ->whereHas('koordinator', fn($q) => $q->where('id_bidang', $id_bidang))
            ->update(['status_pengajuan' => 'approved']);

        Tugas::where('id_jadwal', $id_jadwal)
            ->whereHas('user', fn($q) => $q->where('id_bidang', $id_bidang))
            ->where('status_tugas', 'pending')
            ->update(['status_tugas' => 'approved']);

        $masihPending = PengajuanJadwal::where('id_jadwal', $id_jadwal)
            ->where('status_pengajuan', 'pending')
            ->exists();

        // // Jika semua bidang sudah diproses → publish jadwal
        // if (!$masihPending) {
        //     JadwalKebaktian::where('id_jadwal', $id_jadwal)
        //         ->update(['status' => 'approved']);
        // }

        return back()->with('success', 'Pengajuan bidang berhasil disetujui.');
    }

    public function approve(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal'
        ]);

        $jadwal = JadwalKebaktian::with(['tugas.user', 'pembicaraEksternal'])->findOrFail($request->id_jadwal);

        if ($jadwal->status !== 'draft') {
            return back()->with('error', 'Jadwal sudah diproses.');
        }

        $aturan = $this->aturanPekerjaPerBidang();

        foreach ($aturan as $id_bidang => $rule) {
            $jumlahApproved = $jadwal->tugas
                ->where('status_tugas', 'approved')
                ->filter(fn ($t) => $t->user && $t->user->id_bidang == $id_bidang)
                ->count();

            if ($id_bidang == 2 && !empty($jadwal->pembicaraEksternal)) {
                $jumlahApproved += 1;
            }

            if ($jumlahApproved < $rule['min']) {
                return back()->with(
                    'error',
                    "Bidang {$rule['nama']} belum memenuhi minimal ({$jumlahApproved}/{$rule['min']})"
                );
            }
        }

        $jadwal->update([
            'status' => 'pending'
        ]); 

        $penatua = User::where('role', 'penatua')->get();

        foreach ($penatua as $p) {
            Notifikasi::create([
                'id_user' => $p->id_user,
                'pesan'   => 'Ada jadwal pelayanan yang menunggu persetujuan Anda (' .
                    Carbon::parse($jadwal->tanggal_pelayanan)->format('d M Y') . ')',
                'tipe' => 'approval_jadwal',
                'status_baca' => 0,
            ]);
        }

        return back()->with('success', 'Jadwal berhasil diajukan ke Penatua.');
    }

    public function destroy($id)
    {
        $tugas = Tugas::findOrFail($id);

        if ($tugas->status_tugas !== 'pending') {
            return back()->with('error', 'Tugas sudah dikonfirmasi dan tidak bisa dibatalkan.');
        }

        $tugas->delete();

        return back()->with('success', 'Pengajuan pelayanan berhasil dibatalkan.');
    }

    public function ajukanUlang($id)
    {
        $jadwalLama = JadwalKebaktian::with([
            'tugas',
            'kategoriPenolakan'
        ])->findOrFail($id);

        if ($jadwalLama->status !== 'declined') {
            return back()->with('error', 'Jadwal ini tidak dapat diajukan ulang.');
        }

        $dampak = $jadwalLama->kategoriPenolakan?->dampak ?? [];

        DB::transaction(function () use ($jadwalLama, $dampak) {
            $jadwalBaru = JadwalKebaktian::create([
                'tanggal_pelayanan' => $jadwalLama->tanggal_pelayanan,
                'waktu_mulai'       => $jadwalLama->waktu_mulai,
                'waktu_selesai'     => $jadwalLama->waktu_selesai,
                'tema'              => $jadwalLama->tema,
                'jenis_kebaktian'   => $jadwalLama->jenis_kebaktian,
                'status'            => 'draft',
                'dibuat_oleh'       => Auth::id(),
                'asal_jadwal'       => $jadwalLama->id_jadwal,
            ]);

            if (($dampak['tugas'] ?? null) === 'pending') {
                foreach ($jadwalLama->tugas as $tugas) {
                    if ($tugas->status_tugas === 'approved') {
                        Tugas::create([
                            'id_jadwal'   => $jadwalBaru->id_jadwal,
                            'id_user'     => $tugas->id_user,
                            'peran_tugas' => $tugas->peran_tugas,
                            'status_tugas'=> 'pending',
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Jadwal draft baru berhasil dibuat.');
    }

    public function duplicateWithPending($id)
    {
        $jadwalLama = JadwalKebaktian::with(['tugas.user'])->findOrFail($id);

        if ($jadwalLama->status !== 'declined') {
            return back()->with('error', 'Hanya jadwal yang ditolak yang bisa diduplikasi ulang.');
        }

        DB::transaction(function () use ($jadwalLama) {
            $jadwalBaru = JadwalKebaktian::create([
                'tanggal_pelayanan' => $jadwalLama->tanggal_pelayanan,
                'waktu_mulai'       => $jadwalLama->waktu_mulai,
                'waktu_selesai'     => $jadwalLama->waktu_selesai,
                'tema'              => $jadwalLama->tema,
                'jenis_kebaktian'   => $jadwalLama->jenis_kebaktian,
                'status'            => 'draft',
                'dibuat_oleh'       => Auth::id(),
                'asal_jadwal'       => $jadwalLama->id_jadwal,
                'kategori_penolakan_id' => null,
                'alasan_penolakan' => 'testing',
                'disetujui_oleh' => null,
            ]);

            $bidangYangTerlibat = $jadwalLama->tugas
                ->where('status_tugas', 'approved')
                ->pluck('user.id_bidang')
                ->unique();
            
            foreach ($bidangYangTerlibat as $idBidang) {      
                $koordinator = User::where('role', 'koordinator')
                    ->where('id_bidang', $idBidang)
                    ->first();

                if ($koordinator) {
                    PengajuanJadwal::create([
                        'id_jadwal'        => $jadwalBaru->id_jadwal,
                        'id_koordinator'   => $koordinator->id_user,
                        'status_pengajuan' => 'pending',
                        'tanggal_pengajuan'=> now(),
                    ]);
                }
            }

            foreach ($jadwalLama->tugas as $tugasLama) {
                if ($tugasLama->status_tugas === 'approved') {
                    $tugasLama->update(['status_tugas' => 'declined']);

                    if (class_exists(JadwalKebaktianHistory::class)) {
                        JadwalKebaktianHistory::create([
                            'id_jadwal' => $jadwalLama->id_jadwal,
                            'status' => 'declined',
                            'alasan' => 'Tugas ini akan diminta konfirmasi ulang setelah jadwal diduplikasi',
                            'oleh_user' => Auth::id()
                        ]);
                    }

                    Tugas::create([
                        'id_jadwal'   => $jadwalBaru->id_jadwal,
                        'id_user'     => $tugasLama->id_user,
                        'peran_tugas' => $tugasLama->peran_tugas,
                        'status_tugas'=> 'pending',
                    ]);

                    Notifikasi::create([
                        'id_user' => $tugasLama->id_user,
                        'pesan'   => "Konfirmasi ulang diperlukan untuk pelayanan sebagai {$tugasLama->peran_tugas} pada " .
                                    Carbon::parse($jadwalBaru->tanggal_pelayanan)->format('d M Y') .
                                    " (jadwal telah diperbaiki)",
                        'tipe' => 'permintaan_pelayanan',
                        'status_baca' => 0,
                    ]);
                }
            }

            if (class_exists(JadwalKebaktianHistory::class)) {
                JadwalKebaktianHistory::create([
                    'id_jadwal' => $jadwalBaru->id_jadwal,
                    'status' => 'draft',
                    'alasan' => 'Jadwal diduplikasi dari jadwal yang ditolak (ID: ' . $jadwalLama->id_jadwal . ') - semua personil perlu konfirmasi ulang',
                    'oleh_user' => Auth::id()
                ]);
            }
        });

        return redirect()
            ->route('sekretaris.pengajuan.index')
            ->with('success', 'Jadwal berhasil diduplikasi. Semua personil perlu konfirmasi ulang ketersediaan mereka.');
    }
}