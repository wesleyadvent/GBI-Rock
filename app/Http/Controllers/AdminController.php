<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
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

        return redirect()->back()->with('success', 'Status user berhasil diubah.');
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
}
