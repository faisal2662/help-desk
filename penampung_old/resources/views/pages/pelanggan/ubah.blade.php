<?php
	$pelanggan = DB::table('tb_pegawai')
	->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.sebagai_pegawai','Mitra/Pelanggan'],['tb_pegawai.id_pegawai', $_GET['update']]])
	->get();
	if($pelanggan->count() < 1){
		header('Location: '.route('pelanggan'));
		exit();
	}else{
		foreach($pelanggan as $data_pelanggan);
		$id_kantor = 0;
		$id_bagian = 0;
		$class_kantor = '-';
		$nama_kantor = '-';
		$nama_bagian = '-';
		$class_bagian = '-';
		
		if($data_pelanggan->kantor_pegawai == 'Kantor Pusat'){

			$unit_kerja = DB::table('tb_kepala_unit_kerja')
			->where([['tb_kepala_unit_kerja.id_pegawai', $data_pelanggan->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_pelanggan->kantor_pegawai]])
			->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
			->limit(1)
			->get();

			if($unit_kerja->count() > 0){
				foreach($unit_kerja as $data_unit_kerja){

					$kantor_bagian = DB::table('tb_bagian_kantor_pusat')
					->join('tb_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
					->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_unit_kerja->id_bagian_kantor_pusat)
					->get();
					if($kantor_bagian->count() > 0){
						foreach($kantor_bagian as $data_kantor_bagian){
							$id_kantor = $data_kantor_bagian->id_kantor_pusat;
							$id_bagian = $data_kantor_bagian->id_bagian_kantor_pusat;
							$nama_kantor = $data_kantor_bagian->nama_kantor_pusat;
							$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_pusat;
							$class_kantor = 'Kantor_Pusat';
							$class_bagian = 'kantor_pusat_'.$id_kantor;
						}
					}

				}
			}else{

				$kantor_bagian = DB::table('tb_bagian_kantor_pusat')
				->join('tb_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
				->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pelanggan->id_bagian_kantor_pusat)
				->get();
				if($kantor_bagian->count() > 0){
					foreach($kantor_bagian as $data_kantor_bagian){
						$id_kantor = $data_kantor_bagian->id_kantor_pusat;
						$id_bagian = $data_kantor_bagian->id_bagian_kantor_pusat;
						$nama_kantor = $data_kantor_bagian->nama_kantor_pusat;
						$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_pusat;
						$class_kantor = 'Kantor_Pusat';
						$class_bagian = 'kantor_pusat_'.$id_kantor;
					}
				}

			}
			
		}else if($data_pelanggan->kantor_pegawai == 'Kantor Cabang'){

			$unit_kerja = DB::table('tb_kepala_unit_kerja')
			->where([['tb_kepala_unit_kerja.id_pegawai', $data_pelanggan->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_pelanggan->kantor_pegawai]])
			->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
			->limit(1)
			->get();

			if($unit_kerja->count() > 0){
				foreach($unit_kerja as $data_unit_kerja){

					$kantor_bagian = DB::table('tb_bagian_kantor_cabang')
					->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
					->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_unit_kerja->id_bagian_kantor_cabang)
					->get();
					if($kantor_bagian->count() > 0){
						foreach($kantor_bagian as $data_kantor_bagian){
							$id_kantor = $data_kantor_bagian->id_kantor_cabang;
							$id_bagian = $data_kantor_bagian->id_bagian_kantor_cabang;
							$nama_kantor = $data_kantor_bagian->nama_kantor_cabang;
							$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_cabang;
							$class_kantor = 'Kantor_Cabang';
							$class_bagian = 'kantor_cabang_'.$id_kantor;
						}
					}

				}
			}else{

				$kantor_bagian = DB::table('tb_bagian_kantor_cabang')
				->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
				->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pelanggan->id_bagian_kantor_cabang)
				->get();
				if($kantor_bagian->count() > 0){
					foreach($kantor_bagian as $data_kantor_bagian){
						$id_kantor = $data_kantor_bagian->id_kantor_cabang;
						$id_bagian = $data_kantor_bagian->id_bagian_kantor_cabang;
						$nama_kantor = $data_kantor_bagian->nama_kantor_cabang;
						$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_cabang;
						$class_kantor = 'Kantor_Cabang';
						$class_bagian = 'kantor_cabang_'.$id_kantor;
					}
				}

			}
			
		}else if($data_pelanggan->kantor_pegawai == 'Kantor Wilayah'){

			$unit_kerja = DB::table('tb_kepala_unit_kerja')
			->where([['tb_kepala_unit_kerja.id_pegawai', $data_pelanggan->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N'],['tb_kepala_unit_kerja.kantor_pegawai', $data_pelanggan->kantor_pegawai]])
			->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
			->limit(1)
			->get();

			if($unit_kerja->count() > 0){
				foreach($unit_kerja as $data_unit_kerja){

					$kantor_bagian = DB::table('tb_bagian_kantor_wilayah')
					->join('tb_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
					->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_unit_kerja->id_bagian_kantor_wilayah)
					->get();
					if($kantor_bagian->count() > 0){
						foreach($kantor_bagian as $data_kantor_bagian){
							$id_kantor = $data_kantor_bagian->id_kantor_wilayah;
							$id_bagian = $data_kantor_bagian->id_bagian_kantor_wilayah;
							$nama_kantor = $data_kantor_bagian->nama_kantor_wilayah;
							$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_wilayah;
							$class_kantor = 'Kantor_Wilayah';
							$class_bagian = 'kantor_wilayah_'.$id_kantor;
						}
					}

				}
			}else{

				$kantor_bagian = DB::table('tb_bagian_kantor_wilayah')
				->join('tb_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
				->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pelanggan->id_bagian_kantor_wilayah)
				->get();
				if($kantor_bagian->count() > 0){
					foreach($kantor_bagian as $data_kantor_bagian){
						$id_kantor = $data_kantor_bagian->id_kantor_wilayah;
						$id_bagian = $data_kantor_bagian->id_bagian_kantor_wilayah;
						$nama_kantor = $data_kantor_bagian->nama_kantor_wilayah;
						$nama_bagian = $data_kantor_bagian->nama_bagian_kantor_wilayah;
						$class_kantor = 'Kantor_Wilayah';
						$class_bagian = 'kantor_wilayah_'.$id_kantor;
					}
				}

			}
			
		}
	}
?>

@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
				<div class="card">
				  <div class="card-body">
					<div class="card-title"><b><i class='bx bx-edit'></i> Perbarui User</b></div>
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
							<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('pelanggan.update') ?>">
							  <?= csrf_field() ?>
							  
								<label>NPP</label>
								<input type="number" name="npp" value="<?= htmlspecialchars($data_pelanggan->npp_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Nama Lengkap</label>
								<input type="text" name="nama" value="<?= htmlspecialchars($data_pelanggan->nama_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Jenis Kelamin</label><br>
								<input type="radio" name="jenkel" value="Laki-laki" <?= $data_pelanggan->jenkel_pegawai == 'Laki-laki' ? 'checked' : '' ?>> Laki-laki
								&nbsp;&nbsp;&nbsp;
								<input type="radio" name="jenkel" value="Perempuan" <?= $data_pelanggan->jenkel_pegawai == 'Perempuan' ? 'checked' : '' ?>> Perempuan
								<br><br>

								<label>No.Telp</label>
								<input type="number" name="telp" value="<?= htmlspecialchars($data_pelanggan->telp_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Email</label>
								<input type="email" name="email" value="<?= htmlspecialchars($data_pelanggan->email_pegawai) ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>
								
								<label>Password Baru (Opsional)</label>
								<input type="password" name="password"  id="txt_password" class="form-control" maxlength="255" placeholder="Opsional ...">
								<label for="" id="txt_label_password" class="text-danger" style="font-size: 12px;"></label>
								<br>

								<label>Unit Kerja</label>
								<select name="kantor" class="form-control" required="">
								  <?php  
									echo '<option value="'.$data_pelanggan->kantor_pegawai.'">'.$data_pelanggan->kantor_pegawai.'</option>';
									
									foreach(array('Kantor Pusat', 'Kantor Cabang', 'Kantor Wilayah') as $kantor){
									  if($kantor != $data_pelanggan->kantor_pegawai){  
										echo '<option value="'.$kantor.'">'.$kantor.'</option>';
									  }
									}
								  ?>
								</select>
								<br>

								<label>Sub Unit Kerja</label>
								<select name="unit_kerja" class="form-control" required="">
								  <?php  
									echo '<option class="'.$class_kantor.'" value="kantor_pusat_'.$id_kantor.'">'.$nama_kantor.'</option>';
									$kantor_pusat = DB::table('tb_kantor_pusat')
									->where('tb_kantor_pusat.id_kantor_pusat','!=', $id_kantor)
									->where('tb_kantor_pusat.delete_kantor_pusat','=','N')
									->orderBy('tb_kantor_pusat.nama_kantor_pusat','ASC')
									->get();
									if($kantor_pusat->count() > 0){
										foreach($kantor_pusat as $data_kantor_pusat){
											echo '<option class="Kantor_Pusat" value="kantor_pusat_'.$data_kantor_pusat->id_kantor_pusat.'">'.$data_kantor_pusat->nama_kantor_pusat.'</option>';
										}
									}
									
									$kantor_cabang = DB::table('tb_kantor_cabang')
									->where('tb_kantor_cabang.delete_kantor_cabang','=','N')
									->where('tb_kantor_cabang.id_kantor_cabang','!=', $id_kantor)
									->orderBy('tb_kantor_cabang.nama_kantor_cabang','ASC')
									->get();
									if($kantor_cabang->count() > 0){
										foreach($kantor_cabang as $data_kantor_cabang){
											echo '<option class="Kantor_Cabang" value="kantor_cabang_'.$data_kantor_cabang->id_kantor_cabang.'">'.$data_kantor_cabang->nama_kantor_cabang.'</option>';
										}
									}
									
									$kantor_wilayah = DB::table('tb_kantor_wilayah')
									->where('tb_kantor_wilayah.delete_kantor_wilayah','=','N')
									->where('tb_kantor_wilayah.id_kantor_wilayah','!=', $id_kantor)
									->orderBy('tb_kantor_wilayah.nama_kantor_wilayah','ASC')
									->get();
									if($kantor_wilayah->count() > 0){
										foreach($kantor_wilayah as $data_kantor_wilayah){
											echo '<option class="Kantor_Wilayah" value="kantor_wilayah_'.$data_kantor_wilayah->id_kantor_wilayah.'">'.$data_kantor_wilayah->nama_kantor_wilayah.'</option>';
										}
									}
								  ?>
								</select>
								<br>

								<label>Bagian Unit Kerja</label>
								<select name="bagian" class="form-control" required="">
								  <?php  
									
									if($data_pelanggan->multi_pegawai == 'Tidak'){

										echo '<option class="'.$class_bagian.'" value="'.$id_bagian.'">'.$nama_bagian.'</option>';
										echo '<option value="all" class="All">Semua Bagian</option>';

									}else{

										echo '<option value="all" class="All">Semua Bagian</option>';
										echo '<option class="'.$class_bagian.'" value="'.$id_bagian.'">'.$nama_bagian.'</option>';

									}

									$bagian_kantor_pusat = DB::table('tb_bagian_kantor_pusat')
									->join('tb_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
									->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','!=', $id_bagian)
									->where('tb_bagian_kantor_pusat.delete_bagian_kantor_pusat','=','N')
									->orderBy('tb_bagian_kantor_pusat.nama_bagian_kantor_pusat','ASC')
									->get();
									if($bagian_kantor_pusat->count() > 0){
										foreach($bagian_kantor_pusat as $data_bagian_kantor_pusat){
											echo '<option class="kantor_pusat_'.$data_bagian_kantor_pusat->id_kantor_pusat.'" value="'.$data_bagian_kantor_pusat->id_bagian_kantor_pusat.'">'.$data_bagian_kantor_pusat->nama_bagian_kantor_pusat.'</option>';
										}
									}
									
									$bagian_kantor_cabang = DB::table('tb_bagian_kantor_cabang')
									->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
									->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','!=', $id_bagian)
									->where('tb_bagian_kantor_cabang.delete_bagian_kantor_cabang','=','N')
									->orderBy('tb_bagian_kantor_cabang.nama_bagian_kantor_cabang','ASC')
									->get();
									if($bagian_kantor_cabang->count() > 0){
										foreach($bagian_kantor_cabang as $data_bagian_kantor_cabang){
											echo '<option class="kantor_cabang_'.$data_bagian_kantor_cabang->id_kantor_cabang.'" value="'.$data_bagian_kantor_cabang->id_bagian_kantor_cabang.'">'.$data_bagian_kantor_cabang->nama_bagian_kantor_cabang.'</option>';
										}
									}
									
									$bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
									->join('tb_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
									->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','!=', $id_bagian)
									->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah','=','N')
									->orderBy('tb_bagian_kantor_wilayah.nama_bagian_kantor_wilayah','ASC')
									->get();
									if($bagian_kantor_wilayah->count() > 0){
										foreach($bagian_kantor_wilayah as $data_bagian_kantor_wilayah){
											echo '<option class="kantor_wilayah_'.$data_bagian_kantor_wilayah->id_kantor_wilayah.'" value="'.$data_bagian_kantor_wilayah->id_bagian_kantor_wilayah.'">'.$data_bagian_kantor_wilayah->nama_bagian_kantor_wilayah.'</option>';
										}
									}
								  ?>
								</select>
								<br>

								<label>Sebagai</label>
								<select name="level" class="form-control" required="">
								  <?php  
									echo '<option class="'.str_replace(' ', '_', $data_pelanggan->level_pegawai).'" value="'.$data_pelanggan->level_pegawai.'">'.$data_pelanggan->level_pegawai.'</option>';
									
									foreach(array('Kepala Unit Kerja', 'Kepala Bagian Unit Kerja', 'Staff') as $level){
									  if($level != $data_pelanggan->level_pegawai){
										echo '<option class="'.str_replace(' ', '_', $level).'" value="'.$level.'">'.$level.'</option>';
									  }
									}
								  ?>
								</select>
								<br>

								<label>Status</label>
								<select name="status" class="form-control" required="">
								  <?php  
									echo '<option value="'.$data_pelanggan->status_pegawai.'">'.$data_pelanggan->status_pegawai.'</option>';
									
									foreach(array('Aktif', 'Tidak Aktif') as $status){
										if($status != $data_pelanggan->status_pegawai){
											echo '<option value="'.$status.'">'.$status.'</option>';
										}
									}
								  ?>
								</select>
								<br>

								<div class="form-group">
								  <label>Unggah Foto (Opsional)</label>
								  <br>
								  <label for="file-1">
									<img src="<?= url($data_pelanggan->foto_pegawai) ?>" id="image-1" style="width: 150px;border-radius: 5px;">
									<input type="file" accept="image/*" name="foto" id="file-1" class="form-control" onchange="previewImage('image-1','file-1')" style="display: none;">
								  </label>
								</div>

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
		loadPage('<?= route('pelanggan') ?>');
	  });
	  
        $('#txt_password').on('keyup', function(){
        
            $('#txt_label_password').html("");
        
            var InputValue = $("#txt_password").val();
            var regex = new RegExp("^(?=.*[a-z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
            $("#passwordText").text(`Password value:- ${InputValue}`);
        
            console.log(InputValue);
        
            if(!regex.test(InputValue)) {
                $('#txt_label_password').show();
                $('#txt_label_password').html("Minimal 8 digit dengan kombinasi huruf, angka dan karakter spesial");
                $('.btn-primary').attr("disabled", "disabled");
            }
            else{
        
                $('#txt_label_password').hide();
                $('.btn-primary').removeAttr('disabled');
            }
         
        });
	</script>
	
	<script>
		$('select[name="unit_kerja"] option').hide();
		$('select[name="bagian"] option').hide();
		
		<?php if($data_pelanggan->multi_pegawai == 'Tidak'){ ?>

			// $('select[name="bagian"] .All').hide();
			$('select[name="level"] .Kepala_Unit_Kerja').hide();

		<?php }else{ ?>

			$('select[name="level"] option').hide();
			$('select[name="level"] .Kepala_Unit_Kerja').show();

		<?php } ?>

		<?php if($class_kantor == 'Kantor_Pusat'){ ?>
		
			$('select[name="unit_kerja"] .Kantor_Pusat').show();
			$('select[name="bagian"] .All').show();
			$('select[name="bagian"] .<?= $class_bagian ?>').show();

		<?php }else if($class_kantor == 'Kantor_Cabang'){ ?>
		
			$('select[name="unit_kerja"] .Kantor_Cabang').show();
			$('select[name="bagian"] .All').show();
			$('select[name="bagian"] .<?= $class_bagian ?>').show();
		
		<?php }else if($class_kantor == 'Kantor_Wilayah'){ ?>
		
			$('select[name="unit_kerja"] .Kantor_Wilayah').show();
			$('select[name="bagian"] .All').show();
			$('select[name="bagian"] .<?= $class_bagian ?>').show();

		<?php } ?>
	</script>
	
	<script>
		$('select[name="kantor"]').on('change', function(){
			if($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Pusat'){
				$('select[name="unit_kerja"] option').hide();
				$('select[name="unit_kerja"] .Kantor_Pusat').show();
			}else if($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Cabang'){
				$('select[name="unit_kerja"] option').hide();
				$('select[name="unit_kerja"] .Kantor_Cabang').show();
			}else if($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Wilayah'){
				$('select[name="unit_kerja"] option').hide();
				$('select[name="unit_kerja"] .Kantor_Wilayah').show();
			}else{
				$('select[name="unit_kerja"] option').show();
			}
		});
		
		$('select[name="unit_kerja"]').on('change', function(){
			var value = $('select[name="unit_kerja"] option').filter(':selected').val()
			$('select[name="bagian"] option').hide();
			$('select[name="bagian"] .' + value).show();
			$('select[name="bagian"] .All').show();
		});

		$('select[name="bagian"]').on('change', function(){
			if($('select[name="bagian"] option').filter(':selected').val() == 'all'){
				$('select[name="level"] option').hide();
				$('select[name="level"] .Kepala_Unit_Kerja').show();
			}else{
				$('select[name="level"] option').hide();
				$('select[name="level"] option').show();
				$('select[name="level"] .Kepala_Unit_Kerja').hide();
			}
		});
	</script>

@stop