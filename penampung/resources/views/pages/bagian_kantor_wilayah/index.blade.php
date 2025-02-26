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
	Bagian Kantor Wilayah - Helpdesk
@stop

	<?php if(isset($_GET['create'])){ ?>

		@include('pages.bagian_kantor_wilayah.tambah')

	<?php }else if(isset($_GET['update'])){ ?>

		@include('pages.bagian_kantor_wilayah.ubah')

	<?php }else{ ?>

	@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b>Bagian Kantor Wilayah</b></div>
				<p>
					<?php echo htmlspecialchars_decode($input); ?>
				</p>
				<hr style="border-style: dashed;">
				
				<?php
					$bagian_kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
					->join('tb_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
					->where('tb_bagian_kantor_wilayah.delete_bagian_kantor_wilayah','=','N')
					->orderBy('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','DESC')
					->get();
				?>
				
				<?php if($bagian_kantor_wilayah->count() < 1){ ?>
				
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
								<td><b>Kantor Wilayah</b></td>
								<td><b>Bagian Kantor Wilayah</b></td>
								<td><b>Action</b></td>
							  </tr>
							</thead>
						</table>
					</div>

					<span style="display: none;">
						<form method="GET" onsubmit="show(true)" id="form-update">
							<input type="text" name="update" id="input-update" readonly="" required="">
						</form>

						<form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('bagian_kantor_wilayah.delete') ?>">
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
				 "url": "<?= route('bagian_kantor_wilayah.datatables') ?>",
				 "dataType": "json",
				 "type": "POST",
				 "data":{ _token: "<?= csrf_token() ?>"}
			   },
			columns: [
				{data: 'no'},
				{data: 'nama_kantor_wilayah'},
				{data: 'nama_bagian_kantor_wilayah'},
				{data: 'action'},
			]
		  });
		</script>

	@stop

<?php } ?>