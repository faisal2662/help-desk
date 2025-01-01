<?php
	$bagian_kantor_pusat = DB::table('tb_bagian_kantor_pusat')
	->join('tb_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
	->where([['tb_bagian_kantor_pusat.delete_bagian_kantor_pusat','N'],['tb_bagian_kantor_pusat.id_bagian_kantor_pusat', $_GET['update']]])
	->get();
	if($bagian_kantor_pusat->count() < 1){
		header('Location: '.route('bagian_kantor_pusat'));
		exit();
	}else{
		foreach($bagian_kantor_pusat as $data_bagian_kantor_pusat);
	}
?>

@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
				<div class="card">
				  <div class="card-body">
					<div class="card-title"><b><i class='bx bx-edit'></i> Perbarui Bagian Kantor Pusat</b></div>
					<hr style="border-style: dashed;">
					
					<div class="row">
						<div class="col-md-6" align="center">
							<img src="<?= url('logos/edit.png') ?>" style="max-width: 100%;">
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
							
							<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('bagian_kantor_pusat.update') ?>">
							  <?= csrf_field() ?>

								<label>Kantor Pusat</label>
								<select name="kantor" class="form-control" required="">
								  <?php  
									echo '<option value="'.$data_bagian_kantor_pusat->id_kantor_pusat.'">'.$data_bagian_kantor_pusat->nama_kantor_pusat.'</option>';
									
									$kantor_pusat = DB::table('tb_kantor_pusat')
									->where('tb_kantor_pusat.delete_kantor_pusat','=','N')
									->where('tb_kantor_pusat.id_kantor_pusat','!=', $data_bagian_kantor_pusat->id_kantor_pusat)
									->orderBy('tb_kantor_pusat.nama_kantor_pusat','ASC')
									->get();
									if($kantor_pusat->count() > 0){
										foreach($kantor_pusat as $data_kantor_pusat){
										  echo '<option value="'.$data_kantor_pusat->id_kantor_pusat.'">'.$data_kantor_pusat->nama_kantor_pusat.'</option>';
										}
									}
								  ?>
								</select>
								<br>
								
								<label>Bagian Kantor Pusat</label>
								<input type="text" name="nama" value="<?= htmlspecialchars($data_bagian_kantor_pusat->nama_bagian_kantor_pusat) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>
								
								<button type="button" class="btn btn-sm btn-warning" id="kembali">
								  <i class='bx bx-arrow-back'></i> Kembali
								</button>

								<button type="submit" name="update" value="<?= $_GET['update'] ?>" class="btn btn-sm btn-primary">
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
		loadPage('<?= route('bagian_kantor_pusat') ?>');
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