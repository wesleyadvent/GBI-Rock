<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AkunPekerjaController extends Controller
{
   public function index()
    {
        $koordinator = Auth::user();

        $pekerja = User::with('bidang')
            ->where('role', 'pekerja')
            ->where('id_bidang', $koordinator->id_bidang)
            ->get();

        return view('koordinator.indexPekerja', compact('pekerja'));
    }


    public function create()
    {
    $koordinator = Auth::user();

    return view('koordinator.createAkunPekerja', [
        'bidang' => $koordinator->bidang ? $koordinator->bidang->nama_bidang : '-'
    ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pekerja',
            'status_aktif' => 1,
            'id_bidang' => Auth::user()->id_bidang,
        ]);

        return redirect()->route('koordinator.pekerja.index')->with('success', 'Akun pekerja berhasil dibuat.');
    }

    public function edit($id)
    {
        $user = User::where('id_user', $id)
            ->where('role', 'pekerja')
            ->firstOrFail();

        $koordinator = Auth::user();

        return view('koordinator.editAkunPekerja', [
            'user'   => $user,
            'bidang' => $koordinator->bidang ? $koordinator->bidang->nama_bidang : '-',
            'isEdit' => true
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id_user', $id)->firstOrFail();

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:user,email,' . $user->id_user . ',id_user',
            'password' => 'nullable|min:6',
        ]);

        $user->nama = $request->nama;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('koordinator.pekerja.index')->with('success', 'Akun pekerja berhasil diupdate.');
    }

    public function destroy($id)
    {
        User::where('id_user', $id)->delete();

        return redirect()->route('koordinator.pekerja.index')->with('success', 'Akun pekerja berhasil dihapus.');
    }
}
