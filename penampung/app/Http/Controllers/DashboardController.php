<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('NamaPosisi')
            ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')], ['tb_pegawai.status_pegawai', 'Aktif']])
            ->get();
            if(auth()->user()->sebagai_pegawai == 'Administrator'){
                $pengaduan = Pengaduan::where('delete_pengaduan', 'N')->get();
            }else {
                $pengaduan = Pengaduan::where('id_pegawai', auth()->user()->id_pegawai)->where('delete_pengaduan', 'N')->get();
            }
        return view('pages.dashboard.index', compact('pegawai', 'pengaduan'));
    }
}
