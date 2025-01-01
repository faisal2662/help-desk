@extends('template')

@section('title')
    Nama Jabatan | Helpdesk
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <p>&nbsp;</p>
            <div class="card">
              <div class="card-body">
                <div class="card-title"><b>Nama Jabatan</b></div>
                <p>
                    <?php echo htmlspecialchars_decode($input); ?>
                </p>
                <?php
						  if(session()->has('alert')){
							$explode = explode('_', session()->get('alert'));
							echo '
							  <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
							';
						  }
						?>
                <hr style="border-style: dashed;">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
                            <thead>
                              <tr>
                                <td><b>No</b></td>
                           
                                <td><b>Nama Posisi </b></td>
                                <td><b>Sebagai Posisi </b></td>
                                <td><b>Action</b></td>
                              </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <span style="display: none;">


                        <form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('nama_jabatan.delete') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="delete" id="input-delete" readonly="" required="">
                        </form>
                    </span>

                    <script type="text/javascript">
                        function delete_data (id, name ){
                            var r = confirm('Hapus data '+name+'?');
                            if(r == true){
                              show(true);
                              document.getElementById('input-delete').value = id;
                              document.getElementById('form-delete').submit();
                            }
                        }


                    </script>


              </div>
            </div>
        <p>&nbsp;</p>
    </div>
</div>
@section('script')

		<script type="text/javascript">
		  $('#dataTables').DataTable({
			"processing": true,
			"serverSide": true,
			"ajax":{
				 "url": "<?= route('nama_jabatan.datatables') ?>",
				 "dataType": "json",
				 "type": "POST",
				 "data":{ _token: "<?= csrf_token() ?>"}
			   },
			columns: [
				{data: 'no'},

				{data: 'nama_posisi'},
				{data: 'sebagai_posisi'},
				{data: 'action'},
			]
		  });
		</script>

	@stop
@endsection
