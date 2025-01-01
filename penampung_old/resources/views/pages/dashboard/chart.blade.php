<?php
	$bulan = array('Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des');
	$month = array('01','02','03','04','05','06','07','08','09','10','11','12');
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://www.chartjs.org/samples/2.6.0/utils.js"></script>
<script type="text/javascript">
    var xValues = [
		<?php foreach($bulan as $data_bulan){ ?>

			'<?= $data_bulan ?>',
			
		<?php } ?>
	];

	<?php if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan'){ ?>

		var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>
		
			<?php
				$data_month = str_replace($bulan, $month, $data_bulan);
				
				if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){ 
				
					$pengaduan_pending = DB::table('tb_pengaduan')
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Pending')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
				
				}else if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai != 'Administrator'){
					
					$pengaduan_pending = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Pending')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Agent'){
					
					$pengaduan_pending = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Pending')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai != 'Staff'){
					
					if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Kepala Unit Kerja'){

						$unit_kerja = DB::table('tb_kepala_unit_kerja')
						->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
						->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
						->limit(1)
						->get();

						if($unit_kerja->count() < 1){

							$pengaduan_pending = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
										tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
										tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
										tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Pending')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}else{

							$pengaduan_pending = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai IN (
											SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_pusat IN (
											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_cabang IN (
											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_wilayah IN (
											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										)
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Pending')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}

					}else{

						$pengaduan_pending = DB::table('tb_pengaduan')
						->whereRaw('
							tb_pengaduan.id_pegawai IN (
								Select
									tb_pegawai.id_pegawai
								From
									tb_pegawai
								Where
									tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
									tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
									tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
									tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
							)
						')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.status_pengaduan','=','Pending')
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
						->get();

					}

				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Staff'){
					
					$pengaduan_pending = DB::table('tb_pengaduan')
					->where('tb_pengaduan.id_pegawai','=', $data_pegawai->id_pegawai)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Pending')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();

				}
			?>

			<?= $pengaduan_pending->count() ?>,
			
		<?php } ?>
    ];

    new Chart("pengaduan-pending", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Jumlah",
          fill: false,
          backgroundColor: window.chartColors.yellow,
          borderColor: window.chartColors.yellow,
          data: yValues
        }]
      },
      options: {
        responsive: true,
        legend: {display: false},
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
		
	<?php }else{ ?>

	<?php } ?>
	
	var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>
		
			<?php
				$data_month = str_replace($bulan, $month, $data_bulan);
				
				if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){ 
				
					$pengaduan_Approve = DB::table('tb_pengaduan')
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Approve')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
				
				}else if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai != 'Administrator'){
					
					$pengaduan_Approve = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Approve')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Agent'){
					
					$pengaduan_Approve = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Approve')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai != 'Staff'){

					if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Kepala Unit Kerja'){

						$unit_kerja = DB::table('tb_kepala_unit_kerja')
						->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
						->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
						->limit(1)
						->get();

						if($unit_kerja->count() < 1){

							$pengaduan_Approve = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
										tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
										tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
										tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Approve')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}else{

							$pengaduan_Approve = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai IN (
											SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_pusat IN (
											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_cabang IN (
											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_wilayah IN (
											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										)
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Approve')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}

					}else{

						$pengaduan_Approve = DB::table('tb_pengaduan')
						->whereRaw('
							tb_pengaduan.id_pegawai IN (
								Select
									tb_pegawai.id_pegawai
								From
									tb_pegawai
								Where
									tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
									tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
									tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
									tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
							)
						')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.status_pengaduan','=','Approve')
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
						->get();

					}

				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Staff'){
					
					$pengaduan_Approve = DB::table('tb_pengaduan')
					->where('tb_pengaduan.id_pegawai','=', $data_pegawai->id_pegawai)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Approve')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();

				}
			?>

			<?= $pengaduan_Approve->count() ?>,
			
		<?php } ?>
    ];

    new Chart("pengaduan-approve", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Jumlah",
          fill: false,
          backgroundColor: window.chartColors.blue,
          borderColor: window.chartColors.blue,
          data: yValues
        }]
      },
      options: {
        responsive: true,
        legend: {display: false},
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
	
	var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>
		
			<?php
				$data_month = str_replace($bulan, $month, $data_bulan);
				
				if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){ 
				
					$pengaduan_on_progress = DB::table('tb_pengaduan')
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','On Progress')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
				
				}else if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai != 'Administrator'){
					
					$pengaduan_on_progress = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','On Progress')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Agent'){
					
					$pengaduan_on_progress = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','On Progress')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai != 'Staff'){

					if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Kepala Unit Kerja'){

						$unit_kerja = DB::table('tb_kepala_unit_kerja')
						->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
						->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
						->limit(1)
						->get();

						if($unit_kerja->count() < 1){

							$pengaduan_on_progress = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
										tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
										tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
										tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','On Progress')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}else{

							$pengaduan_on_progress = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai IN (
											SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_pusat IN (
											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_cabang IN (
											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_wilayah IN (
											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										)
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','On Progress')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}

					}else{

						$pengaduan_on_progress = DB::table('tb_pengaduan')
						->whereRaw('
							tb_pengaduan.id_pegawai IN (
								Select
									tb_pegawai.id_pegawai
								From
									tb_pegawai
								Where
									tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
									tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
									tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
									tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
							)
						')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.status_pengaduan','=','On Progress')
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
						->get();

					}

				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Staff'){
					
					$pengaduan_on_progress = DB::table('tb_pengaduan')
					->where('tb_pengaduan.id_pegawai','=', $data_pegawai->id_pegawai)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','On Progress')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();

				}
			?>

			<?= $pengaduan_on_progress->count() ?>,
			
		<?php } ?>
    ];

    new Chart("pengaduan-on-progress", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Jumlah",
          fill: false,
          backgroundColor: window.chartColors.purple,
          borderColor: window.chartColors.purple,
          data: yValues
        }]
      },
      options: {
        responsive: true,
        legend: {display: false},
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
	
	var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>
		
			<?php
				$data_month = str_replace($bulan, $month, $data_bulan);
				
				if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){ 
				
					$pengaduan_holding = DB::table('tb_pengaduan')
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Holding')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
				
				}else if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai != 'Administrator'){
					
					$pengaduan_holding = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Holding')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Agent'){
					
					$pengaduan_holding = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Holding')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai != 'Staff'){

					if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Kepala Unit Kerja'){

						$unit_kerja = DB::table('tb_kepala_unit_kerja')
						->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
						->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
						->limit(1)
						->get();

						if($unit_kerja->count() < 1){

							$pengaduan_holding = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
										tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
										tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
										tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Holding')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}else{

							$pengaduan_holding = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai IN (
											SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_pusat IN (
											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_cabang IN (
											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_wilayah IN (
											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										)
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Holding')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}

					}else{

						$pengaduan_holding = DB::table('tb_pengaduan')
						->whereRaw('
							tb_pengaduan.id_pegawai IN (
								Select
									tb_pegawai.id_pegawai
								From
									tb_pegawai
								Where
									tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
									tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
									tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
									tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
							)
						')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.status_pengaduan','=','Holding')
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
						->get();

					}

				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Staff'){
					
					$pengaduan_holding = DB::table('tb_pengaduan')
					->where('tb_pengaduan.id_pegawai','=', $data_pegawai->id_pegawai)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Holding')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();

				}
			?>

			<?= $pengaduan_holding->count() ?>,
			
		<?php } ?>
    ];

    new Chart("pengaduan-holding", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Jumlah",
          fill: false,
          backgroundColor: window.chartColors.red,
          borderColor: window.chartColors.red,
          data: yValues
        }]
      },
      options: {
        responsive: true,
        legend: {display: false},
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
	
	var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>
		
			<?php
				$data_month = str_replace($bulan, $month, $data_bulan);
				
				if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){ 
				
					$pengaduan_finish = DB::table('tb_pengaduan')
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Finish')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
				
				}else if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai != 'Administrator'){
					
					$pengaduan_finish = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Finish')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Agent'){
					
					$pengaduan_finish = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Finish')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai != 'Staff'){

					if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Kepala Unit Kerja'){

						$unit_kerja = DB::table('tb_kepala_unit_kerja')
						->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
						->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
						->limit(1)
						->get();

						if($unit_kerja->count() < 1){

							$pengaduan_finish = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
										tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
										tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
										tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Finish')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}else{

							$pengaduan_finish = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai IN (
											SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_pusat IN (
											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_cabang IN (
											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_wilayah IN (
											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										)
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Finish')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}

					}else{

						$pengaduan_finish = DB::table('tb_pengaduan')
						->whereRaw('
							tb_pengaduan.id_pegawai IN (
								Select
									tb_pegawai.id_pegawai
								From
									tb_pegawai
								Where
									tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
									tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
									tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
									tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
							)
						')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.status_pengaduan','=','Finish')
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
						->get();

					}

				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Staff'){
					
					$pengaduan_finish = DB::table('tb_pengaduan')
					->where('tb_pengaduan.id_pegawai','=', $data_pegawai->id_pegawai)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Finish')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();

				}
			?>

			<?= $pengaduan_finish->count() ?>,
			
		<?php } ?>
    ];
	
	new Chart("pengaduan-finish", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Jumlah",
          fill: false,
          backgroundColor: window.chartColors.green,
          borderColor: window.chartColors.green,
          data: yValues
        }]
      },
      options: {
        responsive: true,
        legend: {display: false},
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
	
	var yValues = [
        <?php foreach($bulan as $data_bulan){ ?>
		
			<?php
				$data_month = str_replace($bulan, $month, $data_bulan);
				
				if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai == 'Administrator'){ 
				
					$pengaduan_late = DB::table('tb_pengaduan')
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Late')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
				
				}else if($data_pegawai->sebagai_pegawai == 'Petugas' && $data_pegawai->level_pegawai != 'Administrator'){
					
					$pengaduan_late = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Late')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Agent'){
					
					$pengaduan_late = DB::table('tb_pengaduan')
					->where('tb_pengaduan.kantor_pengaduan','=', $data_pegawai->kantor_pegawai)
					->where('tb_pengaduan.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
					->where('tb_pengaduan.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
					->where('tb_pengaduan.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Late')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();
					
				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai != 'Staff'){
					
					if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Kepala Unit Kerja'){

						$unit_kerja = DB::table('tb_kepala_unit_kerja')
						->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai],['tb_kepala_unit_kerja.delete_kepala_unit_kerja','N']])
						->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja','ASC')
						->limit(1)
						->get();

						if($unit_kerja->count() < 1){

							$pengaduan_late = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
										tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
										tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
										tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Late')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}else{

							$pengaduan_late = DB::table('tb_pengaduan')
							->whereRaw('
								tb_pengaduan.id_pegawai IN (
									Select
										tb_pegawai.id_pegawai
									From
										tb_pegawai
									Where
										tb_pegawai.kantor_pegawai IN (
											SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_pusat IN (
											SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_cabang IN (
											SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										) And
										tb_pegawai.id_bagian_kantor_wilayah IN (
											SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE 
											delete_kepala_unit_kerja = "N" And 
											id_pegawai = "'.$data_pegawai->id_pegawai.'"
										)
								)
							')
							->where('tb_pengaduan.delete_pengaduan','=','N')
							->where('tb_pengaduan.status_pengaduan','=','Late')
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
							->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
							->get();

						}

					}else{

						$pengaduan_late = DB::table('tb_pengaduan')
						->whereRaw('
							tb_pengaduan.id_pegawai IN (
								Select
									tb_pegawai.id_pegawai
								From
									tb_pegawai
								Where
									tb_pegawai.kantor_pegawai = "'.$data_pegawai->kantor_pegawai.'" And
									tb_pegawai.id_bagian_kantor_pusat = "'.$data_pegawai->id_bagian_kantor_pusat.'" And
									tb_pegawai.id_bagian_kantor_cabang = "'.$data_pegawai->id_bagian_kantor_cabang.'" And
									tb_pegawai.id_bagian_kantor_wilayah = "'.$data_pegawai->id_bagian_kantor_wilayah.'"
							)
						')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.status_pengaduan','=','Late')
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
						->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
						->get();

					}

				}else if($data_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_pegawai->level_pegawai == 'Staff'){
					
					$pengaduan_late = DB::table('tb_pengaduan')
					->where('tb_pengaduan.id_pegawai','=', $data_pegawai->id_pegawai)
					->where('tb_pengaduan.delete_pengaduan','=','N')
					->where('tb_pengaduan.status_pengaduan','=','Late')
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%Y\') = ? ', [$tahun])
					->whereRaw('date_format(tb_pengaduan.tgl_pengaduan,\'%m\') = ? ', [$data_month])
					->get();

				}
			?>

			<?= $pengaduan_late->count() ?>,
			
		<?php } ?>
    ];
	
	new Chart("pengaduan-late", {
      type: "line",
      data: {
        labels: xValues,
        datasets: [{
          label: "Jumlah",
          fill: false,
          backgroundColor: window.chartColors.red,
          borderColor: window.chartColors.red,
          data: yValues
        }]
      },
      options: {
        responsive: true,
        legend: {display: false},
        tooltips: {
          mode: 'index',
          intersect: false,
        },
        hover: {
          mode: 'nearest',
          intersect: true
        }
      }
    });
</script>