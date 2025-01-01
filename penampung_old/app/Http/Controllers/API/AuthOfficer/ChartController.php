<?php

namespace App\Http\Controllers\API\AuthOfficer;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\Utils\ComplaintUtilController;
use Illuminate\Http\Request;
use Auth;
use App\Models\API\Pengaduan;

class ChartController extends Controller
{
    public function getStatusComplaints(Request $request)
    {
        $officer = Auth::user();

        $complaints = Pengaduan::where([
                'id_bagian_kantor_pusat' => $officer->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $officer->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $officer->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereYear('tgl_pengaduan', $request->year ?? date('Y'))
            ->get();

        $complaintUtilController = new ComplaintUtilController();
        return $complaintUtilController->getStatusComplaints($complaints);
    }

    public function getMonthlyComplaints(Request $request)
    {
        $officer = Auth::user();

        $complaints = Pengaduan::where([
                'id_bagian_kantor_pusat' => $officer->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $officer->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $officer->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->whereYear('tgl_pengaduan', $request->year ?? date('Y'))
            ->get();

        $complaintUtilController = new ComplaintUtilController();
        return $complaintUtilController->getMonthlyComplaints($complaints);
    }
}
