<?php

namespace App\Http\Controllers\API\AuthOfficer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\API\Pengaduan;
use App\Models\API\Status;

class ComplaintController extends Controller
{
    public function getComplaints()
    {
        $officer = Auth::user();

        $complaints = Pengaduan::with([
                'employee',
                'headOfficeSection.headOffice',
                'regionalOfficeSection.regionalOffice',
                'branchOfficeSection.branchOffice',
                'knows',
                'reads.employee',
                'done',
                'answers'
            ])
            ->where([
                'id_bagian_kantor_pusat' => $officer->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $officer->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $officer->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->orderBy('tgl_pengaduan', 'DESC')
            ->get();

        return $complaints;
    }

    public function getComplaint($id)
    {
        $officer = Auth::user();

        $complaint = Pengaduan::with([
                'employee.headOfficeSection.headOffice',
                'employee.regionalOfficeSection.regionalOffice',
                'employee.branchOfficeSection.branchOffice',
                'employee.headOffice',
                'employee.regionalOffice',
                'employee.branchOffice',
                'headOfficeSection.headOffice',
                'regionalOfficeSection.regionalOffice',
                'branchOfficeSection.branchOffice',
                'knows',
                'reads.employee',
                'done',
                'attachments' => function ($query) {
                    $query->where('delete_lampiran', 'N');
                },
                'answers.employee',
                'answers.responses'
            ])
            ->where([
                'id_pengaduan' => $id,
                'id_bagian_kantor_pusat' => $officer->id_bagian_kantor_pusat,
                'id_bagian_kantor_wilayah' => $officer->id_bagian_kantor_wilayah,
                'id_bagian_kantor_cabang' => $officer->id_bagian_kantor_cabang,
                'delete_pengaduan' => 'N'
            ])
            ->first();

        if ($complaint == null) {
            return response()->json([
                'message' => 'Pengaduan tidak ditemukan.'
            ], 404);
        }

        return $complaint;
    }
}
