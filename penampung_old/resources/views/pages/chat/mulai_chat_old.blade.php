<?php
$where = [
    'id_kontak' => $id_kontak,
    'id_pegawai' => Session::get('id_pegawai'),
    'delete_log_kontak' => 'N',
];
$values = [
    'status_log_kontak' => 'Read',
];
DB::table('tb_log_kontak')->where($where)->update($values);

$where = [
    'room_chat' => $id_kontak,
    'delete_chat' => 'N',
];
$values = [
    'status_chat' => 'Read',
];
DB::table('tb_chat')->where($where)->where('id_pegawai', '!=', Session::get('id_pegawai'))->update($values);
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
$pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();

if ($pegawai->count() < 1) {
    header('Location: ' . route('keluar'));
    exit();
} else {
    foreach ($pegawai as $data_pegawai);

    $kantor = '-';
    $bagian = '-';

    if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();

        if ($unit_kerja->count() > 0) {
            foreach ($unit_kerja as $data_unit_kerja) {
                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                    ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                    ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_unit_kerja->id_bagian_kantor_pusat)
                    ->get();
                if ($kantor_pusat->count() > 0) {
                    foreach ($kantor_pusat as $data_kantor_pusat);
                    $kantor = $data_kantor_pusat->nama_kantor_pusat;
                    $bagian = 'Semua Bagian';
                }
            }
        } else {
            $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                ->get();
            if ($kantor_pusat->count() > 0) {
                foreach ($kantor_pusat as $data_kantor_pusat);
                $kantor = $data_kantor_pusat->nama_kantor_pusat;
                $bagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
            }
        }
    } elseif ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();

        if ($unit_kerja->count() > 0) {
            foreach ($unit_kerja as $data_unit_kerja) {
                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                    ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                    ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_unit_kerja->id_bagian_kantor_cabang)
                    ->get();
                if ($kantor_cabang->count() > 0) {
                    foreach ($kantor_cabang as $data_kantor_cabang);
                    $kantor = $data_kantor_cabang->nama_kantor_cabang;
                    $bagian = 'Semua Bagian';
                }
            }
        } else {
            $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                ->get();
            if ($kantor_cabang->count() > 0) {
                foreach ($kantor_cabang as $data_kantor_cabang);
                $kantor = $data_kantor_cabang->nama_kantor_cabang;
                $bagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
            }
        }
    } elseif ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();

        if ($unit_kerja->count() > 0) {
            foreach ($unit_kerja as $data_unit_kerja) {
                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                    ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                    ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_unit_kerja->id_bagian_kantor_wilayah)
                    ->get();
                if ($kantor_wilayah->count() > 0) {
                    foreach ($kantor_wilayah as $data_kantor_wilayah);
                    $kantor = $data_kantor_wilayah->nama_kantor_wilayah;
                    $bagian = 'Semua Bagian';
                }
            }
        } else {
            $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                ->get();
            if ($kantor_wilayah->count() > 0) {
                foreach ($kantor_wilayah as $data_kantor_wilayah);
                $kantor = $data_kantor_wilayah->nama_kantor_wilayah;
                $bagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
            }
        }
    }
}
?>

<?php
// if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan'){

if ($data_pegawai->level_pegawai == 'Kepala Unit Kerja') {
    $kontak = DB::table('tb_kontak')
        ->join('tb_pegawai', 'tb_kontak.created_pengaduan', '=', 'tb_pegawai.id_pegawai')
        ->where([['tb_kontak.dari_kontak', 'LIKE', '%' . $kantor . ' - ' . '%'], ['tb_kontak.delete_kontak', 'N']])
        ->where('tb_kontak.id_kontak', $id_kontak)
        ->orderBy('tb_kontak.tgl_kontak', 'DESC')
        ->get();
} else {
    $kontak = DB::table('tb_kontak')
        ->join('tb_pegawai', 'tb_kontak.created_pengaduan', '=', 'tb_pegawai.id_pegawai')
        ->where([['tb_kontak.dari_kontak', $kantor . ' - ' . $bagian], ['tb_kontak.delete_kontak', 'N']])
        ->where('tb_kontak.created_pengaduan', $data_pegawai->id_pegawai)
        ->where('tb_kontak.id_kontak', $id_kontak)
        ->orderBy('tb_kontak.tgl_kontak', 'DESC')
        ->get();
    // $pengaduan = App\Models\Pengaduan::where('id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat)
    //                 ->where('id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang)
    //                 ->where('id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah)
    //                 ->where('delete_pengaduan', 'N')
    //                 ->first();

    //                     $kontak = DB::table('tb_kontak')
    //                 ->join('tb_pegawai','tb_kontak.created_pengaduan','=','tb_pegawai.id_pegawai')
    //                     ->where([['kode_pengaduan', 'P'. date('y'. '-0000'. $pengaduan->id_pengaduan)],['tb_kontak.delete_kontak','N']])
    //                     // ->where('tb_kontak.created_pengaduan', $data_pegawai->id_pegawai)
    //                 ->orderBy('tb_kontak.tgl_kontak','DESC')
    //                 ->get();

    // dd($kontak);
}

// }else{

// 	$kontak = DB::table('tb_kontak')
// 	->join('tb_pegawai','tb_kontak.created_pengaduan','=','tb_pegawai.id_pegawai')
// 	->where([['tb_kontak.kepada_kontak', $kantor.' - '.$bagian],['tb_kontak.delete_kontak','N']])
// 	->where('tb_kontak.id_kontak', $id_kontak)
// 	->orderBy('tb_kontak.tgl_kontak','DESC')
// 	->get();

// }d

?>

<?php if($kontak->count() < 1){ ?>

<div class="card">
    <div class="card-body">
        <div class="card-title"><b><i class='bx bx-chat'></i> Mulai Chat</b></div>
        <hr style="border-style: dashed;">
        <center>
            <img src="<?= url('logos/chat.png') ?>" style="width: 170px;">
            <p>Silahkan lakukan pengaduan terlebih dahulu untuk memulai Chat.</p>
        </center>
    </div>
</div>
<p>&nbsp;</p>

<?php }else{ ?>

<?php foreach ($kontak as $data_kontak); ?>

<?php
$chat = DB::table('tb_chat')
    ->join('tb_pegawai', 'tb_chat.id_pegawai', '=', 'tb_pegawai.id_pegawai')
    ->where([['tb_chat.delete_chat', 'N'], ['tb_chat.room_chat', $data_kontak->id_kontak]])
    ->orderBy('tb_chat.tgl_chat', 'ASC')
    ->get();

$fromBagianPusat = 0;
$fromBagianCabang = 0;
$fromBagianWilayah = 0;

$kepala_unit = DB::table('tb_kepala_unit_kerja')
    ->where('id_pegawai', $data_pegawai->id_pegawai)
    ->where('delete_kepala_unit_kerja', 'N')
    ->get();
if ($kepala_unit->count() > 0) {
    foreach ($kepala_unit as $data_kepala_unit);
    if ($data_kepala_unit->id_bagian_kantor_pusat) {
        $fromBagianPusat = DB::table('tb_bagian_kantor_pusat')
            ->where('id_bagian_kantor_pusat', $data_kepala_unit->id_bagian_kantor_pusat)
            ->where('delete_bagian_kantor_pusat', 'N')
            ->first()->id_bagian_kantor_pusat;
    } elseif ($data_kepala_unit->id_bagian_kantor_cabang) {
        $fromBagianCabang = DB::table('tb_bagian_kantor_cabang')
            ->where('id_bagian_kantor_cabang', $data_kepala_unit->id_bagian_kantor_cabang)
            ->where('delete_bagian_kantor_cabang', 'N')
            ->first()->id_bagian_kantor_cabang;
    }
    if ($data_kepala_unit->id_bagian_kantor_wilayah) {
        $fromBagianWilayah = DB::table('tb_bagian_kantor_wilayah')
            ->where('id_bagian_kantor_wilayah', $data_kepala_unit->id_bagian_kantor_wilayah)
            ->where('delete_bagian_kantor_wilayah', 'N')
            ->first()->id_bagian_kantor_wilayah;
    }
} else {
    if ($data_pegawai->id_bagian_kantor_pusat != 0) {
        $fromBagianPusat = DB::table('tb_bagian_kantor_pusat')
            ->where('id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat)
            ->where('delete_bagian_kantor_pusat', 'N')
            ->first()->id_bagian_kantor_pusat;
    } elseif ($data_pegawai->id_bagian_kantor_cabang != 0) {
        # code...
        $fromBagianCabang = DB::table('tb_bagian_kantor_cabang')
            ->where('id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang)
            ->where('delete_bagian_kantor_cabang', 'N')
            ->first()->id_bagian_kantor_cabang;
    } elseif ($data_pegawai->id_bagian_kantor_wilayah != 0) {
        $frombBagianWilayah = DB::table('tb_bagian_kantor_wilayah')
            ->where('id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah)
            ->where('delete_bagian_kantor_wilayah', 'N')
            ->first()->id_bagian_kantor_wilayah;
    }
}

?>

<div class="card">
    <div class="card-body">
        <div class="card-title" style="margin-bottom: 0;">
            <span style="font-size: 11px;">
                <i class='bx bx-user'></i> <?= $data_kontak->nama_pegawai ?>
            </span><br>
            <b>
                <i class='bx bxs-coupon'></i> <?= $data_kontak->kode_pengaduan ?> - <?= $data_kontak->nama_pengaduan ?>
            </b><br>
            <span style="font-size: 11px;">
                <?php if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan'){ ?>

                <i class='bx bx-building-house'></i> <?= $data_kontak->kepada_kontak ?>

                <?php }else{ ?>

                <i class='bx bx-building-house'></i> <?= $data_kontak->dari_kontak ?>

                <?php } ?>
            </span>
        </div>
        <hr style="border-style: dashed;">
    </div>
</div>

<div class="card" style="margin-top: -30px;">
    <div class="card-body" id="group-chat" style="height: 385px;overflow-y: scroll;z-index: 17;overflow-x: hidden;">

        <?php if($chat->count() < 1){ ?>

        <center>
            <img src="<?= url('logos/edit.png') ?>" style="width: 170px;">
            <p>Belum ada chat saat ini.</p>
        </center>

        <?php }else{ ?>

        <?php foreach($chat as $data_chat){ ?>



        <?php if($data_chat->id_bagian_kantor_pusat == $fromBagianPusat && $data_chat->id_bagian_kantor_cabang == $fromBagianCabang && $data_chat->id_bagian_kantor_wilayah == $fromBagianWilayah){  ?>


        <div class="row" id="box-chat">
            <div class="col-md-6"></div>
            <div class="col-md-6" align="right" style="margin-bottom: 10px;">
                <label style="font-size: 12px;">
                    <img src="<?= url($data_chat->foto_pegawai) ?>"
                        style="width: 15px;height: 15px;border-radius: 100%;"> <?= $data_chat->nama_pegawai ?>
                    (<?= $data_chat->level_pegawai ?>)
                </label><br>

                <?php if($data_chat->status_chat == 'Delivery'){ ?>

                <label id="status-chat">
                    <i class='bx bx-check-double'></i>
                </label>

                <?php }else{ ?>

                <label id="status-chat">
                    <i class='bx bx-show-alt'></i>
                </label>

                <?php } ?>

                <span class="badge badge-primary"
                    style="font-size: 13px;font-weight:normal;border-radius: 10px 10px 0 10px;white-space: normal;text-align: left;">
                    <?= htmlspecialchars($data_chat->keterangan_chat) ?>
                </span>

                <br>
                <label class="text-muted" style="font-size: 9px;">
                    <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($data_chat->tgl_chat))) ?>
                </label>
            </div>
        </div>

        <?php }else{ ?>

        <div class="row" id="box-chat">
            <div class="col-md-6" style="margin-bottom: 10px;">
                <label style="font-size: 12px;">
                    <img src="<?= url($data_chat->foto_pegawai) ?>"
                        style="width: 15px;height: 15px;border-radius: 100%;"> <?= $data_chat->nama_pegawai ?>
                    (<?= $data_chat->level_pegawai ?>)
                </label><br>
                <span class="badge"
                    style="background-color: rgb(180, 178, 178);font-size: 13px;border-radius: 10px 10px 10px 0;font-weight:normal;white-space: normal;text-align: left;">
                    <?= htmlspecialchars($data_chat->keterangan_chat) ?>
                </span><br>
                <span style="font-size: 9px;">
                    <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($data_chat->tgl_chat))) ?>
                </span>
            </div>
        </div>

        <?php } ?>

        <?php } ?>

        <?php } ?>

    </div>
</div>

<p>&nbsp;</p>

<?php } ?>
