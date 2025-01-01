<!-- autorisasi -->
<?php  
	$npp = '-';
	$nama = '-';
	$jenkel = '-';
	$telp = '-';
	$email = '-';
	$kantor = '-';
	$divisi = '-';
	$sebagai = '-';
	$foto = 'logos/avatar.png';
	$tgl = '-';
	$status = '-';
	$role = '-';

?>
<!-- autorisasi -->

@extends('template')

@section('title')
	Helpdesk - Profil
@stop

@section('content')

	<div class="row">
		<div class="col-md-12">
			<div class="card">
			  <div class="card-body">
			    <div class="card-title">
			    	<b>
			    		Role Setting
			    	</b>

			    	

			    </div>

				<?php  
				  if(session()->has('alert')){
				    $explode = explode('_', session()->get('alert'));
				    echo '
				      <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
				    ';
				  }
				?>

				<div class="row">
					<div class="col-sm-12 col-md-12">
						<table class="table table-bordered" id="dataTable">
							<thead>
								<th align="center"><b>No</b></th>
								<th align="center"><b>Nama</b></th>
								<th align="center"><b>Position</b></th>
								<th align="center"><b>Level</b></th>
								<th align="center"><b>Action</b></th>
							</thead>

							<tbody></tbody>

						</table>
				      	
					</div>
				</div>
			    
				<div class="table-responsive">
				  
				</div>
				<p>&nbsp;</p>

			  </div>
			</div>
			<p>&nbsp;</p>
		</div>

		<?php  
		  $bulan = date('Y-m');

		  if(isset($_GET['bulan'])){
		    $bulan = $_GET['bulan'];
		  }
		?>

		
	</div>

@stop

@section('script')

	<script type="text/javascript">
	  function previewImage(preview, source) {
	    var oFReader = new FileReader();
	     oFReader.readAsDataURL(document.getElementById(source).files[0]);
	  
	    oFReader.onload = function(oFREvent) {
	      document.getElementById(preview).src = oFREvent.target.result;
	    };
	  };
	</script>

	<script type="text/javascript">
		$(document).ready(function(){
			var modalMenu = $('#modalMenu');

			$('#dataTable').DataTable({
	            processing: true,
	            serverSide: true,
	            stateSave: true,
	            ajax:{
	                url: "{{ route('role.account.datatables') }}",
	                type: "GET"
	            },
	            columns: [
	                {'data': 'no'},
	                {'data': 'nama_pegawai'},
	                {'data': 'level_pegawai'},
	                {'data': 'sebagai_pegawai'},
	                {'data': 'action'}
	            ],
	           
	        });


			function clean()
			{
				$('#menu').val('');
				$('#icon').val('');
				$('#route_name').val('');
				$('#actor').val('');
				$('#type').val('');

			}


			$('#add').on('click', function(e){
				$('#modal-title-header').html("Add Menu");
				clean();


				$('#formMenu').attr("action", "<?= route('role.menu.save') ?>");
				$(modalMenu).modal('show');

			});

			$('.table').on('click', '.btn-edit', function(e){
				var id = this.getAttribute('data-id');


				$.ajax({
					url: "{{ route('role.menu') }}/"+id+"/detail",
					type: "GET",
					success: function(res){
						$('#menu').val(res.data.menu);
						$('#icon').val(res.data.icon);
						$('#route_name').val(res.data.route_name);
						$('#actor').val(res.data.actor);
						$('#type').val(res.data.type);
						$('#formMenu').attr("action", "<?= route('role.menu') ?>/"+id+"/update");

						$('#modal-title-header').html("Edit Menu");
						$(modalMenu).modal('show');

					}
				});

				console.log(id);
			});

		});
	</script>

	<script type="text/javascript">
	    $("#bulan").datepicker({
	        format: "yyyy-mm",
	        viewMode: "months", 
	        minViewMode: "months"
	    });
	</script>



	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
	<script src="https://www.chartjs.org/samples/2.6.0/utils.js"></script>

@stop