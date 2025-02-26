@extends('template')

@section('title')
	Helpdesk - Setting Email
@stop

@section('content')

	<?php  
		$smtp = DB::table('tb_smtp')
		->where('tb_smtp.delete_smtp','=','N')
		->orderBy('tb_smtp.id_smtp','DESC')
		->limit(1)
		->get();
	?>

	<?php if($smtp->count() < 1){ ?>

		<div class="row">
			<div class="col-md-12">
				<div class="card">
				  <div class="card-body">
				    <div class="card-title"><b><i class='bx bx-mail-send'></i> Setting Email</b></div>
				    <hr style="border-style: dashed;">

					<div class="row">
						<div class="col-md-6" align="center">
							<img src="<?= url('logos/edit.png') ?>" style="max-width: 100%;">
						</div>

						<div class="col-md-6">

							<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('smtp.save') ?>">
							  <?= csrf_field() ?>

								<label>Host</label>
								<input type="text" name="host" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Port</label>
								<input type="number" name="port" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Username</label>
								<input type="text" name="username" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Password</label>
								<input type="text" name="password" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Enkripsi</label>
								<input type="text" name="enkripsi" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Alamat Email</label>
								<input type="text" name="alamat_email" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Nama Pengguna Email</label>
								<input type="text" name="nama_email" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

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

	<?php }else{ ?>

		<?php foreach($smtp as $data_smtp); ?>

		<div class="row">
			<div class="col-md-12">
				<div class="card">
				  <div class="card-body">
				    <div class="card-title"><b><i class='bx bx-mail-send'></i> Setting Email</b></div>
				    <hr style="border-style: dashed;">

					<div class="row">
						<div class="col-md-6" align="center">
							<img src="<?= url('logos/edit.png') ?>" style="max-width: 100%;">
						</div>

						<div class="col-md-6">

							<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('smtp.update') ?>">
							  <?= csrf_field() ?>

								<label>Host</label>
								<input type="text" name="host" value="<?= $data_smtp->host_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Port</label>
								<input type="number" name="port" value="<?= $data_smtp->port_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Username</label>
								<input type="text" name="username" value="<?= $data_smtp->username_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Password</label>
								<input type="text" name="password" value="<?= $data_smtp->password_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Enkripsi</label>
								<input type="text" name="enkripsi" value="<?= $data_smtp->enkripsi_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Alamat Email</label>
								<input type="text" name="alamat_email" value="<?= $data_smtp->alamat_email_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<label>Nama Pengguna Email</label>
								<input type="text" name="nama_email" value="<?= $data_smtp->nama_email_smtp ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
								<br>

								<button type="submit" name="update" value="<?= $data_smtp->id_smtp ?>" class="btn btn-sm btn-primary">
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

	<?php } ?>

@stop

@section('script')

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
		                              Status
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