<?php

namespace App\Http\Controllers;

use App\Models\JadwalKebaktian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JadwalKebaktianController extends Controller
{
    public function index()
    {
        $events = JadwalKebaktian::all()->map(function ($jadwal) {
            return [
                'id'    => $jadwal->id_jadwal,
                'title' => $jadwal->jenis_kebaktian . ' (' . $jadwal->status . ')',
                'start' => $jadwal->tanggal_pelayanan,
            ];
        });

        return view('sekretaris.indexKebaktian', [
            'events' => $events,
        ]);
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

        $jadwal = JadwalKebaktian::findOrFail($id);

        $jadwal->update([
            'tanggal_pelayanan' => $request->tanggal_pelayanan,
            'jenis_kebaktian'   => $request->jenis_kebaktian,
            'waktu_mulai'       => $request->waktu_mulai,
            'waktu_selesai'     => $request->waktu_selesai,
            'lokasi'            => $request->lokasi,
            'tema'              => $request->tema,
        ]);

        return redirect()->route('sekretaris.jadwal.index')
            ->with('success', 'Jadwal kebaktian berhasil diperbarui.');
    }


    public function destroy($id)
    {
        JadwalKebaktian::findOrFail($id)->delete();

        return redirect()->route('sekretaris.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus');
    }
}
