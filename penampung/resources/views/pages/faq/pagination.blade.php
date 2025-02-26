<?php
	$pegawai = DB::table('tb_pegawai')
	->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
	->get();
	if($pegawai->count() < 1){
		header('Location: '.route('faq'));
		exit();
	}else{
		foreach($pegawai as $data_pegawai);
	}
?>

<?php
	function time_elapsed_string($datetime, $full = false) {
	    $now = new DateTime;
	    $ago = new DateTime($datetime);
	    $diff = $now->diff($ago);

	    $diff->w = floor($diff->d / 7);
	    $diff->d -= $diff->w * 7;

	    $string = array(
	        'y' => 'Tahun',
	        'm' => 'Bulan',
	        'w' => 'Minggu',
	        'd' => 'Hari',
	        'h' => 'Jam',
	        'i' => 'Menit',
	        's' => 'Detik',
	    );
	    foreach ($string as $k => &$v) {
	        if ($diff->$k) {
	            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
	        } else {
	            unset($string[$k]);
	        }
	    }

	    if (!$full) $string = array_slice($string, 0, 1);
	    return $string ? implode(', ', $string) . ' Berlalu' : 'Baru Saja';
	}
?>

<?php
	$kategori_FAQ = DB::table('tb_kategori_faq')
	->where('tb_kategori_faq.is_delete','=','N')
	->get();
?>

<?php if($kategori_FAQ->count() < 1){ ?>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
			  <div class="card-body">
				<center>
				   <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
				   <p>Belum ada FAQ saat ini.</p>
				</center>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>

<?php }else{ ?>

	<div class="row">

	<?php $no = 1; foreach($kategori_FAQ as $data_FAQ){ ?>

		<div class="col-md-6">
			<div class="card">
			  <div class="card-body">
                <div class="text-primary" style="text-align: end;float: right; ">
                    <h3><a href=" {{ route('faq.Quest', $data_FAQ->id_kategori_faq) }} "> <i
                                class='bx bx-list-ul'></i></a></h3>
                </div>
			    <h5 class="text-primary" id="ellipsis" style="cursor: pointer;" data-toggle="collapse" data-target="#faq_<?= $data_FAQ->id_kategori_faq ?>">
			    	<b> <?= $data_FAQ->nama_kategori_faq ?></b>
			    </h5>

			    <div class="row collapse" id="faq_<?= $data_FAQ->id_kategori_faq ?>">
			    	<div class="col-md-12">
			    		<hr style="border-style: dashed;">

					    <p class="text-muted">
					    	<i class='bx bx-time-five' ></i> <?= time_elapsed_string(date('Y-m-d H:i:s', strtotime($data_FAQ->created_date))); ?>
					    </p>

						<?php if( $data_pegawai->sebagai_pegawai == 'Administrator'){ ?>

						    <hr style="border-style: dashed;">
						    <p>
						    	<a href="?update=<?= $data_FAQ->id_kategori_faq ?>">
									<span class="badge badge-primary">
									  <i class='bx bx-edit' ></i> Perbarui
									</span>
						    	</a>

						    	<a href="javascript:;" onclick="delete_data(<?= $data_FAQ->id_kategori_faq ?>, '<?= $data_FAQ->nama_kategori_faq ?>');">
									<span class="badge badge-danger">
									  <i class='bx bx-trash' ></i> Hapus
									</span>
						    	</a>
						    </p>

						<?php } ?>
			    	</div>
			    </div>

			  </div>
			</div>
			<p>&nbsp;</p>
		</div>

	<?php $no ++; } ?>

	</div>


<?php } ?>
