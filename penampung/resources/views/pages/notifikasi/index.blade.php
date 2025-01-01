@extends('template')

@section('title')
	Notifikasi - Helpdesk
@stop

@section('content')

<?php
if(!isset($_GET['status'])){ 

	header('Location: '.route('dashboard'));
	exit();

}else{
	if($_GET['status'] == ''){
		header('Location: '.route('dashboard'));
		exit();
	}
}
?>

<div class="row">
	<div class="col-md-12">
		<p>&nbsp;</p>
		<div class="card">
		  <div class="card-body">
			<div class="card-title"><b><i class='bx bx-bell' ></i> Pengaduan <?= htmlspecialchars(str_replace('Holding', 'SLA', $_GET['status'])) ?></b></div>
			<hr style="border-style: dashed;">
			
			<?php
				$notifikasi = DB::table('tb_notifikasi')
				->join('tb_pegawai','tb_notifikasi.id_pegawai','=','tb_pegawai.id_pegawai')
				->where([['tb_notifikasi.id_pegawai', Session::get('id_pegawai')],['tb_notifikasi.delete_notifikasi','N'],['tb_notifikasi.nama_notifikasi', 'Pengaduan '.$_GET['status']]])
				->get();
			?>
			
			<?php if($notifikasi->count() < 1){ ?>
			
				<center>
				   <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
				   <p>Belum ada notifikasi saat ini.</p>
				</center>

			<?php }else{ ?>
			
				<div class="table-responsive">
					<table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
						<thead>
						  <tr>
							<td><b>No</b></td>
							<td><b>Pengaduan</b></td>
							<td><b>Keterangan</b></td>
							<td><b>Tanggal</b></td>
						  </tr>
						</thead>
					</table>
				</div>

				<span style="display: none;">
					<form method="POST" onsubmit="show(true)" id="form-notifikasi" action="<?= route('notifikasi.read_notifikasi') ?>?filter=<?= $_GET['status'] ?>">
						<?= csrf_field() ?>
						<input type="text" name="id_notifikasi" id="input-notifikasi" readonly="" required="">
					</form>
				</span>

				<script type="text/javascript">
					function read_notifikasi (id){
					  show(true);
					  document.getElementById('input-notifikasi').value = id;
					  document.getElementById('form-notifikasi').submit();
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
			 "url": "<?= route('notifikasi.datatables') ?>?status=<?= $_GET['status'] ?>",
			 "dataType": "json",
			 "type": "POST",
			 "data":{ _token: "<?= csrf_token() ?>"}
		   },
		columns: [
			{data: 'no'},
			{data: 'nama_notifikasi'},
			{data: 'keterangan_notifikasi'},
			{data: 'tgl_notifikasi'},
		]
	  });
	</script>

@stop