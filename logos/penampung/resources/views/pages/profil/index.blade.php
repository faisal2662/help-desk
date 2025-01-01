<?php


	if($pegawai->count() < 1){
		header('Location: '.route('logout'));
		exit();
	}
		$data_pegawai =  $pegawai;

		$kantor = '-';
		$bagian = '-';

		if($data_pegawai->kantor_pegawai == 'Kantor Pusat'){

			$unit_kerja = DB::table('tb_kepala_unit_kerja')
			->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
			->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
			->limit(1)
			->get();


			if($unit_kerja->count() > 0){
				foreach($unit_kerja as $data_unit_kerja){

					$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
					->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
					->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_unit_kerja->id_bagian_kantor_pusat)
					->get();
					if($kantor_pusat->count() > 0){
						foreach($kantor_pusat as $data_kantor_pusat);
						$kantor = $data_kantor_pusat->nama_kantor_pusat;
						$bagian = 'Semua Bagian';
					}

				}
			}else{

				$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
				->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
				->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
				->get();
				if($kantor_pusat->count() > 0){
					foreach($kantor_pusat as $data_kantor_pusat);
					$kantor = $data_kantor_pusat->nama_kantor_pusat;
					$bagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
				}

			}

		}else if($data_pegawai->kantor_pegawai == 'Kantor Cabang'){

			$unit_kerja = DB::table('tb_kepala_unit_kerja')
			->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
			->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
			->limit(1)
			->get();

			if($unit_kerja->count() > 0){
				foreach($unit_kerja as $data_unit_kerja){

					$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
					->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
					->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_unit_kerja->id_bagian_kantor_cabang)
					->get();
					if($kantor_cabang->count() > 0){
						foreach($kantor_cabang as $data_kantor_cabang);
						$kantor = $data_kantor_cabang->nama_kantor_cabang;
						$bagian = 'Semua Bagian';
					}

				}
			}else{

				$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
				->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
				->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
				->get();
				if($kantor_cabang->count() > 0){
					foreach($kantor_cabang as $data_kantor_cabang);
					$kantor = $data_kantor_cabang->nama_kantor_cabang;
					$bagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
				}

			}

		}else if($data_pegawai->kantor_pegawai == 'Kantor Wilayah'){

			$unit_kerja = DB::table('tb_kepala_unit_kerja')
			->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
			->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
			->limit(1)
			->get();

			if($unit_kerja->count() > 0){
				foreach($unit_kerja as $data_unit_kerja){

					$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
					->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
					->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_unit_kerja->id_bagian_kantor_wilayah)
					->get();
					if($kantor_wilayah->count() > 0){
						foreach($kantor_wilayah as $data_kantor_wilayah);
						$kantor = $data_kantor_wilayah->nama_kantor_wilayah;
						$bagian = 'Semua Bagian';
					}

				}
			}else{

				$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
				->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
				->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
				->get();
				if($kantor_wilayah->count() > 0){
					foreach($kantor_wilayah as $data_kantor_wilayah);
					$kantor = $data_kantor_wilayah->nama_kantor_wilayah;
					$bagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
				}

			}

		}


?>

<?php
  $bulan = date('Y-m');

  if(isset($_GET['bulan'])){
	if($_GET['bulan'] == ''){
		$bulan = date('Y-m');
	}else{
		$bulan = $_GET['bulan'];
	}
  }
?>

@extends('template')

@section('title')
	Profil - Helpdesk
@stop

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
				<div class="card-title"><b><i class='bx bx-user' ></i> Profil Saya</b></div>
				<hr style="border-style: dashed;">

				<center>
				  <form method="POST" enctype="multipart/form-data" id="form-upload" onsubmit="show(true)" action="<?= route('profil.upload') ?>">
					<?= csrf_field() ?>

					<div class="form-group">
					  <label for="file-1">
                        @if ($data_pegawai->foto_pegawai)
						<img src="<?= asset($data_pegawai->foto_pegawai) ?>" id="image-1" style="width: 120px;border-radius: 100%;cursor: pointer;"><br>
                        @else
						<img src="<?= asset('logos/avatar.png') ?>" id="image-1" style="width: 120px;border-radius: 100%;cursor: pointer;"><br>
                        @endif
						{{-- <span class="text-primary" style="margin-top: 10px;cursor: pointer;">
						  <i class='bx bx-upload'></i> Unggah Foto
						</span>
						<input type="file" accept="image/*" name="foto" id="file-1" class="form-control" onchange="previewImage('image-1','file-1');show(true);document.getElementById('form-upload').submit();" style="display: none;"> --}}
					  </label>
					</div>

				  </form>
				</center>

				<div class="table-responsive">
				  <table class="table" style="width: 100%;">
					<thead>
					  <tr>
						<td>NPP</td>
						<td>: <?= htmlspecialchars($data_pegawai->employee_id) ?></td>
					  </tr>
					  <tr>
						<td>Nama Lengkap</td>
						<td>: <?= htmlspecialchars($data_pegawai->employee_name) ?></td>
					  </tr>
					  <tr>
						<td>Jenis Kelamin</td>
                        @if ($data_pegawai->gender == 'L')

						<td>: Laki - Laki</td>
                        @else
						<td>: Perempuan</td>

                        @endif
					  </tr>
					  <tr>
						<td>No.Telp</td>
						<td>: <?= htmlspecialchars($data_pegawai->telp_pegawai) ?></td>
					  </tr>
					  <tr>
						<td>Email</td>
						<td>: <?= htmlspecialchars($data_pegawai->email_pegawai) ?></td>
					  </tr>
					  <tr>
						<td>Unit Kerja</td>
						<td>: <?= htmlspecialchars($data_pegawai->kantor_pegawai) ?></td>
					  </tr>
					  <tr>
						<td>Bagian Unit Kerja</td>
						<td>: <?= htmlspecialchars($kantor.' - '.$bagian) ?></td>
					  </tr>
					  <tr>
						<td>Sebagai</td>
						<td>: <?= htmlspecialchars($data_pegawai->NamaPosisi->sebagi_posisi) ?></td>
					  </tr>
					  <tr>
						<td>Status</td>
						<td>: <?= htmlspecialchars($data_pegawai->status_pegawai) ?></td>
					  </tr>
					  <tr>
						<td>Role</td>
						<td>: <?= htmlspecialchars($data_pegawai->sebagai_pegawai) ?></td>
					  </tr>
					  <tr>
						<td>Tanggal</td>
						<td>: <?= date('j F Y, H:i', strtotime($data_pegawai->created_date)) ?></td>
					  </tr>
					</thead>
				  </table>
				</div>
				<br>
{{--
				<button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#perbarui-profil">
				  <i class='bx bx-edit-alt' ></i> Perbarui Profil
				</button>

				<button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#ganti-password">
				  <i class='bx bx-lock-alt'></i> Ganti Password
				</button> --}}

			  </div>
			</div>
			<p>&nbsp;</p>
		</div>

		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bxs-bar-chart-alt-2' ></i> Riwayat Akses</b></div>
				<hr style="border-style: dashed;">
				<form method="GET" onsubmit="show(true);">
					<div class="input-group">
						<input type="text" name="bulan" id="bulan" value="<?= $bulan ?>" readonly="" required="" maxlength="255" placeholder="Atur Bulan ..." class="form-control">
						<div class="input-group-append">
							<button type="submit" class="btn btn-sm btn-primary">
								<i class='bx bx-calendar-check' ></i>
							</button>
						</div>
					</div>
				</form>
				<br>
				<label>
					Tanggal : <?= date('F Y', strtotime($bulan)) ?>
				</label>
				<canvas id="riwayat-akses" style="width: 100%;"></canvas>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-list-ul' ></i> Rincian Riwayat Akses</b></div>
				<hr style="border-style: dashed;">

				<?php
					$log = DB::table('tb_log_pegawai')
					->join('tb_pegawai','tb_log_pegawai.id_pegawai','=','tb_pegawai.id_pegawai')
					->where([['tb_log_pegawai.delete_log_pegawai','N'],['tb_log_pegawai.id_pegawai', Session::get('id_pegawai')]])
					->orderBy('tb_log_pegawai.id_log_pegawai','DESC')
					->get();
				?>

				<?php if($log->count() < 1){ ?>

					<center>
					   <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
					   <p>Data saat ini tidak ditemukan.</p>
					</center>

				<?php }else{ ?>

					<div class="table-responsive">
						<table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
							<thead>
							  <tr>
								<td><b>No</b></td>
								<td><b>Atas Nama</b></td>
								<td><b>Tanggal</b></td>
							  </tr>
							</thead>
						</table>
					</div>

				<?php } ?>

			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>

@stop

@section('script')

<!-- Classic Modal -->
<div class="modal fade" id="perbarui-profil" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <table style="width: 100%;">
              <tbody>
                  <tr>
                      <td>
                          <b>
                              <i class='bx bx-edit-alt' ></i> Perbarui Profil
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

		<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('profil.update') ?>">
		  <?= csrf_field() ?>

			<label>Nama Lengkap</label>
			<input type="text" name="nama" value="<?= htmlspecialchars($data_pegawai->nama_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<br>

			<label>Jenis Kelamin</label><br>
			<input type="radio" name="jenkel" value="Laki-laki" <?= $data_pegawai->jenkel_pegawai == 'Laki-laki' ? 'checked' : '' ?>> Laki-laki
			&nbsp;&nbsp;&nbsp;
			<input type="radio" name="jenkel" value="Perempuan" <?= $data_pegawai->jenkel_pegawai == 'Perempuan' ? 'checked' : '' ?>> Perempuan
			<br><br>

			<label>No.Telp</label>
			<input type="number" name="telp" value="<?= htmlspecialchars($data_pegawai->telp_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<br>

			<label>Email</label>
			<input type="email" name="email" value="<?= htmlspecialchars($data_pegawai->email_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<br>

			<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">
			  <i class='bx bx-arrow-back'></i> Kembali
			</button>

			<button type="submit" class="btn btn-sm btn-primary">
			  <i class='bx bx-check-double'></i> Selesai
			</button>

		</form>

      </div>
    </div>
  </div>
</div>
<!--  End Modal -->

<!-- Classic Modal -->
<div class="modal fade" id="ganti-password" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <table style="width: 100%;">
              <tbody>
                  <tr>
                      <td>
                          <b>
                              <i class='bx bx-lock-alt' ></i> Ganti Password
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

		<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('profil.ganti_password') ?>">
		  <?= csrf_field() ?>

			<label>Password Lama</label>
			<input type="password" name="pass_lama" value="<?= old('pass_lama') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<br>

			<label>Password Baru</label>
			<input type="password" name="pass_baru" value="<?= old('pass_baru') ?>" id="txt_password" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<label for="" id="txt_label_password" class="text-danger" style="font-size: 12px;"></label>
			<br>

			<label>Konfirmasi Password</label>
			<input type="password" name="confirm_pass" value="<?= old('confirm_pass') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<br>

			<button type="button" class="btn btn-sm btn-warning" data-dismiss="modal">
			  <i class='bx bx-arrow-back'></i> Kembali
			</button>

			<button type="submit" class="btn btn-sm btn-primary btn-ganti-password">
			  <i class='bx bx-lock-open-alt' ></i> Ganti Password
			</button>

		</form>

      </div>
    </div>
  </div>
</div>
<!--  End Modal -->

	<script type="text/javascript">
	  function previewImage(preview, source) {
		var oFReader = new FileReader();
		 oFReader.readAsDataURL(document.getElementById(source).files[0]);

		oFReader.onload = function(oFREvent) {
		  document.getElementById(preview).src = oFREvent.target.result;
		};
	  };

        $('#txt_password').on('keyup', function(){

            $('#txt_label_password').html("");

            var InputValue = $("#txt_password").val();
            var regex = new RegExp("^(?=.*[a-z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
            $("#passwordText").text(`Password value:- ${InputValue}`);

            console.log(InputValue);

            if(!regex.test(InputValue)) {
                $('#txt_label_password').show();
                $('#txt_label_password').html("Minimal 8 digit dengan kombinasi huruf, angka dan karakter spesial");
                $('.btn-ganti-password').attr("disabled", "disabled");
            }
            else{

                $('#txt_label_password').hide();
                $('.btn-ganti-password').removeAttr('disabled');
            }

        });
	</script>

	<script type="text/javascript">
		$("#bulan").datepicker({
			format: "yyyy-mm",
			viewMode: "months",
			minViewMode: "months"
		});
	</script>

	<script type="text/javascript">

	  $('#dataTables').DataTable({
		"processing": true,
		"serverSide": true,
		"ajax":{
			 "url": "{{ route('profil.log') }}",
			 "dataType": "json",
			 "type": "POST",
			 "data":{ _token: "<?= csrf_token() ?>"}
		   },
		columns: [
			{data: 'no'},
			{data: 'employee_name'},
			{data: 'tgl_log_pegawai'},
		]
	  });
	</script>

	<?php
	  $hari = array('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu');
	  $day = array('Mon','Tue','Wed','Thu','Fri','Sat','Sun');
	?>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
	<script src="https://www.chartjs.org/samples/2.6.0/utils.js"></script>
	<script type="text/javascript">
		var xValues = [
			<?php foreach($hari as $data_hari){ ?>
				'<?= $data_hari ?>',
			<?php } ?>
		];
		var yValues = [
			<?php foreach($hari as $data_hari){ ?>

				<?php
					$data_day = str_replace($hari, $day, $data_hari);
					$log = DB::table('tb_log_pegawai')
					->join('tb_pegawai','tb_pegawai.id_pegawai','=','tb_log_pegawai.id_pegawai')
					->where('tb_log_pegawai.delete_log_pegawai','=','N')
					->where('tb_log_pegawai.id_pegawai','=', Session::get('id_pegawai'))
					->whereRaw('date_format(tb_log_pegawai.tgl_log_pegawai,\'%Y-%m\') = ? ', [$bulan])
					->whereRaw('date_format(tb_log_pegawai.tgl_log_pegawai,\'%a\') = ? ', [$data_day])
					->get();
				?>

				<?= $log->count() ?>,
			<?php } ?>
		];

		new Chart("riwayat-akses", {
		  type: "bar",
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
			legend: {display: false},
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
									  Perhatian
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
				  if(session()->has('alert')){
					$explode = explode('_', session()->get('alert'));
					echo '
					  <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
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
