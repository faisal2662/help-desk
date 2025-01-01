@extends('template')

@section('title')
	Dashboard - Helpdesk
@stop

@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
			<h4>
				Dashboard - Helpdesk
			</h4>
			<p>&nbsp;</p>
		</div>
	</div>
	
	<?php  
	  $tahun = date('Y');

	  if(isset($_GET['tahun'])){
		if($_GET['tahun'] == ''){
			$tahun = date('Y');
		}else{
			$tahun = $_GET['tahun'];
		}
	  }
	?>
	
	<div class="row">
		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-calendar' ></i> Filter Tahun</b></div>
				<hr style="border-style: dashed;">
				<form method="GET" onsubmit="show(true);">
					<div class="input-group">
						<input type="text" name="tahun" id="tahun" value="<?= $tahun ?>" required="" readonly="" maxlength="255" placeholder="Atur Tahun ..." class="form-control">
						<div class="input-group-append">
							<button type="submit" class="btn btn-sm btn-primary">
								<i class='bx bx-calendar-check' ></i>
							</button>
						</div>
					</div>
				</form>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>
	
	<div class="row">
        @foreach ($pegawai as $data_pegawai)
            @if ($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan')
                @if ($data_pegawai->level_pegawai != 'Kepala Unit Kerja')
                    <div class="col-md-6">
                        <div class="card">
                        <div class="card-body">
                            <div class="card-title"><b><i class='bx bx-loader' ></i> Pengaduan Pending</b></div>
                            <hr style="border-style: dashed;">
                            <canvas id="pengaduan-pending" style="width: 100%;"></canvas>
                        </div>
                        </div>
                        <p>&nbsp;</p>
                    </div>
                @endif
            @endif
        @endforeach

		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-list-check' ></i> Pengaduan Approve</b></div>
				<hr style="border-style: dashed;">
				<canvas id="pengaduan-approve" style="width: 100%;"></canvas>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-refresh' ></i> Pengaduan On Progress</b></div>
				<hr style="border-style: dashed;">
				<canvas id="pengaduan-on-progress" style="width: 100%;"></canvas>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-block' ></i> Pengaduan SLA</b></div>
				<hr style="border-style: dashed;">
				<canvas id="pengaduan-holding" style="width: 100%;"></canvas>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-check-double' ></i> Pengaduan Finish</b></div>
				<hr style="border-style: dashed;">
				<canvas id="pengaduan-finish" style="width: 100%;"></canvas>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
				<div class="card-title"><b><i class='bx bx-time-five' ></i> Pengaduan Late</b></div>
				<hr style="border-style: dashed;">
				<canvas id="pengaduan-late" style="width: 100%;"></canvas>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>

@stop

@section('script')

	<script type="text/javascript">
		$("#tahun").datepicker({
			format: "yyyy",
			viewMode: "years", 
			minViewMode: "years"
		});
	</script>

	{{-- @include('pages.dashboard.chart') --}}

@stop