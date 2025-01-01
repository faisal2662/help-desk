<?php
	$jawaban = DB::table('tb_jawaban')
	->where('tb_jawaban.id_jawaban','=', $id_jawaban)
	->get();
?>

<?php if($jawaban->count() < 1){ ?>

	<center>
	   <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
	   <p>Jawaban tidak ditemukan.</p>
	</center>

<?php }else{ ?>

	<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('pengaduan.tanggapan') ?>">
	  <?= csrf_field() ?>
	  
		<input type="hidden" name="pengaduan" value="<?= $id_pengaduan ?>" readonly="" required="">
	  
		<label>Jawaban</label>
		<textarea name="keterangan" class="form-control" required="" placeholder="Harap di isi ..."></textarea>
		<br>

		<div class="form-group">
		  <label>Unggah Foto (Opsional)</label>
		  <br>
		  <label for="file-1">
			<img src="<?= url('logos/image.png') ?>" id="image-1" style="width: 150px;border-radius: 5px;">
			<input type="file" accept="image/*" name="foto" id="file-1" class="form-control" onchange="previewImage('image-1','file-1')" style="display: none;">
		  </label>
		</div>

		<button type="submit" name="jawaban" value="<?= $id_jawaban ?>" class="btn btn-sm btn-primary">
		   <i class='bx bx-send' ></i> Kirim Tanggapan
		</button>

	</form>

<?php } ?>