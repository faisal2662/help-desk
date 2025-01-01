<?php
	$pengaduan = DB::table('tb_pengaduan')
	->where([['tb_pengaduan.id_pengaduan', $id_pengaduan],['tb_pengaduan.delete_pengaduan','N']])
	->get();
?>

<?php if($pengaduan->count() > 0){ ?>

	<?php foreach($pengaduan as $data_pengaduan); ?>

	<?php
		$lampiran = DB::table('tb_lampiran')
		->where([['tb_lampiran.delete_lampiran','N'],['tb_lampiran.id_pengaduan', $data_pengaduan->id_pengaduan]])
		->orderBy('tb_lampiran.id_lampiran','ASC')
		->get();

		// kantor bagian pengaduan
		$kantor_pengaduan = '-';
		$bagian_pengaduan = '-';

		if($data_pengaduan->kantor_pengaduan == 'Kantor Pusat'){

			$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
			->join('tb_kantor_pusat','tb_bagian_kantor_pusat.id_kantor_pusat','=','tb_kantor_pusat.id_kantor_pusat')
			->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pengaduan->id_bagian_kantor_pusat)
			->get();
			if($kantor_pusat->count() > 0){
				foreach($kantor_pusat as $data_kantor_pusat);
				$kantor_pengaduan = $data_kantor_pusat->nama_kantor_pusat;
				$bagian_pengaduan = $data_kantor_pusat->nama_bagian_kantor_pusat;
			}

		}else if($data_pengaduan->kantor_pengaduan == 'Kantor Cabang'){

			$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
			->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
			->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pengaduan->id_bagian_kantor_cabang)
			->get();
			if($kantor_cabang->count() > 0){
				foreach($kantor_cabang as $data_kantor_cabang);
				$kantor_pengaduan = $data_kantor_cabang->nama_kantor_cabang;
				$bagian_pengaduan = $data_kantor_cabang->nama_bagian_kantor_cabang;
			}

		}else if($data_pengaduan->kantor_pengaduan == 'Kantor Wilayah'){

			$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
			->join('tb_kantor_wilayah','tb_bagian_kantor_wilayah.id_kantor_wilayah','=','tb_kantor_wilayah.id_kantor_wilayah')
			->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pengaduan->id_bagian_kantor_wilayah)
			->get();
			if($kantor_wilayah->count() > 0){
				foreach($kantor_wilayah as $data_kantor_wilayah);
				$kantor_pengaduan = $data_kantor_wilayah->nama_kantor_wilayah;
				$bagian_pengaduan = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
			}

		}
		// end kantor bagian pengaduan
	?>

	<?php
		$pegawai = DB::table('tb_pegawai')
		->where('tb_pegawai.id_pegawai','=', $data_pengaduan->id_pegawai)
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

			}else if($data_pegawai->kantor_pegawai == 'Kantor Cabang'){

				$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
				->join('tb_kantor_cabang','tb_bagian_kantor_cabang.id_kantor_cabang','=','tb_kantor_cabang.id_kantor_cabang')
				->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
				->get();
				if($kantor_cabang->count() > 0){
					foreach($kantor_cabang as $data_kantor_cabang);
					$kantor_pegawai = $data_kantor_cabang->nama_kantor_cabang;
					$bagian_pegawai = $data_kantor_cabang->nama_bagian_kantor_cabang;
				}

			}else if($data_pegawai->kantor_pegawai == 'Kantor Wilayah'){

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
	?>

	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">

	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Helpdesk - Jamkrindo</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>

	<body style="margin: 0; padding: 0;background-color: #ecf0f1;">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="padding: 10px 0 30px 0;">
					<table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color: #ffffff;border-radius: 10px;">
						<tr>
							<td align="center" bgcolor="#ffffff" style="padding: 40px 0 30px 0; color: #ffffff; font-size: 28px; font-weight: bold; font-family: Arial, sans-serif;">
							   <img src="cid:logo_cid" style="width: 250px;">
							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
								<h2>
									Pengaduan Terlambat
								</h2>
								<hr style="border-style: dashed;">

								<!-- detail pengaduan -->
								<h4>
									<img src="cid:gambar_cid" style="width: 20px;height: 20px;border-radius: 100%;"> <?= $data_pegawai->employee_name ?>
								</h4>
								<p>
								    <strong>Unit Kerja : </strong> <?= $kantor_pegawai ?> <br>
								 <strong>Unit Bagian Kerja : </strong> <?= $bagian_pegawai ?>
								</p>
								<hr style="border-style: dashed;">

								<p>
								    <strong>Kepada : </strong> <?= $kantor_pengaduan.' - '.$bagian_pengaduan ?>
								</p>
                                <p>
                                    @if ($data_pengaduan->kategori_pengaduan)
                                        <strong>Kategori :</strong> {{ $data_pengaduan->kategori_pengaduan }}
                                    @else
                                        <strong>Kategori :</strong> -
                                    @endif
                                </p>
                                <p>
                                    @if ($data_pengaduan->jenis_produk)
                                        <strong>Jenis Produk :</strong>
                                        {{ $data_pengaduan->jenis_produk . ' - ' . $data_pengaduan->sub_jenis_produk }}
                                    @else
                                    @endif
                                </p>
                                <p>
                                    <b><?= $data_pengaduan->nama_pengaduan ?></b>
                                </p>
                                <p>
                                    <strong> Keterangan : </strong><br>
                                    <?= $data_pengaduan->keterangan_pengaduan ?>
                                </p>
                                @if ($data_pengaduan->klasifikasi_pengaduan)
                                    <p>
                                        <strong>Klasifikasi :</strong> <b
                                            class="badge bg-warning"><?= $data_pengaduan->klasifikasi_pengaduan ?></b>
                                    </p>
                                @else
                                    <p>
                                        <strong> Klasifikasi :</strong> -</b>
                                    </p>
                                @endif
								<?php if($lampiran->count() > 0){ ?>

									<p>
										<ol>

											<?php foreach($lampiran as $data_lampiran){ ?>

												<li>
													<a href="<?= url($data_lampiran->file_lampiran) ?>" target="_blank">
														Lihat Lampiran
													</a>
												</li>

											<?php } ?>

										</ol>
									</p>

								<?php } ?>
								<p>
									Status :
									<span style="padding:5px 10px;border-radius: 10px;background-color: #e74c3c;color: #fff;">
										<?= $data_pengaduan->status_pengaduan ?>
									</span>
								</p>
								<p>
									Tanggal : <?= date('j F Y, H:i', strtotime($data_pengaduan->tgl_pengaduan)) ?>
								</p>
								<!-- end detail pengaduan -->

							</td>
						</tr>
						<tr>
							<td bgcolor="#188fff" style="padding: 30px 30px 30px 30px;">
								<table border="0" cellpadding="0" cellspacing="0" width="100%">
									<tr>
										<td style="color: #ffffff; font-family: Arial, sans-serif; font-size: 14px;" width="75%">
											&copy; Copyright 2021 Helpdesk - Jamkrindo
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</body>

	</html>

<?php } ?>
