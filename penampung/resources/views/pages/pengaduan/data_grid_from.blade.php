@php
    if (!function_exists('time_elapsed_string')) {
        function time_elapsed_string($datetime, $full = false)
        {
            $now = new DateTime();
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = [
                'y' => 'Tahun',
                'm' => 'Bulan',
                'w' => 'Minggu',
                'd' => 'Hari',
                'h' => 'Jam',
                'i' => 'Menit',
                's' => 'Detik',
            ];

            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v;
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) {
                $string = array_slice($string, 0, 1);
            }

            return $string ? implode(', ', $string) . ' Berlalu' : 'Baru Saja';
        }
    }

@endphp

<?php

// $session_pegawai = DB::table('tb_pegawai')
//     ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
//     ->get();
// if ($session_pegawai->count() > 0) {
//     foreach ($session_pegawai as $data_session_pegawai);

//     if ($data_session_pegawai->level_pegawai == 'Administrator') {
//         if ($_GET['filter'] == 'Semua') {
//             if (isset($_GET['search'])) {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->whereRaw(
//                         '
// 							tb_pengaduan.nama_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.status_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%"
// 						',
//                     )
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             } else {
//                 $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->paginate(12);
//             }
//         } else {
//             if (isset($_GET['search'])) {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                     ->whereRaw(
//                         '
// 							tb_pengaduan.nama_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.status_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%"
// 						',
//                     )
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             } else {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             }
//         }
//     } elseif ($data_session_pegawai->level_pegawai == 'Staff') {
//         if ($_GET['filter'] == 'Semua') {
//             if (isset($_GET['search'])) {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->whereRaw(
//                         '
// 							tb_pengaduan.nama_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.status_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%"
// 						',
//                     )
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             } else {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             }
//         } else {
//             if (isset($_GET['search'])) {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                     ->whereRaw(
//                         '
// 							tb_pengaduan.nama_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.status_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%"
// 						',
//                     )
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             } else {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             }
//         }
//     } elseif ($data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
//         if ($_GET['filter'] == 'Semua') {
//             if (isset($_GET['search'])) {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->whereRaw(
//                         '
// 							tb_pengaduan.nama_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.status_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%"
// 						',
//                     )
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             } else {
//                 if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
//                     echo 'pusat';die;
//                     $pengaduan = \App\Models\Pengaduan::with(['BagianKantorPusat'])
//                         ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                         ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         ->paginate(12);
//                 } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0 ){

//                     $pengaduan = \App\Models\Pengaduan::with(['BagianKantorcabang'])
//                         ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                         ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         ->paginate(12);
//                 } else {
//                     echo 'wilayah ';die;
//                     $pengaduan = \App\Models\Pengaduan::with(['BagianKantorWilayah'])
//                         ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                         ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                         ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                         ->paginate(12);
//                 }

//                 dd($pengaduan);
//             }
//         } else {
//             if (isset($_GET['search'])) {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                     ->whereRaw(
//                         '
// 							tb_pengaduan.nama_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.keterangan_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.klasifikasi_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%" Or
// 							tb_pengaduan.status_pengaduan LIKE "%' .
//                             $_GET['search'] .
//                             '%"
// 						',
//                     )
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             } else {
//                 $pengaduan = DB::table('tb_pengaduan')
//                     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
//                     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
//                     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
//                     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
//                     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
//                     ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
//                     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
//                     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
//                     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
//                     ->paginate(12);
//             }
//         }
//     }
// else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff'){

// 	if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Kepala Unit Kerja'){

// 		$unit_kerja = DB::table('tb_kepala_unit_kerja')
// 		->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
// 		->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
// 		->limit(1)
// 		->get();

// 		if($unit_kerja->count() < 1){

// 			if($_GET['filter'] == 'Semua'){

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
// 							tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
// 							tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
// 							tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->where('tb_pengaduan.status_pengaduan','!=','Pending')
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}else{

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
// 							tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
// 							tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
// 							tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->where('tb_pengaduan.status_pengaduan','!=','Pending')
// 				->where('tb_pengaduan.status_pengaduan','=', $_GET['filter'])
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}

// 		}else{

// 			if($_GET['filter'] == 'Semua'){

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai IN (
// 								SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							) And
// 							tb_pegawai.id_bagian_kantor_pusat IN (
// 								SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							) And
// 							tb_pegawai.id_bagian_kantor_cabang IN (
// 								SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							) And
// 							tb_pegawai.id_bagian_kantor_wilayah IN (
// 								SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							)
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->where('tb_pengaduan.status_pengaduan','!=','Pending')
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}else{

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai IN (
// 								SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							) And
// 							tb_pegawai.id_bagian_kantor_pusat IN (
// 								SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							) And
// 							tb_pegawai.id_bagian_kantor_cabang IN (
// 								SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							) And
// 							tb_pegawai.id_bagian_kantor_wilayah IN (
// 								SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
// 								delete_kepala_unit_kerja = "N" And
// 								id_pegawai = "'.$data_session_pegawai->id_pegawai.'"
// 							)
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->where('tb_pengaduan.status_pengaduan','!=','Pending')
// 				->where('tb_pengaduan.status_pengaduan','=', $_GET['filter'])
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}

// 		}

// 	}else{

// 		if($_GET['filter'] == 'Semua'){

// 			if(isset($_GET['search'])){

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
// 							tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
// 							tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
// 							tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->whereRaw('
// 					tb_pengaduan.nama_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.keterangan_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.klasifikasi_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.status_pengaduan LIKE "%'.$_GET['search'].'%"
// 				')
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}else{

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
// 							tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
// 							tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
// 							tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}

// 		}else{

// 			if(isset($_GET['search'])){

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
// 							tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
// 							tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
// 							tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->where('tb_pengaduan.status_pengaduan','=', $_GET['filter'])
// 				->whereRaw('
// 					tb_pengaduan.nama_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.keterangan_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.klasifikasi_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.status_pengaduan LIKE "%'.$_GET['search'].'%"
// 				')
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}else{

// 				$pengaduan = DB::table('tb_pengaduan')
// 				->whereRaw('
// 					tb_pengaduan.id_pegawai IN (
// 						Select
// 							tb_pegawai.id_pegawai
// 						From
// 							tb_pegawai
// 						Where
// 							tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
// 							tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
// 							tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
// 							tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
// 					)
// 				')
// 				->where('tb_pengaduan.delete_pengaduan','=','N')
// 				->where('tb_pengaduan.status_pengaduan','=', $_GET['filter'])
// 				->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 				->paginate(12);

// 			}

// 		}

// 	}

// }
// else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff'){

// 	if($_GET['filter'] == 'Semua'){

// 		if(isset($_GET['search'])){

// 			$pengaduan = DB::table('tb_pengaduan')
// 			->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
// 			->where('tb_pengaduan.delete_pengaduan','=','N')
// 			->whereRaw('
// 					tb_pengaduan.nama_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.keterangan_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.klasifikasi_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.status_pengaduan LIKE "%'.$_GET['search'].'%"
// 				')
// 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 			->paginate(12);

// 		}else{

// 			$pengaduan = DB::table('tb_pengaduan')
// 			->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
// 			->where('tb_pengaduan.delete_pengaduan','=','N')
// 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 			->paginate(12);

// 		}

// 	}else{

// 		if(isset($_GET['search'])){

// 			$pengaduan = DB::table('tb_pengaduan')
// 			->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
// 			->where('tb_pengaduan.delete_pengaduan','=','N')
// 			->where('tb_pengaduan.status_pengaduan','=', $_GET['filter'])
// 			->whereRaw('
// 					tb_pengaduan.nama_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.keterangan_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.klasifikasi_pengaduan LIKE "%'.$_GET['search'].'%" Or
// 					tb_pengaduan.status_pengaduan LIKE "%'.$_GET['search'].'%"
// 				')
// 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 			->paginate(12);

// 		}else{

// 			$pengaduan = DB::table('tb_pengaduan')
// 			->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
// 			->where('tb_pengaduan.delete_pengaduan','=','N')
// 			->where('tb_pengaduan.status_pengaduan','=', $_GET['filter'])
// 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
// 			->paginate(12);

// 		}

// 	}

// }

$status_pengaduan = [
    'Pending' => 'warning',
    'Checked' => 'warning',
    'Approve' => 'info',
    'Read' => 'info',
    'Holding' => 'danger',
    'Moving' => 'danger',
    'On Progress' => 'primary',
    'Solved' => 'primary',
    'Late' => 'danger',
    'Finish' => 'success',
];
$klasifikasi_pengaduan = [
    'Rendah' => 'info',
    'Menengah' => 'warning',
    'Tinggi' => 'danger',
];
// }

?>

<?php if($pengaduan->count() < 1){

    ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <center>
                    <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                    <p>Data saat ini tidak ditemukan.</p>
                </center>
            </div>
        </div>
        <p>&nbsp;</p>
    </div>
</div>

<?php }else{ ?>

<div class="row">

    <?php
    $status_klasifikasi = [
        'High' => 'danger',
        'Medium' => 'warning',
        'Low' => 'info',
    ];
    ?>

    <?php
    foreach($pengaduan as $data_pengaduan){
      
				$jawaban = DB::table('tb_jawaban')
				->join('tb_pegawai','tb_pegawai.id_pegawai','=','tb_jawaban.id_pegawai')
				->where([['tb_jawaban.delete_jawaban','N'],['tb_jawaban.id_pengaduan', $data_pengaduan->id_pengaduan]])
				->orderBy('tb_jawaban.id_jawaban', 'DESC')
				->get();

				$tanggapan = DB::table('tb_tanggapan')
				->join('tb_jawaban','tb_jawaban.id_jawaban','=','tb_tanggapan.id_jawaban')
				->where([['tb_tanggapan.delete_tanggapan','N'],['tb_jawaban.id_pengaduan', $data_pengaduan->id_pengaduan],['tb_jawaban.delete_jawaban','N']])
				->get();

				$lampiran = DB::table('tb_lampiran')
				->where([['tb_lampiran.delete_lampiran','N'],['tb_lampiran.id_pengaduan', $data_pengaduan->id_pengaduan]])
				->orderBy('tb_lampiran.id_lampiran','ASC')
				->get();

				// get data pegawai
				$data_pegawai = DB::table('tb_pegawai')
                ->join('tb_posisi_pegawai', 'tb_pegawai.id_posisi_pegawai', '=', 'tb_posisi_pegawai.id_posisi_pegawai')
				->where('tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai)
				->first();
                $kantor_pegawai = '-';
                $bagian_pegawai = '-';
                    
            $kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
            $kantorWilayah  = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', 'N')->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')->toArray();

  
				if(!is_null($data_pegawai)){


					if($data_pegawai->kantor_pegawai == 'Kantor Pusat'){
                        $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
						->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
						->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
						->get();
						if($kantor_pusat->count() > 0){
                            foreach($kantor_pusat as $data_kantor_pusat);
							$kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
							$bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
						}
                      

					}else if(array_key_exists($data_pegawai->kantor_pegawai, $kantorCabang)){

						$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
						->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
						->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
						->get();
						if($kantor_cabang->count() > 0){
							foreach($kantor_cabang as $data_kantor_cabang);
							$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
							$bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
						}

					}else if(array_key_exists($data->kantor_pegawai, $kantorWilayah)){

						$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
						->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
						->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
						->get();
						if($kantor_wilayah->count() > 0){
							foreach($kantor_wilayah as $data_kantor_wilayah);
							$kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
							$bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
						}

					}

				}
				// end get data pegawai

				// kantor bagian pengaduan
				$kantor_pengaduan = '-';
				$bagian_pengaduan = '-';

                if($data_pengaduan->kantor_pengaduan == 'Kantor Pusat'){

                    	$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                    	->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
                    	->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
                    	->get();
                    	if($kantor_pusat->count() > 0){
                    		foreach($kantor_pusat as $data_kantor_pusat);
                    		$kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
                    		$bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    	}
                    // $kantor_pusat = DB::table('tb_kantor_pusat')
                    //     ->join('tb_bagian_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
                    //     ->where('tb_kantor_pusat.id_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
                    //     ->get();
                    //     if($kantor_pusat->count() > 0){
                    //         foreach($kantor_pusat as $data_kantor_pusat);
                    //         $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
                    //         $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    //     }

                    }else if($data_pengaduan->kantor_pengaduan == 'Kantor Cabang'){
                        
                    	$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                    	->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
                    	->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
                    	->get();
                    	if($kantor_cabang->count() > 0){
                    		foreach($kantor_cabang as $data_kantor_cabang);
                    		$kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
                    		$bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    	}
                    // $kantor_cabang = DB::table('tb_kantor_cabang')
                    //     ->join('tb_bagian_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
                    //     ->where('tb_kantor_cabang.id_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
                    //     ->get();
                    //     if($kantor_cabang->count() > 0){
                    //         foreach($kantor_cabang as $data_kantor_cabang);
                    //         $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
                    //         $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    //     }

                    }else if($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah'){

                    	$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                    	->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
                    	->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
                    	->get();
                    	if($kantor_wilayah->count() > 0){
                    		foreach($kantor_wilayah as $data_kantor_wilayah);
                    		$kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
                    		$bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    	}
                    // $kantor_wilayah = DB::table('tb_kantor_wilayah')
                    //     ->join('tb_bagian_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
                    //     ->where('tb_kantor_wilayah.id_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
                    //     ->get();
                    //     if($kantor_wilayah->count() > 0){
                    //         foreach($kantor_wilayah as $data_kantor_wilayah);
                    //         $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
                    //         $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    //     }

                    }
				// end kantor bagian pengaduan

			?>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
           
                        <b>
                            <img src="<?= asset('logos/avatar.png') ?>"
                                style="width: 20px;height: 20px;border-radius: 100%;">
                            <?= htmlspecialchars($data_pegawai->employee_name) ?>
                        </b>
                 
                </div>
                <p>
                    <strong>Unit Kerja :</strong> <?= htmlspecialchars($kantor_pegawai) ?> <br>
                    <strong> Unit Bagian Kerja : </strong><?= htmlspecialchars($bagian_pegawai) ?>
                </p>
                <hr style="border-style: dashed;">
                <p>
                    <strong>Kode Pengaduan :</strong>
                    P<?= date('y') ?>-0000<?= $data_pengaduan->id_pengaduan ?>
                </p>
                <p>
                    <strong>Kepada :</strong> <?= htmlspecialchars($kantor_pengaduan) ?> -
                    <?= htmlspecialchars($bagian_pengaduan) ?>
                </p>
                <p>
                    @if ($data_pengaduan->kategori_pengaduan)
                        <strong>Kategori :</strong> {{ $data_pengaduan->kategori_pengaduan }}
                    @else
                        <strong>Kategori :</strong> -
                    @endif
                </p>
                <p>
                    @if ($data_pengaduan->jenis_produk)
                        <strong>Jenis Produk :</strong>
                        {{ $data_pengaduan->jenis_produk . ' - ' . $data_pengaduan->sub_jenis_produk }}
                        @else
                        <strong>Jenis Produk :</strong> -
                    @endif
                </p>
                <p>
                    <a href="?filter=<?= $_GET['filter'] ?>&view=<?= $data_pengaduan->id_pengaduan ?>"
                        class="text-info">
                        <b><i class='bx bxs-coupon'></i> <?= htmlspecialchars($data_pengaduan->nama_pengaduan) ?></b>
                    </a>
                </p>
                <p>
                    <strong>Deskripsi :</strong> <br>
                    <?= $data_pengaduan->keterangan_pengaduan ?>
                </p>
                @if ($data_pengaduan->sla_pengaduan)
                    <p>
                        <strong>SLA :</strong>
                        {{ \Carbon\Carbon::parse($data_pengaduan->sla_pengaduan)->translatedFormat('l, j F Y') }}
                    </p>
                @else 
                    <p>
                        <strong> SLA :</strong>
                    </p>
                @endif
                @if ($data_pengaduan->klasifikasi_pengaduan)
                    <p>
                        <strong>Klasifikasi :</strong> <b
                            class="text-<?= $status_klasifikasi[$data_pengaduan->klasifikasi_pengaduan] ?>"><?= $data_pengaduan->klasifikasi_pengaduan ?></b>
                    </p>
                @else
                    <p>
                        <strong> Klasifikasi :</strong> -</b>
                    </p>
                @endif
                <?php if($lampiran->count() > 0){ ?>
                <p>
                <ol>
                    <?php foreach($lampiran as $data_lampiran){ ?>
                    <li><a href="<?= url($data_lampiran->file_lampiran) ?>" target="_blank">Lampiran</a></li>
                    <?php } ?>
                </ol>
                </p>
                <?php } ?>

                <p>
                    <strong>Status :</strong>
                    <span class="badge badge-<?= $status_pengaduan[$data_pengaduan->status_pengaduan] ?>">

                        <?php if($data_pengaduan->status_pengaduan == "Late"){ ?>

                        <?= str_replace(['Holding', 'Hold'], ['Pengaduan SLA', 'Pengaduan SLA'], $data_pengaduan->status_pengaduan) ?>
                        <?php
                        if ($jawaban->count() < 1) {
                        } else {
                            foreach ($jawaban as $data_jawaban);
                        
                            echo '(' . time_elapsed_string($data_jawaban->durasi_sla_jawaban) . ')';
                        }
                        ?>

                        <?php }else{ ?>

                        <?= str_replace(['Holding', 'Hold'], ['Pengaduan SLA', 'Pengaduan SLA'], $data_pengaduan->status_pengaduan) ?>

                        <?php } ?>

                    </span>
                </p>
                <p>
                    <i class='bx bx-time'></i> <?= time_elapsed_string($data_pengaduan->tgl_pengaduan) ?>
                </p>

                <?php if($jawaban->count() > 0){ ?>

                <p>
                    <?= number_format($jawaban->count()) ?> Jawaban | <?= number_format($tanggapan->count()) ?>
                    Tanggapan
                </p>

                <?php } ?>


                <?php
                $klasifikasi = 'klasifikasi_data(' . $data_pengaduan->id_pengaduan . ", '" . $data_pengaduan->nama_pengaduan . "', '" . $data_pengaduan->klasifikasi_pengaduan . "')";
                // dd($data_session_pegawai);
                if ($data_session_pegawai->sebagai_pegawai == 'Administrator') {
                    $data_pengaduan->action = '-';
                }
                
                //  elseif ($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai != 'Administrator') {
                //     $data_pengaduan->action = '-';
                // }
                // elseif ($data_session_pegawai->sebagai_pegawai == 'Agent') {
                //     if ($data_pengaduan->status_pengaduan != 'Pending' && $data_pengaduan->status_pengaduan != 'Finish') {
                //         echo '<hr style="border-style: dashed;">';
                //         echo '
                // 									<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&alihkan=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                // 										<span class="badge badge-primary">
                // 										  <i class="bx bx-redo"></i> Alihkan
                // 										</span>
                // 									</a>
                // 								';
                //     } else {
                //         // $data_pengaduan->action = '-';
                //     }
                // }
                // elseif ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff') {
                // if ($data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
                //     if ($data_pengaduan->status_pengaduan == 'Checked') {
                //         echo '<hr style="border-style: dashed;">';
                //         echo '
                //                                 										<a href="javascript:;" onclick="' .
                //             $approve .
                //             '">
                //                                 											<span class="badge badge-info">
                //                                 											  <i class="bx bx-check-shield"></i> Approve
                //                                 											</span>
                //                                 										</a>
                //                                 									';
                //     } else {
                //         // $data_pengaduan->action = '-';
                //     }
                //     if ($data_pengaduan->status_pengaduan == 'Pending') {
                //         echo '<hr style="border-style: dashed;">';
                //         echo '
                //                                 										<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&lampiran=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                //                                 											<span class="badge badge-info">
                //                                 											  <i class="bx bx-layer-plus"></i> Lampiran
                //                                 											</span>
                //                                 										</a>
                //                                 										<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&update=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                //                                 											<span class="badge badge-primary">
                //                                 											  <i class="bx bx-edit"></i> Ubah
                //                                 											</span>
                //                                 										</a>
                //                                 										<a href="javascript:;" onclick="' .
                //             $delete .
                //             '">
                //                                 											<span class="badge badge-danger">
                //                                 											  <i class="bx bx-trash"></i> Hapus
                //                                 											</span>
                //                                 										</a>
                //                                 										<a href="javascript:;" onclick="' .
                //             $checked .
                //             '">
                //                                 											<span class="badge badge-warning">
                //                                 											  <i class="bx bx-check"></i> Checked
                //                                 											</span>
                //                                 										</a>
                //                                 									';
                //     } else {
                //         if ($data_pengaduan->status_pengaduan == 'On Progress') {
                //             echo '<hr style="border-style: dashed;">';
                //             echo '
                //                                     										<a href="javascript:;" onclick="' .
                //                 $finish .
                //                 '">
                //                                     											<span class="badge badge-success">
                //                                     											  <i class="bx bx-check-double"></i> Finish
                //                                     											</span>
                //                                     										</a>
                //                                     									';
                //         } else {
                //             // $data_pengaduan->action = '-';
                //         }
                //     }
                // } else
                if ($data_session_pegawai->sebagai_pegawai == 'PIC') {
                    if (isset($_GET['filter']) == 'Friend') {
                        echo '<hr style="border-style: dashed;">';
                        echo '<a href="javascript:;" onclick="' .
                            $klasifikasi .
                            '">	<span class="badge badge-danger">
                                                                 <i class="bx bx-category-alt"></i> Kategori
                                                              </span></a>';
                    }
                }
                // if ($data_session_pegawai->level_pegawai == 'Staff') {
                //     if ($data_pengaduan->status_pengaduan == 'Checked') {
                //         echo '<hr style="border-style: dashed;">';
                //         echo '
                // 									<a href="javascript:;" onclick="' .
                //             $approve .
                //             '">
                // 										<span class="badge badge-info">
                // 										  <i class="bx bx-check-shield"></i> Approve
                // 										</span>
                // 									</a>
                // 								';
                //     } else {
                //         $data_pengaduan->action = '-';
                //     }
                // } else {
                //     if ($data_pengaduan->status_pengaduan == 'Pending') {
                //         echo '<hr style="border-style: dashed;">';
                //         echo '
                // 									<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&lampiran=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                // 										<span class="badge badge-info">
                // 										  <i class="bx bx-layer-plus"></i> Lampiran
                // 										</span>
                // 									</a>
                // 									<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&update=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                // 										<span class="badge badge-primary">
                // 										  <i class="bx bx-edit"></i> Ubah
                // 										</span>
                // 									</a>
                // 									<a href="javascript:;" onclick="' .
                //             $delete .
                //             '">
                // 										<span class="badge badge-danger">
                // 										  <i class="bx bx-trash"></i> Hapus
                // 										</span>
                // 									</a>
                // 									<a href="javascript:;" onclick="' .
                //             $checked .
                //             '">
                // 										<span class="badge badge-warning">
                // 										  <i class="bx bx-check"></i> Checked
                // 										</span>
                // 									</a>
                // 								';
                //     } else {
                //         if ($data_pengaduan->status_pengaduan == 'On Progress') {
                //             echo '<hr style="border-style: dashed;">';
                //             echo '
                // 										<a href="javascript:;" onclick="' .
                //                 $finish .
                //                 '">
                // 											<span class="badge badge-success">
                // 											  <i class="bx bx-check-double"></i> Finish
                // 											</span>
                // 										</a>
                // 									';
                //         } else {
                //             // $data_pengaduan->action = '-';
                //         }
                //     }
                // }
                // } elseif ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff') {
                //     if ($data_pengaduan->status_pengaduan == 'Pending') {
                //         echo '<hr style="border-style: dashed;">';
                //         echo '
                // 									<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&lampiran=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                // 										<span class="badge badge-info">
                // 										  <i class="bx bx-layer-plus"></i> Lampiran
                // 										</span>
                // 									</a>
                // 									<a href="?filter=' .
                //             $_GET['filter'] .
                //             '&update=' .
                //             $data_pengaduan->id_pengaduan .
                //             '">
                // 										<span class="badge badge-primary">
                // 										  <i class="bx bx-edit"></i> Ubah
                // 										</span>
                // 									</a>
                // 									<a href="javascript:;" onclick="' .
                //             $delete .
                //             '">
                // 										<span class="badge badge-danger">
                // 										  <i class="bx bx-trash"></i> Hapus
                // 										</span>
                // 									</a>
                // 								';
                //     } else {
                //         if ($data_pengaduan->status_pengaduan == 'On Progress') {
                //             echo '<hr style="border-style: dashed;">';
                //             echo '
                // 										<a href="javascript:;" onclick="' .
                //                 $finish .
                //                 '">
                // 											<span class="badge badge-success">
                // 											  <i class="bx bx-check-double"></i> Finish
                // 											</span>
                // 										</a>
                // 									';
                //         } else {
                //             // $data_pengaduan->action = '-';
                //         }
                //     }
                // }
                ?>


                @if ($data_pengaduan->status_pengaduan == 'On Progress')
                    @if ($data_pegawai->sebagai_posisi == 'Staff' || $data_pegawai->sebagai_posisi == 'Staf')

                        @if (
                            $data_pengaduan->klasifikasi_pengaduan == 'Low' ||
                                $data_pengaduan->klasifikasi_pengaduan == 'Medium' ||
                                $data_pengaduan->klasifikasi_pengaduan == 'High')
                            <hr style="border-style:dashed;">
                            <!-- Button trigger modal -->
                            <span class="badge bg-primary text-white" style="cursor:pointer;" data-bs-toggle="modal"
                                data-bs-target="#resolve">
                                <i class='bx bx-check-square'></i> Resolve
                            </span>
                        @endif
                    @elseif($data__pegawai->sebagai_posisi == 'Kepala Bagian Unit Kerja')
                        @if ($data_pengaduan->klasifikasi_pengaduan == 'Low' || $data_pengaduan->klasifikasi_pengaduan == 'Medium')
                            <hr style="border-style:dashed;">
                            <!-- Button trigger modal -->
                            <span class="badge bg-primary text-white" style="cursor:pointer;" data-bs-toggle="modal"
                                data-bs-target="#resolve">
                                <i class='bx bx-check-square'></i> Resolve
                            </span>
                        @endif
                    @elseif($data_pegawai->sebagai_posisi == 'Kepala Unit Kerja')
                        @if ($data_pengaduan->klasifikasi_pengaduan == 'High')
                            <hr style="border-style:dashed;">
                            <!-- Button trigger modal -->
                            <span class="badge bg-primary text-white" style="cursor:pointer;" data-bs-toggle="modal"
                                data-bs-target="#resolve">
                                <i class='bx bx-check-square'></i> Resolve
                            </span>
                        @endif
                    @endif
                @endif

            </div>
        </div>
        <p>&nbsp;</p>
    </div>

    {{-- <!-- Modal -->
    <div class="modal fade" id="resolve" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Keterangan Solve</h5>
                    <span> <i class='bx bx-x-circle'></i></span>
                </div>
                <form action="{{ route('pengaduan.solved') }}" method="POST" id="form-solve">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id_pengaduan" value="{{ $data_pengaduan->id_pengaduan }}">
                        <input type="hidden" name="id_pegawai" value="{{ $data_pegawai->id_pegawai }}">
                        <label for="" class="form-label"> Note: </label>
                        <textarea name="keterangan" id="keterangan" required class="form-control" cols="30"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary btn-sm">Solve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end modal --> --}}
</div>
<?php } ?>

<script>
    $('#form-solve').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Kamu yakin ingin melakukan resolve ? ')) {

            this.submit()
        } else {
            $('#keterangan').val('')
            $('#resolve').modal('hide')
            return false;
        }
    })
</script>
{{-- @section('script')
@endsection --}}

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <?= $pengaduan->links() ?>
            <p>&nbsp;</p>
        </div>
    </div>
</div>


<?php } ?>
