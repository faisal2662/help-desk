<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Log;
use Exception;
use App\Models\API\Pengaduan;
use App\Models\API\Notifikasi;

class SendNotificationController extends Controller
{
    public static function complaintCreatedToUnit($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendBroadcast(
                    'customer-head-'.$complaint->id_bagian_kantor_pusat.'-regional-'.$complaint->id_bagian_kantor_wilayah.'-branch-'.$complaint->id_bagian_kantor_cabang,
                    'Pengaduan Baru',
                    'Ada Pengaduan Baru dari Mitra/Pelanggan.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintCreatedToHead($complaintId)
    {
        try {
            $complaint = Pengaduan::with([
                    'employee.headOfficeSection',
                    'employee.regionalOfficeSection',
                    'employee.branchOfficeSection',
                ])
                ->find($complaintId);
            if ($complaint != null) {
                switch ($complaint->employee->kantor_pegawai) {
                    case 'Kantor Pusat':
                        $type = 'head';
                        $officeId = $complaint->employee->headOfficeSection->id_kantor_pusat;
                        break;
                    case 'Kantor Cabang':
                        $type = 'branch';
                        $officeId = $complaint->employee->branchOfficeSection->id_kantor_cabang;
                        break;
                    default:
                        $type = 'regional';
                        $officeId = $complaint->employee->regionalOfficeSection->id_kantor_wilayah;
                }
                FirebaseMessagingController::sendBroadcast(
                    'customer-'.$type.'-'.$officeId,
                    'Pengaduan Baru',
                    'Ada Pengaduan Baru dari Mitra/Pelanggan.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintCreatedToAgent($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendBroadcast(
                    'agent-head-'.$complaint->id_bagian_kantor_pusat.'-regional-'.$complaint->id_bagian_kantor_wilayah.'-branch-'.$complaint->id_bagian_kantor_cabang,
                    'Pengaduan Baru',
                    'Ada Pengaduan Baru dari Mitra/Pelanggan.',
                    [
                        'screen' => '/agent-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintApprovedUnitToCustomer($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendMessageToEmployee(
                    $complaint->id_pegawai,
                    'Pengaduan Approve',
                    'Pengaduan telah di approve oleh Mitra/Pelanggan.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintApprovedHeadToCustomer($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendMessageToEmployee(
                    $complaint->id_pegawai,
                    'Pengaduan Approve',
                    'Pengaduan telah di approve oleh Mitra/Pelanggan.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintBeReadToCustomer($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendMessageToEmployee(
                    $complaint->id_pegawai,
                    'Pengaduan Read',
                    'Pengaduan sudah di read oleh Agent.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintAnsweredToCustomer($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendMessageToEmployee(
                    $complaint->id_pegawai,
                    'Pengaduan On Progress',
                    'Pengaduan sedang dalam proses.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintHoldingToCustomer($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendMessageToEmployee(
                    $complaint->id_pegawai,
                    'Pengaduan Holding',
                    'Pengaduan tertunda sementara.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintTransferToCustomer($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendMessageToEmployee(
                    $complaint->id_pegawai,
                    'Pengaduan Moving',
                    'Pengaduan telah dialihkan.',
                    [
                        'screen' => '/customer-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintCreatedToBranchAndDivision($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendBroadcast(
                    'head-'.$complaint->id_bagian_kantor_pusat.'-regional-'.$complaint->id_bagian_kantor_wilayah.'-branch-'.$complaint->id_bagian_kantor_cabang,
                    'Pengaduan Baru',
                    'Ada Pengaduan Baru dari Mitra/Pelanggan.',
                    [
                        'screen' => '/agent-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintRepliedToAgent($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendBroadcast(
                    'agent-head-'.$complaint->id_bagian_kantor_pusat.'-regional-'.$complaint->id_bagian_kantor_wilayah.'-branch-'.$complaint->id_bagian_kantor_cabang,
                    'Pengaduan Dijawab',
                    'Jawaban Anda telah direspon oleh Mitra/Pelanggan.',
                    [
                        'screen' => '/agent-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }

    public static function complaintDoneToAgent($complaintId)
    {
        try {
            $complaint = Pengaduan::find($complaintId);
            if ($complaint != null) {
                FirebaseMessagingController::sendBroadcast(
                    'agent-head-'.$complaint->id_bagian_kantor_pusat.'-regional-'.$complaint->id_bagian_kantor_wilayah.'-branch-'.$complaint->id_bagian_kantor_cabang,
                    'Pengaduan Selesai',
                    'Pengaduan telah diselesaikan oleh Mitra/Pelanggan.',
                    [
                        'screen' => '/agent-complaint-detail',
                        'id' => $complaintId
                    ]
                );
            }
        } catch (Exception $error) {
            Log::error($error);
        }
    }
}
