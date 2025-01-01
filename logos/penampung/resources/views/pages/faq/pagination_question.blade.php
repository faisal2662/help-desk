<?php
$pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();

// dd(auth()->user());
if ($pegawai->count() < 1) {
    header('Location: ' . route('faq'));
    exit();
} else {
    foreach ($pegawai as $data_pegawai);
}
?>

<?php
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
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) {
        $string = array_slice($string, 0, 1);
    }
    return $string ? implode(', ', $string) . ' Berlalu' : 'Baru Saja';
}
?>

<?php
$FAQ = DB::table('tb_faq')->where('tb_faq.id_kategori_faq', $id)->where('tb_faq.delete_faq', '=', 'N')->orderBy('tb_faq.urutan_faq', 'ASC')->paginate(12);

?>

<?php if($FAQ->count() < 1){ ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <center>
                    <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                    <p>Belum ada FAQ saat ini.</p>
                </center>
            </div>
        </div>
        <p>&nbsp;</p>
    </div>
</div>

<?php }else{ ?>

<div class="row">

    <?php $no = 1; foreach($FAQ as $data_FAQ){ ?>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                {{-- @if (auth()->user()->level_pegawai === 'Administrator' || auth()->user()->sebagai_pegawai === 'PIC')
                    <div class="text-primary" style="text-align: end;float: right; ">
                        <h3><a href=" {{ route('faq.detailQuest', $data_FAQ->id_faq) }} "> <i
                                    class='bx bx-list-ul'></i></a></h3>
                    </div>
                @endif --}}

                <h5 class="text-primary" style="cursor: pointer;" data-toggle="collapse"
                    data-target="#faq_<?= $data_FAQ->id_faq ?>">
                    <b> <?= $data_FAQ->pertanyaan_faq ?></b>
                </h5>

                <div class="row collapse" id="faq_<?= $data_FAQ->id_faq ?>">
                    <div class="col-md-12">
                        <hr style="border-style: dashed;">


                            <p><?= $data_FAQ->jawaban_faq ?></p>

                        <p class="text-muted">
                            <i class='bx bx-time-five'></i>
                            <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($data_FAQ->tgl_faq))) ?>
                        </p>

                        <?php if( $data_pegawai->sebagai_pegawai == 'Administrator'){ ?>

                        <hr style="border-style: dashed;">
                        <p>
                            <a href="{{ route('faq.editQuest', $data_FAQ->id_faq) }}">
                                <span class="badge badge-primary">
                                    <i class='bx bx-edit'></i> Perbarui
                                </span>
                            </a>

                            <a href="javascript:;"
                                onclick="delete_data(<?= $data_FAQ->id_faq ?>, '<?= $data_FAQ->pertanyaan_faq ?>');">
                                <span class="badge badge-danger">
                                    <i class='bx bx-trash'></i> Hapus
                                </span>
                            </a>
                        </p>

                        <?php } ?>
                    </div>
                </div>

            </div>
        </div>
        <p>&nbsp;</p>
    </div>

    <?php $no ++; } ?>

</div>

<div class="row">
    <div class="col-md-12">
        <div class="table-responsive">
            <?= $FAQ->links() ?>
            <p>&nbsp;</p>
        </div>
    </div>
</div>

<?php } ?>
