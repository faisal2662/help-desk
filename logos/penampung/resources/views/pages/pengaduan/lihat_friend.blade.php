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
$status_klasifikasi = [
    'High' => 'danger',
    'Medium' => 'warning',
    'Low' => 'info',
];
?>

<?php

$status_pengaduan = [
    'Pending' => 'warning',
    'Checked' => 'warning',
    'Approve' => 'info',
    'Read' => 'info',
    'Holding' => 'danger',
    'Moving' => 'danger',
    'Solved' => 'primary',
    'On Progress' => 'primary',
    'Late' => 'danger',
    'Finish' => 'success',
];

// $session_pegawai =  App\Models\Pegawai::with('NamaPosisi')->where('tb_pegawai.delete_pegawai', 'N')
//     ->where('tb_pegawai.id_pegawai', auth()->user()->id_pegawai)
//     ->where('tb_pegawai.status_pegawai', 'Aktif')
//     ->get();
$session_pegawai = DB::table('tb_pegawai')
    ->join('tb_posisi_pegawai', 'tb_pegawai.id_posisi_pegawai', '=', 'tb_posisi_pegawai.id_posisi_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
    ->get();
// dd($session_pegawai);
$data_pengaduan = DB::table('tb_pengaduan')
    ->where('id_pengaduan', $_GET['view'])
    ->where('delete_pengaduan', 'N')
    ->first();

if (!is_null($data_pengaduan)) {
    // read pengaduan
    if ($data_pengaduan->status_pengaduan == 'Approve') {
        DB::table('tb_pengaduan')
            ->where('id_pengaduan', $_GET['view'])
            ->where('delete_pengaduan', 'N')
            ->update(['status_pengaduan' => 'Read']);
    }
}

if ($session_pegawai->count() < 1) {
    header('Location: ' . route('logout'));
    exit();
} else {
    foreach ($session_pegawai as $data_session_pegawai);

    // if($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai == 'Administrator'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['view'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }else if($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai != 'Administrator'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
    // 	->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
    // 	->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
    // 	->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['view'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }else if($data_session_pegawai->sebagai_pegawai == 'Agent'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
    // 	->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
    // 	->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
    // 	->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['view'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff'){

    if ($data_session_pegawai->sebagai_posisi == 'Kepala Unit Kerja') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();
        foreach ($unit_kerja as $data_unit_kerja);
        if ($unit_kerja->count() < 1) {
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
                ->where('tb_pengaduan.id_pengaduan', '=', $_GET['view'])
                ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                ->get();
        } else {
            $kantor_pusat = App\Models\KantorPusat::with('BagianKantorPusat')
                ->whereHas('BagianKantorPusat', function ($query) use ($data_unit_kerja) {
                    $query->where('id_bagian_kantor_pusat', $data_unit_kerja->id_bagian_kantor_pusat)->where('delete_bagian_kantor_pusat', 'N');
                })
                ->first();

            // Kumpulkan semua ID bagian kantor wilayah
            $bagian_ids = $kantor_pusat->BagianKantorPusat->pluck('id_bagian_kantor_pusat');

            // Ambil semua pengaduan terkait sekaligus
            $pengaduan = App\Models\Pengaduan::with('BagianKantorPusat')
                ->whereIn('id_bagian_kantor_pusat', $bagian_ids)
                ->where('id_pengaduan', $_GET['view'])
                ->where('delete_pengaduan', 'N')
                ->where(function ($query) {
                    $query->where('tb_pengaduan.status_pengaduan', '=', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', '=', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Solved')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Read')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Finish');
                })
                ->get();
        }
    } else {
        $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.id_bagian_kantor_pusat', $data_session_pegawai->id_bagian_kantor_pusat)
            ->where('tb_pengaduan.id_bagian_kantor_cabang', $data_session_pegawai->id_bagian_kantor_cabang)
            ->where('tb_pengaduan.id_bagian_kantor_wilayah', $data_session_pegawai->id_bagian_kantor_wilayah)
            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            ->where('tb_pengaduan.id_pengaduan', '=', $_GET['view'])
            ->where(function ($query) {
                $query->where('tb_pengaduan.status_pengaduan', 'Approve')->orWhere('tb_pengaduan.status_pengaduan', 'On Progress')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Moving')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Solved')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Read')->orWhere('tb_pengaduan.status_pengaduan', '=', 'Finish');
            })
            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            ->get();
    }
}

if ($pengaduan->count() < 1) {
    header('Location: ' . route('pengaduan'));
    exit();
} else {
    foreach ($pengaduan as $data_pengaduan);

    $jawaban = DB::table('tb_jawaban')
        ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_jawaban.id_pegawai')
        ->where([['tb_jawaban.delete_jawaban', 'N'], ['tb_jawaban.id_pengaduan', $data_pengaduan->id_pengaduan]])
        ->orderBy('tb_jawaban.id_jawaban', 'DESC')
        ->get();

    $tanggapan = DB::table('tb_tanggapan')
        ->join('tb_jawaban', 'tb_jawaban.id_jawaban', '=', 'tb_tanggapan.id_jawaban')
        ->where([['tb_tanggapan.delete_tanggapan', 'N'], ['tb_jawaban.id_pengaduan', $data_pengaduan->id_pengaduan], ['tb_jawaban.delete_jawaban', 'N']])
        ->get();

    $lampiran = DB::table('tb_lampiran')
        ->where([['tb_lampiran.delete_lampiran', 'N'], ['tb_lampiran.id_pengaduan', $data_pengaduan->id_pengaduan]])
        ->orderBy('tb_lampiran.id_lampiran', 'ASC')
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
}

?>

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>
                <button type="button" class="btn btn-sm btn-warning" id="kembali">
                    <i class='bx bx-arrow-back'></i> Kembali
                </button>
            </p>
            <p>&nbsp;</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">
                                <b>
                                    <img src="<?= asset($data_pegawai->foto_pegawai) ?>"
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
                                @endif
                            </p>
                            <p>
                                <a href="?filter=<?= $_GET['filter'] ?>&view=<?= $data_pengaduan->id_pengaduan ?>"
                                    class="text-info">
                                    <b><i class='bx bxs-coupon'></i>
                                        <?= htmlspecialchars($data_pengaduan->nama_pengaduan) ?></b>
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

                            @if ($data_pengaduan->status_pengaduan == 'On Progress')
                                @if ($data_session_pegawai->sebagai_posisi == 'Staff' || $data_session_pegawai->sebagai_posisi == 'Staf')
                                    @if (
                                        $data_pengaduan->klasifikasi_pengaduan == 'Low' ||
                                            $data_pengaduan->klasifikasi_pengaduan == 'Medium' ||
                                            $data_pengaduan->klasifikasi_pengaduan == 'High')
                                        <hr style="border-style:dashed;">
                                        <!-- Button trigger modal -->
                                        <span class="badge bg-primary text-white" style="cursor:pointer;"
                                            data-bs-toggle="modal" data-bs-target="#resolve">
                                            <i class='bx bx-check-square'></i> Resolve
                                        </span>
                                    @endif
                                @elseif($data_session_pegawai->sebagai_posisi == 'Kepala Bagian Unit Kerja')
                                    @if ($data_pengaduan->klasifikasi_pengaduan == 'Medium' || $data_pengaduan->klasifikasi_pengaduan == 'High')
                                        <hr style="border-style:dashed;">
                                        <!-- Button trigger modal -->
                                        <span class="badge bg-primary text-white" style="cursor:pointer;"
                                            data-bs-toggle="modal" data-bs-target="#resolve">
                                            <i class='bx bx-check-square'></i> Resolve
                                        </span>
                                    @endif
                                @elseif($data_session_pegawai->sebagai_posisi == 'Kepala Unit Kerja')
                                    @if ($data_pengaduan->klasifikasi_pengaduan == 'High')
                                        <hr style="border-style:dashed;">
                                        <!-- Button trigger modal -->
                                        <span class="badge bg-primary text-white" style="cursor:pointer;"
                                            data-bs-toggle="modal" data-bs-target="#resolve">
                                            <i class='bx bx-check-square'></i> Resolve
                                        </span>
                                    @endif
                                @endif
                            @endif



                        </div>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </div>

            <?php
            $mengetahui = DB::table('tb_mengetahui')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_mengetahui.id_pegawai')
                ->where([['tb_mengetahui.id_pengaduan', $_GET['view']], ['tb_mengetahui.delete_mengetahui', 'N']])
                ->get();
            ?>

            <?php if($mengetahui->count() > 0){ ?>

            <?php
            $sla = DB::table('tb_jawaban')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_jawaban.id_pegawai')
                ->where([['tb_jawaban.id_pengaduan', $_GET['view']], ['tb_jawaban.delete_jawaban', 'N'], ['tb_jawaban.sla_jawaban', 'Ya']])
                ->get();

            $dibaca = DB::table('tb_dibaca')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_dibaca.id_pegawai')
                ->where([['tb_dibaca.id_pengaduan', $_GET['view']], ['tb_dibaca.delete_dibaca', 'N']])
                ->get();

            $alihkan = DB::table('tb_alihkan')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_alihkan.id_pegawai')
                ->where([['tb_alihkan.id_pengaduan', $_GET['view']], ['tb_alihkan.delete_alihkan', 'N']])
                ->get();

            $selesai = DB::table('tb_selesai')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_selesai.id_pegawai')
                ->where([['tb_selesai.id_pengaduan', $_GET['view']], ['tb_selesai.delete_selesai', 'N']])
                ->get();
            $resolve = DB::table('tb_solve')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_solve.id_pegawai')
                ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                ->get();
            ?>
            @if ($resolve->count() > 0)
                @foreach ($resolve as $item)
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4>
                                        Solved
                                    </h4>
                                    <hr style="border-style:hashed">

                                    <p>
                                        <b class="text-success">Resolve By</b> :
                                        <?= htmlspecialchars($item->employee_name) ?>
                                    </p>
                                    <p>
                                        <b class="text-success">Resolve Date</b> :
                                        <?= date('j F Y, H:i', strtotime($item->created_date)) ?>
                                    </p>
                                    <p > <b class="text-success">Note: </b> {{ $item->keterangan_solve }} </p>


                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title"><b><i class='bx bx-history'></i> Riwayat Pengaduan</b></div>
                            <hr style="border-style: dashed;">

                            <?php $list = 1; foreach($mengetahui as $data_mengetahui){ ?>

                            <?php if($mengetahui->count() < 2){ ?>

                            <p>
                                <b class="text-primary">Approved By</b> :
                                <?= htmlspecialchars($data_mengetahui->employee_name) ?>
                            </p>
                            <p>
                                <b class="text-primary">Approved Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_mengetahui->tgl_mengetahui)) ?>
                            </p>

                            <hr style="border-style: dashed;">

                            <?php }else{ ?>

                            <?php if($list == 1){ ?>

                            <p>
                                <b class="text-warning">Checked By</b> :
                                <?= htmlspecialchars($data_mengetahui->employee_name) ?>
                            </p>
                            <p>
                                <b class="text-warning">Checked Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_mengetahui->tgl_mengetahui)) ?>
                            </p>

                            <hr style="border-style: dashed;">

                            <?php }else{ ?>

                            <p>
                                <b class="text-primary">Approved By</b> :
                                <?= htmlspecialchars($data_mengetahui->employee_name) ?>
                            </p>
                            <p>
                                <b class="text-primary">Approved Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_mengetahui->tgl_mengetahui)) ?>
                            </p>

                            <hr style="border-style: dashed;">

                            <?php } ?>

                            <?php } ?>

                            <?php $list ++; } ?>

                            <?php if($dibaca->count() > 0){ ?>

                            <?php foreach($dibaca as $data_dibaca){ ?>

                            <p>
                                <b class="text-info">Read By</b> :
                                <?= htmlspecialchars($data_dibaca->employee_name) ?>
                            </p>
                            <p>
                                <b class="text-info">Read Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_dibaca->tgl_dibaca)) ?>
                            </p>

                            <hr style="border-style: dashed;">

                            <?php } ?>

                            <?php } ?>

                            <?php if($sla->count() > 0){ ?>

                            <?php foreach($sla as $data_sla){ ?>

                            <p>
                                <b class="text-danger">SLA By</b> :
                                <?= htmlspecialchars($data_sla->employee_name) ?>
                            </p>
                            <p>
                                <b class="text-danger">SLA Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_sla->durasi_sla_jawaban)) ?>
                            </p>
                            <p>
                                <b class="text-danger">Reason</b> :
                                <?= $data_sla->alasan_sla_jawaban ?>
                            </p>

                            <hr style="border-style: dashed;">

                            <?php } ?>

                            <?php } ?>

                            <?php if($alihkan->count() > 0){ ?>

                            <?php foreach($alihkan as $data_alihkan){ ?>

                            <p>
                                <b class="text-warning">Moved By</b> :
                                <?= htmlspecialchars($data_alihkan->nama_pegawai) ?>
                            </p>
                            <p>
                                <b class="text-warning">Reason</b> :
                                <?= $data_alihkan->keterangan_alihkan == '' ? '-' : $data_alihkan->keterangan_alihkan ?>
                            </p>
                            <p>
                                <b class="text-warning">Moved Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_alihkan->tgl_alihkan)) ?>
                            </p>

                            <hr style="border-style: dashed;">

                            <?php } ?>

                            <?php } ?>

                            <?php if($selesai->count() > 0){ ?>

                            <?php foreach($selesai as $data_selesai){ ?>

                            <p>
                                <b class="text-success">Finish By</b> :
                                <?= htmlspecialchars($data_selesai->employee_name) ?>
                            </p>
                            <p>
                                <b class="text-success">Finish Date</b> :
                                <?= date('j F Y, H:i', strtotime($data_selesai->tgl_selesai)) ?>
                            </p>

                            <?php } ?>

                            <?php } ?>

                        </div>
                    </div>
                    <p>&nbsp;</p>
                </div>
            </div>

            <?php } ?>

        </div>

        <div class="col-md-6">


            @if ($data_session_pegawai->sebagai_posisi == 'Staf' || $data_session_pegawai->sebagai_posisi == 'Staff')
                @if (
                    $data_pengaduan->klasifikasi_pengaduan == 'Low' ||
                        $data_pengaduan->klasifikasi_pengaduan == 'Medium' ||
                        $data_pengaduan->klasifikasi_pengaduan == 'High')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="cursor: pointer;" class="text-primary" data-toggle="collapse"
                                        data-target="#buat-jawaban">
                                        <b><i class='bx bxs-comment-add'></i> Buat Jawaban</b>
                                    </h4>

                                    <div class="row collapse" id="buat-jawaban">
                                        <div class="col-md-12">
                                            <hr style="border-style: dashed;">

                                            <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                                                action="<?= route('pengaduan.jawaban') ?>">
                                                <?= csrf_field() ?>

                                                <label>Jawaban</label>
                                                <textarea name="keterangan" class="form-control" required="" placeholder="Harap di isi ..."></textarea>
                                                <br>

                                                <div class="form-group">
                                                    <label>Unggah Foto (Opsional)</label>
                                                    <br>
                                                    <label for="file-1">
                                                        <img src="<?= url('logos/image.png') ?>" id="image-1"
                                                            style="width: 150px;border-radius: 5px;">
                                                        <input type="file" accept="image/*" name="foto"
                                                            id="file-1" class="form-control"
                                                            onchange="previewImage('image-1','file-1')"
                                                            style="display: none;">
                                                    </label>
                                                </div>

                                                <!--<label>SLA</label><br>-->
                                                <!--<input type="radio" name="sla" id="ya" value="Ya"> Ya-->
                                                <!--&nbsp;/&nbsp;-->
                                                <!--<input type="radio" name="sla" id="tidak" value="Tidak"-->
                                                <!--    checked="">-->
                                                <!--Tidak-->
                                                <!--<br>-->
                                                <!--<br>-->

                                                <span id="form-sla" style="display: none;">
                                                    <label>Durasi</label>
                                                    <select name="durasi_sla" class="form-control" required="">
                                                        <?php
                                                        echo '<option value="0">- Pilih salah satu -</option>';
                                                        for ($durasi = 1; $durasi <= 10; $durasi++) {
                                                            echo '<option value="' . $durasi . '">' . $durasi . ' Hari</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <br>

                                                    <label>Alasan</label>
                                                    <textarea name="alasan_sla" class="form-control" required="" placeholder="Harap di isi ...">Tidak ada ...</textarea>
                                                    <br>
                                                </span>

                                                <button type="submit" name="pengaduan" value="<?= $_GET['view'] ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class='bx bx-send'></i> Kirim
                                                </button>

                                            </form>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                @endif
            @elseif($data_session_pegawai->sebagai_posisi == 'Kepala Bagian Unit Kerja')
                @if ($data_pengaduan->klasifikasi_pengaduan == 'Medium' || $data_pengaduan->klasifikasi_pengaduan == 'High')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="cursor: pointer;" class="text-primary" data-toggle="collapse"
                                        data-target="#buat-jawaban">
                                        <b><i class='bx bxs-comment-add'></i> Buat Jawaban</b>
                                    </h4>

                                    <div class="row collapse" id="buat-jawaban">
                                        <div class="col-md-12">
                                            <hr style="border-style: dashed;">

                                            <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                                                action="<?= route('pengaduan.jawaban') ?>">
                                                <?= csrf_field() ?>

                                                <label>Jawaban</label>
                                                <textarea name="keterangan" class="form-control" required="" placeholder="Harap di isi ..."></textarea>
                                                <br>

                                                <div class="form-group">
                                                    <label>Unggah Foto (Opsional)</label>
                                                    <br>
                                                    <label for="file-1">
                                                        <img src="<?= url('logos/image.png') ?>" id="image-1"
                                                            style="width: 150px;border-radius: 5px;">
                                                        <input type="file" accept="image/*" name="foto"
                                                            id="file-1" class="form-control"
                                                            onchange="previewImage('image-1','file-1')"
                                                            style="display: none;">
                                                    </label>
                                                </div>

                                                {{-- <label>SLA</label><br>
                                                <input type="radio" name="sla" id="ya" value="Ya"> Ya
                                                &nbsp;/&nbsp;
                                                <input type="radio" name="sla" id="tidak" value="Tidak"
                                                    checked="">
                                                Tidak
                                                <br>
                                                <br>

                                                <span id="form-sla" style="display: none;">
                                                    <label>Durasi</label>
                                                    <select name="durasi_sla" class="form-control" required="">
                                                        <?php
                                                        echo '<option value="0">- Pilih salah satu -</option>';
                                                        for ($durasi = 1; $durasi <= 10; $durasi++) {
                                                            echo '<option value="' . $durasi . '">' . $durasi . ' Hari</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <br>

                                                    <label>Alasan</label>
                                                    <textarea name="alasan_sla" class="form-control" required="" placeholder="Harap di isi ...">Tidak ada ...</textarea>
                                                    <br>
                                                </span> --}}

                                                <button type="submit" name="pengaduan" value="<?= $_GET['view'] ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class='bx bx-send'></i> Kirim
                                                </button>

                                            </form>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                @endif
            @elseif($data_session_pegawai->sebagai_posisi == 'Kepala Unit Kerja')
                @if ($data_pengaduan->klasifikasi_pengaduan == 'High')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4 style="cursor: pointer;" class="text-primary" data-toggle="collapse"
                                        data-target="#buat-jawaban">
                                        <b><i class='bx bxs-comment-add'></i> Buat Jawaban</b>
                                    </h4>

                                    <div class="row collapse" id="buat-jawaban">
                                        <div class="col-md-12">
                                            <hr style="border-style: dashed;">

                                            <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                                                action="<?= route('pengaduan.jawaban') ?>">
                                                <?= csrf_field() ?>

                                                <label>Jawaban</label>
                                                <textarea name="keterangan" class="form-control" required="" placeholder="Harap di isi ..."></textarea>
                                                <br>

                                                <div class="form-group">
                                                    <label>Unggah Foto (Opsional)</label>
                                                    <br>
                                                    <label for="file-1">
                                                        <img src="<?= url('logos/image.png') ?>" id="image-1"
                                                            style="width: 150px;border-radius: 5px;">
                                                        <input type="file" accept="image/*" name="foto"
                                                            id="file-1" class="form-control"
                                                            onchange="previewImage('image-1','file-1')"
                                                            style="display: none;">
                                                    </label>
                                                </div>

                                                {{-- <label>SLA</label><br>
                                                <input type="radio" name="sla" id="ya" value="Ya"> Ya
                                                &nbsp;/&nbsp;
                                                <input type="radio" name="sla" id="tidak" value="Tidak"
                                                    checked="">
                                                Tidak
                                                <br>
                                                <br>

                                                <span id="form-sla" style="display: none;">
                                                    <label>Durasi</label>
                                                    <select name="durasi_sla" class="form-control" required="">
                                                        <?php
                                                        echo '<option value="0">- Pilih salah satu -</option>';
                                                        for ($durasi = 1; $durasi <= 10; $durasi++) {
                                                            echo '<option value="' . $durasi . '">' . $durasi . ' Hari</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                    <br>

                                                    <label>Alasan</label>
                                                    <textarea name="alasan_sla" class="form-control" required="" placeholder="Harap di isi ...">Tidak ada ...</textarea>
                                                    <br>
                                                </span> --}}

                                                <button type="submit" name="pengaduan" value="<?= $_GET['view'] ?>"
                                                    class="btn btn-sm btn-primary">
                                                    <i class='bx bx-send'></i> Kirim
                                                </button>

                                            </form>

                                        </div>
                                    </div>

                                </div>
                            </div>
                            <p>&nbsp;</p>
                        </div>
                    </div>
                @endif
            @endif




            <div class="row">
                <div class="col-md-12">
                    <h4>
                        <i class='bx bx-comment-detail'></i> Jawaban Pengaduan
                    </h4>
                    <p>&nbsp;</p>
                </div>
            </div>

            <div id="data_pagination">
                <!-- data pagination -->
            </div>

            <script>
                $(document).ready(function() {

                    $(document).on('click', '.pagination a', function(event) {
                        event.preventDefault();
                        var page = $(this).attr('href').split('page=')[1];
                        fetch_data(page);
                    });

                    function fetch_data(page) {
                        // preloader
                        document.getElementById('data_pagination').innerHTML =
                            '<div class="card"><div class="card-body" align="center"><img src="<?= url('logos/loader.gif') ?>" style="width: 150px;"><p class="text-primary">Sedang memproses ...</p></div></div>';

                        var http = new XMLHttpRequest();
                        var url = '<?= route('pengaduan.pagination') ?>?pengaduan=<?= $_GET['view'] ?>&page=' + page;
                        var params = '_token=<?= csrf_token() ?>';
                        http.open('POST', url, true);

                        //Send the proper header information along with the request
                        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                        http.onreadystatechange = function() { //Call a function when the state changes.
                            if (http.readyState == 4 && http.status == 200) {
                                document.getElementById('data_pagination').innerHTML = http.responseText;
                            }
                        }
                        http.send(params);
                    }

                    fetch_data(1);

                });
            </script>

        </div>
    </div>

    <span style="display: none;">
        <form method="GET" onsubmit="show(true)" id="form-update">
            <input type="text" name="update" id="input-update" readonly="" required="">
        </form>

        <form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('pengaduan.delete') ?>">
            <?= csrf_field() ?>
            <input type="text" name="delete" id="input-delete" readonly="" required="">
        </form>

        <form method="POST" onsubmit="show(true)" id="form-approve" action="<?= route('pengaduan.approve') ?>">
            <?= csrf_field() ?>
            <input type="text" name="pengaduan" id="input-approve" readonly="" required="">
        </form>

        <form method="POST" onsubmit="show(true)" id="form-checked" action="<?= route('pengaduan.checked') ?>">
            <?= csrf_field() ?>
            <input type="text" name="pengaduan" id="input-checked" readonly="" required="">
        </form>

        <form method="POST" onsubmit="show(true)" id="form-finish" action="<?= route('pengaduan.finish') ?>">
            <?= csrf_field() ?>
            <input type="text" name="pengaduan" id="input-finish" readonly="" required="">
        </form>
    </span>

    <script type="text/javascript">
        function delete_data(id, name) {
            var r = confirm('Hapus data ' + name + '?');
            if (r == true) {
                show(true);
                document.getElementById('input-delete').value = id;
                document.getElementById('form-delete').submit();
            }
        }

        function approve_data(id, name) {
            var r = confirm('Approve pengaduan ' + name + '?');
            if (r == true) {
                show(true);
                document.getElementById('input-approve').value = id;
                document.getElementById('form-approve').submit();
            }
        }

        function checked_data(id, name) {
            var r = confirm('Checked pengaduan ' + name + '?');
            if (r == true) {
                show(true);
                document.getElementById('input-checked').value = id;
                document.getElementById('form-checked').submit();
            }
        }

        function update_data(id) {
            show(true);
            document.getElementById('input-update').value = id;
            document.getElementById('form-update').submit();
        }

        function finish_data(id, name) {
            var r = confirm('Selesaikan pengaduan ' + name + '?');
            if (r == true) {
                show(true);
                document.getElementById('input-finish').value = id;
                document.getElementById('form-finish').submit();
            }
        }
    </script>

@stop

@section('script')

    <!-- Classic Modal -->
    <div class="modal fade" id="modal-tanggapi" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <b>
                                        <i class='bx bx-plus'></i> Buat Tanggapan
                                    </b>
                                </td>
                                <td align="right">
                                    <span class="text-danger" data-dismiss="modal" style="cursor: pointer;">
                                        <i class='bx bx-x-circle' style="font-size: 17px;"></i>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-body" id="content">
                    <!-- content here -->
                </div>
            </div>
        </div>
    </div>
    <!--  End Modal -->

    <!-- Modal -->
    <div class="modal fade" id="resolve" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Keterangan Solve</h5>
                    <span> <i class='bx bx-x-circle'></i></span>
                </div>
                <div class="modal-body">
                    <form action="{{ route('pengaduan.solved') }}" method="POST" id="form-solve">
                        @csrf
                        <input type="hidden" name="id_pengaduan" value="{{ $data_pengaduan->id_pengaduan }}">
                        <input type="hidden" name="id_pegawai" value="{{ $data_session_pegawai->id_pegawai }}">
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
    <!-- end modal -->


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

    <script type="text/javascript">
        function detail(id) {
            $('#modal-tanggapi').modal('show');

            document.getElementById('content').innerHTML =
                '<center><img src="<?= url('logos/loader.gif') ?>" style="width: 120px;"><p class="text-primary">Sedang memproses ...</p></center>';

            var http = new XMLHttpRequest();
            var url = '<?= route('pengaduan.form_tanggapan') ?>';
            var params = '_token=<?= csrf_token() ?>&id_jawaban=' + id + '&id_pengaduan=<?= $_GET['view'] ?>';
            http.open('POST', url, true);

            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function() { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {
                    document.getElementById('content').innerHTML = http.responseText;
                }
            }
            http.send(params);
        }
    </script>

    <script type="text/javascript">
        $('#kembali').on('click', function() {
            loadPage('<?= route('pengaduan.friend') ?>?filter=<?= $_GET['filter'] ?>');
        });
    </script>

    <script type="text/javascript">
        function previewImage(preview, source) {
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById(source).files[0]);

            oFReader.onload = function(oFREvent) {
                document.getElementById(preview).src = oFREvent.target.result;
            };
        };
    </script>

    {{-- <script type="text/javascript">
        $('#ya').on('click', function() {
            $('#form-sla').show();
        });
        $('#tidak').on('click', function() {
            $('#form-sla').hide();
        });
    </script> --}}

    <script type="text/javascript">
        function count_down(time, id) {
            // Set the date we're counting down to
            // var countDownDate = new Date("Jan 5, 2022 15:37:25").getTime();
            var countDownDate = new Date(time).getTime();

            // Update the count down every 1 second
            var x = setInterval(function() {

                // Get today's date and time
                var now = new Date().getTime();

                // Find the distance between now and the count down date
                var distance = countDownDate - now;

                // Time calculations for days, hours, minutes and seconds
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                if (hours.toString().length < 2) {
                    hours = '0' + hours;
                }

                if (minutes.toString().length < 2) {
                    minutes = '0' + minutes;
                }

                if (seconds.toString().length < 2) {
                    seconds = '0' + seconds;
                }

                // Output the result in an element with id="demo"
                document.getElementById(id).innerHTML = "<i class='bx bx-stopwatch'></i> " + hours + ":" + minutes +
                    ":" + seconds;

                // If the count down is over, write some text
                if (distance < 0) {
                    clearInterval(x);
                    document.getElementById(id).innerHTML = "<i class='bx bx-stopwatch'></i> -:-:-";
                }
            }, 1000);
        }

        <?php if($jawaban->count() < 1){ ?>

        count_down('<?= date('M d, Y H:i:s', strtotime($data_pengaduan->respon_pengaduan)) ?>', 'count-down');

        <?php } ?>
    </script>

    <?php if(session()->has('alert')){ ?>

    <!-- Classic Modal -->
    <div class="modal fade" id="modal-alert" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <table style="width: 100%;">
                        <tbody>
                            <tr>
                                <td>
                                    <b>
                                        Status
                                    </b>
                                </td>
                                <td align="right">
                                    <span class="text-danger" data-dismiss="modal" style="cursor: pointer;">
                                        <i class='bx bx-x-circle' style="font-size: 17px;"></i>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-body">
                    <?php
                    if (session()->has('alert')) {
                        $explode = explode('_', session()->get('alert'));
                        echo '
                                                                                                                                            				      <div class="alert alert-' .
                            $explode[0] .
                            '"><i class="bx bx-error-circle"></i> ' .
                            $explode[1] .
                            '</div>
                                                                                                                                            				    ';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!--  End Modal -->

    <script>
        $('#modal-alert').modal('show');
    </script>

    <?php } ?>

@stop
