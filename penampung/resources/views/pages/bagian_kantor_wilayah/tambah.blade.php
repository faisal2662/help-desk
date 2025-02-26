@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
				<div class="card">
				  <div class="card-body">
					<div class="card-title"><b><i class='bx bx-plus'></i> Tambah Bagian Kantor Wilayah</b></div>
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
							
							<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('bagian_kantor_wilayah.save') ?>">
							  <?= csrf_field() ?>

								<label>Kantor Wilayah</label>
								<select name="kantor" class="form-control" required="">
								  <?php  
									echo '<option value="">- Pilih salah satu -</option>';
									
									$kantor_wilayah = DB::table('tb_kantor_wilayah')
									->where('tb_kantor_wilayah.delete_kantor_wilayah','=','N')
									->orderBy('tb_kantor_wilayah.nama_kantor_wilayah','ASC')
									->get();
									if($kantor_wilayah->count() > 0){
										foreach($kantor_wilayah as $data_kantor_wilayah){
										  echo '<option value="'.$data_kantor_wilayah->id_kantor_wilayah.'">'.$data_kantor_wilayah->nama_kantor_wilayah.'</option>';
										}
									}
								  ?>
								</select>
								<br>
								
								<label>Bagian Kantor Wilayah</label>
								<input type="text" name="nama" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
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
		loadPage('<?= route('bagian_kantor_wilayah') ?>');
	  });
	</script>
	
	<script type="text/javascript">
		$(document).ready(function () {
		  $('select').selectize({
			  sortField: 'text'
		  });
		});
	</script>

@stop