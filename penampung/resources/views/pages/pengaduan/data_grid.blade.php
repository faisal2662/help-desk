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

@if ($pengaduan->count() < 1)



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
@else
    <div class="row">
        @foreach ($pengaduan as $data_pengaduan)
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            @foreach ($pegawai as $data_pegawai)
                                <b>
                                    <img src="<?= asset($data_pegawai->foto_pegawai) ?>"
                                        style="width: 20px;height: 20px;border-radius: 100%;">
                                    <?= htmlspecialchars($data_pegawai->employee_name) ?>
                                </b>
                            @endforeach
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
                            <a href="{{ route('pengaduan.show', ['id' => $data_pengaduan->id_pengaduan, 'status' => request('filter')]) }}"
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

                        <?php

                        if ($data_session_pegawai->sebagai_pegawai == 'Administrator') {
                            if ($data_pengaduan->status_pengaduan == 'Approve') {
                                echo '<hr style="border-style: dashed;">';
                                echo '<a href="?filter=' .
                                    $_GET['filter'] .
                                    '&alihkan=' .
                                    $data_pengaduan->id_pengaduan .
                                    '">
                                        <span class="badge badge-primary">
                                            <i class="bx bx-redo"></i> Alihkan
                                            </span>
                                        </a>
                                        <a href="javascript:;" onclick="' .
                                    $klasifikasi .
                                    '">
                                        <span class="badge badge-danger">
                                            <i class="bx bx-category-alt"></i> Kategori
                                    </span>
                                        </a>';
                            } else {
                                $data_pengaduan->action = '-';
                            }
                            if ($data_pengaduan->status_pengaduan == 'On Progress' || $data_pengaduan->status_pengaduan == 'Read') {
                                echo '<hr style="border-style: dashed;">';
                                echo '<a href="javascript:;" onclick="' .
                                    $klasifikasi .
                                    '">
                                        <span class="badge badge-danger">
                                            <i class="bx bx-category-alt"></i> Kategori
                                    </span>
                                        </a>';
                            } else {
                                $data_pengaduan->action = '-';
                            }
                        } elseif ($data_session_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja') {
                            if ($data_pengaduan->status_pengaduan == 'Checked') {
                                echo '<hr style="border-style: dashed;">';
                                echo '
                                <a href="javascript:;" onclick="' .
                                    $approve .
                                    '">
                                        <span class="badge badge-info">
                                            <i class="bx bx-check-shield"></i> Approve
                                        </span>
                                    </a>
                                ';
                            } else {
                                // $data_pengaduan->action = '-';
                            }

                            if ($data_pengaduan->status_pengaduan == 'On Progress') {
                                echo '<hr style="border-style: dashed;">';
                                echo '
                                    <a href="javascript:;" onclick="' .
                                    $finish .
                                    '">
                                    <span class="badge badge-success">
                                        <i class="bx bx-check-double"></i> Finish
                                    </span>
                                        </a>
                                    ';
                            } else {
                                // $data_pengaduan->action = '-';
                            }
                        } elseif ($data_session_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') {
                            if ($data_pengaduan->status_pengaduan == 'Pending') {
                                echo '<hr style="border-style: dashed;">';
                                echo '
                                                    <a href="?filter=' .
                                    $_GET['filter'] .
                                    '&lampiran=' .
                                    $data_pengaduan->id_pengaduan .
                                    '">
                                    <span class="badge badge-info">
                                        <i class="bx bx-layer-plus"></i> Lampiran
                                    </span>
                                        </a>
                                        <a href="?filter=' .
                                    $_GET['filter'] .
                                    '&update=' .
                                    $data_pengaduan->id_pengaduan .
                                    '">
                                        <span class="badge badge-primary">
                                            <i class="bx bx-edit"></i> Ubah
                                        </span>
                                    </a>
                                    <a href="javascript:;" onclick="' .
                                    $delete .
                                    '">
                                            <span class="badge badge-danger">
                                                <i class="bx bx-trash"></i> Hapus
                                            </span>
                                        </a>
                                        <a href="javascript:;" onclick="' .
                                    $checked .
                                    '">
                                        <span class="badge badge-warning">
                                            <i class="bx bx-check"></i> Checked
                                        </span>
                                    </a>
                                    ';
                            } else {
                                if ($data_pengaduan->status_pengaduan == 'On Progress') {
                                    echo '<hr style="border-style: dashed;">';
                                    echo '
                                                    <a href="javascript:;" onclick="' .
                                        $finish .
                                        '">
                                                <span class="badge badge-success">
                                                    <i class="bx bx-check-double"></i> Finish
                                                </span>
                                            </a>
                                        ';
                                } else {
                                    // $data_pengaduan->action = '-';
                                }
                            }
                        } elseif ($data_session_pegawai->NamaPosisi->sebagai_posisi == 'Staff' || $data_session_pegawai->NamaPosisi->sebagai_posisi == 'Staf') {
                            if ($data_pengaduan->status_pengaduan == 'Pending') {
                                echo '<hr style="border-style: dashed;">';
                                echo '
                                                        <a href="?filter=' .
                                    $_GET['filter'] .
                                    '&lampiran=' .
                                    $data_pengaduan->id_pengaduan .
                                    '">
                                            <span class="badge badge-info">
                                                <i class="bx bx-layer-plus"></i> Lampiran
                                            </span>
                                        </a>
                                        <a href="?filter=' .
                                    $_GET['filter'] .
                                    '&update=' .
                                    $data_pengaduan->id_pengaduan .
                                    '">
                                        <span class="badge badge-primary">
                                            <i class="bx bx-edit"></i> Ubah
                                        </span>
                                    </a>
                                    <a href="javascript:;" onclick="' .
                                    $delete .
                                    '">
                                            <span class="badge badge-danger">
                                                <i class="bx bx-trash"></i> Hapus
                                            </span>
                                        </a>
                                            ';
                            } else {
                                if ($data_pengaduan->status_pengaduan == 'On Progress' || $data_pengaduan->status_pengaduan == 'Solved' || $data_pengaduan->status_pengaduan == 'Read') {
                                    echo '<hr style="border-style: dashed;">';
                                    echo '
                                                            <a href="javascript:;" onclick="' .
                                        $finish .
                                        '">
                                                <span class="badge badge-success">
                                                    <i class="bx bx-check-double"></i> Finish
                                                </span>
                                            </a>
                                            ';
                                } else {
                                    // $data_pengaduan->action = '-';
                                }
                            }
                        }

                        ?>

                    </div>
                </div>
                <p>&nbsp;</p>
            </div>
        @endforeach

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
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <?= $pengaduan->links() ?>
                <p>&nbsp;</p>
            </div>
        </div>
    </div>
@endif
