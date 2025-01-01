<?php
$pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();
if ($pegawai->count() < 1) {
    header('Location: ' . route('logout'));
    exit();
} else {
    foreach ($pegawai as $data_pegawai);

    if ($data_pegawai->level_pegawai == 'Kepala Unit Kerja') {
        $status_pengaduan = [
            'Checked',
            'Approve',
            'Read',
            // 'Holding',
            // 'Moving',
            'On Progress',
            'Late',
            'Finish',
            'Friend',
        ];
    } else {
        $status_pengaduan = [
            'Pending',
            'Checked',
            'Approve',
            'Read',
            // 'Holding',
            // 'Moving',
            'On Progress',
            'Late',
            'Finish',
            'Friend',
        ];
    }
}

$klasifikasi_pengaduan = ['High', 'Medium', 'Low'];
?>

@extends('template')

@section('title')
    Laporan Pengaduan - Helpdesk
@stop

@section('content')

    <?php
    $status = '0';
    $klasifikasi = '0';
    $tgl_awal = '';
    $tgl_selesai = '';

    if (isset($_GET['tgl_awal'])) {
        if (empty($_GET['tgl_awal'])) {
            $tgl_awal = '';
        } else {
            $tgl_awal = $_GET['tgl_awal'];
        }
    }

    if (isset($_GET['tgl_selesai'])) {
        if (empty($_GET['tgl_selesai'])) {
            $tgl_selesai = '';
        } else {
            $tgl_selesai = $_GET['tgl_selesai'];
        }
    }

    if (isset($_GET['status'])) {
        if (empty($_GET['status'])) {
            $status = '0';
        } else {
            $status = $_GET['status'];
        }
    }

    if (isset($_GET['klasifikasi'])) {
        if (empty($_GET['klasifikasi'])) {
            $klasifikasi = '0';
        } else {
            $klasifikasi = $_GET['klasifikasi'];
        }
    }
    ?>

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <h4>
                <i class='bx bx-receipt'></i> Laporan Pengaduan
            </h4>
            <p>&nbsp;</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-filter-alt'></i> Filter Laporan Pengaduan</b></div>
                    <hr style="border-style: dashed;">

                    <form method="GET" onsubmit="show(true)">

                        <div class="row">
                            <div class="col-md-6">
                                <label>Tanggal Awal</label>
                                <input type="date" name="tgl_awal" value="<?= $tgl_awal ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>
                            </div>

                            <div class="col-md-6">
                                <label>Tanggal Selesai</label>
                                <input type="date" name="tgl_selesai" value="<?= $tgl_selesai ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label>Status Pengaduan</label>
                                <select name="status" class="form-control" required="">
                                    <?php
                                    if ($status == '0') {
                                        echo '<option value="0">- Semua Pengaduan -</option>';
                                        foreach ($status_pengaduan as $data_status_pengaduan) {
                                            echo '<option value="' . $data_status_pengaduan . '">' . str_replace('Holding', 'Pengaduan SLA', $data_status_pengaduan) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="' . $status . '">' . str_replace('Holding', 'Pengaduan SLA', $status) . '</option>';
                                        foreach ($status_pengaduan as $data_status_pengaduan) {
                                            if ($data_status_pengaduan != $status) {
                                                echo '<option value="' . $data_status_pengaduan . '">' . str_replace('Holding', 'Pengaduan SLA', $data_status_pengaduan) . '</option>';
                                            }
                                        }
                                        echo '<option value="0">- Semua Pengaduan -</option>';
                                    }
                                    ?>
                                </select>
                                <br>
                            </div>
                            <div class="col-md-6">
                                <label>Klasifikasi Pengaduan</label>
                                <select name="klasifikasi" class="form-control" required="">
                                    <?php
                                    if ($klasifikasi == '0') {
                                        echo '<option value="0">- Semua Klasifikasi Pengaduan -</option>';
                                        foreach ($klasifikasi_pengaduan as $data_klasifikasi_pengaduan) {
                                            echo '<option value="' . $data_klasifikasi_pengaduan . '">' . $data_klasifikasi_pengaduan . '</option>';
                                        }
                                    } else {
                                        echo '<option value="' . $klasifikasi . '">' . str_replace('Holding', 'Pengaduan SLA', $klasifikasi) . '</option>';
                                        foreach ($klasifikasi_pengaduan as $data_klasifikasi_pengaduan) {
                                            if ($data_klasifikasi_pengaduan != $klasifikasi) {
                                                echo '<option value="' . $data_klasifikasi_pengaduan . '">' . $data_klasifikasi_pengaduan . '</option>';
                                            }
                                        }
                                        echo '<option value="0">- Semua Klasifikasi Pengaduan -</option>';
                                    }
                                    ?>
                                </select>
                                <br>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class='bx bx-filter-alt'></i> Filter Laporan
                        </button>

                    </form>

                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

    <?php if($tgl_awal != '' && $tgl_selesai != ''){ ?>

    <?php
    $session_pegawai = DB::table('tb_pegawai')
        ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
        ->get();

    if ($session_pegawai->count() < 1) {
        header('Location: ' . route('keluar'));
        exit();
    } else {
        foreach ($session_pegawai as $data_session_pegawai);

        if ($data_session_pegawai->level_pegawai == 'Administrator') {
            if ($status == '0') {
                if ($klasifikasi == '0') {
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
                        ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                }
            } else {
                if ($klasifikasi == '0') {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $status)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                } else {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $status)
                        ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                }
            }
        }
        // else if($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai != 'Administrator'){
        // 	if($status == '0'){
        // 		if($klasifikasi == '0'){
        // 			$pengaduan = DB::table('tb_pengaduan')
        // 			->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
        // 			->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
        // 			->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
        // 			->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
        // 			->where('tb_pengaduan.status_pengaduan','!=','Pending')
        // 			->where('tb_pengaduan.delete_pengaduan','=','N')
        // 			->where(function ($query) use ($tgl_awal, $tgl_selesai) { $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])
        // 	        ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);})
        // 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
        // 			->get();
        // 		}else{
        // 			$pengaduan = DB::table('tb_pengaduan')
        // 			->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
        // 			->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
        // 			->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
        // 			->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
        // 			->where('tb_pengaduan.status_pengaduan','!=','Pending')
        // 			->where('tb_pengaduan.delete_pengaduan','=','N')
        // 			->where('tb_pengaduan.klasifikasi_pengaduan','=', $klasifikasi)
        // 			->where(function ($query) use ($tgl_awal, $tgl_selesai) { $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])
        // 	        ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);})
        // 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
        // 			->get();
        // 		}
        // 	}else{
        // 		if($klasifikasi == '0'){
        // 			$pengaduan = DB::table('tb_pengaduan')
        // 			->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
        // 			->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
        // 			->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
        // 			->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
        // 			->where('tb_pengaduan.status_pengaduan','!=','Pending')
        // 			->where('tb_pengaduan.status_pengaduan','=', $status)
        // 			->where('tb_pengaduan.delete_pengaduan','=','N')
        // 			->where(function ($query) use ($tgl_awal, $tgl_selesai) { $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])
        // 	        ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);})
        // 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
        // 			->get();
        // 		}else{
        // 			$pengaduan = DB::table('tb_pengaduan')
        // 			->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
        // 			->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
        // 			->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
        // 			->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
        // 			->where('tb_pengaduan.status_pengaduan','!=','Pending')
        // 			->where('tb_pengaduan.status_pengaduan','=', $status)
        // 			->where('tb_pengaduan.delete_pengaduan','=','N')
        // 			->where('tb_pengaduan.klasifikasi_pengaduan','=', $klasifikasi)
        // 			->where(function ($query) use ($tgl_awal, $tgl_selesai) { $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])
        // 	        ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);})
        // 			->orderBy('tb_pengaduan.id_pengaduan','DESC')
        // 			->get();
        // 		}
        // 	}
        // }
        elseif ($data_session_pegawai->level_pegawai == 'Staff' || $data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
            if ($status == '0') {
                if ($klasifikasi == '0') {
                    $pengaduan = DB::table('tb_pengaduan')
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
            }
            else if ($status == 'Friend')
            {
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
            }
            else {
                if ($klasifikasi == '0') {
                    if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_bagian', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_bagian', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_bagian', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                } else {
                    //     $pengaduan = DB::table('tb_pengaduan')
                    //         ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                    //         ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                    //         ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                    //         ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                    //         ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                    //         ->where('tb_pengaduan.status_pengaduan', '=', $status)
                    //         ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    //         ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                    //         ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                    //             $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                    //         })
                    //         ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                    //         ->get();
                    // }
                    if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_bagian', $data_session_pegawai->id_bagian_kantor_pusat)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_bagian', $data_session_pegawai->id_bagian_kantor_cabang)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)

                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } elseif ($data_session_pegawai->id_bagian_kantor_wilayah != 0) {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->where('tb_pengaduan.id_from_bagian', $data_session_pegawai->id_bagian_kantor_wilayah)
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
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
        // else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff'){
        elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();
            foreach ($unit_kerja as $kepala_unit);

            if ($unit_kerja->count() < 1) {
                if ($status == '0') {
                    if ($klasifikasi == '0') {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai = "' .
                                    $data_session_pegawai->kantor_pegawai .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_pusat = "' .
                                    $data_session_pegawai->id_bagian_kantor_pusat .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_cabang = "' .
                                    $data_session_pegawai->id_bagian_kantor_cabang .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_wilayah = "' .
                                    $data_session_pegawai->id_bagian_kantor_wilayah .
                                    '"
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai = "' .
                                    $data_session_pegawai->kantor_pegawai .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_pusat = "' .
                                    $data_session_pegawai->id_bagian_kantor_pusat .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_cabang = "' .
                                    $data_session_pegawai->id_bagian_kantor_cabang .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_wilayah = "' .
                                    $data_session_pegawai->id_bagian_kantor_wilayah .
                                    '"
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                } else {
                    if ($klasifikasi == '0') {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai = "' .
                                    $data_session_pegawai->kantor_pegawai .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_pusat = "' .
                                    $data_session_pegawai->id_bagian_kantor_pusat .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_cabang = "' .
                                    $data_session_pegawai->id_bagian_kantor_cabang .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_wilayah = "' .
                                    $data_session_pegawai->id_bagian_kantor_wilayah .
                                    '"
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    } else {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai = "' .
                                    $data_session_pegawai->kantor_pegawai .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_pusat = "' .
                                    $data_session_pegawai->id_bagian_kantor_pusat .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_cabang = "' .
                                    $data_session_pegawai->id_bagian_kantor_cabang .
                                    '" And
                    												tb_pegawai.id_bagian_kantor_wilayah = "' .
                                    $data_session_pegawai->id_bagian_kantor_wilayah .
                                    '"
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                }
            } else {
                if ($status == '0') {
                    if ($klasifikasi == '0') {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.id_from_bagian', $kepala_unit->id_bagian_kantor_pusat)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.id_from_bagian', $kepala_unit->id_bagian_kantor_cabang)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $pengaduan = DB::table('tb_pengaduan')
                                ->where('tb_pengaduan.id_from_bagian', $kepala_unit->id_bagian_kantor_wilayah)
                                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                    $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                                })
                                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                ->get();
                        }
                    } else {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai IN (
                    													SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_pusat IN (
                    													SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_cabang IN (
                    													SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_wilayah IN (
                    													SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												)
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                } elseif ($status == 'Friend') {

                    if ($klasifikasi == '0') {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            // $kantor_pusat =KantorPusat::with('BagianKantorPusat')->where('BagianKantorPusat.id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->get();
                            $kantor_pusat = App\Models\KantorPusat::with('BagianKantorPusat')
                                ->whereHas('BagianKantorPusat', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N');
                                })
                                ->first();
                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_pusat->BagianKantorPusat->pluck('id_bagian_kantor_pusat');
                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = App\Models\pengaduan::with('BagianKantorPusat')
                                ->whereIn('id_bagian_kantor_pusat', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                ->where(function ($query) {
                                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                })
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $kantor_cabang = App\Models\KantorCabang::with('BagianKantorCabang')
                                ->whereHas('BagianKantorCabang', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)->where('delete_bagian_kantor_cabang', 'N');
                                })
                                ->first();
                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_cabang->BagianKantorCabang->pluck('id_bagian_kantor_cabang');
                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = App\Models\pengaduan::with('BagianKantorCabang')
                                ->whereIn('id_bagian_kantor_cabang', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                ->where(function ($query) {
                                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                })
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $kantor_wilayah = App\Models\KantorWilayah::with('BagianKantorWilayah')
                                ->whereHas('BagianKantorWilayah', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayah)->where('delete_bagian_kantor_wilayah', 'N');
                                })
                                ->first();
                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_wilayah->BagianKantorWilayah->pluck('id_bagian_kantor_wilayah');
                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = App\Models\pengaduan::with('BagianKantorWilayah')
                                ->whereIn('id_bagian_kantor_wilayah', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                ->where(function ($query) {
                                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                })
                                ->get();
                        }
                    } else {
                        if ($kepala_unit->id_bagian_kantor_pusat != 0) {
                            // $kantor_pusat =KantorPusat::with('BagianKantorPusat')->where('BagianKantorPusat.id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N')->get();
                            $kantor_pusat = App\Models\KantorPusat::with('BagianKantorPusat')
                                ->whereHas('BagianKantorPusat', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_pusat', $kepala_unit->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N');
                                })
                                ->first();
                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_pusat->BagianKantorPusat->pluck('id_bagian_kantor_pusat');
                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = App\Models\pengaduan::with('BagianKantorPusat')
                                ->whereIn('id_bagian_kantor_pusat', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                ->where(function ($query) {
                                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                })
                                ->where('klasifikasi_pengaduan', $klasifikasi)
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_cabang != 0) {
                            $kantor_cabang = App\Models\KantorCabang::with('BagianKantorCabang')
                                ->whereHas('BagianKantorCabang', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_cabang', $kepala_unit->id_bagian_kantor_cabang)->where('delete_bagian_kantor_cabang', 'N');
                                })
                                ->first();
                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_cabang->BagianKantorCabang->pluck('id_bagian_kantor_cabang');
                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = App\Models\pengaduan::with('BagianKantorCabang')
                                ->whereIn('id_bagian_kantor_cabang', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                ->where(function ($query) {
                                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                })
                                ->where('klasifikasi_pengaduan', $klasifikasi)
                                ->get();
                        } elseif ($kepala_unit->id_bagian_kantor_wilayah != 0) {
                            $kantor_wilayah = App\Models\KantorWilayah::with('BagianKantorWilayah')
                                ->whereHas('BagianKantorWilayah', function ($query) use ($kepala_unit) {
                                    $query->where('id_bagian_kantor_wilayah', $kepala_unit->id_bagian_kantor_wilayah)->where('delete_bagian_kantor_wilayah', 'N');
                                })
                                ->first();
                            // Kumpulkan semua ID bagian kantor wilayah
                            $bagian_ids = $kantor_wilayah->BagianKantorWilayah->pluck('id_bagian_kantor_wilayah');
                            // Ambil semua pengaduan terkait sekaligus
                            $pengaduan = App\Models\pengaduan::with('BagianKantorWilayah')
                                ->whereIn('id_bagian_kantor_wilayah', $bagian_ids)
                                ->where('delete_pengaduan', 'N')
                                ->where(function ($query) {
                                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress');
                                })
                                ->where('klasifikasi_pengaduan', $klasifikasi)
                                ->get();
                        }
                    }
                }
                else {
                    if ($klasifikasi == '0') {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai IN (
                    													SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_pusat IN (
                    													SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_cabang IN (
                    													SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_wilayah IN (
                    													SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												)
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();

                    } else {
                        $pengaduan = DB::table('tb_pengaduan')
                            ->whereRaw(
                                '
                    										tb_pengaduan.id_pegawai IN (
                    											Select
                    												tb_pegawai.id_pegawai
                    											From
                    												tb_pegawai
                    											Where
                    												tb_pegawai.kantor_pegawai IN (
                    													SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_pusat IN (
                    													SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_cabang IN (
                    													SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												) And
                    												tb_pegawai.id_bagian_kantor_wilayah IN (
                    													SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                    													delete_kepala_unit_kerja = "N" And
                    													id_pegawai = "' .
                                    $data_session_pegawai->id_pegawai .
                                    '"
                    												)
                    										)
                    									',
                            )
                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                            ->where('tb_pengaduan.status_pengaduan', '=', $status)
                            ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                            ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                                $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                            })
                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            ->get();
                    }
                }
            }
        } else {
            if ($status == '0') {
                if ($klasifikasi == '0') {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereRaw(
                            '
                    									tb_pengaduan.id_pegawai IN (
                    										Select
                    											tb_pegawai.id_pegawai
                    										From
                    											tb_pegawai
                    										Where
                    											tb_pegawai.kantor_pegawai = "' .
                                $data_session_pegawai->kantor_pegawai .
                                '" And
                    											tb_pegawai.id_bagian_kantor_pusat = "' .
                                $data_session_pegawai->id_bagian_kantor_pusat .
                                '" And
                    											tb_pegawai.id_bagian_kantor_cabang = "' .
                                $data_session_pegawai->id_bagian_kantor_cabang .
                                '" And
                    											tb_pegawai.id_bagian_kantor_wilayah = "' .
                                $data_session_pegawai->id_bagian_kantor_wilayah .
                                '"
                    									)
                    								',
                        )
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                } else {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereRaw(
                            '
                    									tb_pengaduan.id_pegawai IN (
                    										Select
                    											tb_pegawai.id_pegawai
                    										From
                    											tb_pegawai
                    										Where
                    											tb_pegawai.kantor_pegawai = "' .
                                $data_session_pegawai->kantor_pegawai .
                                '" And
                    											tb_pegawai.id_bagian_kantor_pusat = "' .
                                $data_session_pegawai->id_bagian_kantor_pusat .
                                '" And
                    											tb_pegawai.id_bagian_kantor_cabang = "' .
                                $data_session_pegawai->id_bagian_kantor_cabang .
                                '" And
                    											tb_pegawai.id_bagian_kantor_wilayah = "' .
                                $data_session_pegawai->id_bagian_kantor_wilayah .
                                '"
                    									)
                    								',
                        )
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.klasifikasi_pengaduan', '=', $klasifikasi)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                }
            } else {
                if ($klasifikasi == '0') {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereRaw(
                            '
                    									tb_pengaduan.id_pegawai IN (
                    										Select
                    											tb_pegawai.id_pegawai
                    										From
                    											tb_pegawai
                    										Where
                    											tb_pegawai.kantor_pegawai = "' .
                                $data_session_pegawai->kantor_pegawai .
                                '" And
                    											tb_pegawai.id_bagian_kantor_pusat = "' .
                                $data_session_pegawai->id_bagian_kantor_pusat .
                                '" And
                    											tb_pegawai.id_bagian_kantor_cabang = "' .
                                $data_session_pegawai->id_bagian_kantor_cabang .
                                '" And
                    											tb_pegawai.id_bagian_kantor_wilayah = "' .
                                $data_session_pegawai->id_bagian_kantor_wilayah .
                                '"
                    									)
                    								',
                        )
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $status)
                        ->where(function ($query) use ($tgl_awal, $tgl_selesai) {
                            $query->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') >= ? ', [date('Y-m-d', strtotime($tgl_awal))])->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y-%m-%d\') <= ? ', [date('Y-m-d', strtotime($tgl_selesai))]);
                        })
                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        ->get();
                } else {
                    $pengaduan = DB::table('tb_pengaduan')
                        ->whereRaw(
                            '
                    									tb_pengaduan.id_pegawai IN (
                    										Select
                    											tb_pegawai.id_pegawai
                    										From
                    											tb_pegawai
                    										Where
                    											tb_pegawai.kantor_pegawai = "' .
                                $data_session_pegawai->kantor_pegawai .
                                '" And
                    											tb_pegawai.id_bagian_kantor_pusat = "' .
                                $data_session_pegawai->id_bagian_kantor_pusat .
                                '" And
                    											tb_pegawai.id_bagian_kantor_cabang = "' .
                                $data_session_pegawai->id_bagian_kantor_cabang .
                                '" And
                    											tb_pegawai.id_bagian_kantor_wilayah = "' .
                                $data_session_pegawai->id_bagian_kantor_wilayah .
                                '"
                    									)
                    								',
                        )
                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        ->where('tb_pengaduan.status_pengaduan', '=', $status)
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

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-receipt'></i> Laporan Pengaduan</b></div>
                    <p>
                        Tanggal Periode : <?= date('j F Y', strtotime($tgl_awal)) ?> s/d
                        <?= date('j F Y', strtotime($tgl_selesai)) ?>
                    </p>
                    <p>
                        Status Pengaduan :
                        <?= $status == '0' ? 'Semua Pengaduan' : 'Pengaduan ' . str_replace('Holding', 'Pengaduan SLA', $status) ?>
                    </p>
                    <p>
                        Klasifikasi Pengaduan : <?= $klasifikasi == '0' ? 'Semua Klasifikasi Pengaduan' : $klasifikasi ?>
                    </p>
                    <hr style="border-style: dashed;">

                    <?php if($pengaduan->count() < 1){ ?>

                    <center>
                        <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                        <p>Belum ada laporan pengaduan saat ini.</p>
                    </center>

                    <?php }else{ ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <td><b>No</b></td>
                                    <td><b>Kode Pengaduan</b></td>
                                    <td><b>Pengaduan</b></td>
                                    <td><b>Dari</b></td>
                                    <td><b>Kepada</b></td>
                                    <td><b>Keterangan</b></td>
                                    <td><b>Klasifikasi</b></td>
                                    <td><b>Status</b></td>
                                    <td><b>Created By</b></td>
                                    <td><b>Created Date</b></td>
                                    <td><b>Finish Date</b></td>
                                </tr>
                            </thead>

                            <tbody>

                                <?php $no = 1; foreach($pengaduan as $data_pengaduan){ ?>

                                <?php
                                $selesai = DB::table('tb_selesai')
                                    ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_selesai.id_pegawai')
                                    ->where([['tb_selesai.id_pengaduan', $data_pengaduan->id_pengaduan], ['tb_selesai.delete_selesai', 'N']])
                                    ->get();

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
                                    <td><?= $no ?></td>
                                    <td>P<?= date('y') ?>-0000<?= $data_pengaduan->id_pengaduan ?></td>
                                    <td><?= $data_pengaduan->nama_pengaduan ?></td>
                                    <td><?= $kantor_pegawai . ' - ' . $bagian_pegawai ?></td>
                                    <td><?= $kantor_pengaduan . ' - ' . $bagian_pengaduan ?></td>
                                    <td><?= $data_pengaduan->keterangan_pengaduan ?></td>
                                    <td><?= $data_pengaduan->klasifikasi_pengaduan ?></td>
                                    <td><?= str_replace('Holding', 'Pengaduan SLA', $data_pengaduan->status_pengaduan) ?>
                                    </td>
                                    <td><?= $data_pegawai->nama_pegawai . ' (' . $data_pegawai->level_pegawai . ')' ?></td>
                                    <td><?= date('j F Y, H:i', strtotime($data_pengaduan->tgl_pengaduan)) ?></td>
                                    <td>
                                        <?php if($selesai->count() < 1){ ?>
                                        -
                                        <?php }else{ ?>

                                        <?php foreach ($selesai as $data_selesai); ?>

                                        <?= date('j F Y, H:i', strtotime($data_selesai->tgl_selesai)) ?>

                                        <?php } ?>
                                    </td>
                                </tr>

                                <?php $no ++; } ?>

                            </tbody>
                        </table>
                    </div>
                    <br>

                    <span style="display: none;">
                        <form method="POST" onsubmit="show(true)" id="form-cetak"
                            action="<?= route('laporan_pengaduan.cetak') ?>" target="_blank">
                            <?= csrf_field() ?>
                            <input type="text" name="tgl_awal" value="<?= $tgl_awal ?>" readonly="" required="">
                            <input type="text" name="tgl_selesai" value="<?= $tgl_selesai ?>" readonly=""
                                required="">
                            <input type="text" name="status" value="<?= $status ?>" readonly="" required="">
                            <input type="text" name="klasifikasi" value="<?= $klasifikasi ?>" readonly=""
                                required="">
                            <input type="text" name="format" id="input-format" readonly="" required="">
                        </form>
                    </span>

                    <script type="text/javascript">
                        function cetak(format) {
                            document.getElementById('input-format').value = format;
                            document.getElementById('form-cetak').submit();
                        }
                    </script>

                    <button type="button" class="btn btn-sm btn-primary" onclick="cetak ('cetak');" style="zoom: 90%;">
                        <i class='bx bx-printer'></i> Cetak Laporan
                    </button>

                    {{-- <button type="button" class="btn btn-sm btn-success" onclick="cetak ('excel');" style="zoom: 90%;">
                        <i class='bx bxs-file-export'></i> Export to Excel
                    </button> --}}

                    <?php } ?>

                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

    <?php }else{ ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <center>
                        <img src="<?= url('logos/edit.png') ?>" style="width: 170px;">
                        <p>Silahkan tentukan tanggal terlebih dahulu.</p>
                    </center>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

    <?php } ?>

@stop

@section('script')

    <script type="text/javascript">
        $(document).ready(function() {
            $('select').selectize({
                sortField: 'text'
            });
        });
    </script>

@stop
