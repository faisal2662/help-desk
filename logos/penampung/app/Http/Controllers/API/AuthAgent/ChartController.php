<?php

namespace App\Http\Controllers\API\AuthAgent;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Utils\ComplaintUtilController;
use Illuminate\Http\Request;
use Auth;
use App\Models\API\Pengaduan;

class ChartController extends Controller
{
    public function getStatusComplaints(Request $request)
    {
        $employee = Auth::user();

        $complaints = Pengaduan::where([
                'id_bagian_kantor_pusat' => $employee->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $employee->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $employee->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereYear('tgl_pengaduan', $request->year ?? date('Y'))
            ->get();

        $complaintUtilController = new ComplaintUtilController();
        return $complaintUtilController->getStatusComplaints($complaints);
    }

    public function getMonthlyComplaints(Request $request)
    {
        $agent = Auth::user();

        $complaints = Pengaduan::where([
                'id_bagian_kantor_pusat' => $agent->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $agent->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $agent->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereYear('tgl_pengaduan', $request->year ?? date('Y'))
            ->get();

        $complaintUtilController = new ComplaintUtilController();
        return $complaintUtilController->getMonthlyComplaints($complaints);
    }
}
