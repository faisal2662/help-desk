<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\API\KantorPusat;
use App\Models\API\BagianKantorPusat;

use Log;
class HeadOfficeController extends Controller
{
    public function getHeadOffices()
    {
        $headOffices = KantorPusat::where('delete_kantor_pusat', 'N')->get();

        return $headOffices;
    }

    public function getHeadOfficeSections($id)
    {
        $headOffice = KantorPusat::find($id);

        if ($headOffice == null) {
            return response()->json([
                'message' => 'Kantor Pusat tidak ditemukan.'
            ], 404);
        }

        $sections = BagianKantorPusat::where([
                'id_kantor_pusat' => $id,
                'delete_bagian_kantor_pusat' => 'N'
            ])
            ->get();

        return $sections;
    }
}
