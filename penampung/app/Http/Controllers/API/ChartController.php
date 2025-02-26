<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\Pengaduan;

class ChartController extends Controller
{
    public function getStatusComplaints()
    {
        $complaints = Pengaduan::with('know', 'deadline', 'done', 'answers')
            ->where('delete_pengaduan', 'N')
            ->get();

        $pendingComplaints = $complaints->filter(function ($complaint) {
                return $complaint->know == null;
            });

        $approvedComplaints = $complaints->filter(function ($complaint) {
                return $complaint->know != null && $complaint->deadline == null;
            });

        $doneComplaints = $complaints->filter(function ($complaint) {
                return $complaint->done != null;
            });

        $todayComplaints = [
            'pending' => $pendingComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
            'approved' => $approvedComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
            'done' => $doneComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
        ];

        $weekComplaints = [
            'pending' => $pendingComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
            'approved' => $approvedComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
            'done' => $doneComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
        ];

        $monthComplaints = [
            'pending' => $pendingComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
            'approved' => $approvedComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
            'done' => $doneComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
        ];

        $yearComplaints = [
            'pending' => $pendingComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
            'approved' => $approvedComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
            'done' => $doneComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
        ];

        $totalComplaints = [
            'pending' => $pendingComplaints->count(),
            'approved' => $approvedComplaints->count(),
            'done' => $doneComplaints->count(),
        ];

        return [
            'today' => $todayComplaints,
            'this_week' => $weekComplaints,
            'this_month' => $monthComplaints,
            'this_year' => $yearComplaints,
            'total' => $totalComplaints,
        ];
    }

    public function getMonthlyComplaints()
    {
        $complaints = Pengaduan::with('know', 'deadline', 'done', 'answers')
            ->where('delete_pengaduan', 'N')
            ->whereYear('tgl_pengaduan', '2021')
            ->get();

        $monthlyPending = [];
        $monthlyApproved = [];
        $monthlyDone = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPending[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $complaint->know == null;
                })
                ->count();

            $monthlyApproved[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $complaint->know != null && $complaint->deadline == null;
                })
                ->count();

            $monthlyDone[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $complaint->done != null;
                })
                ->count();
        }

        return [
            'pending' => $monthlyPending,
            'approved' => $monthlyApproved,
            'done' => $monthlyDone,
        ];
    }
}
