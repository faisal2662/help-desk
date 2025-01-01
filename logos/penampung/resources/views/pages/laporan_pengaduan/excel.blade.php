<?php
header('Content-Type: application/xls');
header('Content-Disposition: attachment; filename=Laporan_pengaduan_helpdesk_' . date('Y_m_d', strtotime($tgl_awal)) . '_sd_' . date('Y_m_d', strtotime($tgl_selesai)) . '.xls');
header('Pragma: no-cache');
header('Expires: 0');
?>

<?php
$session_pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();

if ($session_pegawai->count() < 1) {
    header('Location: ' . route('keluar'));
    exit();
} else {
    foreach ($session_pegawai as $data_session_pegawai);

    foreach ($session_pegawai as $data_session_pegawai) {
            if ($data_session_pegawai->level_pegawai == 'Administrator') {
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
            }
            elseif ($data_session_pegawai->level_pegawai == 'Staff' || $data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
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
            } elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
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
];
?>

<table border="1" style="width: 100%;">
    <thead>
        <tr>
            <td style="padding: 10px 15px;" colspan="13">
                Keterangan : <b>Laporan Pengaduan
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 15px;" colspan="13">
                Tanggal Periode : <b><?= date('j F Y', strtotime($tgl_awal)) ?> s/d
                    <?= date('j F Y', strtotime($tgl_selesai)) ?></b>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 15px;" colspan="13">
                Status Pengaduan :
                <b><?= $status == '0' ? 'Semua Pengaduan' : 'Pengaduan ' . str_replace('Holding', 'Pengaduan SLA', $status) ?></b>
            </td>
        </tr>
        <tr>
            <td style="padding: 10px 15px;" colspan="13">
                Klasifikasi Pengaduan : <b><?= $klasifikasi == '0' ? 'Semua Klasifikasi Pengaduan' : $klasifikasi ?></b>
            </td>
        </tr>
    </thead>

    <thead>
        <tr>
            <td style="padding: 10px 15px;"><b>No</b></td>
            <td style="padding: 10px 15px;"><b>Data Unit Kerja</b></td>
            <td style="padding: 10px 15px;"><b>Pengaduan</b></td>
            <td style="padding: 10px 15px;"><b>Tindak Lanjut Pengaduan</b></td>
            <td style="padding: 10px 15px;"><b>Authority Information</b></td>

            {{-- <td style="padding: 10px 15px;"><b>Kepada</b></td>
            <td style="padding: 10px 15px;"><b>Keterangan</b></td>
            <td style="padding: 10px 15px;"><b>Klasifikasi</b></td>
            <td style="padding: 10px 15px;"><b>Status</b></td>
            <td style="padding: 10px 15px;"><b>Created By</b></td>
            <td style="padding: 10px 15px;"><b>Created Date</b></td>
            <td style="padding: 10px 15px;"><b>Finish Date</b></td>
            <td style="padding: 10px 15px;"><b>Analisa Akar Masalah</b></td>
            <td style="padding: 10px 15px;"><b>Pengaduan Berulang <br> (Ya / Tidak)</b></td> --}}
        </tr>
    </thead>

    <tbody>

        <?php if($pengaduan->count() < 1){ ?>

        <tr>
            <td style="padding: 10px 15px;" colspan="9">
                Laporan Pengaduan tidak ditemukan.
            </td>
        </tr>

        <?php }else{ ?>

        <?php $no = 1; foreach($pengaduan as $data_pengaduan){ ?>

        <?php
        $selesai = DB::table('tb_selesai')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_selesai.id_pegawai')
            ->where([['tb_selesai.id_pengaduan', $data_pengaduan->id_pengaduan], ['tb_selesai.delete_selesai', 'N']])
            ->get();

         $checker = DB::table('tb_checked')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', 'tb_checked.id_pegawai')
            ->where([['tb_checked.id_pengaduan', $data_pengaduan->id_pengaduan]])
            ->select('tb_checked.created_date', 'tb_pegawai.nama_pegawai')
            ->first();

        $signer = DB::table('tb_approved')
            ->join('tb_pegawai', 'tb_pegawai.id_pegawai', 'tb_approved.id_pegawai')
            ->where([['tb_approved.id_pengaduan', $data_pengaduan->id_pengaduan]])
            ->select('tb_approved.created_date', 'tb_pegawai.nama_pegawai')
            ->first();

        // get data pegawai
        $pegawai = DB::table('tb_pegawai')
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
        // end kantor bagian pengaduan
        ?>

        <tr>
            <td style="padding: 10px 15px;"><?= $no ?></td>
            <td style="padding: 10px ">
                <p style="margin-bottom: -5px;"><strong class="text-primary">Nama : </strong> </p>
                <p> {{ $data_pegawai->nama_pegawai }}</p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">jabatan :</strong> </p>
                <p>{{ $data_pegawai->level_pegawai }}</p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Unit Kerja :</strong> </p>
                <p>{{ $kantor_pegawai . ' - ' . $bagian_pegawai }}</p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Tanggal Pengaduan : </strong> </p>
                <p> {{ \Carbon\Carbon::parse($data_pengaduan->tgl_pengaduan)->translatedFormat('l, j F Y') }} </p>
            </td>
            <td style="padding: 10px ">
                <p style="margin-bottom: -5px;"><strong class="text-primary">No. Tiket : </strong> </p>
                <p> P<?= date('y') ?>-0000<?= $data_pengaduan->id_pengaduan ?></p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Kepada :</strong></p>
                <p>{{ $kantor_pengaduan }}</p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Department :</strong> </p>
                <p>{{ $bagian_pengaduan }}</p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Keterangan : </strong> </p>
                <p> {{ $data_pengaduan->keterangan_pengaduan }}</p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Kategori Kendala : </strong> </p>
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
                <p style="margin-bottom: -5px;"><strong class="text-primary">Status Pengaduan : </strong>
                </p>
                <p>{{ $data_pengaduan->status_pengaduan }} </p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Tgl Pengaduan Diterima :</strong>
                </p>
                <p>{{ \Carbon\Carbon::parse($data_pengaduan->respon_pengaduan)->translatedFormat('l, j F Y') }}
                </p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Tgl Pengaduan Selesai :</strong></p>
                @if ($selesai->count() > 0)
                    <p>{{ \Carbon\Carbon::parse($data_selesai->tgl_selesai)->translatedFormat('l, j F Y') }}
                    </p>
                @endif
                {{-- <p><strong class="text-primary">Status SLA :</strong></p> --}}
                {{-- <p><strong class="text-primary">Keterangan Tindak Lanjut</strong></p> --}}
            </td>
            <td style="padding: 10px ">
                <p style="margin-bottom: -5px;"><strong class="text-primary">Maker :</strong></p>
                <p> {{ $data_pegawai->nama_pegawai }} </p>
                <p style="margin-bottom: -5px;"><strong class="text-primary">Checker :</strong></p>
              @if ($checker)
                    <p> {{ \Carbon\Carbon::parse($checker->created_date)->translatedFormat('l, j F Y h:i') . ' - ' . $checker->nama_pegawai }}
                    </p>
                @endif
                <p style="margin-bottom: -5px;"><strong class="text-primary">Signer :</strong></p>
                @if ($signer)
                    <p> {{ \Carbon\Carbon::parse($signer->created_date)->translatedFormat('l, j F Y h:i') . ' - ' . $signer->nama_pegawai }}
                    </p>
                @endif
            </td>
            {{-- <td style="padding: 10px 15px;"><?= $data_pengaduan->nama_pengaduan ?></td> --}}
            {{-- <td style="padding: 10px 15px;"><?= $kantor_pegawai . ' - ' . $bagian_pegawai ?></td>
    <td style="padding: 10px 15px;"><?= $kantor_pengaduan . ' - ' . $bagian_pengaduan ?>
    </td>
    <td style="padding: 10px 15px;"><?= $data_pengaduan->keterangan_pengaduan ?></td>
    <td style="padding: 10px 15px;"><?= $data_pengaduan->klasifikasi_pengaduan ?></td>
    <td style="padding: 10px 15px;">
        <?= str_replace('Holding', 'Pengaduan SLA', $data_pengaduan->status_pengaduan) ?>
    </td>
    <td style="padding: 10px 15px;">
        <?= $data_pegawai->nama_pegawai . ' (' . $data_pegawai->level_pegawai . ')' ?></td>
    <td style="padding: 10px 15px;">
        <?= date('j F Y, H:i', strtotime($data_pengaduan->tgl_pengaduan)) ?></td>
    <td style="padding: 10px 15px;">
        <?php if($selesai->count() < 1){ ?>
        -
        <?php }else{ ?>

        <?php foreach ($selesai as $data_selesai); ?>

        <?= date('j F Y, H:i', strtotime($data_selesai->tgl_selesai)) ?>

        <?php } ?>
    </td>
    <td style="padding: 10px 15px;">&nbsp;</td>
    <td style="padding: 10px 15px;">&nbsp;</td> --}}
        </tr>

        <?php $no ++; } ?>

        <?php } ?>

    </tbody>
</table>
