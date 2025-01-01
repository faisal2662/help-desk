@extends('template')
@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-plus' ></i> Tambah Nama Jabatan</b></div>
				<hr style="border-style: dashed;">

				<div class="row">
					<div class="col-md-6" align="center">
						<img src="<?= url('logos/add.png') ?>" style="max-width: 100%;">
					</div>

					<div class="col-md-6">
						<?php
						  if(session()->has('alert')){
							$explode = explode('_', session()->get('alert'));
							echo '
							  <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
							';
						  }
						?>
						<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('nama_jabatan.save') ?>">
						  <?= csrf_field() ?>

                            <label>Nama Jabatan</label>
							{{--  <input type="text" name="nama" class="form-control" required="" maxlength="255" placeholder="Harap di isi ..."> --}}
							<textarea name="nama" id="nama"  rows="3" required class="form-control"></textarea>
                            <br>
							<label>Sebagai Jabatan</label>
							 <select name="sebagai" id="sebagai" required class="form-control">
                                <option value="" disabled selected> - Pilih Salah Satu - </option>
                                <option value="Staff">Staff</option>
                                <option value="Kepala Bagian Unit Kerja">Kepala Bagian Unit Kerja</option>
                                <option value="Kepala Unit Kerja">Kepala Unit Kerja</option>
                            </select>
							<!--<input type="text" name="sebagai" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">-->
							<br>

							<button type="button" class="btn btn-sm btn-warning" id="kembali">
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
			<p>&nbsp;</p>
		</div>
	</div>

@stop

@section('script')

	<script type="text/javascript">
	  $('#kembali').on('click', function() {
		loadPage('<?= route('nama_jabatan') ?>');
	  });
	</script>

@stop
