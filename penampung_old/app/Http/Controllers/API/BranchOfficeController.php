<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\KantorCabang;
use App\Models\API\BagianKantorCabang;

class BranchOfficeController extends Controller
{
    public function getBranchOffices()
    {
        $branchOffices = KantorCabang::where('delete_kantor_cabang', 'N')->get();

        return $branchOffices;
    }

    public function getBranchOfficeSections($id)
    {
        $headOffice = KantorCabang::find($id);

        if ($headOffice == null) {
            return response()->json([
                'message' => 'Kantor Cabang tidak ditemukan.'
            ], 404);
        }

        $sections = BagianKantorCabang::where([
                'id_kantor_cabang' => $id,
                'delete_bagian_kantor_cabang' => 'N'
            ])
            ->get();

        return $sections;
    }
}
