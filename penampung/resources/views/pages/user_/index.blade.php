<?php
// 	$pegawai = DB::table('tb_pegawai')
// 	->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
// 	->get();
// 	if($pegawai->count() < 1){
// 		header('Location: '.route('keluar'));
// 		exit();
// 	}else{
// 		foreach($pegawai as $data_pegawai);
// 		if($data_pegawai->level_pegawai != 'Administrator'){
// 			header('Location: '.route('dashboard'));
// 			exit();
// 		}
// 	}
?>

@extends('template')

@section('title')
	User - Helpdesk
@stop

<?php if(isset($_GET['create'])){ ?>

	@include('pages.user.tambah')

<?php }else if(isset($_GET['update'])){ ?>

	@include('pages.user.ubah')

<?php }else if(isset($_GET['view'])){ ?>

	@include('pages.user.lihat')

<?php }else{ ?>

	@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
				<div class="card">
				  <div class="card-body">
					<div class="card-title"><b>User</b></div>
					<p>
						<?php echo htmlspecialchars_decode($input); ?>
					</p>
					<hr style="border-style: dashed;">
					<?php
						$pelanggan = DB::table('tb_pegawai')
						->where([['tb_pegawai.delete_pegawai','N']])
						->orderBy('tb_pegawai.id_pegawai','DESC')
						->get();
					?>

					<?php if($pelanggan->count() < 1){ ?>

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
									<td><b>NPP</b></td>
									<td><b>Nama Lengkap</b></td>
									<td><b>No.Telp</b></td>
									<td><b>Email</b></td>
									<td><b>Unit Kerja</b></td>
									<td><b>Bagian Unit Kerja</b></td>
									<td><b>Jabatan</b></td>
									<td><b>Sebagai</b></td>
									<td><b>Status</b></td>
									<td><b>Tanggal</b></td>
									<td><b>Action</b></td>
								  </tr>
								</thead>
							</table>
						</div>

						<span style="display: none;">
							<form method="GET" onsubmit="show(true)" id="form-update">
								<input type="text" name="update" id="input-update" readonly="" required="">
							</form>

							<form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('pelanggan.delete') ?>">
								<?= csrf_field() ?>
								<input type="text" name="delete" id="input-delete" readonly="" required="">
							</form>
						</span>

						<script type="text/javascript">
							function delete_data (id, name){
								var r = confirm('Hapus data '+name+'?');
								if(r == true){
								  show(true);
								  document.getElementById('input-delete').value = id;
								  document.getElementById('form-delete').submit();
								}
							}

							function update_data (id){
								show(true);
								document.getElementById('input-update').value = id;
								document.getElementById('form-update').submit();
							}
						</script>

					<?php } ?>
				  </div>
				</div>
			<p>&nbsp;</p>
		</div>
	</div>

	@stop

	@section('script')

		<script type="text/javascript">
		  $('#dataTables').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax":{
				 "url": "<?= route('pelanggan.datatables') ?>",
				 "dataType": "json",
				 "type": "POST",
				 "data":{ _token: "<?= csrf_token() ?>"}
			   },
			columns: [
				{data: 'no'},
				{data: 'npp_pegawai'},
				{data: 'nama_pegawai'},
				{data: 'telp_pegawai'},
				{data: 'email_pegawai'},
				{data: 'kantor_pegawai'},
				{data: 'bagian'},
				{data: 'level_pegawai'},
				{data: 'sebagai_pegawai'},
				{data: 'status_pegawai'},
				{data: 'tgl_pegawai'},
				{data: 'action'},
			]
		  });
		</script>

	@stop


<?php } ?>
