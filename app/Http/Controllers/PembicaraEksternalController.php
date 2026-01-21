<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembicaraEksternal;
use App\Models\JadwalKebaktian;

class PembicaraEksternalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'id_jadwal' => 'required|exists:jadwal_kebaktian,id_jadwal',
            'nama_pembicara' => 'required|string|max:100',
            'asal_gereja' => 'nullable|string|max:100',
            'kontak' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string'
        ]);

        $jadwal = JadwalKebaktian::with(['tugas.user'])->findOrFail($request->id_jadwal);
        
        $tugasPembicara = $jadwal->tugas->filter(function($tugas) {
            return $tugas->user && $tugas->user->id_bidang == 2;
        });
        
        if ($tugasPembicara->isNotEmpty()) {
            $statusPembicara = $tugasPembicara->first()->status_tugas;
            
            if (in_array($statusPembicara, ['pending', 'approved', 'published'])) {
                return back()->with('error', 'Tidak dapat menambah pembicara eksternal. Pembicara internal sudah diajukan dengan status: ' . $statusPembicara);
            }
        }

        if ($jadwal->pembicaraEksternal) {
            return back()->with('error', 'Jadwal ini sudah memiliki pembicara eksternal.');
        }

        PembicaraEksternal::create($request->all());

        return back()->with('success', 'Pembicara eksternal berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pembicara' => 'required|string|max:100',
            'asal_gereja' => 'nullable|string|max:100',
            'kontak' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string'
        ]);

        $pembicara = PembicaraEksternal::with(['jadwal.tugas.user'])->findOrFail($id);
        
        $tugasPembicara = $pembicara->jadwal->tugas->filter(function($tugas) {
            return $tugas->user && $tugas->user->id_bidang == 2;
        });
        
        if ($tugasPembicara->isNotEmpty()) {
            $statusPembicara = $tugasPembicara->first()->status_tugas;
            
            if (in_array($statusPembicara, ['pending', 'approved', 'published'])) {
                return back()->with('error', 'Tidak dapat mengedit pembicara eksternal. Pembicara internal sudah diajukan dengan status: ' . $statusPembicara);
            }
        }

        $pembicara->update($request->only([
            'nama_pembicara',
            'asal_gereja',
            'kontak',
            'keterangan'
        ]));

        return back()->with('success', 'Data pembicara berhasil diperbarui');
    }

    public function destroy($id)
    {
        $pembicara = PembicaraEksternal::with(['jadwal.tugas.user'])->findOrFail($id);
        
        $tugasPembicara = $pembicara->jadwal->tugas->filter(function($tugas) {
            return $tugas->user && $tugas->user->id_bidang == 2;
        });
        
        if ($tugasPembicara->isNotEmpty()) {
            $statusPembicara = $tugasPembicara->first()->status_tugas;
            
            if (in_array($statusPembicara, ['pending', 'approved', 'published'])) {
                return back()->with('error', 'Tidak dapat menghapus pembicara eksternal. Pembicara internal sudah diajukan dengan status: ' . $statusPembicara);
            }
        }

        $pembicara->delete();

        return back()->with('success', 'Pembicara eksternal berhasil dihapus');
    }
}