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



@extends('template')

@section('title')
    Pengaduan - Helpdesk
@stop



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
                                    <img src="<?= asset('logos/avatar.png') ?>"
                                        style="width: 20px;height: 20px;border-radius: 100%;">
                                    <?= htmlspecialchars($data_pegawai->employee_name ?? '-') ?>
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

                                    <b><i class='bx bxs-coupon'></i>
                                        <?= htmlspecialchars($data_pengaduan->nama_pengaduan) ?></b>

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
                                    @if ($data_pengaduan->klasifikasi_pengaduan == 'Low' || $data_pengaduan->klasifikasi_pengaduan == 'Medium')
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
                ->where([['tb_mengetahui.id_pengaduan', $id_pengaduan], ['tb_mengetahui.delete_mengetahui', 'N']])
                ->get();
            ?>

            <?php if($mengetahui->count() > 0){ ?>

            <?php
            $sla = DB::table('tb_jawaban')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_jawaban.id_pegawai')
                ->where([['tb_jawaban.id_pengaduan', $id_pengaduan], ['tb_jawaban.delete_jawaban', 'N'], ['tb_jawaban.sla_jawaban', 'Ya']])
                ->get();

            $dibaca = DB::table('tb_dibaca')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_dibaca.id_pegawai')
                ->where([['tb_dibaca.id_pengaduan', $id_pengaduan], ['tb_dibaca.delete_dibaca', 'N']])
                ->get();

            $alihkan = DB::table('tb_alihkan')
                ->join('tb_pegawai', 'tb_pegawai.id_pegawai', '=', 'tb_alihkan.id_pegawai')
                ->where([['tb_alihkan.id_pengaduan', $id_pengaduan], ['tb_alihkan.delete_alihkan', 'N']])
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
                                    <p> <b class="text-success">Note: </b> {{ $item->keterangan_solve }} </p>


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

                            @php
                                $checked = DB::table('tb_checked')
                                    ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                    ->first();
                                $approved = DB::table('tb_approved')
                                    ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                    ->first();
                                $read = DB::table('tb_dibaca')
                                    ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                    ->first();
                                $finish = DB::table('tb_selesai')
                                    ->where('id_pengaduan', $data_pengaduan->id_pengaduan)
                                    ->first();
                            @endphp
                            @if (!is_null($checked))
                                <p>
                                    <b class="text-primary">Checked By</b> :
                                    <?= htmlspecialchars($checked->created_by) ?>
                                </p>
                                <p>
                                    <b class="text-primary">Checked Date</b> :
                                    <?= date('j F Y, H:i', strtotime($checked->created_date)) ?>
                                </p>
                                <hr style="border-style: dashed;">
                            @endif
                            @if (!is_null($approved))
                                <p>
                                    <b class="text-warning">Approved By</b> :
                                    <?= htmlspecialchars($approved->created_by) ?>
                                </p>
                                <p>
                                    <b class="text-warning">Approved Date</b> :
                                    <?= date('j F Y, H:i', strtotime($approved->created_date)) ?>
                                </p>
                                <hr style="border-style: dashed;">
                            @endif
                            @if (!is_null($read))
                                <p>
                                    <b class="text-info">Read By</b> :
                                    <?= htmlspecialchars($read->created_by) ?>
                                </p>
                                <p>
                                    <b class="text-info">Read Date</b> :
                                    <?= date('j F Y, H:i', strtotime($read->tgl_dibaca)) ?>
                                </p>
                                <hr style="border-style: dashed;">
                            @endif
                            <?php if($alihkan->count() > 0){ ?>

                            <?php foreach($alihkan as $data_alihkan){ ?>

                            <p>
                                <b class="text-warning">Moved By</b> :
                                <?= htmlspecialchars($data_alihkan->employee_name) ?>
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

                            @if (!is_null($finish))
                                <p>
                                    <b class="text-success">Finish By</b> :
                                    <?= htmlspecialchars($finish->created_by) ?>
                                </p>
                                <p>
                                    <b class="text-success">Finish Date</b> :
                                    <?= date('j F Y, H:i', strtotime($finish->tgl_selesai)) ?>
                                </p>
                            @endif


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

                                                <button type="submit" name="pengaduan" value="<?= $id_pengaduan ?>"
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
                @if ($data_pengaduan->klasifikasi_pengaduan == 'Low' || $data_pengaduan->klasifikasi_pengaduan == 'Medium')
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

                                                <button type="submit" name="pengaduan" value="<?= $id_pengaduan ?>"
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

                                                <button type="submit" name="pengaduan" value="<?= $id_pengaduan ?>"
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
                        var url = '<?= route('pengaduan.pagination') ?>?pengaduan=<?= $id_pengaduan ?>&page=' + page;
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
            var params = '_token=<?= csrf_token() ?>&id_jawaban=' + id + '&id_pengaduan=<?= $id_pengaduan ?>';
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
            loadPage('<?= route('pengaduan.friend') ?>');
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
