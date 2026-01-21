<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::forUser(Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unreadCount = Notifikasi::forUser(Auth::id())
            ->unread()
            ->count();

        return response()->json([
            'notifikasi' => $notifikasi,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $notifikasi = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', Auth::id())
            ->first();

        if ($notifikasi) {
            $notifikasi->update(['status_baca' => 1]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    public function markAllAsRead()
    {
        Notifikasi::forUser(Auth::id())
            ->unread()
            ->update(['status_baca' => 1]);

        return response()->json(['success' => true]);
    }

    public function delete($id)
    {
        $notifikasi = Notifikasi::where('id_notifikasi', $id)
            ->where('id_user', Auth::id())
            ->first();

        if ($notifikasi) {
            $notifikasi->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}