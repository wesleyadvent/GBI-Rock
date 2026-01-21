<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Bidang;
use App\Models\JadwalKebaktian;
use App\Models\Tugas;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status_aktif', 1)->count();

        $totalJadwal = DB::table('jadwal_kebaktian')->count() ?? 0;
        $jadwalBulanIni = DB::table('jadwal_kebaktian')
            ->whereMonth('tanggal_pelayanan', now()->month)
            ->whereYear('tanggal_pelayanan', now()->year)
            ->count();

        
        $totalPelayanan = DB::table('tugas')
            ->where('status_tugas', 'approved')
            ->count();

        $pelayananMingguIni = DB::table('tugas')
            ->join('jadwal_kebaktian', 'tugas.id_jadwal', '=', 'jadwal_kebaktian.id_jadwal')
            ->where('tugas.status_tugas', 'approved')
            ->whereBetween('jadwal_kebaktian.tanggal_pelayanan', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->count();

        $pelayananPerBidang = [];

        for ($i = 1; $i <= 5; $i++) {
            $pelayananPerBidang[$i] = [
                'pekerja' => User::where('id_bidang', $i)
                    ->where('role', 'pekerja')
                    ->count(),

                'pelayanan' => DB::table('tugas')
                    ->join('user', 'tugas.id_user', '=', 'user.id_user')
                    ->where('user.id_bidang', $i)
                    ->where('tugas.status_tugas', 'approved')
                    ->count()
            ];
        }
        
        $onlineUsers = User::where('status_aktif', 1)->count();
        $jadwalHariIni = DB::table('jadwal_kebaktian')
            ->whereDate('tanggal_pelayanan', today())
            ->count();

        return view('dashboard.admin', compact(
            'totalUsers',
            'activeUsers',
            'totalJadwal',
            'jadwalBulanIni',
            'totalPelayanan',
            'pelayananMingguIni',
            'pelayananPerBidang',
            'onlineUsers',
            'jadwalHariIni'
        ));
    }

    public function index()
    {
        $users = User::orderBy('id_user', 'ASC')->get();

        $bidang = [
            1 => 'Usher',
            2 => 'Pembicara',
            3 => 'Pendoa',
            4 => 'PW',
            5 => 'Multimedia'
        ];

        return view('admin.user-index', compact('users', 'bidang'));
    }

    public function create()
    {
        $roles = [
            'admin' => 'Admin',
            'sekretaris' => 'Sekretaris',
            'koordinator_bidang' => 'Koordinator Bidang',
            'penatua' => 'Penatua',
            'pekerja' => 'Pekerja',
        ];

        $bidang = [
            1 => 'Usher',
            2 => 'Pembicara',
            3 => 'Pendoa',
            4 => 'PW',
            5 => 'Multimedia'
        ];

        return view('admin.user-create', compact('roles', 'bidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'email'       => 'required|email|unique:user,email',
            'password'    => 'required|min:6',
            'role'        => 'required',
            'id_bidang'   => 'nullable|required_if:role,pekerja|required_if:role,koordinator_bidang',
        ]);

        User::create([
            'nama'        => $request->nama,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => $request->role,
            'status_aktif'=> 1,
            'id_bidang'   => $request->id_bidang ?? null,
        ]);

        return redirect()->back()->with('success', 'User berhasil dibuat!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $roles = [
            'admin' => 'Admin',
            'sekretaris' => 'Sekretaris',
            'koordinator_bidang' => 'Koordinator Bidang',
            'penatua' => 'Penatua',
            'pekerja' => 'Pekerja',
        ];

        $bidang = [
            1 => 'Usher',
            2 => 'Pembicara',
            3 => 'Pendoa',
            4 => 'PW',
            5 => 'Multimedia'
        ];

        return view('admin.user-edit', compact('user', 'roles', 'bidang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'        => 'required|string|max:255',
            'email'       => 'required|email|unique:user,email,' . $id . ',id_user',
            'role'        => 'required',
            'id_bidang'   => 'nullable|required_if:role,pekerja|required_if:role,koordinator_bidang',
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'nama'      => $request->nama,
            'email'     => $request->email,
            'role'      => $request->role,
            'id_bidang' => $request->id_bidang ?? null,
        ]);

        return redirect()->route('admin.user.index')
            ->with('success', 'User berhasil diupdate.');
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Password baru wajib diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        $user = User::findOrFail($id);
        
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->back()
            ->with('success', 'Password berhasil diperbarui!');
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        $user->status_aktif = $user->status_aktif ? 0 : 1;
        $user->save();

        $status = $user->status_aktif ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Status user berhasil $status.");
    }

    public function pembicaraEksternal(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        
        $currentDate = Carbon::create($year, $month, 1);
        
        $startDate = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endDate = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);
        
        $jadwalKebaktian = JadwalKebaktian::with([
            'tugas.user',
            'pembicaraEksternal',
            'pengajuan'
        ])
        ->whereBetween('tanggal_pelayanan', [$startDate, $endDate])
        ->orderBy('tanggal_pelayanan')
        ->orderBy('waktu_mulai')
        ->get();
        
        $calendarGrid = [];
        $currentWeek = [];
        $tempDate = $startDate->copy();
        
        while ($tempDate <= $endDate) {
            $dayJadwal = $jadwalKebaktian->filter(function ($jadwal) use ($tempDate) {
                return $jadwal->tanggal_pelayanan->isSameDay($tempDate);
            });
            
            $currentWeek[] = [
                'date' => $tempDate->copy(),
                'isCurrentMonth' => $tempDate->month == $currentDate->month,
                'jadwal' => $dayJadwal
            ];
            
            if ($tempDate->isSaturday()) {
                $calendarGrid[] = $currentWeek;
                $currentWeek = [];
            }
            
            $tempDate->addDay();
        }
        
        if (!empty($currentWeek)) {
            $calendarGrid[] = $currentWeek;
        }
        
        return view('admin.pembicara-eksternal.index', compact(
            'calendarGrid',
            'currentDate'
        ));
    }
}