<?php

namespace App\Http\Controllers\API\Utils;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ComplaintUtilController extends Controller
{
    public function getFilterPendingComplaint($complaint)
    {
        return in_array($complaint->status_pengaduan, ['Pending', 'Checker']);
    }

    public function getPendingComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterPendingComplaint($complaint);
        });
    }

    public function getFilterApprovedComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'Approve';
    }

    public function getApprovedComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterApprovedComplaint($complaint);
        });
    }

    public function getFilterReadComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'Read';
    }

    public function getReadComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterReadComplaint($complaint);
        });
    }

    public function getFilterProgressComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'On Progress';
    }

    public function getProgressComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterProgressComplaint($complaint);
        });
    }

    public function getFilterMovedComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'Moving';
    }

    public function getMovedComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterMovedComplaint($complaint);
        });
    }

    public function getFilterHoldComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'Holding';

    }

    public function getHoldComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterHoldComplaint($complaint);
        });
    }

    public function getFilterLateComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'Late';
    }

    public function getLateComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterLateComplaint($complaint);
        });
    }

    public function getFilterDoneComplaint($complaint)
    {
        return $complaint->status_pengaduan == 'Finish';
    }

    public function getDoneComplaints($complaints)
    {
        return $complaints->filter(function ($complaint) {
            return $this->getFilterDoneComplaint($complaint);
        });
    }

    public function getStatusComplaints($complaints)
    {
        $pendingComplaints = $this->getPendingComplaints($complaints);
        $approvedComplaints = $this->getApprovedComplaints($complaints);
        $readComplaints = $this->getReadComplaints($complaints);
        $progressComplaints = $this->getProgressComplaints($complaints);
        $movedComplaints = $this->getMovedComplaints($complaints);
        $holdComplaints = $this->getHoldComplaints($complaints);
        $lateComplaints = $this->getLateComplaints($complaints);
        $doneComplaints = $this->getDoneComplaints($complaints);

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
            'read' => $readComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
            'progress' => $progressComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
            'moved' => $movedComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
            'hold' => $holdComplaints
                ->filter(function ($complaint) {
                    return date('Y-m-d', strtotime($complaint->tgl_pengaduan)) == date('Y-m-d');
                })
                ->count(),
            'late' => $lateComplaints
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
            'read' => $readComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
            'progress' => $progressComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
            'moved' => $movedComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
            'hold' => $holdComplaints
                ->filter(function ($complaint) {
                    return date('Y-W', strtotime($complaint->tgl_pengaduan)) == date('Y-W');
                })
                ->count(),
            'late' => $lateComplaints
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
            'read' => $readComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
            'progress' => $progressComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
            'moved' => $movedComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
            'hold' => $holdComplaints
                ->filter(function ($complaint) {
                    return date('Y-m', strtotime($complaint->tgl_pengaduan)) == date('Y-m');
                })
                ->count(),
            'late' => $lateComplaints
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
            'read' => $readComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
            'progress' => $progressComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
            'moved' => $movedComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
            'hold' => $holdComplaints
                ->filter(function ($complaint) {
                    return date('Y', strtotime($complaint->tgl_pengaduan)) == date('Y');
                })
                ->count(),
            'late' => $lateComplaints
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
            'read' => $readComplaints->count(),
            'progress' => $progressComplaints->count(),
            'moved' => $movedComplaints->count(),
            'hold' => $holdComplaints->count(),
            'late' => $lateComplaints->count(),
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

    public function getMonthlyComplaints($complaints)
    {
        $monthlyPending = [];
        $monthlyApproved = [];
        $monthlyRead = [];
        $monthlyProgress = [];
        $monthlyMoved = [];
        $monthlyHold = [];
        $monthlyLate = [];
        $monthlyDone = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyPending[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterPendingComplaint($complaint);
                })
                ->count();

            $monthlyApproved[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterApprovedComplaint($complaint);
                })
                ->count();

            $monthlyRead[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterReadComplaint($complaint);
                })
                ->count();

            $monthlyProgress[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterProgressComplaint($complaint);
                })
                ->count();

            $monthlyMoved[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterMovedComplaint($complaint);
                })
                ->count();

            $monthlyHold[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterHoldComplaint($complaint);
                })
                ->count();

            $monthlyLate[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterLateComplaint($complaint);
                })
                ->count();

            $monthlyDone[] = $complaints
                ->filter(function ($complaint) use ($i) {
                    return date('n', strtotime($complaint->tgl_pengaduan)) == $i && $this->getFilterDoneComplaint($complaint);
                })
                ->count();
        }

        return [
            'pending' => $monthlyPending,
            'approved' => $monthlyApproved,
            'read' => $monthlyRead,
            'progress' => $monthlyProgress,
            'moved' => $monthlyMoved,
            'hold' => $monthlyHold,
            'late' => $monthlyLate,
            'done' => $monthlyDone,
        ];
    }
}
