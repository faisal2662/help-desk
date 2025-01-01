<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\API\Notifikasi;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $employee = Auth::user();

        $notifications = Notifikasi::where([
                'id_pegawai' => $employee->id_pegawai,
                'delete_notifikasi' => 'N'
            ])
            ->orderBy('id_notifikasi', 'DESC')
            ->get();

        return $notifications;
    }

    public function getNotification($id)
    {
        $employee = Auth::user();

        $notification = Notifikasi::where([
                'id_notifikasi' => $id,
                'id_pegawai' => $employee->id_pegawai,
                'delete_notifikasi' => 'N'
            ])
            ->first();

        if ($notification == null) {
            return response()->json([
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        return $notification;
    }

    public function markRead($id)
    {
        $employee = Auth::user();

        $notification = Notifikasi::where([
                'id_notifikasi' => $id,
                'id_pegawai' => $employee->id_pegawai,
                'delete_notifikasi' => 'N'
            ])
            ->first();

        if ($notification == null) {
            return response()->json([
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        $notification->update([
            'status_notifikasi' => 'Read'
        ]);
        $notification->refresh();

        return $notification;
    }
}
