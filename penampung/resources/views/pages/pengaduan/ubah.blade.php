<?php
$pengaduan = DB::table('tb_pengaduan')
    ->where([['tb_pengaduan.delete_pengaduan', 'N'], ['tb_pengaduan.status_pengaduan', 'Pending'], ['tb_pengaduan.id_pengaduan', $_GET['update']]])
    ->get();
   

if ($pengaduan->isEmpty()) {
    header('Location: ' . route('pengaduan'));
    exit();
} else {
    foreach ($pengaduan as $data_pengaduan);
    $get_pegawai = DB::table('tb_pegawai')
        ->where('tb_pegawai.id_pegawai', '=', $data_pengaduan->id_pegawai)
        ->get();

    foreach ($get_pegawai as $data_get_pegawai);

    $mitra_kepala_bagian_unit_kerja = DB::table('tb_pegawai')
        ->where([['tb_pegawai.id_pegawai', auth()->user()->id_pegawai], ['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai], ['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat], ['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang], ['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah], ['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif']])
        ->get();
    if ($mitra_kepala_bagian_unit_kerja->count() < 1) {
        $staff_mitra = DB::table('tb_pegawai')
            ->where('tb_pegawai.id_pegawai', '=', auth()->user()->id_pegawai)
            ->where('tb_pegawai.id_pegawai', '=', $data_pengaduan->id_pegawai)
            ->where('tb_pegawai.delete_pegawai', '=', 'N')
            ->where('tb_pegawai.status_pegawai', '=', 'Aktif')
            ->where('tb_pegawai.level_pegawai', '=', 'Staff')
            ->get();
        if ($staff_mitra->count() < 1) {
            header('Location: ' . route('pengaduan'));
            exit();
        }
    }
    $id_pengaduan = $_GET['update'];
    // $unit_kerja = ['Kantor Pusat', 'Kantor Cabang', 'Kantor Wilayah'];
    $id_kantor = 0;
    $id_bagian = 0;
    $class_kantor = '-';
    $nama_kantor = '-';
    $nama_bagian = '-';
    $class_bagian = '-';
    $unit_kerja = ['Pusat', 'Cabang', 'Wilayah'];
    $data_pengaduan = App\Models\Pengaduan::where('id_pengaduan', $id_pengaduan)->first();
    $kantor_pusat = DB::table('tb_kantor_pusat')->where('delete_kantor_pusat', 'N')->orderBy('nama_kantor_pusat', 'ASC')->get();
    $kantor_cabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', '=', 'N')->orderBy('nama_kantor_cabang', 'ASC')->get();

    $kantor_wilayah = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', '=', 'N')->orderBy('nama_kantor_wilayah', 'ASC')->get();
    if ($data_pengaduan->id_bagian_kantor_pusat != 0) {
        $bagian_kantor_pusat = DB::table('tb_bagian_kantor_pusat')
            ->join('tb_kantor_pusat', 'tb_kantor_pusat.id_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_kantor_pusat')
            ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $data_pengaduan->id_bagian_kantor_pusat)
            ->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat', '=', 'N')
            ->first();
    } elseif ($data_pengaduan->id_bagian_kantor_cabang != 0) {
        $bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')
            ->join('tb_kantor_cabang', 'tb_kantor_cabang.id_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_kantor_cabang')
            ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $data_pengaduan->id_bagian_kantor_cabang)
            ->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang', '=', 'N')
            ->first();
    } else {
        $bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
            ->join('tb_kantor_wilayah', 'tb_kantor_wilayah.id_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_kantor_wilayah')
            ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $data_pengaduan->id_bagian_kantor_wilayah)
            ->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah', '=', 'N')
            ->first();
    }

    $data_kantor = explode(' ', $data_pengaduan->kantor_pengaduan);
    $data_kantor = $data_kantor[1];
    if ($data_pengaduan->id_bagian_kantor_pusat != 0) {
        $bagian_kantor_pusat = DB::table('tb_kantor_pusat')
            ->join('tb_bagian_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
            ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $data_pengaduan->id_bagian_kantor_pusat)
            ->where('tb_kantor_pusat.delete_kantor_pusat', '=', 'N')
            ->first();

        $id_unit_kerja = $data_kantor . ',' . $bagian_kantor_pusat->id_kantor_pusat;
    } elseif ($data_pengaduan->id_bagian_kantor_cabang != 0) {
        $bagian_kantor_cabang = DB::table('tb_kantor_cabang')
            ->join('tb_bagian_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
            ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $data_pengaduan->id_bagian_kantor_cabang)
            ->where('tb_kantor_cabang.delete_kantor_cabang', '=', 'N')
            ->first();
        $id_unit_kerja = $data_kantor . ',' . $bagian_kantor_cabang->id_kantor_cabang;
    } else {
        $bagian_kantor_wilayah = DB::table('tb_kantor_wilayah')
            ->join('tb_bagian_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
            ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', $data_pengaduan->id_bagian_kantor_wilayah)
            ->where('tb_kantor_wilayah.delete_kantor_wilayah', '=', 'N')
            ->first();
        $id_unit_kerja = $data_kantor . ',' . $bagian_kantor_wilayah->id_bagian_kantor_wilayah;
    }
    // if($data_pengaduan->kantor_pengaduan == 'Kantor Pusat'){

    // 	$kantor_bagian = DB::table('tb_kantor_pusat')
    // 	->join('tb_bagian_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
    // 	->where('tb_kantor_pusat.id_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
    // 	->get();
    // 	if($kantor_bagian->count() > 0){
    // 		foreach($kantor_bagian as $data_kantor_bagian){
    // 			$id_kantor = $data_kantor_bagian->id_kantor_pusat;
    // 			$id_bagian = $data_kantor_bagian->id_bagian_kantor_pusat;
    // 			$nama_kantor = $data_kantor_bagian->nama_kantor_pusat;
    // 			$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_pusat;
    // 			$class_kantor = 'Kantor_Pusat';
    // 			$class_bagian = 'kantor_pusat_'.$id_kantor;
    // 		}
    // 	}

    // }else if($data_pengaduan->kantor_pengaduan == 'Kantor Cabang'){

    // 	$kantor_bagian = DB::table('tb_kantor_cabang')
    // 	->join('tb_bagian_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
    // 	->where('tb_kantor_cabang.id_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
    // 	->get();
    // 	if($kantor_bagian->count() > 0){
    // 		foreach($kantor_bagian as $data_kantor_bagian){
    // 			$id_kantor = $data_kantor_bagian->id_kantor_cabang;
    // 			$id_bagian = $data_kantor_bagian->id_bagian_kantor_cabang;
    // 			$nama_kantor = $data_kantor_bagian->nama_kantor_cabang;
    // 			$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_cabang;
    // 			$class_kantor = 'Kantor_Cabang';
    // 			$class_bagian = 'kantor_cabang_'.$id_kantor;
    // 		}
    // 	}

    // }else if($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah'){

    // 	$kantor_bagian = DB::table('tb_kantor_wilayah')
    // 	->join('tb_bagian_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
    // 	->where('tb_kantor_wilayah.id_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
    // 	->get();
    // 	if($kantor_bagian->count() > 0){
    // 		foreach($kantor_bagian as $data_kantor_bagian){
    // 			$id_kantor = $data_kantor_bagian->id_kantor_wilayah;
    // 			$id_bagian = $data_kantor_bagian->id_bagian_kantor_wilayah;
    // 			$nama_kantor = $data_kantor_bagian->nama_kantor_wilayah;
    // 			$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_wilayah;
    // 			$class_kantor = 'Kantor_Wilayah';
    // 			$class_bagian = 'kantor_wilayah_'.$id_kantor;
    // 		}
    // 	}

    // }
    // if($data_pengaduan->kantor_pengaduan == 'Kantor Pusat'){

    // 	$kantor_bagian = DB::table('tb_bagian_kantor_pusat')
    // 	->join('tb_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
    // 	->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
    // 	->get();
    // 	if($kantor_bagian->count() > 0){
    // 		foreach($kantor_bagian as $data_kantor_bagian){
    // 			$id_kantor = $data_kantor_bagian->id_kantor_pusat;
    // 			$id_bagian = $data_kantor_bagian->id_bagian_kantor_pusat;
    // 			$nama_kantor = $data_kantor_bagian->nama_kantor_pusat;
    // 			$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_pusat;
    // 			$class_kantor = 'Kantor_Pusat';
    // 			$class_bagian = 'kantor_pusat_'.$id_kantor;
    // 		}
    // 	}

    // }else if($data_pengaduan->kantor_pengaduan == 'Kantor Cabang'){

    // 	$kantor_bagian = DB::table('tb_bagian_kantor_cabang')
    // 	->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
    // 	->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
    // 	->get();
    // 	if($kantor_bagian->count() > 0){
    // 		foreach($kantor_bagian as $data_kantor_bagian){
    // 			$id_kantor = $data_kantor_bagian->id_kantor_cabang;
    // 			$id_bagian = $data_kantor_bagian->id_bagian_kantor_cabang;
    // 			$nama_kantor = $data_kantor_bagian->nama_kantor_cabang;
    // 			$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_cabang;
    // 			$class_kantor = 'Kantor_Cabang';
    // 			$class_bagian = 'kantor_cabang_'.$id_kantor;
    // 		}
    // 	}

    // }else if($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah'){

    // 	$kantor_bagian = DB::table('tb_bagian_kantor_wilayah')
    // 	->join('tb_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
    // 	->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
    // 	->get();
    // 	if($kantor_bagian->count() > 0){
    // 		foreach($kantor_bagian as $data_kantor_bagian){
    // 			$id_kantor = $data_kantor_bagian->id_kantor_wilayah;
    // 			$id_bagian = $data_kantor_bagian->id_bagian_kantor_wilayah;
    // 			$nama_kantor = $data_kantor_bagian->nama_kantor_wilayah;
    // 			$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_wilayah;
    // 			$class_kantor = 'Kantor_Wilayah';
    // 			$class_bagian = 'kantor_wilayah_'.$id_kantor;
    // 		}
    // 	}

    // }
}
// dd($id_unit_kerja);
?>

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-edit'></i> Perbarui Pengaduan</b></div>
                    <hr style="border-style: dashed;">
                    <div class="row">
                        <div class="col-md-6" align="center">
                            <img src="<?= url('logos/edit.png') ?>" style="max-width: 100%;">
                        </div>

                        <div class="col-md-6">
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
                            <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                                action="<?= route('pengaduan.update') ?>">
                                <?= csrf_field() ?>

                                <label>Unit Kerja</label>
                                <select name="kantor" class="form-control" id="unit_kerja" required="">
                                    {{-- <option value="">- Pilih -</option> --}}
                                    @foreach ($unit_kerja as $kantor)
                                        {{-- <option value="{{$id_unit_kerja}}" selected> {{$data_kantor}}</option> --}}
                                        @if ($kantor == 'Pusat')
                                            <optgroup label="Kantor Pusat">
                                                @foreach ($kantor_pusat as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_pusat }}"
                                                        @if ("$id_unit_kerja" == "$kantor,$item->id_kantor_pusat") selected @endif>
                                                        {{ $kantor }} - {{ $item->nama_kantor_pusat }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @elseif($kantor == 'Cabang')
                                            <optgroup label="kantor Cabang">
                                                @foreach ($kantor_cabang as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_cabang }}"
                                                        @if ("$id_unit_kerja" == "$kantor,$item->id_kantor_cabang") selected @endif>
                                                        {{ $kantor }} - {{ $item->nama_kantor_cabang }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @elseif($kantor == 'Wilayah')
                                            <optgroup label="Kantor Wilayah">
                                                @foreach ($kantor_wilayah as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_wilayah }}"
                                                        @if ("$id_unit_kerja" == "$kantor,$item->id_kantor_wilayah") selected @endif>
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
                                    echo '<option class="' . $class_kantor . '" value="kantor_pusat_' . $id_kantor . '">' . $nama_kantor . '</option>';

                                    $kantor_pusat = DB::table('tb_kantor_pusat')->where('tb_kantor_pusat.id_kantor_pusat', '!=', $id_kantor)->where('tb_kantor_pusat.delete_kantor_pusat', '=', 'N')->orderBy('tb_kantor_pusat.nama_kantor_pusat', 'ASC')->get();
                                    if ($kantor_pusat->count() > 0) {
                                        foreach ($kantor_pusat as $data_kantor_pusat) {
                                            echo '<option class="Kantor_Pusat" value="kantor_pusat_' . $data_kantor_pusat->id_kantor_pusat . '">' . $data_kantor_pusat->nama_kantor_pusat . '</option>';
                                        }
                                    }

                                    $kantor_cabang = DB::table('tb_kantor_cabang')->where('tb_kantor_cabang.delete_kantor_cabang', '=', 'N')->where('tb_kantor_cabang.id_kantor_cabang', '!=', $id_kantor)->orderBy('tb_kantor_cabang.nama_kantor_cabang', 'ASC')->get();
                                    if ($kantor_cabang->count() > 0) {
                                        foreach ($kantor_cabang as $data_kantor_cabang) {
                                            echo '<option class="Kantor_Cabang" value="kantor_cabang_' . $data_kantor_cabang->id_kantor_cabang . '">' . $data_kantor_cabang->nama_kantor_cabang . '</option>';
                                        }
                                    }

                                    $kantor_wilayah = DB::table('tb_kantor_wilayah')->where('tb_kantor_wilayah.delete_kantor_wilayah', '=', 'N')->where('tb_kantor_wilayah.id_kantor_wilayah', '!=', $id_kantor)->orderBy('tb_kantor_wilayah.nama_kantor_wilayah', 'ASC')->get();
                                    if ($kantor_wilayah->count() > 0) {
                                        foreach ($kantor_wilayah as $data_kantor_wilayah) {
                                            echo '<option class="Kantor_Wilayah" value="kantor_wilayah_' . $data_kantor_wilayah->id_kantor_wilayah . '">' . $data_kantor_wilayah->nama_kantor_wilayah . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                                <br> --}}

                                <label>Bagian Unit Kerja</label>
                                <select name="bagian" class="form-control" id="bagian_unit_kerja" required="">
                                    {{-- <option value="{{$id_bagian}}"></option> --}}
                                    @if ($data_pengaduan->id_bagian_kantor_pusat != 0)
                                        <option value="{{ $bagian_kantor_pusat->id_bagian_kantor_pusat }}">
                                            {{ $bagian_kantor_pusat->nama_bagian_kantor_pusat }} </option>
                                    @elseif($data_pengaduan->id_bagian_kantor_cabang != 0)
                                        <option value="{{ $bagian_kantor_cabang->id_bagian_kantor_cabang }}">
                                            {{ $bagian_kantor_cabang->nama_bagian_kantor_cabang }} </option>
                                    @elseif($data_pengaduan->id_bagian_kantor_wilayah != 0)
                                        <option value="{{ $bagian_kantor_wilayah->id_bagian_kantor_wilayah }}">
                                            {{ $bagian_kantor_wilayah->nama_bagian_kantor_wilayah }}</option>
                                    @endif
                                </select>
                                <br>
                                <label for="">Kategori Pengaduan</label>
                                <select name="kategori_pengaduan" id="kategori_pengaduan" class="form-control">
                                    <option value="" disabled >- Pilih Salah Satu -</option>
                                    <option value="Bisnis" @if ($data_pengaduan->kategori_pengaduan == 'Bisnis') selected @endif>Bisnis</option>
                                    <option value="Klaim" @if ($data_pengaduan->kategori_pengaduan == 'Klaim') selected @endif>Klaim</option>
                                    <option value="Peraturan" @if ($data_pengaduan->kategori_pengaduan == 'Peraturan') selected @endif>Peraturan
                                    </option>
                                    <option value="Dan Lainnya" @if ($data_pengaduan->kategori_pengaduan == 'Dan Lainnya') selected @endif>Dan
                                        Lainnya</option>
                                </select>
                                <br>
                                <label for="">Jenis Produk</label>
                                <select name="jenis_produk" class="form-control" id="jenis_produk" required>
                                    <option value="" disabled selected>- Pilih salah satu -</option>
                                    <optgroup label="KUR">
                                        <option value="KUR,Produk KUR" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'KUR,Produk KUR') selected @endif>
                                            Produk KUR
                                        </option>
                                    </optgroup>
                                    <optgroup label="KBG & Suretyship">
                                        <option value="KBG & Suretyship,Custom Bond"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'KBG & Suretyship,Custom Bond') selected @endif>Customer Bond</option>
                                        <option value="KBG & Suretyship,KBG"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'KBG & Suretyship,KBG') selected @endif>KBG</option>
                                        <option value="KBG & Suretyship,Surety Bond"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'KBG & Suretyship,Surety Bond') selected @endif>Surety Bond</option>
                                        <option value="KBG & Suretyship,Payment Bond"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'KBG & Suretyship,Payment Bond') selected @endif>Payment Bond</option>
                                    </optgroup>
                                    <optgroup label="Produktif">
                                        <option value="Produktif,ATMR" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,ATMR') selected @endif>
                                            ATMR</option>
                                        <option value="Produktif,Keagenan Kargo"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Keagenan Kargo') selected @endif>Keagenan Kargo</option>
                                        <option value="Produktif,KKPE" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,KKPE') selected @endif>
                                            KKPE</option>
                                        <option value="Produktif,Kontruksi"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Kontruksi') selected @endif>Kontruksi</option>
                                        <option value="Produktif,Mikro" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Mikro') selected @endif>
                                            Mikro</option>
                                        <option value="Produktif,Distribusi Barang"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Distribusi Barang') selected @endif>Distribusi Barang</option>
                                        <option value="Produktif,Pembiayaan Invoice"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Pembiayaan Invoice') selected @endif>Pembiayaan Invoice</option>
                                        <option value="Produktif,Subsidi Resi Gudang"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Subsidi Resi Gudang') selected @endif>Subsidi Resi Gudang</option>
                                        <option value="Produktif,Super Mikro"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Super Mikro') selected @endif>Super Mikro</option>
                                        <option value="Produktif,Umum" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Produktif,Umum') selected @endif>
                                            Umum</option>
                                    </optgroup>
                                    <optgroup label="Konsumtif">
                                        <option value="Konsumtif,FLPP" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,FLPP') selected @endif>
                                            FLPP</option>
                                        <option value="Konsumtif,OTO" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,OTO') selected @endif>
                                            OTO</option>
                                        <option value="Konsumtif,KPR" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,KPR') selected @endif>
                                            KPR</option>
                                        <option value="Konsumtif,Multiguna"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,Multiguna') selected @endif>Multiguna</option>
                                        <option value="Konsumtif,KSM" @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,KSM') selected @endif>
                                            KSM</option>
                                        <option value="Konsumtif,Mandiri"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,Mandiri') selected @endif>Mandiri</option>
                                        <option value="Konsumtif,Briguna"
                                            @if ("$data_pengaduan->jenis_produk,$data_pengaduan->sub_jenis_produk" == 'Konsumtif,Briguna') selected @endif>Briguna</option>
                                    </optgroup>
                                </select>
                                <br>

                                <label>Pengaduan</label>
                                <input type="text" name="nama"
                                    value="<?= htmlspecialchars($data_pengaduan->nama_pengaduan) ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Deskripsi</label>
                                <textarea name="keterangan" class="form-control" required="" placeholder="Harap di isi ..."><?= str_replace('<br />', '', $data_pengaduan->keterangan_pengaduan) ?></textarea>
                                <br>

                                <button type="button" class="btn btn-sm btn-warning" id="kembali">
                                    <i class='bx bx-arrow-back'></i> Kembali
                                </button>

                                <button type="submit" name="update" value="<?= $_GET['update'] ?>"
                                    class="btn btn-sm btn-primary">
                                    <i class='bx bx-check-double'></i> Selesai
                                </button>

                            </form>
                        </div>
                    </div>
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
        $('#bagian_unit_kerja').select2({
            theme: 'bootstrap-5',
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



@stop

