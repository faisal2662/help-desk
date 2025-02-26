<?php  
	$pegawai = DB::table('tb_pegawai')
	->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
	->get();
	if($pegawai->count() < 1){
		header('Location: '.route('faq'));
		exit();
	}else{
		foreach($pegawai as $data_pegawai);
		if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){

		}else{
			header('Location: '.route('faq'));
			exit();
		}
	}
?>

@section('content')

	<div class="row">
		<div class="col-md-12">
			<div class="card">
			  <div class="card-body">
			    <div class="card-title"><b><i class='bx bx-plus'></i> Tambah FAQ</b></div>
			    <hr style="border-style: dashed;">
				<?php  
				  if(session()->has('alert')){
				    $explode = explode('_', session()->get('alert'));
				    echo '
				      <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
				    ';
				  }
				?>
				<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('faq.save') ?>">
				    <?= csrf_field() ?>

				    <div class="row">
				    	<div class="col-md-6">
							<label>Pertanyaan</label>
							<input type="text" name="pertanyaan" value="<?= old('pertanyaan') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
				    		<br>
				    	</div>
				    	<div class="col-md-6">
							<label>Urutan FAQ</label>
							<input type="number" name="urutan" value="<?= old('urutan') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
				    		<br>
				    	</div>
				    </div>

					<label>Penjelasan</label>
					<textarea name="keterangan" id="ckeditor" class="form-control" required="" placeholder="Harap di isi ..."><?= old('keterangan') ?></textarea>
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
			<p>&nbsp;</p>
		</div>
	</div>

@stop

@section('script')

	<script type="text/javascript">
	  $('#kembali').on('click', function() {
	    loadPage('<?= route('faq') ?>');
	  });
	</script>

	<script src="//cdn.ckeditor.com/4.16.0/full/ckeditor.js"></script>
	<script type="text/javascript">
	  CKEDITOR.replace('ckeditor');
	</script>

@stop