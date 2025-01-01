<?php
	$bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')
	->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
	->where([['tb_bagian_kantor_cabang.delete_bagian_kantor_cabang','N'],['tb_bagian_kantor_cabang.id_bagian_kantor_cabang', $_GET['update']]])
	->get();
	if($bagian_kantor_cabang->count() < 1){
		header('Location: '.route('bagian_kantor_cabang'));
		exit();
	}else{
		foreach($bagian_kantor_cabang as $data_bagian_kantor_cabang);
	}
?>

@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
				<div class="card">
				  <div class="card-body">
					<div class="card-title"><b><i class='bx bx-edit'></i> Perbarui Bagian Kantor Cabang</b></div>
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
							
							<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('bagian_kantor_cabang.update') ?>">
							  <?= csrf_field() ?>

								<label>Kantor Cabang</label>
								<select name="kantor" class="form-control" required="">
								  <?php  
									echo '<option value="'.$data_bagian_kantor_cabang->id_kantor_cabang.'">'.$data_bagian_kantor_cabang->nama_kantor_cabang.'</option>';
									
									$kantor_cabang = DB::table('tb_kantor_cabang')
									->where('tb_kantor_cabang.delete_kantor_cabang','=','N')
									->where('tb_kantor_cabang.id_kantor_cabang','!=', $data_bagian_kantor_cabang->id_kantor_cabang)
									->orderBy('tb_kantor_cabang.nama_kantor_cabang','ASC')
									->get();
									if($kantor_cabang->count() > 0){
										foreach($kantor_cabang as $data_kantor_cabang){
										  echo '<option value="'.$data_kantor_cabang->id_kantor_cabang.'">'.$data_kantor_cabang->nama_kantor_cabang.'</option>';
										}
									}
								  ?>
								</select>
								<br>
								
								<label>Bagian Kantor Cabang</label>
								<input type="text" name="nama" value="<?= htmlspecialchars($data_bagian_kantor_cabang->nama_bagian_kantor_cabang) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
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
		loadPage('<?= route('bagian_kantor_cabang') ?>');
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