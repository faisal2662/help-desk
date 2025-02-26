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
    foreach($pengaduan as $data_pengaduan){

				// end kantor bagian pengaduan

			?>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    @if (!is_null($data_pegawai))
                        <b>
                            <img src="<?= asset('logos/avatar.png') ?>"
                                style="width: 20px;height: 20px;border-radius: 100%;">
                            <?= htmlspecialchars($data_pegawai->employee_name) ?>
                        </b>
                    @endif
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
                    <a href="{{ route('pengaduan.show_friend', $data_pengaduan->id_pengaduan )}}"
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

                if ($data_session_pegawai->sebagai_pegawai == 'Administrator') {
                    $data_pengaduan->action = '-';
                }
                if ($data_session_pegawai->sebagai_pegawai == 'PIC') {
                    if (isset($_GET['filter']) == 'Friend') {
                        echo '<hr style="border-style: dashed;">';
                        echo '<a href="javascript:;" onclick="' .
                            $klasifikasi .
                            '">
                                <span class="badge badge-danger">
                                    <i class="bx bx-category-alt"></i> Kategori
                                </span>
                            </a>';
                    }
                }

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

    <!-- Modal -->
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
                        <input type="hidden" name="id_pegawai" value="{{ $data_pegawai->id_pegawai ?? '' }}">
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
    <?php } ?>

</div>
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
