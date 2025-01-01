<?php

namespace App\Http\Controllers;

use DB;
use Mail;
use Image;
use Session;
use Socialite;
use DataTables;
use Illuminate\Http\Request;

class LaporanPengaduan extends Controller
{

	public function index (){
		return view('pages.laporan_pengaduan.index');
	}

	public function cetak (Request $request){
		$tgl_awal = $request->tgl_awal;
		$tgl_selesai = $request->tgl_selesai;
		$status = $request->status;
		$klasifikasi = $request->klasifikasi;
		$format = $request->format;

		return view('pages.laporan_pengaduan.cetak', compact('tgl_awal', 'tgl_selesai', 'status', 'klasifikasi', 'format'));
	}

}
