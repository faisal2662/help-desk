<?php
	$pegawai = DB::table('tb_pegawai')
	->where([['tb_pegawai.id_pegawai', $id],['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif']])
	->get();
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
                           <img src="<?= url('logos/logo.png') ?>" style="width: 250px;">
                        </td>
                    </tr>
					
					<?php if($pegawai->count() > 0){ ?>

						<?php foreach($pegawai as $data_pegawai); ?>
						
						<?php
							$kantor = '-';
							$bagian = '-';
							
							if($data_pegawai->kantor_pegawai == 'Kantor Pusat'){
								
								$kantor_pusat = DB::table('tb_bagian_kantor_pusat')
								->join('tb_kantor_pusat','tb_kantor_pusat.id_kantor_pusat','=','tb_bagian_kantor_pusat.id_kantor_pusat')
								->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat','=', $data_pegawai->id_bagian_kantor_pusat)
								->get();
								if($kantor_pusat->count() > 0){
									foreach($kantor_pusat as $data_kantor_pusat){
										$kantor = $data_kantor_pusat->nama_kantor_pusat;
										$bagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
									}
								}
								
							}else if($data_pegawai->kantor_pegawai == 'Kantor Cabang'){
								
								$kantor_cabang = DB::table('tb_bagian_kantor_cabang')
								->join('tb_kantor_cabang','tb_kantor_cabang.id_kantor_cabang','=','tb_bagian_kantor_cabang.id_kantor_cabang')
								->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang','=', $data_pegawai->id_bagian_kantor_cabang)
								->get();
								if($kantor_cabang->count() > 0){
									foreach($kantor_cabang as $data_kantor_cabang){
										$kantor = $data_kantor_cabang->nama_kantor_cabang;
										$bagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
									}
								}
								
							}else if($data_pegawai->kantor_pegawai == 'Kantor Wilayah'){
								
								$kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
								->join('tb_kantor_wilayah','tb_kantor_wilayah.id_kantor_wilayah','=','tb_bagian_kantor_wilayah.id_kantor_wilayah')
								->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah','=', $data_pegawai->id_bagian_kantor_wilayah)
								->get();
								if($kantor_wilayah->count() > 0){
									foreach($kantor_wilayah as $data_kantor_wilayah){
										$kantor = $data_kantor_wilayah->nama_kantor_wilayah;
										$bagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
									}
								}
								
							}
						?>
					
						<tr>
							<td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
								<h2>
									Konfirmasi Akun Helpdesk
								</h2>
								<table>
									<tr>
										<td style="padding: 5px 10px;">
											NPP
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->npp_pegawai) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											Nama Lengkap
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->nama_pegawai) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											No.Telp
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->telp_pegawai) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											Email
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->email_pegawai) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											Unit Kerja
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->kantor_pegawai) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											Bagian Unit Kerja
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($kantor.' - '.$bagian) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											Sebagai
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->level_pegawai) ?>
										</td>
									</tr>
									<tr>
										<td style="padding: 5px 10px;">
											Role
										</td>
										<td style="padding: 5px 10px;">
											: <?= htmlspecialchars($data_pegawai->sebagai_pegawai) ?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td bgcolor="#ffffff" style="padding: 40px 30px 40px 30px;">
								Jika data diatas benar adalah akun helpdesk jamkrindo & ingin reset password silahkan tekan tombol dibawah ini : <br>
								<center>
									<p>
										<a href="<?= route('lupa_password') ?>?role=<?= md5($data_pegawai->sebagai_pegawai) ?>&email=<?= md5($data_pegawai->email_pegawai) ?>" target="_blank" style="padding: 10px 15px;color: #ffffff;border-radius: 10px;background-color: #2980b9;text-decoration: none;">
											RESET PASSWORD
										</a>
									</p>
								</center>
							</td>
						</tr>

					<?php } ?>
					
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