<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawai = DB::table('tb_pegawai')
            ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')], ['tb_pegawai.status_pegawai', 'Aktif']])
            ->get();

        return view('pages.dashboard.index', compact('pegawai'));
    }
}
