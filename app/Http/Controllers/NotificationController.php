<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        AppNotification::checkLowStock();

        $notifications = AppNotification::where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', Auth::id());
            })
            ->latest()
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(AppNotification $notification)
    {
        $notification->update(['is_read' => true]);
        if ($notification->link) {
            return redirect($notification->link);
        }
        return back();
    }

    public function markAllRead()
    {
        AppNotification::where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', Auth::id());
            })
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    public function unreadCount()
    {
        $count = AppNotification::where(function ($q) {
                $q->whereNull('user_id')->orWhere('user_id', Auth::id());
            })
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
