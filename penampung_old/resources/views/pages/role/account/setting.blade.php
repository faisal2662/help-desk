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
			    		Role Menu {{ $user->nama_pegawai }}
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
					<form method="POST" action="{{ route('role.account.update.setting') }}" style="width: 100%;" onsubmit="show(true)">
						{{ csrf_field() }}
						<div class="col-sm-12 col-md-12">
							<table class="table table-bordered">
								<tr>
									<td align="center"><b>No</b></td>
									<td align="center"><b>Menu</b></td>
									<td align="center"><b>Can Access</b></td>
									<td align="center"><b>Can Input</b></td>
									<td align="center"><b>Can Update</b></td>
									<td align="center"><b>Can Delete</b></td>
								</tr>
								<?php 
									$no = 1;
								?>
								
									@foreach($role_menu as $rm)
										<tr>
											<td>{{ $no++ }}</td>
											@if($rm->type == "TITLE")
												<td><b>{{ $rm->menu }}</b></td>
											@else
												<td>{{ $rm->menu }}</td>
											@endif
											<input type="hidden" name="id_menu[]" value="{{ $rm->id_role_menu }}">
											<input type="hidden" name="id_account[]" value="{{ Request()->id }}">
											<input type="hidden" name="actor[]" value="{{ Request()->actor }}">
											<td align="center"><input type="checkbox" value="Y" name="access-{{ $rm->id_role_menu }}" <?php echo $rm->can_access == "Y" ? "checked" : "" ?> ></td>
											<td align="center"><input type="checkbox" value="Y" name="input-{{ $rm->id_role_menu }}" <?php echo $rm->can_create == "Y" ? "checked" : "" ?>></td>
											<td align="center"><input type="checkbox" value="Y" name="update-{{ $rm->id_role_menu }}" <?php echo $rm->can_update == "Y" ? "checked" : "" ?>></td>
											<td align="center"><input type="checkbox" value="Y" name="delete-{{ $rm->id_role_menu }}" <?php echo $rm->can_delete == "Y" ? "checked" : "" ?>></td>
										</tr>
									@endforeach
							</table>
					      	
						</div>

						<div class="col-sm-12 mt-5">
							<button type="submit" class="btn btn-info">Update Role</button>
						</div>

					</form>
				</div>
			    
			    <div class="modal fade" id="modalMenu">
			    	<div class="modal-dialog">
			    		<div class="modal-content">
			    			<div class="modal-header">
			    				 <table style="width: 100%;">
						              <tbody>
						                  <tr>
						                      <td>
						                          <b id="modal-title-header">

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
			    			<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" id="formMenu">
			    				{{ csrf_field() }}
			    				<div class="modal-body">
				    				<div class="form-group">
				    					<label><b>Menu</b></label>
				    					<input type="text" class="form-control" name="menu" id="menu" placeholder="Menu">
				    				</div>
				    				<div class="form-group">
				    					<label><b>Icon</b></label>
				    					<input type="text" class="form-control" name="icon" id="icon" placeholder="Icon">
				    				</div>
				    				<div class="form-group">
				    					<label><b>Route</b></label>
				    					<input type="text" class="form-control" name="route_name" id="route_name" placeholder="Route">
				    				</div>
				    				<div class="form-group">
				    					<label><b>Actor</b></label>
				    					<select name="actor" id="actor">
				    						<option>Pilih Actor</option>
				    						<option value="PETUGAS">Petugas</option>
				    						<option value="AGENT">Agent</option>
				    						<option value="MITRA">Mitra</option>
				    					</select>
				    				</div>
				    				<div class="form-group">
				    					<label><b>Type</b></label>
				    					<select name="type" id="type">
				    						<option>Pilih Type</option>
				    						<option value="TITLE">Title</option>
				    						<option value="MENU">Menu</option>
				    					</select>
				    				</div>
				    				<button type="submit" class="btn btn-primary">Save</button>
				    			</div>
			    			</form>
			    			
			    		</div>
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
