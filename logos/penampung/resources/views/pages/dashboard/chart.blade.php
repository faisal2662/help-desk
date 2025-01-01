<?php
$bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$month = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
?>


<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://www.chartjs.org/samples/2.6.0/utils.js"></script>
<script type="text/javascript">
    var xValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        '<?= $data_bulan ?>',

        <?php } ?>
    ];



    var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        <?php
        $pengaduan_pending = [];
        $data_month = str_replace($bulan, $month, $data_bulan);

        if ($data_pegawai->sebagai_pegawai == 'Administrator') {
            $pengaduan_pending = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Pending')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() < 1) {
                $pengaduan_pending = DB::table('tb_pengaduan')
                    ->whereRaw(
                        '
                                                                        tb_pengaduan.id_pegawai IN (
                                                                            Select
                                                                                tb_pegawai.id_pegawai
                                                                            From
                                                                                tb_pegawai
                                                                            Where
                                                                                tb_pegawai.kantor_pegawai = "' .
                            $data_pegawai->kantor_pegawai .
                            '" And
                                                                                tb_pegawai.id_bagian_kantor_pusat = "' .
                            $data_pegawai->id_bagian_kantor_pusat .
                            '" And
                                                                                tb_pegawai.id_bagian_kantor_cabang = "' .
                            $data_pegawai->id_bagian_kantor_cabang .
                            '" And
                                                                                tb_pegawai.id_bagian_kantor_wilayah = "' .
                            $data_pegawai->id_bagian_kantor_wilayah .
                            '"
                                                                        )
                                                                    ',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Pending')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            } else {
                $pengaduan_pending = DB::table('tb_pengaduan')
                    ->where('tb_pengaduan.delete_pengaduan', 'N')
                    ->where('tb_pengaduan.status_pengaduan', 'Pending')
                    ->whereYear('tb_pengaduan.tgl_pengaduan', $tahun)
                    ->whereMonth('tb_pengaduan.tgl_pengaduan', $data_month)
                    ->whereIn('tb_pengaduan.id_pegawai', function ($query) use ($data_pegawai) {
                        $query
                            ->select('tb_pegawai.id_pegawai')
                            ->from('tb_pegawai')
                            ->whereIn('tb_pegawai.kantor_pegawai', function ($subQuery) use ($data_pegawai) {
                                $subQuery
                                    ->select('kantor_pegawai')
                                    ->from('tb_kepala_unit_kerja')
                                    ->where('delete_kepala_unit_kerja', 'N')
                                    ->where('id_pegawai', $data_pegawai->id_pegawai);
                            })
                            ->whereIn('tb_pegawai.id_bagian_kantor_pusat', function ($subQuery) use ($data_pegawai) {
                                $subQuery
                                    ->select('id_bagian_kantor_pusat')
                                    ->from('tb_kepala_unit_kerja')
                                    ->where('delete_kepala_unit_kerja', 'N')
                                    ->where('id_pegawai', $data_pegawai->id_pegawai);
                            })
                            ->whereIn('tb_pegawai.id_bagian_kantor_cabang', function ($subQuery) use ($data_pegawai) {
                                $subQuery
                                    ->select('id_bagian_kantor_cabang')
                                    ->from('tb_kepala_unit_kerja')
                                    ->where('delete_kepala_unit_kerja', 'N')
                                    ->where('id_pegawai', $data_pegawai->id_pegawai);
                            })
                            ->whereIn('tb_pegawai.id_bagian_kantor_wilayah', function ($subQuery) use ($data_pegawai) {
                                $subQuery
                                    ->select('id_bagian_kantor_wilayah')
                                    ->from('tb_kepala_unit_kerja')
                                    ->where('delete_kepala_unit_kerja', 'N')
                                    ->where('id_pegawai', $data_pegawai->id_pegawai);
                            });
                    })
                    ->get();
            }
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            $pengaduan_pending = DB::table('tb_pengaduan')
                ->whereRaw(
                    '
                                                                    tb_pengaduan.id_pegawai IN (
                                                                        Select
                                                                            tb_pegawai.id_pegawai
                                                                        From
                                                                            tb_pegawai
                                                                        Where
                                                                            tb_pegawai.kantor_pegawai = "' .
                        $data_pegawai->kantor_pegawai .
                        '" And
                                                                            tb_pegawai.id_bagian_kantor_pusat = "' .
                        $data_pegawai->id_bagian_kantor_pusat .
                        '" And
                                                                            tb_pegawai.id_bagian_kantor_cabang = "' .
                        $data_pegawai->id_bagian_kantor_cabang .
                        '" And
                                                                            tb_pegawai.id_bagian_kantor_wilayah = "' .
                        $data_pegawai->id_bagian_kantor_wilayah .
                        '"
                                                                    )
                                                                ',
                )
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Pending')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
            // $pengaduan_pending = DB::table('tb_pengaduan')
            //     ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
            //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            //     ->where('tb_pengaduan.status_pengaduan', '=', 'Pending')
            //     ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
            //     ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
            //     ->get();
            $pengaduan_pending = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Pending')
                ->whereYear('tb_pengaduan.tgl_pengaduan', $tahun) // Filter berdasarkan tahun
                ->whereMonth('tb_pengaduan.tgl_pengaduan', $data_month) // Filter berdasarkan bulan
                ->get();
        }
        // dd($data_pegawai->NamaPosisi->sebagai_posisi);
        // dd($pengaduan_pending);
        if (!$data_pegawai->NamaPosisi || $data_pegawai->NamaPosisi->sebagai_posisi == '') {
            $pengaduan_pending = 0;
        } else {
            $pengaduan_pending->count();
        }
        ?>

        <?php } ?>
    ];

    new Chart("pengaduan-pending", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Jumlah",
                fill: false,
                backgroundColor: window.chartColors.yellow,
                borderColor: window.chartColors.yellow,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    });


    var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        <?php
        $data_month = str_replace($bulan, $month, $data_bulan);

        if ($data_pegawai->sebagai_pegawai == 'Administrator') {
            $pengaduan_Approve = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() < 1) {
                $pengaduan_Approve = DB::table('tb_pengaduan')
                    ->whereRaw(
                        '
                                                        								tb_pengaduan.id_pegawai IN (
                                                        									Select
                                                        										tb_pegawai.id_pegawai
                                                        									From
                                                        										tb_pegawai
                                                        									Where
                                                        										tb_pegawai.kantor_pegawai = "' .
                            $data_pegawai->kantor_pegawai .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_pusat = "' .
                            $data_pegawai->id_bagian_kantor_pusat .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_cabang = "' .
                            $data_pegawai->id_bagian_kantor_cabang .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_wilayah = "' .
                            $data_pegawai->id_bagian_kantor_wilayah .
                            '"
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            } else {
                $pengaduan_Approve = DB::table('tb_pengaduan')
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
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_pusat IN (
                                                        											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_cabang IN (
                                                        											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_wilayah IN (
                                                        											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										)
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            }
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            $pengaduan_Approve = DB::table('tb_pengaduan')
                ->whereRaw(
                    '
                                                        							tb_pengaduan.id_pegawai IN (
                                                        								Select
                                                        									tb_pegawai.id_pegawai
                                                        								From
                                                        									tb_pegawai
                                                        								Where
                                                        									tb_pegawai.kantor_pegawai = "' .
                        $data_pegawai->kantor_pegawai .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_pusat = "' .
                        $data_pegawai->id_bagian_kantor_pusat .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_cabang = "' .
                        $data_pegawai->id_bagian_kantor_cabang .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_wilayah = "' .
                        $data_pegawai->id_bagian_kantor_wilayah .
                        '"
                                                        							)
                                                        						',
                )
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
            $pengaduan_Approve = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Approve')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        }
        if (!$data_pegawai->NamaPosisi || $data_pegawai->NamaPosisi->sebagai_pegawai == '') {
            $pengaduan_approve = 0;
        } else {
            $pengaduan_approve->count();
        }
        ?>

        <?php } ?>
    ];

    new Chart("pengaduan-approve", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Jumlah",
                fill: false,
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.blue,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    });

    var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        <?php
        $data_month = str_replace($bulan, $month, $data_bulan);

        if ($data_pegawai->sebagai_pegawai == 'Administrator') {
            $pengaduan_on_progress = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'On Progress')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() < 1) {
                $pengaduan_on_progress = DB::table('tb_pengaduan')
                    ->whereRaw(
                        '
                                        								tb_pengaduan.id_pegawai IN (
                                        									Select
                                        										tb_pegawai.id_pegawai
                                        									From
                                        										tb_pegawai
                                        									Where
                                        										tb_pegawai.kantor_pegawai = "' .
                            $data_pegawai->kantor_pegawai .
                            '" And
                                        										tb_pegawai.id_bagian_kantor_pusat = "' .
                            $data_pegawai->id_bagian_kantor_pusat .
                            '" And
                                        										tb_pegawai.id_bagian_kantor_cabang = "' .
                            $data_pegawai->id_bagian_kantor_cabang .
                            '" And
                                        										tb_pegawai.id_bagian_kantor_wilayah = "' .
                            $data_pegawai->id_bagian_kantor_wilayah .
                            '"
                                        								)
                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'On Progress')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            } else {
                $pengaduan_on_progress = DB::table('tb_pengaduan')
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
                            $data_pegawai->id_pegawai .
                            '"
                                        										) And
                                        										tb_pegawai.id_bagian_kantor_pusat IN (
                                        											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                        											delete_kepala_unit_kerja = "N" And
                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                        										) And
                                        										tb_pegawai.id_bagian_kantor_cabang IN (
                                        											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                        											delete_kepala_unit_kerja = "N" And
                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                        										) And
                                        										tb_pegawai.id_bagian_kantor_wilayah IN (
                                        											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                        											delete_kepala_unit_kerja = "N" And
                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                        										)
                                        								)
                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'On Progress')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            }
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            $pengaduan_on_progress = DB::table('tb_pengaduan')
                ->whereRaw(
                    '
                                        							tb_pengaduan.id_pegawai IN (
                                        								Select
                                        									tb_pegawai.id_pegawai
                                        								From
                                        									tb_pegawai
                                        								Where
                                        									tb_pegawai.kantor_pegawai = "' .
                        $data_pegawai->kantor_pegawai .
                        '" And
                                        									tb_pegawai.id_bagian_kantor_pusat = "' .
                        $data_pegawai->id_bagian_kantor_pusat .
                        '" And
                                        									tb_pegawai.id_bagian_kantor_cabang = "' .
                        $data_pegawai->id_bagian_kantor_cabang .
                        '" And
                                        									tb_pegawai.id_bagian_kantor_wilayah = "' .
                        $data_pegawai->id_bagian_kantor_wilayah .
                        '"
                                        							)
                                        						',
                )
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'On Progress')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
            $pengaduan_on_progress = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'On Progress')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        }

        if (!$data_pegawai->NamaPosisi || $data_pegawai->NamaPosisi->sebagai_pegawai == '') {
            $pengaduan_on_progress = 0;
        } else {
            $pengaduan_on_progress->count();
        }

        ?>

        <?php } ?>
    ];

    new Chart("pengaduan-on-progress", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Jumlah",
                fill: false,
                backgroundColor: window.chartColors.purple,
                borderColor: window.chartColors.purple,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    });

    var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        <?php
        $data_month = str_replace($bulan, $month, $data_bulan);

        if ($data_pegawai->sebagai_pegawai == 'Administrator') {
            $pengaduan_holding = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Holding')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() < 1) {
                $pengaduan_holding = DB::table('tb_pengaduan')
                    ->whereRaw(
                        '
                                                        								tb_pengaduan.id_pegawai IN (
                                                        									Select
                                                        										tb_pegawai.id_pegawai
                                                        									From
                                                        										tb_pegawai
                                                        									Where
                                                        										tb_pegawai.kantor_pegawai = "' .
                            $data_pegawai->kantor_pegawai .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_pusat = "' .
                            $data_pegawai->id_bagian_kantor_pusat .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_cabang = "' .
                            $data_pegawai->id_bagian_kantor_cabang .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_wilayah = "' .
                            $data_pegawai->id_bagian_kantor_wilayah .
                            '"
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Holding')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            } else {
                $pengaduan_holding = DB::table('tb_pengaduan')
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
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_pusat IN (
                                                        											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_cabang IN (
                                                        											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_wilayah IN (
                                                        											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										)
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Holding')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            }
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            $pengaduan_holding = DB::table('tb_pengaduan')
                ->whereRaw(
                    '
                                                        							tb_pengaduan.id_pegawai IN (
                                                        								Select
                                                        									tb_pegawai.id_pegawai
                                                        								From
                                                        									tb_pegawai
                                                        								Where
                                                        									tb_pegawai.kantor_pegawai = "' .
                        $data_pegawai->kantor_pegawai .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_pusat = "' .
                        $data_pegawai->id_bagian_kantor_pusat .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_cabang = "' .
                        $data_pegawai->id_bagian_kantor_cabang .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_wilayah = "' .
                        $data_pegawai->id_bagian_kantor_wilayah .
                        '"
                                                        							)
                                                        						',
                )
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Holding')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
            $pengaduan_holding = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Holding')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        }
        if (!$data_pegawai->NamaPosisi || $data_pegawai->NamaPosisi->sebagai_pegawai == '') {
            $pengaduan_holding = 0;
        } else {
            $pengaduan_holding->count();
        }
        ?>

        <?php } ?>
    ];

    new Chart("pengaduan-holding", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Jumlah",
                fill: false,
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    });

    var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        <?php
        $data_month = str_replace($bulan, $month, $data_bulan);

        if ($data_pegawai->sebagai_pegawai == 'Administrator') {
            $pengaduan_finish = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Finish')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() < 1) {
                $pengaduan_finish = DB::table('tb_pengaduan')
                    ->whereRaw(
                        '
                                                        								tb_pengaduan.id_pegawai IN (
                                                        									Select
                                                        										tb_pegawai.id_pegawai
                                                        									From
                                                        										tb_pegawai
                                                        									Where
                                                        										tb_pegawai.kantor_pegawai = "' .
                            $data_pegawai->kantor_pegawai .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_pusat = "' .
                            $data_pegawai->id_bagian_kantor_pusat .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_cabang = "' .
                            $data_pegawai->id_bagian_kantor_cabang .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_wilayah = "' .
                            $data_pegawai->id_bagian_kantor_wilayah .
                            '"
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Finish')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            } else {
                $pengaduan_finish = DB::table('tb_pengaduan')
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
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_pusat IN (
                                                        											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_cabang IN (
                                                        											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_wilayah IN (
                                                        											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										)
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Finish')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            }
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            $pengaduan_finish = DB::table('tb_pengaduan')
                ->whereRaw(
                    '
                                                        							tb_pengaduan.id_pegawai IN (
                                                        								Select
                                                        									tb_pegawai.id_pegawai
                                                        								From
                                                        									tb_pegawai
                                                        								Where
                                                        									tb_pegawai.kantor_pegawai = "' .
                        $data_pegawai->kantor_pegawai .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_pusat = "' .
                        $data_pegawai->id_bagian_kantor_pusat .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_cabang = "' .
                        $data_pegawai->id_bagian_kantor_cabang .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_wilayah = "' .
                        $data_pegawai->id_bagian_kantor_wilayah .
                        '"
                                                        							)
                                                        						',
                )
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Finish')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
            $pengaduan_finish = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Finish')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        }
        if (!$data_pegawai->NamaPosisi || $data_pegawai->NamaPosisi->sebagai_pegawai == '') {
            $pengaduan_finish = 0;
        } else {
            $pengaduan_finish->count();
        }
        ?>



        <?php } ?>
    ];

    new Chart("pengaduan-finish", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Jumlah",
                fill: false,
                backgroundColor: window.chartColors.green,
                borderColor: window.chartColors.green,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    });

    var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>

        <?php
        $data_month = str_replace($bulan, $month, $data_bulan);

        if ($data_pegawai->sebagai_pegawai == 'Administrator') {
            $pengaduan_late = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Late')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() < 1) {
                $pengaduan_late = DB::table('tb_pengaduan')
                    ->whereRaw(
                        '
                                                        								tb_pengaduan.id_pegawai IN (
                                                        									Select
                                                        										tb_pegawai.id_pegawai
                                                        									From
                                                        										tb_pegawai
                                                        									Where
                                                        										tb_pegawai.kantor_pegawai = "' .
                            $data_pegawai->kantor_pegawai .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_pusat = "' .
                            $data_pegawai->id_bagian_kantor_pusat .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_cabang = "' .
                            $data_pegawai->id_bagian_kantor_cabang .
                            '" And
                                                        										tb_pegawai.id_bagian_kantor_wilayah = "' .
                            $data_pegawai->id_bagian_kantor_wilayah .
                            '"
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Late')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            } else {
                $pengaduan_late = DB::table('tb_pengaduan')
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
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_pusat IN (
                                                        											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_cabang IN (
                                                        											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										) And
                                                        										tb_pegawai.id_bagian_kantor_wilayah IN (
                                                        											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                                        											delete_kepala_unit_kerja = "N" And
                                                        											id_pegawai = "' .
                            $data_pegawai->id_pegawai .
                            '"
                                                        										)
                                                        								)
                                                        							',
                    )
                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                    ->where('tb_pengaduan.status_pengaduan', '=', 'Late')
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                    ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                    ->get();
            }
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
            $pengaduan_late = DB::table('tb_pengaduan')
                ->whereRaw(
                    '
                                                        							tb_pengaduan.id_pegawai IN (
                                                        								Select
                                                        									tb_pegawai.id_pegawai
                                                        								From
                                                        									tb_pegawai
                                                        								Where
                                                        									tb_pegawai.kantor_pegawai = "' .
                        $data_pegawai->kantor_pegawai .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_pusat = "' .
                        $data_pegawai->id_bagian_kantor_pusat .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_cabang = "' .
                        $data_pegawai->id_bagian_kantor_cabang .
                        '" And
                                                        									tb_pegawai.id_bagian_kantor_wilayah = "' .
                        $data_pegawai->id_bagian_kantor_wilayah .
                        '"
                                                        							)
                                                        						',
                )
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Late')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        } elseif ($data_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
            $pengaduan_late = DB::table('tb_pengaduan')
                ->where('tb_pengaduan.id_pegawai', '=', $data_pegawai->id_pegawai)
                ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                ->where('tb_pengaduan.status_pengaduan', '=', 'Late')
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
                ->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
                ->get();
        }
        if (!$data_pegawai->NamaPosisi || $data_pegawai->NamaPosisi->sebagai_posisi == '') {
            $pengaduan_late = 0;
        } else {
            $pengaduan_late->count();
        }
        ?>


        <?php } ?>
    ];

    new Chart("pengaduan-late", {
        type: "line",
        data: {
            labels: xValues,
            datasets: [{
                label: "Jumlah",
                fill: false,
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: yValues
            }]
        },
        options: {
            responsive: true,
            legend: {
                display: false
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },
            hover: {
                mode: 'nearest',
                intersect: true
            }
        }
    });
</script>
