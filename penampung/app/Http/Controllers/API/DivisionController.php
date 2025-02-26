<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Str;
use Log;
use App\Models\API\Divisi;
use App\Models\API\BagianDivisi;

class DivisionController extends Controller
{
    public function getDivisions()
    {
        $divisions = Divisi::where('delete_divisi', 'N')->get();

        return $divisions;
    }

    public function getDivisionSections($id)
    {
        $division = Divisi::find($id);

        if ($division == null) {
            return response()->json([
                'message' => 'Divisi tidak ditemukan.'
            ], 404);
        }

        $sections = BagianDivisi::where([
                'id_divisi' => $id,
                'delete_bagian_divisi' => 'N'
            ])
            ->get();

        return $sections;
    }
}
