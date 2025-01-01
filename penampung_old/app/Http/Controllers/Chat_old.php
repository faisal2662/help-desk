<?php

namespace App\Http\Controllers;

use Mail;
use Image;
use Socialite;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Chat extends Controller
{

	public function index (){
		return view('pages.chat.index');
	}

public function riwayat_chat (Request $request){
		$id_kontak = $request->id_kontak;
		return view('pages.chat.riwayat_chat', compact('id_kontak'));
	}
	public function riwayat_chat_friend (Request $request){
		$id_kontak = $request->id_kontak;
		return view('pages.chat.riwayat_chat_friend', compact('id_kontak'));
	}

    public function getPengaduan(Request $request)
    {
        $kontak = Kontak::where('id_kontak', $request->id)->where('delete_kontak', 'N')->get();
        if($kontak->count() > 0)
        {
            foreach ($kontak as $data_kontak);
            $pengaduan = Pengaduan::where('id_pengaduan', $data_kontak->id_pengaduan)->where('delete_pengaduan', 'N')->first();
            return response()->json($pengaduan);
        }
        return response()->json_encode(['status' => 'gagal'], 404);



    }
	public function mulai_chat (Request $request){
		$id_kontak = $request->id_kontak;
		return view('pages.chat.mulai_chat', compact('id_kontak'));
	}
	public function mulai_chat_friend (Request $request){
		$id_kontak = $request->id_kontak;
		return view('pages.chat.mulai_chat_friend', compact('id_kontak'));
	}
	public function kirim_chat (Request $request){
		$kontak = $request->kontak;
		$pegawai = Session::get('id_pegawai');
		$keterangan = nl2br($request->keterangan);

		$cek_pegawai = DB::table('tb_pegawai')
		->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', $pegawai]])
		->get();

		if($cek_pegawai->count() < 1){

		}else{

			foreach($cek_pegawai as $data_cek_pegawai);

			$values = array(
			  'room_chat' => $kontak,
			  'id_pegawai' => $pegawai,
			  'keterangan_chat' => $keterangan,
			  'foto_chat' => 'logos/image.png',
			  'status_chat' => 'Delivery',
			  'tgl_chat' => date('Y-m-d H:i:s'),
			);
			DB::table('tb_chat')->insert($values);

			$cek_kontak = DB::table('tb_kontak')
			->join('tb_pegawai','tb_kontak.created_pengaduan','=','tb_pegawai.id_pegawai')
			->where([['tb_kontak.id_kontak',$kontak],['tb_kontak.delete_kontak','N']])
			->get();

			$dari_kontak = explode(' - ', '0 - 0');

			if($cek_kontak->count() < 1){

			}else{
				foreach($cek_kontak as $data_cek_kontak);

				$dari_kontak = explode(' - ', $data_cek_kontak->dari_kontak);

				$where = array(
				  'id_kontak' => $data_cek_kontak->id_kontak,
				  'delete_kontak' => 'N',
				);
				$values = array(
				  'role_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
				  'keterangan_kontak' => $keterangan,
				  'tgl_kontak' => date('Y-m-d H:i:s'),
				);
				DB::table('tb_kontak')->where($where)->update($values);


				$list_pegawai = DB::table('tb_pegawai')
				->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif']])
				->get();

				if($list_pegawai->count() < 1){

				}else{

					foreach($list_pegawai as $data_list_pegawai){

						$kantor_pegawai = '-';
						$bagian_pegawai = '-';

						if($data_list_pegawai->kantor_pegawai == 'Kantor Pusat'){

					        $unit_kerja = DB::table('tb_kepala_unit_kerja')
					        ->where([['tb_kepala_unit_kerja.id_pegawai', $data_list_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_list_pegawai->kantor_pegawai]])
					        ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
					        ->limit(1)
					        ->get();

					        if($unit_kerja->count() > 0){
					          foreach($unit_kerja as $data_unit_kerja){

					            $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
								->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
								->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_unit_kerja->id_bagian_kantor_pusat)
								->get();
								if($kantor_pusat->count() > 0){
									foreach($kantor_pusat as $data_kantor_pusat);
									$kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
									$bagian_pegawai = 'Semua Bagian';
								}

					          }
					        }else{

					          	$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
								->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
								->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_list_pegawai->id_bagian_kantor_pusat)
								->get();
								if($kantor_pusat->count() > 0){
									foreach($kantor_pusat as $data_kantor_pusat);
									$kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
									$bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
								}

					        }

						}else if($data_list_pegawai->kantor_pegawai == 'Kantor Cabang'){

							$unit_kerja = DB::table('tb_kepala_unit_kerja')
					        ->where([['tb_kepala_unit_kerja.id_pegawai', $data_list_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_list_pegawai->kantor_pegawai]])
					        ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
					        ->limit(1)
					        ->get();

					        if($unit_kerja->count() > 0){
					          foreach($unit_kerja as $data_unit_kerja){

					            $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
								->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
								->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_unit_kerja->id_bagian_kantor_cabang)
								->get();
								if($kantor_cabang->count() > 0){
									foreach($kantor_cabang as $data_kantor_cabang);
									$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
									$bagian_pegawai = 'Semua Bagian';
								}

					          }
					        }else{

					          	$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
								->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
								->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_list_pegawai->id_bagian_kantor_cabang)
								->get();
								if($kantor_cabang->count() > 0){
									foreach($kantor_cabang as $data_kantor_cabang);
									$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
									$bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
								}

					        }

						}else if($data_list_pegawai->kantor_pegawai == 'Kantor Wilayah'){

							$unit_kerja = DB::table('tb_kepala_unit_kerja')
					        ->where([['tb_kepala_unit_kerja.id_pegawai', $data_list_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_list_pegawai->kantor_pegawai]])
					        ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
					        ->limit(1)
					        ->get();

					        if($unit_kerja->count() > 0){
					          foreach($unit_kerja as $data_unit_kerja){

					            $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
								->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
								->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_unit_kerja->id_bagian_kantor_wilayah)
								->get();
								if($kantor_wilayah->count() > 0){
									foreach($kantor_wilayah as $data_kantor_wilayah);
									$kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
									$bagian_pegawai = 'Semua Bagian';
								}

					          }
					        }else{

					          	$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
								->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
								->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_list_pegawai->id_bagian_kantor_wilayah)
								->get();
								if($kantor_wilayah->count() > 0){
									foreach($kantor_wilayah as $data_kantor_wilayah);
									$kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
									$bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
								}

					        }

						}

						if($data_list_pegawai->sebagai_pegawai == 'Mitra/Pelanggan'){
							if($data_cek_kontak->created_pengaduan == $data_list_pegawai->id_pegawai && $data_cek_kontak->dari_kontak == $kantor_pegawai.' - '.$bagian_pegawai){

								if($data_list_pegawai->id_pegawai == Session::get('id_pegawai')){

									$values = array(
									  'id_kontak' => $data_cek_kontak->id_kontak,
									  'id_pegawai' => $data_list_pegawai->id_pegawai,
									  'role_log_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
								  	  'keterangan_log_kontak' => $keterangan,
									  'status_log_kontak' => 'Delivery',
									  'song_log_kontak' => 'Stop',
									  'tgl_log_kontak' => date('Y-m-d H:i:s'),
									);
									DB::table('tb_log_kontak')->insert($values);

								}else{

									$values = array(
									  'id_kontak' => $data_cek_kontak->id_kontak,
									  'id_pegawai' => $data_list_pegawai->id_pegawai,
									  'role_log_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
								  	  'keterangan_log_kontak' => $keterangan,
									  'status_log_kontak' => 'Delivery',
									  'tgl_log_kontak' => date('Y-m-d H:i:s'),
									);
									DB::table('tb_log_kontak')->insert($values);

								}

							}else if($data_cek_kontak->created_pengaduan != $data_list_pegawai->id_pegawai && $dari_kontak[0] == $kantor_pegawai){

								if($data_list_pegawai->level_pegawai == 'Kepala Unit Kerja'){

									if($data_list_pegawai->id_pegawai == Session::get('id_pegawai')){

										$values = array(
										  'id_kontak' => $data_cek_kontak->id_kontak,
										  'id_pegawai' => $data_list_pegawai->id_pegawai,
										  'role_log_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
									  	  'keterangan_log_kontak' => $keterangan,
										  'status_log_kontak' => 'Delivery',
										  'song_log_kontak' => 'Stop',
										  'tgl_log_kontak' => date('Y-m-d H:i:s'),
										);
										DB::table('tb_log_kontak')->insert($values);

									}else{

										$values = array(
										  'id_kontak' => $data_cek_kontak->id_kontak,
										  'id_pegawai' => $data_list_pegawai->id_pegawai,
										  'role_log_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
									  	  'keterangan_log_kontak' => $keterangan,
										  'status_log_kontak' => 'Delivery',
										  'tgl_log_kontak' => date('Y-m-d H:i:s'),
										);
										DB::table('tb_log_kontak')->insert($values);

									}

								}

							}
						}

						if($data_list_pegawai->sebagai_pegawai == 'Petugas' || $data_list_pegawai->sebagai_pegawai == 'Agent'){
							if($data_cek_kontak->kepada_kontak == $kantor_pegawai.' - '.$bagian_pegawai){

								if($data_list_pegawai->id_pegawai == Session::get('id_pegawai')){

									$values = array(
									  'id_kontak' => $data_cek_kontak->id_kontak,
									  'id_pegawai' => $data_list_pegawai->id_pegawai,
									  'role_log_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
								  	  'keterangan_log_kontak' => $keterangan,
									  'status_log_kontak' => 'Delivery',
									  'song_log_kontak' => 'Stop',
									  'tgl_log_kontak' => date('Y-m-d H:i:s'),
									);
									DB::table('tb_log_kontak')->insert($values);

								}else{

									$values = array(
									  'id_kontak' => $data_cek_kontak->id_kontak,
									  'id_pegawai' => $data_list_pegawai->id_pegawai,
									  'role_log_kontak' => $data_cek_pegawai->nama_pegawai.' ('.$data_cek_pegawai->sebagai_pegawai.')',
								  	  'keterangan_log_kontak' => $keterangan,
									  'status_log_kontak' => 'Delivery',
									  'tgl_log_kontak' => date('Y-m-d H:i:s'),
									);
									DB::table('tb_log_kontak')->insert($values);

								}

							}
						}

					}

				}

			}

		}
	}

	public function cek_riwayat_chat (Request $request){
		$log_kontak = DB::table('tb_log_kontak')
		->where([['tb_log_kontak.id_pegawai',Session::get('id_pegawai')],['tb_log_kontak.delete_log_kontak','N'],['tb_log_kontak.ajax_log_kontak','Run']])
		->get();

		if($log_kontak->count() < 1){

			return 0;

		}else{

			foreach($log_kontak as $data_log_kontak){

				$where = array(
				  'id_log_kontak' => $data_log_kontak->id_log_kontak,
				  'delete_log_kontak' => 'N',
				);
				$values = array(
				  'ajax_log_kontak' => 'Close',
				);
				DB::table('tb_log_kontak')->where($where)->update($values);

			}

			return 'new';

		}
	}


	public function suara_chat (Request $request){
		$log_kontak = DB::table('tb_log_kontak')
		->where([['tb_log_kontak.delete_log_kontak','N'],['tb_log_kontak.id_pegawai', Session::get('id_pegawai')],['tb_log_kontak.song_log_kontak','Play']])
		->get();

		if($log_kontak->count() < 1){

			return 0;

		}else{

			foreach($log_kontak as $data_log_kontak){

				$where = array(
				  'id_pegawai' => $data_log_kontak->id_pegawai,
				  'delete_log_kontak' => 'N',
				);
				$values = array(
				  'song_log_kontak' => 'Stop',
				);
				DB::table('tb_log_kontak')->where($where)->update($values);

			}

			return 'Play';

		}
	}


}
