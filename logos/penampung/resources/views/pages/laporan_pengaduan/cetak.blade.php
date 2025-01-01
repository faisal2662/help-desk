{{-- <?php if($format == 'excel'){ ?>

    @include('pages.laporan_pengaduan.excel')

    <?php }else{ ?> --}}

<?php
$session_pegawai = DB::table('tb_pegawai')
    ->join('tb_posisi_pegawai', 'tb_posisi_pegawai.id_posisi_pegawai', '=','tb_pegawai.id_posisi_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();

if ($session_pegawai->count() < 1) {
    header('Location: ' . route('logout'));
    exit();
} else {
    foreach ($session_pegawai as $data_session_pegawai);
    foreach ($session_pegawai as $data_session_pegawai) {
        if ($data_session_pegawai->sebagai_pegawai == 'Administrator') {
            if ($klasifikasi == '0') {
                if ($status == '0') {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                } else {
                    $pengaduan = DB::table('tb_pengaduan')

                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', $status)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                }
            } else {
                $pengaduan = DB::table('tb_pengaduan')
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', $status)
                    ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                    ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                        $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                    })
                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                    ->get();
            }
        } elseif ($data_session_pegawai->sebagai_posisi == 'Staff' || $data_session_pegawai->sebagai_posisi == 'Staf' || $data_session_pegawai->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            if ($klasifikasi == '0') {
                if ($status == '0') {
                    if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                } elseif ($status == 'Friend') {
                    if ($klasifikasi == '0') {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                            ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                } else {
                    if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                }
            } else {
                if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_pusat)
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', $status)
                        ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_cabang)
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', $status)
                        ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('id_from_bagian', $data_session_pegawai->id_bagian_kantor_wilayah)
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', $status)
                        ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                }
            }
            // if ($klasifikasi == '0') {
            //     $pengaduan = DB::table('tb_pengaduan')
            //         ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
            //         ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
            //         ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
            //         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //         ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
            //             $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
            //         })
            //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //         ->get();
            // } else {
            //     $pengaduan = DB::table('tb_pengaduan')
            //         ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
            //         ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
            //         ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
            //         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //         ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
            //         ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
            //             $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
            //         })
            //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //         ->get();
            // }

            //  elseif ($status == 'Friend') {
            //     if ($klasifikasi == '0') {
            //         $pengaduan = DB::table('tb_pengaduan')
            //             ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
            //             ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
            //             ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
            //             ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
            //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //             ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
            //                 $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
            //             })
            //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //             ->get();
            //     } else {
            //         $pengaduan = DB::table('tb_pengaduan')
            //             ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
            //             ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
            //             ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
            //             ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
            //             ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //             ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
            //             ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
            //                 $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
            //             })
            //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            //             ->get();
            //     }
            // }
        } elseif ($data_session_pegawai->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();
            foreach ($unit_kerja as $kepala_unit);

            if ($unit_kerja->count() < 1) {
                if ($klasifikasi == '0') {
                    if ($status == '0') {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    } else {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.klasifikasi_pengaduan', $klasifikasi)
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.klasifikasi_pengaduan', $klasifikasi)
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.klasifikasi_pengaduan', $klasifikasi)
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    }
                } else {
                    if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                }
            } else {
                if ($klasifikasi == '0') {
                    if ($status == '0') {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    } else {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', $status)
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', $status)
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', $status)
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    }
                } else {
                    if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', $status)
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                }
            }
        }
    }
    //  else {
    //     if ($status == '0') {
    //         if ($klasifikasi == '0') {
    //             $pengaduan = DB::table('tb_pengaduan')
    //                 ->whereRaw(
    //                     '
    //     									tb_pengaduan.id_pegawai IN (
    //     										Select
    //     											tb_pegawai.id_pegawai
    //     										From
    //     											tb_pegawai
    //     										Where
    //     											tb_pegawai.kantor_pegawai = "' .
    //                         $data_session_pegawai->kantor_pegawai .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_pusat = "' .
    //                         $data_session_pegawai->id_bagian_kantor_pusat .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_cabang = "' .
    //                         $data_session_pegawai->id_bagian_kantor_cabang .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_wilayah = "' .
    //                         $data_session_pegawai->id_bagian_kantor_wilayah .
    //                         '"
    //     									)
    //     								',
    //                 )
    //                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
    //                 ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
    //                     $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
    //                 })
    //                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
    //                 ->get();
    //         } else {
    //             $pengaduan = DB::table('tb_pengaduan')
    //                 ->whereRaw(
    //                     '
    //     									tb_pengaduan.id_pegawai IN (
    //     										Select
    //     											tb_pegawai.id_pegawai
    //     										From
    //     											tb_pegawai
    //     										Where
    //     											tb_pegawai.kantor_pegawai = "' .
    //                         $data_session_pegawai->kantor_pegawai .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_pusat = "' .
    //                         $data_session_pegawai->id_bagian_kantor_pusat .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_cabang = "' .
    //                         $data_session_pegawai->id_bagian_kantor_cabang .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_wilayah = "' .
    //                         $data_session_pegawai->id_bagian_kantor_wilayah .
    //                         '"
    //     									)
    //     								',
    //                 )
    //                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
    //                 ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
    //                 ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
    //                     $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
    //                 })
    //                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
    //                 ->get();
    //         }
    //     } else {
    //         if ($klasifikasi == '0') {
    //             $pengaduan = DB::table('tb_pengaduan')
    //                 ->whereRaw(
    //                     '
    //     									tb_pengaduan.id_pegawai IN (
    //     										Select
    //     											tb_pegawai.id_pegawai
    //     										From
    //     											tb_pegawai
    //     										Where
    //     											tb_pegawai.kantor_pegawai = "' .
    //                         $data_session_pegawai->kantor_pegawai .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_pusat = "' .
    //                         $data_session_pegawai->id_bagian_kantor_pusat .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_cabang = "' .
    //                         $data_session_pegawai->id_bagian_kantor_cabang .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_wilayah = "' .
    //                         $data_session_pegawai->id_bagian_kantor_wilayah .
    //                         '"
    //     									)
    //     								',
    //                 )
    //                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
    //                 ->where('tb_pengaduan.status_pengaduan', '=', $status)
    //                 ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
    //                     $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
    //                 })
    //                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
    //                 ->get();
    //         } else {
    //             $pengaduan = DB::table('tb_pengaduan')
    //                 ->whereRaw(
    //                     '
    //     									tb_pengaduan.id_pegawai IN (
    //     										Select
    //     											tb_pegawai.id_pegawai
    //     										From
    //     											tb_pegawai
    //     										Where
    //     											tb_pegawai.kantor_pegawai = "' .
    //                         $data_session_pegawai->kantor_pegawai .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_pusat = "' .
    //                         $data_session_pegawai->id_bagian_kantor_pusat .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_cabang = "' .
    //                         $data_session_pegawai->id_bagian_kantor_cabang .
    //                         '" And
    //     											tb_pegawai.id_bagian_kantor_wilayah = "' .
    //                         $data_session_pegawai->id_bagian_kantor_wilayah .
    //                         '"
    //     									)
    //     								',
    //                 )
    //                 ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
    //                 ->where('tb_pengaduan.status_pengaduan', '=', $status)
    //                 ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
    //                 ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
    //                     $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
    //                 })
    //                 ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
    //                 ->get();
    //         }
    //     }
    // }
}

$color_status_pengaduan = [
    'Pending' => 'warning',
    'Checked' => 'warning',
    'Approve' => 'info',
    'Read' => 'info',
    'Holding' => 'danger',
    'Moving' => 'danger',
    'On Progress' => 'primary',
    'Late' => 'danger',
    'Finish' => 'success',
    'Friend' => 'info',
];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <meta name="description" content="Helpdesk - Jamkrindo.">
    <title>Laporan Pengaduan - Helpdesk</title>
    <link rel="apple-touch-icon" sizes="76x76" href="<?= url('logos/icon.png') ?>">
    <link rel="icon" type="image/png" href="<?= url('logos/icon.png') ?>">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
        @media print {
            @page {
                size: landscape;
            }
        }

        p {
            font-size: 7pt;
        }

        tr td {
            font-size: 8pt;
        }
    </style>

</head>

<body>

    <div class="container">
        <p>&nbsp;</p>
        <div class="row">
            <div class="col-md-12" style="border-bottom: 1px solid #000;">
                <center>
                    <img src="<?= url('logos/kop-surat.png') ?>" style="width: 40%;border-radius: 5px;">
                </center>
                <p>&nbsp;</p>
            </div>
            <div class="col-md-12" style="border-top: 3px solid #000;margin-top: 5px;">
                {{-- <p>&nbsp;</p> --}}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p>Keterangan : <b>Laporan Pengaduan</b></p>
                <p>
                    Tanggal Periode : <b><?= date('j F Y', strtotime($tgl_awal)) ?> s/d
                        <?= date('j F Y', strtotime($tgl_selesai)) ?></b>
                </p>
                <p>
                    Status Pengaduan :
                    <b><?= $status == '0' ? 'Semua Pengaduan' : 'Pengaduan ' . str_replace('Holding', 'Pengaduan SLA', $status) ?></b>
                </p>
                <p>
                    Klasifikasi Pengaduan :
                    <b><?= $klasifikasi == '0' ? 'Semua Klasifikasi Pengaduan' : $klasifikasi ?></b>
                </p>
                {{-- <p>&nbsp;</p> --}}
            </div>
        </div>

        <div class="row">
            <?php if($pengaduan->count() < 1){ ?>

            <center>
                <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                <p>Belum ada laporan pengaduan saat ini.</p>
            </center>

            <?php }
                            else{ ?>
            <div class="col-md-12">

                <div class="">
                    <table border="1" style="width: 100%;">
                        <thead>
                            <tr>
                                <td style="padding: 10px 15px;"><b>No</b></td>
                                <td style="padding: 10px 15px;"><b>Data Unit Kerja</b></td>
                                <td style="padding: 10px 15px;"><b>Pengaduan</b></td>
                                <td style="padding: 10px 15px;"><b>Tindak Lanjut Pengaduan</b></td>
                                <td style="padding: 10px 15px;"><b>Authority Information</b></td>
                                <td style="padding: 10px 15px;"><b>Chatting Information</b></td>
                            </tr>
                        </thead>

                        <tbody>

                            <?php $no = 1; foreach($pengaduan as $data_pengaduan){ ?>

                            <?php
                            $selesai = DB::table('tb_selesai')
                                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_selesai.id_pegawai')
                                ->where([['tb_selesai.id_pengaduan', $data_pengaduan->id_pengaduan], ['tb_selesai.delete_selesai', 'N']])
                                ->get();
                            foreach ($selesai as $data_selesai);

                            // get data pegawai

                            $checker = DB::table('tb_checked')
                                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', 'tb_checked.id_pegawai')
                                ->where([['tb_checked.id_pengaduan', $data_pengaduan->id_pengaduan]])
                                ->select('tb_checked.created_date', 'tb_pegawai.employee_name')
                                ->first();

                            $signer = DB::table('tb_approved')
                                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', 'tb_approved.id_pegawai')
                                ->where([['tb_approved.id_pengaduan', $data_pengaduan->id_pengaduan]])
                                ->select('tb_approved.created_date', 'tb_pegawai.employee_name')
                                ->first();
                            $pegawai = DB::table('tb_pegawai')
                                ->join('tb_posisi_pegawai', 'tb_posisi_pegawai.id_posisi_pegawai', '=','tb_pegawai.id_posisi_pegawai')
                                ->where([['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
                                ->get();
                            if ($pegawai->count() > 0) {
                                foreach ($pegawai as $data_pegawai);

                                $kantor_pegawai = '-';
                                $bagian_pegawai = '-';

                                if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {
                                    $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                                        ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                                        ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                                        ->get();
                                    if ($kantor_pusat->count() > 0) {
                                        foreach ($kantor_pusat as $data_kantor_pusat);
                                        $kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
                                        $bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
                                    }
                                } elseif ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {
                                    $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                                        ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                                        ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                                        ->get();
                                    if ($kantor_cabang->count() > 0) {
                                        foreach ($kantor_cabang as $data_kantor_cabang);
                                        $kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
                                        $bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
                                    }
                                } elseif ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {
                                    $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                                        ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                                        ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                                        ->get();
                                    if ($kantor_wilayah->count() > 0) {
                                        foreach ($kantor_wilayah as $data_kantor_wilayah);
                                        $kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
                                        $bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                                    }
                                }
                            }
                            // end get data pegawai

                            // kantor bagian pengaduan
                            $kantor_pengaduan = '-';
                            $bagian_pengaduan = '-';

                            if ($data_pengaduan->kantor_pengaduan == 'Kantor Pusat') {
                                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                                    ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                                    ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
                                    ->get();
                                if ($kantor_pusat->count() > 0) {
                                    foreach ($kantor_pusat as $data_kantor_pusat);
                                    $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
                                    $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
                                }
                            } elseif ($data_pengaduan->kantor_pengaduan == 'Kantor Cabang') {
                                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                                    ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                                    ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
                                    ->get();
                                if ($kantor_cabang->count() > 0) {
                                    foreach ($kantor_cabang as $data_kantor_cabang);
                                    $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
                                    $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
                                }
                            } elseif ($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah') {
                                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                                    ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                                    ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
                                    ->get();
                                if ($kantor_wilayah->count() > 0) {
                                    foreach ($kantor_wilayah as $data_kantor_wilayah);
                                    $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
                                    $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                                }
                            }

                            $kontak = App\Models\Kontak::with('chats.employee')
                                ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                ->where('delete_kontak', 'N')
                                ->get();

                            foreach ($kontak as $data_kontak);
                            // end kantor bagian pengaduan
                            ?>

                            <tr>
                                <td style="padding: 10px 15px;"><?= $no ?></td>
                                <td style="padding: 10px ">
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Nama : </strong> </p>
                                    <p> {{ $data_pegawai->employee_name }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">jabatan :</strong> </p>
                                    <p>{{ $data_pegawai->sebagai_posisi }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Unit Kerja :</strong>
                                    </p>
                                    <p>{{ $kantor_pegawai . ' - ' . $bagian_pegawai }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Tanggal Pengaduan :
                                        </strong> </p>
                                    <p> {{ \Carbon\Carbon::parse($data_pengaduan->tgl_pengaduan)->translatedFormat('l, j F Y') }}
                                    </p>
                                </td>
                                <td style="padding: 10px ">
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">No. Tiket : </strong>
                                    </p>
                                    <p> P<?= date('y') ?>-0000<?= $data_pengaduan->id_pengaduan ?></p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Kepada :</strong></p>
                                    <p>{{ $kantor_pengaduan }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Department :</strong>
                                    </p>
                                    <p>{{ $bagian_pengaduan }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Keterangan : </strong>
                                    </p>
                                    <p> {{ $data_pengaduan->keterangan_pengaduan }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Kategori Kendala :
                                        </strong> </p>
                                    <p>
                                        @if ($data_pengaduan->klasifikasi_pengaduan)
                                            {{ $data_pengaduan->klasifikasi_pengaduan }}
                                        @endif
                                    </p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Jenis :</strong></p>
                                    <p> {{ $data_pengaduan->jenis_produk }}</p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Produk : </strong> </p>
                                    <p> {{ $data_pengaduan->sub_jenis_produk }}</p>
                                </td>
                                <td style="padding: 10px ">
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Status Pengaduan :
                                        </strong>
                                    </p>
                                    <p>{{ $data_pengaduan->status_pengaduan }} </p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Tgl Pengaduan Diterima
                                            :</strong>
                                    </p>
                                    <p>{{ \Carbon\Carbon::parse($data_pengaduan->respon_pengaduan)->translatedFormat('l, j F Y') }}
                                    </p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Tgl Pengaduan Selesai
                                            :</strong></p>
                                    @if ($selesai->count() > 0)
                                        <p>{{ \Carbon\Carbon::parse($data_selesai->tgl_selesai)->translatedFormat('l, j F Y') }}
                                        </p>
                                    @endif
                                    {{-- <p><strong class="text-primary">Status SLA :</strong></p> --}}
                                    {{-- <p><strong class="text-primary">Keterangan Tindak Lanjut</strong></p> --}}
                                </td>
                                <td style="padding: 10px ">
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Maker :</strong></p>
                                    {{-- <p> {{$data_pegawai->nama_pegawai}} </p> --}}
                                    <p>{{ \Carbon\Carbon::parse($data_pengaduan->tgl_pengaduan)->translatedFormat('l, j F Y h:i') . ' - ' . $data_pegawai->employee_name }}
                                    </p>
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Checker :</strong></p>
                                    @if ($checker)
                                        <p> {{ \Carbon\Carbon::parse($checker->created_date)->translatedFormat('l, j F Y h:i') . ' - ' . $checker->employee_name }}
                                        </p>
                                    @endif
                                    <p style="margin-bottom: -5px;"><strong class="text-primary">Signer :</strong></p>
                                    @if ($signer)
                                        <p> {{ \Carbon\Carbon::parse($signer->created_date)->translatedFormat('l, j F Y h:i') . ' - ' . $signer->employee_name }}
                                        </p>
                                    @endif

                                    <p style=""><strong class="text-primary"> Keterangan Tindak
                                            Lanjut : </strong></p>
                                    @php
                                        // $tindakan = DB::table('tb_jawaban')->join('tb_tanggapan', 'tb_tanggapan.id_tanggapan',
                                        // '=', 'tb_jawaban.id_jawaban')->where('tb_jawaban.id_pengaduan', $data_pengaduan->id_pengaduan)
                                        // ->where('tb_jawaban.delete_jawaban', '=', 'N')->get();
                                        $tindakan = App\Models\Jawaban::with(['employee', 'responses.employee'])
                                            ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                            ->where('delete_jawaban', 'N')
                                            ->get();
                                    @endphp
                                    @foreach ($tindakan as $item)
                                        <p style="margin-bottom: -5px;">
                                            <strong>
                                                {{ $item->employee->nama_pegawai . ' - ' . $item->employee->level_pegawai }}
                                                (Jawaban) </strong>
                                        </p>
                                        <p> {{ $item->keterangan_jawaban }} </p>
                                        @foreach ($item->responses as $resp)
                                            {{-- @php
                                        dd($resp->employee->nama_pega);
                                        @endphp --}}
                                            @if ($resp->employee)
                                                <p style="margin-bottom: -5px; text-align:end;"><strong>
                                                        {{ $resp->employee->nama_pegawai . ' - ' . $resp->employee->level_pegawai }}
                                                        (Tanggapan)
                                                    </strong> </p>
                                            @else
                                                <p style="margin-bottom: -5px; text-align:end;"><strong> -
                                                        (Tanggapan)</strong> </p>
                                            @endif
                                            <p style="text-align: end;">{{ $resp->keterangan_tanggapan }} </p>
                                        @endforeach
                                    @endforeach
                                    <p style="margin-bottom: -3px;"><strong class="text-primary"> Resolve by Note:
                                        </strong></p>
                                    @php
                                        $solve = DB::table('tb_solve')
                                            ->where('is_delete', 'N')
                                            ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                            ->get();
                                    @endphp
                                    @foreach ($solve as $item)
                                        @if ($item)
                                            <p> {{ $item->keterangan_solve }}</p>
                                        @else
                                            <p> - </p>
                                        @endif
                                    @endforeach
                </div>
                </td>
                <td>
                    <div class="row border-bottom border-primary" style="margin: 0;padding:0;">
                        <div class="col  text-center text-primary  ">
                            <span style="display:block;">{{ $kantor_pengaduan }}</span>
                            <span> {{ $bagian_pengaduan }} </span>
                        </div>
                        <div class="col  text-center text-primary  ">
                            <span style="display: block">{{ $kantor_pegawai }}</span>
                            <span> {{ $bagian_pegawai }} </span>
                        </div>
                    </div>
                    @foreach ($kontak as $data_kontak)
                        @if ($data_kontak->chats->count() > 1)
                            @foreach ($data_kontak->chats as $item)
                                <div class="row border-bottom" style="margin: 0;padding:0;">
                                    @if ($item->posisi_pesan == '1')
                                        <div class="col-12 text-start " style=" margin-right: 10px;">
                                            <span class=" text-primary"
                                                style="display:block;">{{ $item->employee->nama_pegawai . ' - ' . $item->employee->level_pegawai }}</span>
                                            <span style="display:block;"> {{ $item->keterangan_chat }} </span>
                                            <span style="display:block;">
                                                {{ \Carbon\Carbon::parse($item->tgl_chat)->translatedFormat('j F Y h:i') }}
                                            </span>
                                        </div>
                                    @elseif($item->posisi_pesan == '0')
                                        <div class="col-12  "
                                            style=" text-align:end; margin-right:5px; margin-left: 10px;">
                                            <span class=" text-primary"
                                                style="display:block;">{{ $item->employee->nama_pegawai . ' - ' . $item->employee->level_pegawai }}</span>
                                            <span style="display:block;"> {{ $item->keterangan_chat }} </span>
                                            <span style="display:block;">
                                                {{ \Carbon\Carbon::parse($item->tgl_chat)->translatedFormat('j F Y h:i') }}
                                            </span>
                                        </div>
                                    @endif
                                    {{-- <div class="col  text-center text-primary"><p class="fw-bold">Divisi Lain</p></div> --}}
                                </div>
                            @endforeach
                        @endif
                    @endforeach

                </td>

                </tr>


                <?php $no ++; } ?>

                </tbody>
                </table>
            </div>

            <?php } ?>

            <p>&nbsp;</p>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            Mengetahui . . .
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>. . .</p>
            <p>&nbsp;</p>
            <p>
                Tanggal . . .
            </p>
            <p>&nbsp;</p>
        </div>
        <div class="col-md-6" align="right">
            {{-- Dicetak Oleh : <b><?= $data_pegawai->nama_pegawai ?></b> --}}
            <p>&nbsp;</p>
            <p>&nbsp;</p>
            <p>. . .</p>
            <p>&nbsp;</p>
            <p>
                Tanggal : <?= date('j F Y, H:i') ?>
            </p>
            <p>&nbsp;</p>
        </div>
    </div>
    <p>&nbsp;</p>
    </div>

    <script>
        self.print();
    </script>

</body>

</html>
{{--
    <?php } ?> --}}
