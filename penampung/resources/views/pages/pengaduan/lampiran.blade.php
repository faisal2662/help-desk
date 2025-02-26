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
	$status_klasifikasi = array(
		'High' => 'danger',
		'Medium' => 'warning',
		'Low' => 'info',
	);
?>

<?php
	$status_pengaduan = array(
		'Pending' => 'warning',
		'Checked' => 'warning',
		'Approve' => 'info',
		'Read' => 'info',
		'Holding' => 'danger',
		'Moving' => 'danger',
		'On Progress' => 'primary',
		'Late' => 'danger',
		'Finish' => 'success',
	);

	$pengaduan = DB::table('tb_pengaduan')
	->where([['tb_pengaduan.delete_pengaduan','N'],['tb_pengaduan.status_pengaduan','Pending'],['tb_pengaduan.id_pengaduan', $_GET['lampiran']]])
	->get();
	if($pengaduan->count() < 1){
		header('Location: '.route('pengaduan'));
		exit();
	}else{
		foreach($pengaduan as $data_pengaduan);

		$get_pegawai = DB::table('tb_pegawai')
		->where('tb_pegawai.id_pegawai','=', $data_pengaduan->id_pegawai)
		->get();

		foreach($get_pegawai as $data_get_pegawai);

		// $mitra_kepala_bagian_unit_kerja = App\Models\Pegawai::
	    // where([['tb_pegawai.id_pegawai', Session::get('id_pegawai')],
        // ['tb_pegawai.kantor_pegawai', $data_get_pegawai->kantor_pegawai],
        // ['tb_pegawai.id_bagian_kantor_pusat', $data_get_pegawai->id_bagian_kantor_pusat],
        // ['tb_pegawai.id_bagian_kantor_cabang', $data_get_pegawai->id_bagian_kantor_cabang],
        // ['tb_pegawai.id_bagian_kantor_wilayah', $data_get_pegawai->id_bagian_kantor_wilayah],
        // ['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],
        // ['tb_pegawai.sebagai_pegawai','PIC']])
        // ->whereHas('NamaPosisi', function ($query) {
        //                     $query->where('sebagai_posisi', '=', 'Kepala Bagian Unit Kerja');
        //                 })
        // ->get();
		// if($mitra_kepala_bagian_unit_kerja->count() < 1){

		// 	$staff_mitra = App\Models\Pegawai::where('tb_pegawai.id_pegawai','=', Session::get('id_pegawai'))
		// 	->where('tb_pegawai.id_pegawai','=', $data_pengaduan->id_pegawai)
		// 	->where('tb_pegawai.delete_pegawai','=','N')
		// 	->where('tb_pegawai.status_pegawai','=','Aktif')
		// 	->where('tb_pegawai.sebagai_pegawai','=','PIC')
		// 	->get();
		// 	if($staff_mitra->count() < 1){
		// 		header('Location: '.route('pengaduan'));
		// 		exit();
		// 	}

		// }

		$lampiran = DB::table('tb_lampiran')
		->where([['tb_lampiran.delete_lampiran','N'],['tb_lampiran.id_pengaduan', $data_pengaduan->id_pengaduan]])
		->orderBy('tb_lampiran.id_lampiran','ASC')
		->get();

		// get data pegawai
		$pegawai = DB::table('tb_pegawai')
		->where([['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
		->get();
		if($pegawai->count() > 0){
			foreach($pegawai as $data_pegawai);

			$kantor_pegawai = '-';
			$bagian_pegawai = '-';

			if($data_pegawai->kantor_pegawai == 'Kantor Pusat'){

				$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
				->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
				->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
				->get();
				if($kantor_pusat->count() > 0){
					foreach($kantor_pusat as $data_kantor_pusat);
					$kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
					$bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
				}

			}else if($data_pegawai->kantor_pegawai == 'Kantor Cabang'){

				$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
				->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
				->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
				->get();
				if($kantor_cabang->count() > 0){
					foreach($kantor_cabang as $data_kantor_cabang);
					$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
					$bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
				}

			}else if($data_pegawai->kantor_pegawai == 'Kantor Wilayah'){

				$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
				->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
				->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
				->get();
				if($kantor_wilayah->count() > 0){
					foreach($kantor_wilayah as $data_kantor_wilayah);
					$kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
					$bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
				}

			}

		}
		// end get data pegawai

		// kantor bagian pengaduan
		$kantor_pengaduan = '-';
		$bagian_pengaduan = '-';

		if($data_pengaduan->kantor_pengaduan == 'Kantor Pusat'){

			$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
			->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
			->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
			->get();
			if($kantor_pusat->count() > 0){
				foreach($kantor_pusat as $data_kantor_pusat);
				$kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
				$bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
			}

		}else if($data_pengaduan->kantor_pengaduan == 'Kantor Cabang'){

			$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
			->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
			->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
			->get();
			if($kantor_cabang->count() > 0){
				foreach($kantor_cabang as $data_kantor_cabang);
				$kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
				$bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
			}

		}else if($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah'){

			$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
			->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
			->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
			->get();
			if($kantor_wilayah->count() > 0){
				foreach($kantor_wilayah as $data_kantor_wilayah);
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
			<p>&nbsp;</p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-layer-plus'></i> Tambah Lampiran</b></div>
				<hr style="border-style: dashed;">
				<?php
				  if(session()->has('alert')){
					$explode = explode('_', session()->get('alert'));
					echo '
					  <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
					';
				  }
				?>
				<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('pengaduan.lampiran') ?>">
				  <?= csrf_field() ?>

					<label>Lampiran</label>
					<input type="file" name="foto" class="form-control" required="" maxlength="255" placeholder="Pilih lampiran ...">
					<br>

					<button type="button" class="btn btn-sm btn-warning" id="kembali">
					  <i class='bx bx-arrow-back'></i> Kembali
					</button>

					<button type="submit" name="pengaduan" value="<?= $_GET['lampiran'] ?>" class="btn btn-sm btn-primary">
					  <i class='bx bx-plus' ></i> Tambahkan
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
						<img src="<?= url($data_pegawai->foto_pegawai) ?>" style="width: 20px;height: 20px;border-radius: 100%;"> <?= htmlspecialchars($data_pegawai->employee_name) ?>
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
					<b><i class='bx bxs-coupon' ></i> <?= htmlspecialchars($data_pengaduan->nama_pengaduan) ?></b>
				</p>
				<p>
					Deskripsi : <br>
					<?= $data_pengaduan->keterangan_pengaduan ?>
				</p>
				@if($data_pengaduan->klasifikasi_pengaduan)
				<p>
					Klasifikasi : <b class="text-<?= $status_klasifikasi[$data_pengaduan->klasifikasi_pengaduan] ?>"><?= $data_pengaduan->klasifikasi_pengaduan ?></b>
				</p>
				@else
				<p>
					Klasifikasi : -
				</p>
				@endif
				<?php if($lampiran->count() > 0){ ?>
					<p>
						<ol>
							<?php foreach($lampiran as $data_lampiran){ ?>

								<li>
								    <a href="<?= url($data_lampiran->file_lampiran) ?>" target="_blank">
								        Lampiran
								    </a>
								    <a href="javascript:;" class="text-danger" onclick="delete_data (<?= $data_lampiran->id_lampiran ?>);">
								        (<i class='bx bx-trash'></i> Batalkan)
								    </a>
								</li>

							<?php } ?>
						</ol>
					</p>
				<?php } ?>
				<p>
					Status :
					<span class="badge badge-<?= $status_pengaduan[$data_pengaduan->status_pengaduan] ?>">
					  <?= $data_pengaduan->status_pengaduan ?>
					</span>
				</p>
				<p>
					<i class='bx bx-time' ></i> <?= time_elapsed_string($data_pengaduan->tgl_pengaduan) ?>
				</p>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>

    <span style="display: none;">
    	<form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('pengaduan.hapus_lampiran') ?>">
    		<?= csrf_field() ?>
    		<input type="text" name="delete" id="input-delete" readonly="" required="">
    	</form>
    </span>

    <script type="text/javascript">
    	function delete_data (id){
    		var r = confirm('Batalkan lampiran?');
    		if(r == true){
    		  show(true);
    		  document.getElementById('input-delete').value = id;
    		  document.getElementById('form-delete').submit();
    		}
    	}
    </script>

@stop

@section('script')
	<script type="text/javascript">
	  $('#kembali').on('click', function() {
		loadPage('<?= route('pengaduan') ?>?filter=<?= $_GET['filter'] ?>');
	  });
	</script>
@stop
