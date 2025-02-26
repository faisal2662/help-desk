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
    'Approve' => 'info',
    'Read' => 'info',
    'Holding' => 'danger',
    'Moving' => 'danger',
    'Solved' => 'primary',
    'On Progress' => 'primary',
    'Late' => 'danger',
    'Finish' => 'success',
];

$session_pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();

if ($session_pegawai->count() < 1) {
    header('Location: ' . route('keluar'));
    exit();
} else {
    foreach ($session_pegawai as $data_session_pegawai);

    if ($data_session_pegawai->sebagai_pegawai == 'Administrator') {
        $pengaduan = DB::table('tb_pengaduan')
            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
            ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
            ->where('tb_pengaduan.status_pengaduan', '!=', 'Checked')
            ->where('tb_pengaduan.status_pengaduan', '!=', 'Finish')
            ->where('tb_pengaduan.id_pengaduan', '=', $_GET['alihkan'])
            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
            ->get();
    }

    // else if($data_session_pegawai->sebagai_pegawai == 'Petugas' && $data_session_pegawai->level_pegawai != 'Administrator'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
    // 	->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
    // 	->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
    // 	->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Pending')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Checked')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Finish')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['alihkan'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }else if($data_session_pegawai->sebagai_pegawai == 'Agent'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where('tb_pengaduan.kantor_pengaduan','=', $data_session_pegawai->kantor_pegawai)
    // 	->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_session_pegawai->id_bagian_kantor_pusat)
    // 	->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_session_pegawai->id_bagian_kantor_cabang)
    // 	->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_session_pegawai->id_bagian_kantor_wilayah)
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Pending')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Checked')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Finish')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['alihkan'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai != 'Staff'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->whereRaw('
    // 		tb_pengaduan.id_pegawai IN (
    // 			Select
    // 				tb_pegawai.id_pegawai
    // 			From
    // 				tb_pegawai
    // 			Where
    // 				tb_pegawai.kantor_pegawai = "'.$data_session_pegawai->kantor_pegawai.'" And
    // 				tb_pegawai.id_bagian_kantor_pusat = "'.$data_session_pegawai->id_bagian_kantor_pusat.'" And
    // 				tb_pegawai.id_bagian_kantor_cabang = "'.$data_session_pegawai->id_bagian_kantor_cabang.'" And
    // 				tb_pegawai.id_bagian_kantor_wilayah = "'.$data_session_pegawai->id_bagian_kantor_wilayah.'"
    // 		)
    // 	')
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Pending')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Finish')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['alihkan'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }else if($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff'){

    // 	$pengaduan = DB::table('tb_pengaduan')
    // 	->where('tb_pengaduan.id_pegawai','=', $data_session_pegawai->id_pegawai)
    // 	->where('tb_pengaduan.delete_pengaduan','=','N')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Pending')
    // 	->where('tb_pengaduan.status_pengaduan','!=','Finish')
    // 	->where('tb_pengaduan.id_pengaduan','=', $_GET['alihkan'])
    // 	->orderBy('tb_pengaduan.id_pengaduan','DESC')
    // 	->get();

    // }
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

    // $pegawai = DB::table('tb_pegawai')
    // ->where('id_pegawai', '=', Session::get('id_pegawai'))
    // ->where([['tb_pegawai.kantor_pegawai', $data_pengaduan->kantor_pengaduan],['tb_pegawai.id_bagian_kantor_pusat', $data_pengaduan->id_bagian_kantor_pusat],['tb_pegawai.id_bagian_kantor_cabang', $data_pengaduan->id_bagian_kantor_cabang],['tb_pegawai.id_bagian_kantor_wilayah', $data_pengaduan->id_bagian_kantor_wilayah],['tb_pegawai.sebagai_pegawai','Agent'],['tb_pegawai.level_pegawai','!=','Kepala Unit Kerja'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.delete_pegawai','N']])
    // ->get();

    // if($pegawai->count() < 1){
    // 	header('Location: '.route('pengaduan'));
    // 	exit();
    // }

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
        // $kantor_pusat = DB::table('tb_kantor_pusat')
        //     ->join('tb_bagian_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
        //     ->where('tb_kantor_pusat.id_kantor_pusat', '=', $data_pengaduan->id_bagian_kantor_pusat)
        //     ->get();
        // if ($kantor_pusat->count() > 0) {
        //     foreach ($kantor_pusat as $data_kantor_pusat);
        //     $kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
        //     $bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
        // }
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
        // $kantor_cabang = DB::table('tb_kantor_cabang')
        //     ->join('tb_bagian_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
        //     ->where('tb_kantor_cabang.id_kantor_cabang', '=', $data_pengaduan->id_bagian_kantor_cabang)
        //     ->get();
        // if ($kantor_cabang->count() > 0) {
        //     foreach ($kantor_cabang as $data_kantor_cabang);
        //     $kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
        //     $bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
        // }
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
        // $kantor_wilayah = DB::table('tb_kantor_wilayah')
        //     ->join('tb_bagian_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
        //     ->where('tb_kantor_wilayah.id_kantor_wilayah', '=', $data_pengaduan->id_bagian_kantor_wilayah)
        //     ->get();
        // if ($kantor_wilayah->count() > 0) {
        //     foreach ($kantor_wilayah as $data_kantor_wilayah);
        //     $kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
        //     $bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
        // }
    }
    // end kantor bagian pengaduan
}
// dd($data_pengaduan);
?>

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-redo'></i> Alihkan Pengaduan</b></div>
                    <hr style="border-style: dashed;">
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
                    @php

                        $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
                        $kantor_pusat = DB::table('tb_kantor_pusat')
                            ->where('delete_kantor_pusat', 'N')
                            ->orderBy('nama_kantor_pusat', 'ASC')
                            ->get();
                        $kantor_cabang = DB::table('tb_kantor_cabang')
                            ->where('delete_kantor_cabang', '=', 'N')
                            ->orderBy('nama_kantor_cabang', 'ASC')
                            ->get();

                        $kantor_wilayah = DB::table('tb_kantor_wilayah')
                            ->where('delete_kantor_wilayah', '=', 'N')
                            ->orderBy('nama_kantor_wilayah', 'ASC')
                            ->get();
                        $bagian_kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                            ->join(
                                'tb_kantor_pusat',
                                'tb_kantor_pusat.id_kantor_pusat',
                                '=',
                                'tb_bagian_kantor_pusat.id_kantor_pusat',
                            )
                            ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', '=', 'N')
                            ->orderBy('tb_bagian_kantor_pusat.nama_bagian_kantor_pusat', 'ASC')
                            ->get();

                        $bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                            ->join(
                                'tb_kantor_cabang',
                                'tb_kantor_cabang.id_kantor_cabang',
                                '=',
                                'tb_bagian_kantor_cabang.id_kantor_cabang',
                            )
                            ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', '=', 'N')
                            ->orderBy('tb_bagian_kantor_cabang.nama_bagian_kantor_cabang', 'ASC')
                            ->get();

                        $bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                            ->join(
                                'tb_kantor_wilayah',
                                'tb_kantor_wilayah.id_kantor_wilayah',
                                '=',
                                'tb_bagian_kantor_wilayah.id_kantor_wilayah',
                            )
                            ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', '=', 'N')
                            ->orderBy('tb_bagian_kantor_wilayah.nama_bagian_kantor_wilayah', 'ASC')
                            ->get();
                    @endphp
                    <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                        action="<?= route('pengaduan.alihkan') ?>?filter=<?= $_GET['filter'] ?>">
                        <?= csrf_field() ?>
                        <label>Unit Kerja</label>
                        <select name="kantor" id="unit_kerja" class="form-control" required="">
                            <option value="" >- Pilih salah satu --</option>
                            @foreach ($unit_kerja as $kantor)
                                @if ($kantor == 'Pusat')
                                    <optgroup label="Kantor Pusat">
                                        @foreach ($kantor_pusat as $item)
                                            <option value="{{ $kantor }},{{ $item->id_kantor_pusat }}">
                                                {{ $kantor }} - {{ $item->nama_kantor_pusat }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @elseif($kantor == 'Cabang')
                                    <optgroup label="kantor Cabang">
                                        @foreach ($kantor_cabang as $item)
                                            <option value="{{ $kantor }},{{ $item->id_kantor_cabang }}">
                                                {{ $kantor }} - {{ $item->nama_kantor_cabang }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @elseif($kantor == 'Wilayah')
                                    <optgroup label="Kantor Wilayah">
                                        @foreach ($kantor_wilayah as $item)
                                            <option value="{{ $kantor }},{{ $item->id_kantor_wilayah }}">
                                                {{ $kantor }} - {{ $item->nama_kantor_wilayah }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            @endforeach
                        </select>
                        <br>

                        {{-- <label>Sub Unit Kerja</label>
                        <select name="unit_kerja" class="form-control" required="">
                            <?php
                            echo '<option value="">- Pilih salah satu -</option>';

                            $kantor_pusat = DB::table('tb_kantor_pusat')->where('tb_kantor_pusat.delete_kantor_pusat', '=', 'N')->orderBy('tb_kantor_pusat.nama_kantor_pusat', 'ASC')->get();
                            if ($kantor_pusat->count() > 0) {
                                foreach ($kantor_pusat as $data_kantor_pusat) {
                                    echo '<option class="Kantor_Pusat" value="kantor_pusat_' . $data_kantor_pusat->id_kantor_pusat . '">' . $data_kantor_pusat->nama_kantor_pusat . '</option>';
                                }
                            }

                            $kantor_cabang = DB::table('tb_kantor_cabang')->where('tb_kantor_cabang.delete_kantor_cabang', '=', 'N')->orderBy('tb_kantor_cabang.nama_kantor_cabang', 'ASC')->get();
                            if ($kantor_cabang->count() > 0) {
                                foreach ($kantor_cabang as $data_kantor_cabang) {
                                    echo '<option class="Kantor_Cabang" value="kantor_cabang_' . $data_kantor_cabang->id_kantor_cabang . '">' . $data_kantor_cabang->nama_kantor_cabang . '</option>';
                                }
                            }

                            $kantor_wilayah = DB::table('tb_kantor_wilayah')->where('tb_kantor_wilayah.delete_kantor_wilayah', '=', 'N')->orderBy('tb_kantor_wilayah.nama_kantor_wilayah', 'ASC')->get();
                            if ($kantor_wilayah->count() > 0) {
                                foreach ($kantor_wilayah as $data_kantor_wilayah) {
                                    echo '<option class="Kantor_Wilayah" value="kantor_wilayah_' . $data_kantor_wilayah->id_kantor_wilayah . '">' . $data_kantor_wilayah->nama_kantor_wilayah . '</option>';
                                }
                            }
                            ?>
                        </select> --}}
                        {{-- <br> --}}

                        <label>Bagian Unit Kerja</label>
                        <select name="bagian" class="form-control" id="bagian_unit_kerja" required="">
                            <option value=""></option>
                        </select>
                        <br>

                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" required="" placeholder="Harap di isi ..."></textarea>
                        <br>

                        <button type="button" class="btn btn-sm btn-warning" id="kembali">
                            <i class='bx bx-arrow-back'></i> Kembali
                        </button>

                        <button type="submit" name="pengaduan" value="<?= $_GET['alihkan'] ?>"
                            class="btn btn-sm btn-primary">
                            <i class='bx bx-redo'></i> Alihkan
                        </button>

                    </form>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <b>
                            {{-- <img src="<?= url($data_pegawai->foto_pegawai) ?>" --}}
                                {{-- style="width: 20px;height: 20px;border-radius: 100%;"> --}}
                            <?= htmlspecialchars($data_pegawai->employee_name) ?>
                        </b>
                    </div>
                    <p>
                        Unit Kerja : <?= htmlspecialchars($kantor_pegawai) ?> <br>
                        Unit Bagian Kerja : <?= htmlspecialchars($bagian_pegawai) ?>
                    </p>
                    <hr style="border-style: dashed;">
                    <p>
                        Kode Pengaduan :
                        P<?= date('y') ?>-0000<?= $data_pengaduan->id_pengaduan ?>
                    </p>
                    <p>
                        Kepada : <?= htmlspecialchars($kantor_pengaduan) ?> - <?= htmlspecialchars($bagian_pengaduan) ?>
                    </p>
                    <p>
                        <b><i class='bx bxs-coupon'></i> <?= htmlspecialchars($data_pengaduan->nama_pengaduan) ?></b>
                    </p>
                    <p>
                        Deskripsi : <br>
                        <?= $data_pengaduan->keterangan_pengaduan ?>
                    </p>
                    @if ($data_pengaduan->sla_pengaduan)
                    <p>
                        <strong>SLA :</strong>  {{ \Carbon\Carbon::parse($data_pengaduan->sla_pengaduan)->translatedFormat('l, j F Y') }}
                    </p>
                @else
                    <p>
                        <strong> SLA :</strong> -</b>
                    </p>
                @endif
                    @if ($data_pengaduan->klasifikasi_pengaduan)
                        <p>
                            Klasifikasi : <b
                                class="text-<?= $status_klasifikasi[$data_pengaduan->klasifikasi_pengaduan] ?>"><?= $data_pengaduan->klasifikasi_pengaduan ?></b>
                        </p>
                    @else
                        <p>
                            Klasifikasi :
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

                    <?php if($jawaban->count() < 1){ ?>

                    <p>
                        Respon Time : <span id="count-down"><i class='bx bx-stopwatch'></i> -:-:-</span>
                    </p>

                    <?php } ?>

                    <p>
                        Status :
                        <span class="badge badge-<?= $status_pengaduan[$data_pengaduan->status_pengaduan] ?>">
                            <?= $data_pengaduan->status_pengaduan ?>
                        </span>
                    </p>
                    <p>
                        <i class='bx bx-time'></i> <?= time_elapsed_string($data_pengaduan->tgl_pengaduan) ?>
                    </p>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

@stop

@section('script')
    <script type="text/javascript">
        $('#kembali').on('click', function() {
            loadPage('<?= route('pengaduan') ?>?filter=<?= $_GET['filter'] ?>');
        });
    </script>

    <script>
        // $('select[name="kantor"]').on('change', function() {
        //     if ($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Pusat') {
        //         $('select[name="unit_kerja"] option').hide();
        //         $('select[name="unit_kerja"] .Kantor_Pusat').show();
        //     } else if ($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Cabang') {
        //         $('select[name="unit_kerja"] option').hide();
        //         $('select[name="unit_kerja"] .Kantor_Cabang').show();
        //     } else if ($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Wilayah') {
        //         $('select[name="unit_kerja"] option').hide();
        //         $('select[name="unit_kerja"] .Kantor_Wilayah').show();
        //     } else {
        //         $('select[name="unit_kerja"] option').show();
        //     }
        // });

        // $('select[name="unit_kerja"]').on('change', function() {
        //     var value = $('select[name="unit_kerja"] option').filter(':selected').val()
        //     $('select[name="bagian"] option').hide();
        //     $('select[name="bagian"] .' + value).show();
        // });
        $('#unit_kerja').select2({
                theme: 'bootstrap-5',
                placeholder: "- Pilih salah satu -",
            });
            $('#jenis_produk').select2({
                theme: 'bootstrap-5',
                placeholder: "- Pilih salah satu -",
            });
            // $('.sub-unit-kerja').select2({
            //     theme: 'bootstrap-5',
            // placeholder: "- Pilih salah satu -",
            // });
            $('.bagian-unit-kerja').select2({
                theme: 'bootstrap-5',
                placeholder: "- Pilih salah satu -",
            });

            $('#unit_kerja').on('change', function() {
                let value = $(this).val();

                let result = value.split(',')
                let kantor = result[0]
                let id = result[1]
                $.post("{{ route('pengaduan.get-bagian-unit') }}", {
                    kantor: kantor,
                    id: id,
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                    $('select[name="bagian"]').empty();
                    data.forEach(function(res) {
                        $('#bagian_unit_kerja').append(
                            `<option value="${res.id_bagian}" >${res.nama_bagian}</option>`
                        )
                    })
                }).fail(function() {
                    alert('error');
                })
            })

    </script>

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
@stop
