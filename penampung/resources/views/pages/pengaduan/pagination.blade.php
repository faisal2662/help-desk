@php
    if (!function_exists('time_elapsed_string')) {
        function time_elapsed_string($datetime, $full = false)
        {
            $now = new DateTime();
            $ago = new DateTime($datetime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;

            $string = [
                'y' => 'Tahun',
                'm' => 'Bulan',
                'w' => 'Minggu',
                'd' => 'Hari',
                'h' => 'Jam',
                'i' => 'Menit',
                's' => 'Detik',
            ];

            foreach ($string as $k => &$v) {
                if ($diff->$k) {
                    $v = $diff->$k . ' ' . $v;
                } else {
                    unset($string[$k]);
                }
            }

            if (!$full) {
                $string = array_slice($string, 0, 1);
            }

            return $string ? implode(', ', $string) . ' Berlalu' : 'Baru Saja';
        }
    }

@endphp


<?php
	$jawaban = DB::table('tb_jawaban')
	->join('tb_pegawai','tb_pegawai.id_pegawai','=','tb_jawaban.id_pegawai')
	->where([['tb_jawaban.delete_jawaban','N'],['tb_jawaban.id_pengaduan', $_GET['pengaduan']]])
	->orderBy('tb_jawaban.id_jawaban', 'ASC')
	->paginate(5);
	$kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
        $kantorWilayah  = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', 'N')->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')->toArray();

?>

<?php if($jawaban->count() > 0){ ?>

	<div class="row">

		<?php foreach($jawaban as $data_jawaban){ ?>

			<?php
				$kantor_jawaban_pegawai = '-';
				$bagian_jawaban_pegawai = '-';

				if($data_jawaban->kantor_pegawai == 'Kantor Pusat'){

					$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
					->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
					->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_jawaban->id_bagian_kantor_pusat)
					->get();
					if($kantor_pusat->count() > 0){
						foreach($kantor_pusat as $data_kantor_pusat);
						$kantor_jawaban_pegawai = $data_kantor_pusat->nama_kantor_pusat;
						$bagian_jawaban_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
					}

				}else if(array_key_exists($data_jawaban->kantor_pegawai, $kantorCabang)){

					$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
					->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
					->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_jawaban->id_bagian_kantor_cabang)
					->get();
					if($kantor_cabang->count() > 0){
						foreach($kantor_cabang as $data_kantor_cabang);
						$kantor_jawaban_pegawai = $data_kantor_cabang->nama_kantor_cabang;
						$bagian_jawaban_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
					}

				}else if(array_key_exists($data_jawaban->kantor_pegawai, $kantorWilayah)){

					$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
					->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
					->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_jawaban->id_bagian_kantor_wilayah)
					->get();
					if($kantor_wilayah->count() > 0){
						foreach($kantor_wilayah as $data_kantor_wilayah);
						$kantor_jawaban_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
						$bagian_jawaban_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
					}

				}
			?>

			<div class="col-md-12">
				<div class="card">
				  <div class="card-body">

					<?php
						$pengaduan = DB::table('tb_pengaduan')
						->where('tb_pengaduan.delete_pengaduan','=','N')
						->where('tb_pengaduan.id_pengaduan','=', $_GET['pengaduan'])
						->orderBy('tb_pengaduan.id_pengaduan','DESC')
						->get();
					?>
					<?php if($pengaduan->count() > 0){ ?>

						<?php foreach($pengaduan as $data_pengaduan); ?>

						<?php
							// get data pegawai
							$pegawai = DB::table('tb_pegawai')
							->where([['tb_pegawai.id_pegawai', $data_pengaduan->id_pegawai]])
							->get();
							if($pegawai->count() > 0){
								foreach($pegawai as $data_pegawai);

								$kantor_pegawai = '-';
								$bagian_pegawai = '-';

								if($data_pegawai->kantor_pegawai == 'Kantor Pusat'){

									$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
									->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
									->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
									->get();
									if($kantor_pusat->count() > 0){
										foreach($kantor_pusat as $data_kantor_pusat);
										$kantor_pegawai = $data_kantor_pusat->nama_kantor_pusat;
										$bagian_pegawai = $data_kantor_pusat->nama_bagian_kantor_pusat;
									}

								}else if(array_key_exists($data_pegawai->kantor_pegawai, $kantorCabang)){

									$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
									->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
									->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
									->get();
									if($kantor_cabang->count() > 0){
										foreach($kantor_cabang as $data_kantor_cabang);
										$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
										$bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
									}

								}else if(array_key_exists($data_pegawai->kantor_pegawai, $kantorWilayah)){

									$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
									->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
									->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
									->get();
									if($kantor_wilayah->count() > 0){
										foreach($kantor_wilayah as $data_kantor_wilayah);
										$kantor_pegawai = $data_kantor_wilayah->nama_kantor_wilayah;
										$bagian_pegawai = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
									}

								}

							}
							// end get data pegawai
						?>

						<?php if($data_pengaduan->status_pengaduan != 'Finish'  && $data_pegawai->sebagai_pegawai == 'PIC' && $data_pengaduan->id_pegawai == auth()->user()->id_pegawai){ ?>

							<div style="position: absolute;top: 0;right: 0;padding: 10px;">
							  <span class="badge badge-primary" style="cursor: pointer;" onclick="detail('<?= $data_jawaban->id_jawaban ?>');">
								<i class='bx bx-reply-all' ></i> Tanggapi
							  </span>
							</div>

						<?php } ?>

					<?php } ?>

					<div class="card-title">
						<img src="<?= asset('logos/avatar.png') ?>" style="width: 20px;height: 20px;border-radius: 100%;">
						<b>
							<?= htmlspecialchars($data_jawaban->employee_name) ?>
						</b>
					</div>
					<p>Unit Kerja : <?= htmlspecialchars($kantor_jawaban_pegawai) ?></p>
					<p>Unit Bagian Kerja : <?= htmlspecialchars($bagian_jawaban_pegawai) ?></p>
					<hr style="border-style: dashed;">
					<p>
						Jawaban : <br>
						<?= $data_jawaban->keterangan_jawaban ?>
					</p>
					<?php if($data_jawaban->foto_jawaban != 'logos/image.png'){ ?>

						<p>
							<a href="<?= url($data_jawaban->foto_jawaban) ?>" target="_blank" class="text-info">
								<i class='bx bx-image' ></i> Lampiran Gambar
							</a>
						</p>

					<?php } ?>



					<p>
						<i class='bx bx-time' ></i> <?= time_elapsed_string($data_jawaban->tgl_jawaban) ?>
					</p>

				  </div>
				</div>
				<p>&nbsp;</p>

				<?php
					$tanggapan_pengaduan = DB::table('tb_tanggapan')
					->where([['tb_tanggapan.id_jawaban', $data_jawaban->id_jawaban],['tb_tanggapan.delete_tanggapan','N']])
					->get();
				?>

				<?php if($tanggapan_pengaduan->count() > 0){ ?>

					<div class="col-md-12">
						<h4>
							<i class='bx bx-reply-all'></i> Tanggapan
						</h4>
						<p>&nbsp;</p>
					</div>

					<?php foreach($tanggapan_pengaduan as $data_tanggapan_pengaduan){ ?>

						<div class="col-md-12">
							<div class="card bg-primary text-white">
							  <div class="card-body">
								<div class="card-title text-white">
									<img src="<?= asset('logos/avatar.png') ?>" style="width: 20px;height: 20px;border-radius: 100%;">
									<b><?= htmlspecialchars($data_pegawai->employee_name) ?></b>
								</div>
								  <p>Unit Kerja : <?= htmlspecialchars($kantor_pegawai) ?></p>
                                  <p>Unit Bagian Kerja : <?= htmlspecialchars($bagian_pegawai) ?></p>
								<hr style="border-style: dashed;background-color: #fff;">
								<p>
									Tanggapan : <br>
									<?= $data_tanggapan_pengaduan->keterangan_tanggapan ?>
								</p>
								<?php if($data_tanggapan_pengaduan->foto_tanggapan != 'logos/image.png'){ ?>

									<p>
										<a href="<?= url($data_tanggapan_pengaduan->foto_tanggapan) ?>" target="_blank" class="text-white">
											<i class='bx bx-image' ></i> Lampiran Gambar
										</a>
									</p>

								<?php } ?>
								<p>
									<i class='bx bx-time' ></i> <?= time_elapsed_string($data_tanggapan_pengaduan->tgl_tanggapan) ?>
								</p>
							  </div>
							</div>
						</div>
						<p>&nbsp;</p>

					<?php } ?>

				<?php } ?>

			</div>

		<?php } ?>

	</div>

	<div class="row">
	  <div class="col-md-12">
		  <div class="table-responsive">
			<?= $jawaban->links() ?>
		  </div>
	  </div>
	</div>

<?php }else{ ?>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
			  <div class="card-body">
				<center>
				   <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
				   <p>Belum ada jawaban saat ini.</p>
				</center>
			  </div>
			</div>
			<p>&nbsp;</p>
		</div>
	</div>

<?php } ?>
