{{-- <?php
	if(Session::has('id_pegawai')){
		header('Location: '.route('dashboard'));
		exit();
	}
?> --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
  <meta name="description" content="Helpdesk - Jamkrindo.">
  <title>Login - Helpdesk</title>
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/logos/icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('/logos/icon.png') }}">
  <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('template/boxicons/css/boxicons.min.css') }}">
  <link rel="stylesheet" href="<?= url('login_template') ?>/assets/css/login.css">
  
<script type="text/javascript">
    function show(value) {
      document.getElementById('loader').style.display = value ? 'block' : 'none';
    }

    function loadPage (URL){
        show(true);
        location = URL;
    }

    function newTab (URL){
        window.open(URL, '_blank');
    }

    setTimeout(function(){ show(false); }, 2000);
</script>

 <style type="text/css">
    #loader{
      width: 100%;
      height: 100%;
      position: fixed;
      background-color: #fff;
      top: 0;
      bottom: : 0;
      left: 0;
      right: : 0;
      z-index: 99999;
      opacity: 90%;
    }

    #center{
        width: 100%;
        position: relative;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }
</style>

<style type="text/css">
    /* width */
    ::-webkit-scrollbar {
      width: 5px;
      height: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
      background: transparent;
    }
     
    /* Handle */
    ::-webkit-scrollbar-thumb {
      background: #341f97; 
      border-radius: 100px;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
      background: #341f97; 
    }
</style>
  
</head>
<body style="position: fixed;width: 100%;top: 0;bottom: 0;left: 0;right: 0;overflow: hidden;">

	<div id="loader">
		<div id="center">
			<center>
				<img src="<?= url('logos/loader.gif') ?>" style="width: 170px;">
				<p class="text-info">Sedang memproses ...</p>
			</center>
		</div>
	</div>

  <main>
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-6 login-section-wrapper">
          <div class="brand-wrapper">
            <img src="<?= url('logos/logo.png') ?>" alt="logo" style="width: 150px;">
          </div>
          <div class="login-wrapper my-auto">			
			<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('authenticate') ?>">
			  <?= csrf_field() ?>
			  
				<label>NPP</label>
				<input type="number" name="npp" value="<?= old('npp') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
				<br>

				<label>Password</label>
				<input type="password" name="password" value="<?= old('password') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
				<br>

				{{-- <label>Sebagai</label>
				<select name="level" class="form-control" required="">
				  <?php  
					echo '<option value="">- Pilih salah satu -</option>';
					foreach(array('Mitra/Pelanggan', 'Petugas', 'Agent') as $level){
					  echo '<option value="'.$level.'">'.$level.'</option>';
					}
				  ?>
				</select>
				<br> --}}

				<button type="submit" class="btn btn-block login-btn">
				   <i class='bx bx-log-in-circle'></i> Login
				</button>

			</form>
			
   <!--         <a href="javascript:;" class="forgot-password-link" data-toggle="modal" data-target="#lupa-password">-->
			<!--	<i class='bx bx-lock-alt' ></i> Lupa Password?-->
			<!--</a>-->
          </div>
        </div>
        <div class="col-sm-6 px-0 d-none d-sm-block">
          <img src="<?= url('logos/background.jpg') ?>" alt="Login - Image" class="login-img">
        </div>
      </div>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>

<!-- Classic Modal -->
<div class="modal fade" id="lupa-password" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
          <table style="width: 100%;">
              <tbody>
                  <tr>
                      <td>
                          <b>
                              <i class='bx bx-lock-alt' ></i> Lupa Password?
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
      <div class="modal-body">
		<form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('authenticate') ?>">
		  <?= csrf_field() ?>
		  
			<label>Email</label>
			<input type="email" name="email" value="<?= old('email') ?>" class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
			<br>
			
			<label>Sebagai</label>
			<select name="level" class="form-control" required="">
			  <?php  
				echo '<option value="">- Pilih salah satu -</option>';
				foreach(array('Mitra/Pelanggan', 'Petugas', 'Agent') as $level){
				  echo '<option value="'.$level.'">'.$level.'</option>';
				}
			  ?>
			</select>
			<br>
			
			<button type="button" class="btn btn-sm btn-warning text-white" data-dismiss="modal">
			  <i class='bx bx-arrow-back'></i> Kembali
			</button>

			<button type="submit" class="btn btn-sm btn-primary">
			  <i class='bx bx-mail-send' ></i> Kirim Email
			</button>

		</form>
      </div>
    </div>
  </div>
</div>
<!--  End Modal -->

<?php if(session()->has('alert')){ ?>

	<!-- Classic Modal -->
	<div class="modal fade" id="modal-alert" tabindex="-1" role="dialog">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		  <div class="modal-header">
			  <table style="width: 100%;">
				  <tbody>
					  <tr>
						  <td>
							  <b>
								  Perhatian
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
		  <div class="modal-body">
			<?php  
			  if(session()->has('alert')){
				$explode = explode('_', session()->get('alert'));
				echo '
				  <div class="alert alert-'.$explode[0].'"><i class="bx bx-error-circle"></i> '.$explode[1].'</div>
				';
			  }
			?>
		  </div>
		</div>
	  </div>
	</div>
	<!--  End Modal -->

	<script>
	   $('#modal-alert').modal('show');
	</script>

<?php } ?>