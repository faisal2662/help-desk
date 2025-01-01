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
    $kantor_unit = DB::table('tb_kepala_unit_kerja')
        ->where('id_pegawai', $data_pegawai->id_pegawai)
        ->where('delete_kepala_unit_kerja', 'N')
        ->get();
    if ($kantor_unit->count() > 1) {
        // $kontak = DB::table('tb_kontak')
        // ->join('tb_pegawai','tb_kontak.created_pengaduan','=','tb_pegawai.id_pegawai')
        // ->where([['tb_kontak.dari_kontak', 'LIKE', '%'.$kantor.' - '.'%'],['tb_kontak.delete_kontak','N']])
        // ->orderBy('tb_kontak.tgl_kontak','DESC')
        // ->get();
        foreach ($kantor_unit as $data_kantor_unit) {
            # code...
        }
        $pengaduan = App\Models\Pengaduan::where('id_bagian_kantor_pusat', $data_kantor_unit->id_bagian_kantor_pusat)
            ->where('id_bagian_kantor_cabang', $data_kantor_unit->id_bagian_kantor_cabang)
            ->where('id_bagian_kantor_wilayah', $data_kantor_unit->id_bagian_kantor_wilayah)
            ->where('delete_pengaduan', 'N')
            ->get();
        foreach ($pengaduan as $value) {
            # code...
            $kontak = DB::table('tb_kontak')
                ->join('tb_pegawai', 'tb_kontak.created_pengaduan', '=', 'tb_pegawai.id_pegawai')
                ->where([['kode_pengaduan', 'P' . date('y' . '-0000' . $value->id_pengaduan)], ['tb_kontak.delete_kontak', 'N']])
                // ->where('tb_kontak.created_pengaduan', $data_pegawai->id_pegawai)
                ->orderBy('tb_kontak.tgl_kontak', 'DESC')
                ->get();
        }
    }
} else {
    $pengaduan = App\Models\Pengaduan::where('id_bagian_kantor_pusat', $data_pegawai->id_bagian_kantor_pusat)
        ->where('id_bagian_kantor_cabang', $data_pegawai->id_bagian_kantor_cabang)
        ->where('id_bagian_kantor_wilayah', $data_pegawai->id_bagian_kantor_wilayah)
        ->where('delete_pengaduan', 'N')
        ->get();
    foreach ($pengaduan as $value) {
        # code...
        $kontak = DB::table('tb_kontak')
            ->join('tb_pegawai', 'tb_kontak.created_pengaduan', '=', 'tb_pegawai.id_pegawai')
            ->where([['kode_pengaduan', 'P' . date('y' . '-0000' . $value->id_pengaduan)], ['tb_kontak.delete_kontak', 'N']])
            // ->where('tb_kontak.created_pengaduan', $data_pegawai->id_pegawai)
            ->orderBy('tb_kontak.tgl_kontak', 'DESC')
            ->get();
    }
    //                 // if($data_pegawai->id_bagian_kantor_pusat != 0){
    //                 //     $pengaduan = Pengaduan::where('id_from_bagian')
    //                 // } else if($data_pegawai->id_bagian_kantor_cabang != 0)
    //                 // {

    //                 // }else
    //                 dd($pengaduan);

    // $kontak = DB::table('tb_kontak')
    // ->join('tb_pegawai','tb_kontak.created_pengaduan','=','tb_pegawai.id_pegawai')
    // ->where([['tb_kontak.dari_kontak', $kantor.' - '.$bagian],['tb_kontak.delete_kontak','N']])

    // ->orderBy('tb_kontak.tgl_kontak','DESC')
    // ->get();
}

// }else{

// 	$kontak = DB::table('tb_kontak')
// 	->join('tb_pegawai','tb_kontak.created_pengaduan','=','tb_pegawai.id_pegawai')
// 	->where([['tb_kontak.kepada_kontak', $kantor.' - '.$bagian],['tb_kontak.delete_kontak','N']])
// 	->orderBy('tb_kontak.tgl_kontak','DESC')
// 	->get();

// }

?>

<?php if($kontak->count() < 1){ ?>

<center>
    <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
    <p>Belum ada Riwayat Chat.</p>
</center>

<?php }else{ ?>

<?php foreach($kontak as $data_kontak){ ?>

<?php
$delivery = DB::table('tb_log_kontak')
    ->where([['tb_log_kontak.id_kontak', $data_kontak->id_kontak], ['tb_log_kontak.id_pegawai', Session::get('id_pegawai')], ['tb_log_kontak.delete_log_kontak', 'N'], ['tb_log_kontak.status_log_kontak', 'Delivery']])
    ->get();

$log_kontak = DB::table('tb_log_kontak')
    ->where([['tb_log_kontak.id_kontak', $data_kontak->id_kontak], ['tb_log_kontak.id_pegawai', Session::get('id_pegawai')], ['tb_log_kontak.delete_log_kontak', 'N']])
    ->orderBy('tb_log_kontak.tgl_log_kontak', 'DESC')
    ->limit(1)
    ->get();
?>

<div class="row">
    <div class="col-md-12" style="cursor: pointer;" onclick="set_id_kontak_friend(<?= $data_kontak->id_kontak ?>);">
        <p <?= $id_kontak == $data_kontak->id_kontak ? 'class="bg-primary text-white" style="border-radius: 10px;padding: 10px;"' : '' ?>
            id="ellipsis">
            <span style="font-size: 11px;">
                <i class='bx bx-user'></i> <?= $data_kontak->nama_pegawai ?>
            </span><br>
            <b>
                <i class='bx bxs-coupon'></i> <?= $data_kontak->kode_pengaduan ?> - <?= $data_kontak->nama_pengaduan ?>
                <?php if($delivery->count() > 0){ ?>

                <span class="badge badge-danger"
                    style="zoom: 75%;position: absolute;top: 0;right: 0;border: 3px solid #fff;">
                    <?= number_format($delivery->count()) ?>
                </span>

                <?php } ?>
            </b><br>
            <span style="font-size: 11px;">
                <?php if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan'){ ?>

                <i class='bx bx-building-house'></i> <?= $data_kontak->kepada_kontak ?>

                <?php }else{ ?>

                <i class='bx bx-building-house'></i> <?= $data_kontak->dari_kontak ?>

                <?php } ?>
            </span>
            <br>
        </p>
        <?php if($log_kontak->count() > 0){ ?>

        <?php foreach ($log_kontak as $data_log_kontak); ?>

        {{-- <p id="ellipsis">
						<?= $data_log_kontak->role_log_kontak ?> : <?= htmlspecialchars($data_log_kontak->keterangan_log_kontak) ?>
					</p> --}}

        <?php } ?>
        <p class="text-muted">
            <?php if($log_kontak->count() > 0){ ?>

            <?php foreach ($log_kontak as $data_log_kontak); ?>

            <?= time_elapsed_string($data_log_kontak->tgl_log_kontak) ?>

            <?php }else{ ?>

            <?= time_elapsed_string($data_kontak->tgl_kontak) ?>

            <?php } ?>
        </p>
        <hr style="border-style: dashed;">
    </div>
</div>

<?php } ?>

<?php } ?>
