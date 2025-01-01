<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\KantorWilayah;
use App\Models\API\BagianKantorWilayah;

class RegionalOfficeController extends Controller
{
    public function getRegionalOffices()
    {
        $regionalOffices = KantorWilayah::where('delete_kantor_wilayah', 'N')->get();

        return $regionalOffices;
    }

    public function getRegionalOfficeSections($id)
    {
        $regionalOffice = KantorWilayah::find($id);

        if ($regionalOffice == null) {
            return response()->json([
                'message' => 'Kantor Wilayah tidak ditemukan.'
            ], 404);
        }

        $sections = BagianKantorWilayah::where([
                'id_kantor_wilayah' => $id,
                'delete_bagian_kantor_wilayah' => 'N'
            ])
            ->get();

        return $sections;
    }
}
