<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bidang;

class BidangController extends Controller
{
    public function index()
    {
        $bidangs = Bidang::withCount('users')->orderBy('id_bidang', 'ASC')->get();
        
        return view('admin.bidang-index', compact('bidangs'));
    }

    public function create()
    {
        return view('admin.bidang-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:bidang,nama_bidang',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_bidang.required' => 'Nama bidang wajib diisi',
            'nama_bidang.unique' => 'Nama bidang sudah ada',
        ]);

        Bidang::create([
            'nama_bidang' => $request->nama_bidang,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.bidang.index')
            ->with('success', 'Bidang berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $bidang = Bidang::withCount('users')->findOrFail($id);

        return view('admin.bidang-edit', compact('bidang'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_bidang' => 'required|string|max:255|unique:bidang,nama_bidang,' . $id . ',id_bidang',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_bidang.required' => 'Nama bidang wajib diisi',
            'nama_bidang.unique' => 'Nama bidang sudah ada',
        ]);

        $bidang = Bidang::findOrFail($id);

        $bidang->update([
            'nama_bidang' => $request->nama_bidang,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('admin.bidang.index')
            ->with('success', 'Bidang berhasil diupdate!');
    }

    public function delete($id)
    {
        $bidang = Bidang::withCount('users')->findOrFail($id);

        if ($bidang->users_count > 0) {
            return redirect()->back()
                ->with('error', "Tidak dapat menghapus bidang {$bidang->nama_bidang} karena masih memiliki {$bidang->users_count} pekerja.");
        }

        $bidang->delete();

        return redirect()->back()
            ->with('success', 'Bidang berhasil dihapus!');
    }
}