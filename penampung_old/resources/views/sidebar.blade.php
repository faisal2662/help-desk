<?php
// $idAccount = Auth::user()->id_pegawai;

$role_menu = DB::table('tb_role_menu')
    ->leftjoin('tb_role_user', 'tb_role_user.id_role_menu', 'tb_role_menu.id_role_menu')
    ->where('tb_role_user.id_account', Auth::user()->id_pegawai)
    ->where('tb_role_user.can_access', 'Y')
    ->select('tb_role_menu.id_role_menu', 'tb_role_menu.menu', 'tb_role_menu.type', 'tb_role_menu.route_name', 'tb_role_menu.icon', 'tb_role_user.can_access', 'tb_role_user.can_create', 'tb_role_user.can_update', 'tb_role_user.can_delete')
    ->where('tb_role_menu.is_deleted', 'N')
    ->orderBy('tb_role_menu.position', 'asc')
    ->get();
// dd($role_menu);
$page = Request::segment(1);
// if($page == ''){
//     header('Location: '.route('dashboard'));
//     exit();
// }

$menu_notifikasi = [];

$status_pengaduan = ['Semua', 'Pending', 'Checked', 'Approve', 'Read', 'Holding', 'Moving', 'On Progress', 'Late', 'Finish'];

foreach ($status_pengaduan as $data_status_pengaduan) {
    $session_pegawai = DB::table('tb_pegawai')
        ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
        ->get();

    if ($session_pegawai->count() > 0) {
        foreach ($session_pegawai as $data_session_pegawai);

        if ($data_session_pegawai->level_pegawai == 'Administrator') {
            if ($data_status_pengaduan == 'Semua') {
                $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->paginate(12);

                $menu_notifikasi['Semua'] = $pengaduan->count();
            } else {
                $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->paginate(12);

                $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
            }
            $menu_notifikasi['Friend'] = 0;
        } elseif ($data_session_pegawai->level_pegawai == 'Staff' || $data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
            if ($data_status_pengaduan == 'Semua') {
                $pengaduan = DB::table('tb_pengaduan')
                    ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                    ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                    ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                    ->paginate(12);

                $menu_notifikasi['Semua'] = $pengaduan->count();
            } else {
                if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                    $bagian = DB::table('tb_bagian_kantor_pusat')
                        ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)
                        ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N')
                        ->first();

                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_pusat)
                        ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_pusat)
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->paginate(12);
                } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                    $bagian = DB::table('tb_bagian_kantor_cabang')
                        ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)
                        ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', 'N')
                        ->first();

                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_cabang)
                        ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_cabang)
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->paginate(12);
                } else {
                    $bagian = DB::table('tb_bagian_kantor_wilayah')
                        ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayah)
                        ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', 'N')
                        ->first();

                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_wilayah)
                        ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_wilayah)
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->paginate(12);
                }

                $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
            }
            $pengaduan = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where(function ($query) {
                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving');
                })
                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                ->paginate(12);
            $menu_notifikasi['Friend'] = $pengaduan->count();
        }
        // elseif ($data_session_pegawai->sebagai_pegawai == 'Agent') {
        //     if ($data_status_pengaduan == 'Semua') {
        //         $pengaduan = DB::table('tb_pengaduan')
        //             ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
        //             ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
        //             ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
        //             ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
        //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
        //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
        //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
        //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
        //             ->paginate(12);
        //         $menu_notifikasi['Semua'] = $pengaduan->count();
        //     } else {
        //         $pengaduan = DB::table('tb_pengaduan')
        //             ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
        //             ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
        //             ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
        //             ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
        //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
        //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
        //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
        //             ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
        //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
        //             ->paginate(12);
        //         $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
        //     }
        // }
        elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();
            foreach ($unit_kerja as $data_unit_kerja);
            // if ($unit_kerja->count() < 1) {
            //     if ($data_status_pengaduan == 'Semua') {
            //         $pengaduan = DB::table('tb_pengaduan')
            //             ->whereRaw(
            //                 '
            //     tb_pengaduan.id_pegawai IN (
            //         Select
            //         tb_pegawai.id_pegawai
            //         From
            //         tb_pegawai
            //         Where

            //         tb_pegawai.id_bagian_kantor_pusat = "' .
            //                     $data_session_pegawai->id_bagian_kantor_pusat .
            //                     '" And
            //         tb_pegawai.id_bagian_kantor_cabang = "' .
            //                     $data_session_pegawai->id_bagian_kantor_cabang .
            //                     '" And
            //         tb_pegawai.id_bagian_kantor_wilayah = "' .
            //                     $data_session_pegawai->id_bagian_kantor_wilayah .
            //                     '"
            //     )
            //     ',
            //             )
            //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //             ->paginate(12);

            //         $menu_notifikasi['Semua'] = $pengaduan->count();
            //     } else {

            //         $pengaduan = DB::table('tb_pengaduan')
            //             ->whereRaw(
            //                 '
            //     tb_pengaduan.id_pegawai IN (
            //         Select
            //         tb_pegawai.id_pegawai
            //         From
            //         tb_pegawai
            //         Where

            //         tb_pegawai.id_bagian_kantor_pusat = "' .
            //                     $data_session_pegawai->id_bagian_kantor_pusat .
            //                     '" And
            //         tb_pegawai.id_bagian_kantor_cabang = "' .
            //                     $data_session_pegawai->id_bagian_kantor_cabang .
            //                     '" And
            //         tb_pegawai.id_bagian_kantor_wilayah = "' .
            //                     $data_session_pegawai->id_bagian_kantor_wilayah .
            //                     '"
            //     )
            //     ',
            //             )
            //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            //             ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
            //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //             ->paginate(12);

            //         $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
            //     }
            // } else {
            if ($unit_kerja->count() > 0) {
                if ($data_status_pengaduan == 'Semua') {
                    //     $pengaduan = DB::table('tb_pengaduan')
                    //         ->whereRaw(
                    //             '
                    // tb_pengaduan.id_pegawai IN (
                    //     Select
                    //     tb_pegawai.id_pegawai
                    //     From
                    //     tb_pegawai
                    //     Where
                    //     tb_pegawai.kantor_pegawai IN (
                    //         SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     ) And
                    //     tb_pegawai.id_bagian_kantor_pusat IN (
                    //         SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     ) And
                    //     tb_pegawai.id_bagian_kantor_cabang IN (
                    //         SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     ) And
                    //     tb_pegawai.id_bagian_kantor_wilayah IN (
                    //         SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     )
                    // )
                    // ',
                    //         )
                    //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    //         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                    //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                    //         ->paginate(12);
                    if ($data_unit_kerja->id_bagian_kantor_pusat != 0) {
                        $bagian = DB::table('tb_bagian_kantor_pusat')
                            ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $data_unit_kerja->id_bagian_kantor_pusat)
                            ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N')
                            ->first();

                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_pusat)
                            ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->paginate(12);
                    } elseif ($data_unit_kerja->id_bagian_kantor_cabang != 0) {
                        $bagian = DB::table('tb_bagian_kantor_cabang')
                            ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $data_unit_kerja->id_bagian_kantor_cabang)
                            ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', 'N')
                            ->first();

                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_cabang)
                            ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->paginate(12);
                    } else {
                        $bagian = DB::table('tb_bagian_kantor_wilayah')
                            ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $data_unit_kerja->id_bagian_kantor_wilayah)
                            ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', 'N')
                            ->first();

                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_wilayah)
                            ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->paginate(12);
                    }
                    $menu_notifikasi['Semua'] = $pengaduan->count();
                } else {
                    //     $pengaduan = DB::table('tb_pengaduan')
                    //         ->whereRaw(
                    //             '
                    // tb_pengaduan.id_pegawai IN (
                    //     Select
                    //     tb_pegawai.id_pegawai
                    //     From
                    //     tb_pegawai
                    //     Where
                    //     tb_pegawai.kantor_pegawai IN (
                    //         SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     ) And
                    //     tb_pegawai.id_bagian_kantor_pusat IN (
                    //         SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     ) And
                    //     tb_pegawai.id_bagian_kantor_cabang IN (
                    //         SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     ) And
                    //     tb_pegawai.id_bagian_kantor_wilayah IN (
                    //         SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                    //         delete_kepala_unit_kerja = "N" And
                    //         id_pegawai = "' .
                    //                 $data_session_pegawai->id_pegawai .
                    //                 '"
                    //     )
                    // )
                    // ',
                    //         )
                    //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    //         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                    //         ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                    //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                    //         ->paginate(12);
                    if ($data_unit_kerja->id_bagian_kantor_pusat != 0) {
                        $bagian = DB::table('tb_bagian_kantor_pusat')
                            ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $data_unit_kerja->id_bagian_kantor_pusat)
                            ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N')
                            ->first();

                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_pusat)
                            ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->paginate(12);
                    } elseif ($data_unit_kerja->id_bagian_kantor_cabang != 0) {
                        $bagian = DB::table('tb_bagian_kantor_cabang')
                            ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $data_unit_kerja->id_bagian_kantor_cabang)
                            ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', 'N')
                            ->first();

                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_cabang)
                            ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->paginate(12);
                    } else {
                        $bagian = DB::table('tb_bagian_kantor_wilayah')
                            ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $data_unit_kerja->id_bagian_kantor_wilayah)
                            ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', 'N')
                            ->first();

                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_wilayah)
                            ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->paginate(12);
                    }

                    // dd($data_unit_kerja);
                    $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
                }

                // $pengaduan = DB::table('tb_pengaduan')
                //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_unit_kerja->id_bagian_kantor_pusat)
                //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_unit_kerja->id_bagian_kantor_cabang)
                //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_unit_kerja->id_bagian_kantor_wilayah)
                //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                //     ->where(function ($query) {
                //         $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                //     })
                //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                //     ->paginate(12);
                $kepala_unit = DB::table('tb_kepala_unit_kerja')
                    ->where('tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai)
                    ->where('tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N')
                    ->first();

                if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                    // $kantor_pusat =KantorPusat::with('BagianKantorPusat')->where('BagianKantorPusat.id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->get();
                    // Ambil kantor pusat beserta bagian terkait
                    // $kantor_pusat = DB::table('tb_kantor_pusat')
                    //     ->join('tb_bagian_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
                    //     ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)
                    //     ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N')
                    //     // ->select('tb_bagian_kantor_pusat.id_bagian_kantor_pusat')
                    //     ->get();
                    $kantor_pusat = App\Models\KantorPusat::with('BagianKantorPusat')
                        ->whereHas('BagianKantorPusat', function ($query) use ($kepala_unit) {
                            $query->where('id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N');
                        })
                        ->first();

                    // Kumpulkan semua ID bagian kantor pusat dalam array
                    $bagian_ids = $kantor_pusat->BagianKantorPusat->pluck('id_bagian_kantor_pusat');

                    // Ambil data pengaduan terkait menggunakan whereIn untuk efisiensi
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereIn('id_bagian_kantor_pusat', $bagian_ids)
                        ->where('delete_pengaduan', 'N')
                        ->where(function ($query) {
                            $query->where('status_pengaduan', '=', 'Approve')->orWhere('status_pengaduan', '=', 'On Progress');
                        })
                        ->orderBy('id_pengaduan', 'DESC')
                        ->paginate(12);
                    // dd($bagian_ids);
                } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                    $kantor_cabang = App\Models\KantorCabang::with('BagianKantorCabang')
                        ->whereHas('BagianKantorCabang', function ($query) use ($kepala_unit) {
                            $query->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)->where('delete_bagian_kantor_cabang', 'N');
                        })
                        ->first();

                    // Kumpulkan semua ID bagian kantor wilayah
                    $bagian_ids = $kantor_cabang->BagianKantorCabang->pluck('id_bagian_kantor_cabang');

                    // Ambil pengaduan berdasarkan ID bagian yang sudah dikumpulkan
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereIn('id_bagian_kantor_cabang', $bagian_ids)
                        ->where('delete_pengaduan', 'N')
                        ->where(function ($query) {
                            $query->where('status_pengaduan', '=', 'Approve')->orWhere('status_pengaduan', '=', 'On Progress');
                        })
                        ->orderBy('id_pengaduan', 'DESC')
                        ->paginate(12);
                } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                    $kantor_wilayah = App\Models\KantorWilayah::with('BagianKantorWilayah')
                        ->whereHas('BagianKantorWilayah', function ($query) use ($kepala_unit) {
                            $query->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayah)->where('delete_bagian_kantor_wilayah', 'N');
                        })
                        ->first();

                    // Kumpulkan semua ID bagian kantor wilayah
                    $bagian_ids = $kantor_wilayah->BagianKantorWilayah->pluck('id_bagian_kantor_wilayah');

                    // Ambil pengaduan berdasarkan ID bagian yang sudah dikumpulkan
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereIn('id_bagian_kantor_wilayah', $bagian_ids)
                        ->where('delete_pengaduan', 'N')
                        ->where(function ($query) {
                            $query->where('status_pengaduan', '=', 'Approve')->orWhere('status_pengaduan', '=', 'On Progress');
                        })
                        ->orderBy('id_pengaduan', 'DESC')
                        ->paginate(12);
                }

                $menu_notifikasi['Friend'] = $pengaduan->count();
                // if ($data_unit_kerja->id_bagian_kantor_pusat != 0) {
                //         $bagian = DB::table('tb_bagian_kantor_pusat')
                //             ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $data_unit_kerja->id_bagian_kantor_pusat)
                //             ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', 'N')
                //             ->first();

                //         $pengaduan = DB::table('tb_pengaduan')
                //             ->where('tb_pengaduan.id_bagian_kantor_pusat', $bagian->id_kantor_pusat)
                //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                //             ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                //             ->paginate(12);
                //     } elseif ($data_unit_kerja->id_bagian_kantor_cabang != 0) {
                //         $bagian = DB::table('tb_bagian_kantor_cabang')
                //             ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $data_unit_kerja->id_bagian_kantor_cabang)
                //             ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', 'N')
                //             ->first();

                //         $pengaduan = DB::table('tb_pengaduan')
                //             ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_cabang)
                //             ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_cabang)
                //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                //             ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                //             ->paginate(12);
                //     } else {
                //         $bagian = DB::table('tb_bagian_kantor_wilayah')
                //             ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $data_unit_kerja->id_bagian_kantor_wilayah)
                //             ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', 'N')
                //             ->first();

                //         $pengaduan = DB::table('tb_pengaduan')
                //             ->where('tb_pengaduan.id_from_kantor', $bagian->id_kantor_wilayah)
                //             ->where('tb_pengaduan.id_from_bagian', '=', $bagian->id_bagian_kantor_wilayah)
                //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                //             ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
                //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                //             ->paginate(12);
                //     }
            }

            // if ($data_status_pengaduan == 'Semua') {
            //     $pengaduan = DB::table('tb_pengaduan')
            //         ->whereRaw(
            //             '
            //         tb_pengaduan.id_pegawai IN (
            //         Select
            //             tb_pegawai.id_pegawai
            //         From
            //             tb_pegawai
            //         Where
            //             tb_pegawai.kantor_pegawai = "' .
            //                 $data_session_pegawai->kantor_pegawai .
            //                 '" And
            //             tb_pegawai.id_bagian_kantor_pusat = "' .
            //                 $data_session_pegawai->id_bagian_kantor_pusat .
            //                 '" And
            //             tb_pegawai.id_bagian_kantor_cabang = "' .
            //                 $data_session_pegawai->id_bagian_kantor_cabang .
            //                 '" And
            //             tb_pegawai.id_bagian_kantor_wilayah = "' .
            //                 $data_session_pegawai->id_bagian_kantor_wilayah .
            //                 '"
            //         )
            //     ',
            //         )
            //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //         ->paginate(12);

            //     $menu_notifikasi['Semua'] = $pengaduan->count();
            // } else {
            //     $pengaduan = DB::table('tb_pengaduan')
            //         ->whereRaw(
            //             '
            //         tb_pengaduan.id_pegawai IN (
            //         Select
            //             tb_pegawai.id_pegawai
            //         From
            //             tb_pegawai
            //         Where
            //             tb_pegawai.kantor_pegawai = "' .
            //                 $data_session_pegawai->kantor_pegawai .
            //                 '" And
            //             tb_pegawai.id_bagian_kantor_pusat = "' .
            //                 $data_session_pegawai->id_bagian_kantor_pusat .
            //                 '" And
            //             tb_pegawai.id_bagian_kantor_cabang = "' .
            //                 $data_session_pegawai->id_bagian_kantor_cabang .
            //                 '" And
            //             tb_pegawai.id_bagian_kantor_wilayah = "' .
            //                 $data_session_pegawai->id_bagian_kantor_wilayah .
            //                 '"
            //         )
            //     ',
            //         )
            //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //         ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
            //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //         ->paginate(12);

            //     $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
            // }
        }
        // elseif ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff') {
        //     if ($data_status_pengaduan == 'Semua') {
        //         $pengaduan = DB::table('tb_pengaduan')
        //             ->where('tb_pengaduan.id_pegawai', '=', $data_session_pegawai->id_pegawai)
        //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
        //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
        //             ->paginate(12);

        //         $menu_notifikasi['Semua'] = $pengaduan->count();
        //     } else {
        //         $pengaduan = DB::table('tb_pengaduan')
        //             ->where('tb_pengaduan.id_pegawai', '=', $data_session_pegawai->id_pegawai)
        //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
        //             ->where('tb_pengaduan.status_pengaduan', '=', $data_status_pengaduan)
        //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
        //             ->paginate(12);

        //         $menu_notifikasi[$data_status_pengaduan] = $pengaduan->count();
        //     }
        // }
    }
}

?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        @foreach ($role_menu as $rm)

            @if ($rm->type == 'TITLE')
                <li class="nav-item">
                    <p>&nbsp;</p>
                    <span class="menu-title">{{ $rm->menu }}</span>
                </li>
            @else
                @php
                    $arr = [
                        'Create Pengaduan',
                        'Pengaduan Pending',
                        'Pengaduan Checked',
                        'Pengaduan Approve',
                        'Pengaduan Read',
                        'Pengaduan On Progress',
                        'Pengaduan SLA',
                        'Pengaduan Finish',
                        'Pengaduan Late',
                        'Pengaduan Friend',
                    ];
                @endphp
                @if (in_array($rm->menu, $arr))
                    @php
                        $menu = str_replace('SLA', 'Holding', str_replace('Pengaduan ', '', $rm->menu));
                    @endphp
                    @if ($rm->menu == 'Create Pengaduan')
                        <li class="nav-item {{ Request()->filter == 'Semua' ? 'active' : '' }}">
                            <a class="nav-link"
                                href="{{ Route::has($rm->route_name) ? route($rm->route_name) . '?filter=Semua' : '' }}">
                                <i class='{{ $rm->icon }} menu-icon'></i>
                                <span class="menu-title">{{ $rm->menu }}</span>
                                {{-- @if ($menu == 'Create Pengaduan')
                                    @if ($menu_notifikasi['Semua'] > 0)
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="badge badge-danger" style="border: 2px solid #ffffff;">
                                            <?= number_format($menu_notifikasi['Semua']) ?>
                                        </span>
                                    @endif
                                @endif --}}
                            </a>
                        </li>
                    @elseif($rm->menu == 'Pengaduan Friend')
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#pengaduan-friend" aria-expanded="false"
                                aria-controls="pengaduan-friend">
                                <i class="{{ $rm->icon }} menu-icon"></i>
                                <span class="menu-title">Your Friend</span>

                                @if ($menu == 'Friend')
                                    @if ($menu_notifikasi['Friend'] > 0)
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="badge badge-danger" style="border: 2px solid #ffffff;">
                                            <?= number_format($menu_notifikasi['Friend']) ?>
                                        </span>
                                    @endif
                                @endif
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="pengaduan-friend">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ Route::has($rm->route_name) ? route($rm->route_name) . '?filter=' . $menu : '#' }}">{{ $rm->menu }}</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @else
                        @if (auth()->user()->level_pegawai == 'Kepala Unit Kerja')
                            @if ($rm->menu != 'Pengaduan Pending')
                            <li class="nav-item {{ Request()->filter == $menu ? 'active' : '' }}">
                                <a class="nav-link"
                                    href="{{ Route::has($rm->route_name) ? route($rm->route_name) . '?filter=' . $menu : '#' }}">
                                    <i class='{{ $rm->icon }} menu-icon'></i>
                                    <span class="menu-title">{{ $rm->menu }}</span>
                                    
                                        @if ($menu_notifikasi[$menu] > 0)
                                            &nbsp;&nbsp;&nbsp;
                                            <span class="badge badge-danger" style="border: 2px solid #ffffff;">
                                                <?= number_format($menu_notifikasi[$menu]) ?>
                                            </span>
                                        @endif
                                    
                                </a>
                            </li>
                            @endif
                        @else
                        <li class="nav-item {{ Request()->filter == $menu ? 'active' : '' }}">
                            <a class="nav-link"
                                href="{{ Route::has($rm->route_name) ? route($rm->route_name) . '?filter=' . $menu : '#' }}">
                                <i class='{{ $rm->icon }} menu-icon'></i>
                                <span class="menu-title">{{ $rm->menu }}</span>
                              
                                    @if ($menu_notifikasi[$menu] > 0)
                                        &nbsp;&nbsp;&nbsp;
                                        <span class="badge badge-danger" style="border: 2px solid #ffffff;">
                                            <?= number_format($menu_notifikasi[$menu]) ?>
                                        </span>

                                @endif
                            </a>
                        </li>
                        @endif

                    @endif
                @else
                    <li class="nav-item" {{ $page == $rm->route_name ? 'active' : '' }}>
                        <a class="nav-link" href="{{ Route::has($rm->route_name) ? Route($rm->route_name) : '#' }}">
                            <i class='{{ $rm->icon }} menu-icon'></i>
                            <span class="menu-title">{{ $rm->menu }}</span>
                        </a>
                    </li>
                @endif
            @endif

        @endforeach 
    </ul>

</nav>
<!-- partial -->
