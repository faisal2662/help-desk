<?php

namespace App\Http\Controllers;


use Mail;
use Image;
use Socialite;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Notifikasi extends Controller
{

	public function index (){
		return view('pages.notifikasi.index');
	}

	public function datatables (Request $request){
		$notifikasi = DB::table('tb_notifikasi')
		->join('tb_pegawai','tb_notifikasi.id_pegawai','=','tb_pegawai.id_pegawai')
		->where([['tb_notifikasi.id_pegawai', Session::get('id_pegawai')],['tb_notifikasi.delete_notifikasi','N'],['tb_notifikasi.nama_notifikasi', 'Pengaduan '.$_GET['status']]])
		->orderBy('id_notifikasi', 'DESC')
		->get();

		$no = 1;
		foreach($notifikasi as $data){

			$read_notifikasi = "read_notifikasi(".$data->id_notifikasi.");";

			$data->no = $no++;

			if($data->status_notifikasi == 'Delivery'){

				$data->nama_notifikasi = '
					<a href="javascript:;" class="text-'.$data->warna_notifikasi.'" onclick="'.$read_notifikasi.'">
						<span class="badge badge-danger">
						  Baru
						</span>
						<b><i class="bx bxs-coupon"></i> '.str_replace('Holding', 'SLA', $data->nama_notifikasi).'</b>
					</a>
				';

			}else{

				$data->nama_notifikasi = '
					<a href="javascript:;" class="text-'.$data->warna_notifikasi.'" onclick="'.$read_notifikasi.'">
						<b><i class="bx bxs-coupon"></i> '.str_replace('Holding', 'SLA', $data->nama_notifikasi).'</b>
					</a>
				';


			}

			$data->keterangan_notifikasi = $data->keterangan_notifikasi;
			$data->tgl_notifikasi = date('j F Y, H:i', strtotime($data->tgl_notifikasi));

		}

		return DataTables::of($notifikasi)->escapecolumns([])->make(true);
	}

	public function read_notifikasi (Request $request){
		$id = $request->id_notifikasi;

		$notifikasi = DB::table('tb_notifikasi')
		->join('tb_pegawai','tb_notifikasi.id_pegawai','=','tb_pegawai.id_pegawai')
		->where([['tb_notifikasi.id_pegawai', Session::get('id_pegawai')],['tb_notifikasi.delete_notifikasi','N'],['tb_notifikasi.id_notifikasi', $id]])
		->orderBy('id_notifikasi', 'DESC')
		->get();


		if($notifikasi->count() < 1){
            return back();
		}else{

            foreach($notifikasi as $data_notifikasi);

			$where = array(
			  'id_notifikasi' => $data_notifikasi->id_notifikasi,
			  'delete_notifikasi' => 'N',
			);
			$values = array(
			  'status_notifikasi' => 'Read',
			);
			DB::table('tb_notifikasi')->where($where)->update($values);
			return redirect()->to($data_notifikasi->url_notifikasi.'&filter='.$_GET['filter']);

		}
	}

}
