<?php
// app/Http/Controllers/NotificationController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Halaman daftar notifikasi
     */
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * API: ambil 5 notifikasi terbaru (untuk dropdown bell)
     */
    public function dropdown(): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notifications = $user->notifications()
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn($n) => [
                'id'         => $n->id,
                'message'    => $n->data['message'] ?? 'Notifikasi',
                'url'        => $n->data['url'] ?? '#',
                'item_name'  => $n->data['item_name'] ?? '',
                'quantity'   => $n->data['quantity'] ?? 0,
                'min_stock'  => $n->data['min_stock'] ?? 0,
                'read_at'    => $n->read_at,
                'created_at' => $n->created_at->diffForHumans(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark 1 notifikasi sebagai read
     */
    public function markAsRead(string $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();

            $url = $notification->data['url'] ?? route('notifications.index');
            return redirect($url);
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark semua notifikasi sebagai read
     */
    public function markAllAsRead(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi telah dibaca.');
    }

    /**
     * Hapus notifikasi
     */
    public function destroy(string $id): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->delete();
        }

        return back()->with('success', 'Notifikasi dihapus.');
    }

    /**
     * Hapus semua notifikasi
     */
    public function destroyAll(): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->notifications()->delete();

        return back()->with('success', 'Semua notifikasi dihapus.');
    }
}