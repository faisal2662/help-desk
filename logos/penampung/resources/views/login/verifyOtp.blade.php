{{-- <?php
if (Session::has('id_pegawai')) {
    header('Location: ' . route('dashboard'));
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
    <title>Verify - Helpdesk</title>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('/logos/icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('/logos/icon.png') }}">
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('template/boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="<?= url('login_template') ?>/assets/css/login.css">

    {{-- <script type="text/javascript">
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

    setTimeout(function(){ show(false); }, 300);
</script> --}}

    <style type="text/css">
        #loader {
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

        #center {
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

    {{-- <div id="loader">
		<div id="center">
			<center>
				<img src="<?= url('logos/loader.gif') ?>" style="width: 170px;">
				<p class="text-info">Sedang memproses ...</p>
			</center>
		</div>
	</div> --}}

    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 login-section-wrapper">
                    <div class="brand-wrapper">
                        <img src="<?= url('logos/logo.png') ?>" alt="logo" style="width: 150px;">
                    </div>

                    <div class="login-wrapper my-auto">
                        <p class="text-center mb-3">Masukkan Kode yang dikirim melalui email <strong class="text-primary"> {{$pegawai->email}} </strong> </p>
                        <form method="POST" id="login-admin" enctype="multipart/form-data"
                            action="<?= route('authenticate_otp') ?>">
                            <?= csrf_field() ?>
                                <input type="hidden" name="id_pegawai" value="{{encrypt($pegawai->id_pegawai)}}">
                            <label>Kode OTP</label>

                            <input type="text" id="otp_code" name="otp_code" value="<?= old('kode') ?>"
                                class="form-control" required="" autocomplete="off" maxlength="255" placeholder="Harap di isi ...">
                            <br>
                         <p>Kode kadaluarsa ?  <a href="{{route('login')}}">Login</a></p>
                            <button type="submit" class="btn btn-block login-btn" id="btn-login">
                                <i class='bx bx-log-in-circle'></i> Verify
                            </button>

                        </form>

                        <!--         <a href="javascript:;" class="forgot-password-link" data-toggle="modal" data-target="#lupa-password">-->
                        <!--	<i class='bx bx-lock-alt' ></i> Lupa Password?-->
                        <!--</a>-->
                    </div>
                </div>
                <div class="col-sm-6 px-0 d-none d-sm-block">
                    <img src="<?= asset('logos/background.png') ?>" alt="Login - Image" class="login-img">
                </div>
            </div>
        </div>
    </main>
    {{-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>

    <script>
        // function btnLogin(){
        //   console.log('masuk')
        // }
        //   $('#login-admin').on('submit', function(e) {
        //       e.preventDefault();
        //       var formData = $(this).serializeArray();
        //       // var content = tinymce.get('deskripsi').getContent();

        //       // console.log(formData);
        //       // $.ajax({
        //       //     url: "{{ route('authenticate') }}",
        //       //     headers: {'X-CSRF-TOKEN': $('meta[name="csrf_token"]').attr('content')},
        //       //     type: "POST",
        //       //     data: $('#login-admin').serialize(),
        //       //     beforeSend: function() {
        //       //         $('.login-btn').html("Loading...");
        //       //         $('.login-btn').attr("disabled", "");
        //       //     },
        //       //     error: function(res) {
        //       //         console.log(res)
        //       //         $('.pesan').text(res.status);
        //       //         $('#error').show('slow');
        //       //         $('#isError').text('Login failed! Please check your NRP and password.')
        //       //         // setTimeout(
        //       //         //     function() {
        //       //         //         $('#error').hide()
        //       //         //     }, 3000);
        //       //         // alert("Error");
        //       //     },
        //       //     success: function(res) {
        //       //         console.log(res)
        //       //         connectWebSocket(res.branch_code, res.kd_user);
        //       //         window.location.href = "{{ route('dashboard') }}";
        //       //         // Inisialisasi WebSocket
        //       //         // $('#customerModal').modal('hide');
        //       //         // $('.pesan').text("Simpan " + res.status);
        //       //         // $('#alert').addClass('show').fadeIn();
        //       //         // setTimeout(
        //       //         //     function() {
        //       //         //         $('#alert').removeClass('show').fadeOut()
        //       //         //     }, 3000);
        //       //     },
        //       //     complete: function() {
        //       //         $('.login-btn').html("Login");
        //       //         $('.login-btn').removeAttr("disabled");
        //       //         // initialForm();
        //       //     }
        //       // });


        //   });
    </script>

    {{-- <script>
        // function untuk generate timestamp now dengan format 2024-09-04 10:51:00 +0700
        function getFormattedDate() {
            const date = new Date();
            const timezoneOffset = -date.getTimezoneOffset(); // Menit perbedaan dari UTC

            // Fungsi untuk menambahkan padding 0 di depan angka jika kurang dari 10
            const pad = (num) => String(num).padStart(2, '0');

            const year = date.getFullYear();
            const month = pad(date.getMonth() + 1);
            const day = pad(date.getDate());
            const hours = pad(date.getHours());
            const minutes = pad(date.getMinutes());
            const seconds = pad(date.getSeconds());

            // Menghitung offset zona waktu dalam format +0700
            const offsetHours = pad(Math.floor(timezoneOffset / 60));
            const offsetMinutes = pad(timezoneOffset % 60);
            const offsetSign = timezoneOffset >= 0 ? '+' : '-';

            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds} ${offsetSign}${offsetHours}${offsetMinutes}`;
        }
        //  const npp = $('#username').val();
        //  console.log(npp);
        //  const pswd = document.getElementById('password').value;
        $('#login-admin').off('submit').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serializeArray();
            console.log(formData);
            const npp = formData[1].value
            const pswd = formData[2].value
            console.log(formData[1].value);
            console.log(formData[2].value);
            const form= this;
            const url =
                "https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo";

            const pw = Sha1.getHash(npp, pswd);
            console.log(pw)

            const data = {
                USERPWD: pw,
                USERNAME: "jamkrindo",
                ACCNAME: "jamkrindo",
                TIMESTAMP: getFormattedDate(),
            };
            //            this.submit(); // Melanjutkan submit setelah berhasil

            // fetch(url, {
            //         method: "POST",
            //         headers: {
            //             "Content-Type": "application/json",
            //         },
            //         body: JSON.stringify(data),
            //     })
            //     .then((response) => response.json())
            //     .then((json) => console.log(json)
            //     e.submit();
            // )
                // .catch((error) => console.error("Error:", error));
            fetch(url, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify(data),

                })
                .then(function(response) {
                    return response.json(); // Menangani response sebagai JSON
                })
                .then(function(json) {
                    // console.log(json); // Menampilkan hasil JSON di konsol
                    // console.log(json.HSTATUS);
                    if(json.HSTATUS == 200){
                       form.submit(); // Melanjutkan submit setelah berhasil
                    }else{
                        $('#alert_api').addClass('show')
                    }

                })
                .catch(function(error) {
                    console.error("Error:", error); // Menangani error jika ada
                });

            // $.ajax({
            //     url: url, // URL tujuan
            //     type: "POST", // Metode HTTP
            //     contentType: "application/json", // Header konten
            //     data: JSON.stringify(data), // Data yang dikirim
            //     success: function(response) {
            //         console.log(response); // Respons berhasil
            //     },
            //     error: function(xhr, status, error) {
            //         console.error("Error:", error); // Error
            //     }
            // });

        });
        // document
        //     .getElementById("btn-login")
        //     .addEventListener("click", function() {
        //         const url =
        //             "https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo";

        //         const pw = Sha1.getHash(pswd, "jamkrindo");
        //         console.log(pw)
        //         console.log(npp)
        //         console.log(pswd)
        //         const data = {
        //             USERPWD: pw,
        //             USERNAME: npp,
        //             ACCNAME: "jamkrindo",
        //             TIMESTAMP: getFormattedDate(),
        //         };

        //         fetch(url, {
        //                 method: "POST",
        //                 headers: {
        //                     "Content-Type": "application/json",
        //                 },
        //                 body: JSON.stringify(data),
        //             })
        //             .then((response) => response.json())
        //             .then((json) => console.log(json))
        //             .catch((error) => console.error("Error:", error));
        //     });
    </script> --}}

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
                                    <i class='bx bx-lock-alt'></i> Lupa Password?
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
                <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                    action="<?= route('authenticate') ?>">
                    <?= csrf_field() ?>

                    <label>Email</label>
                    <input type="email" name="email" value="<?= old('email') ?>" class="form-control" required=""
                        maxlength="255" placeholder="Harap di isi ...">
                    <br>

                    <label>Sebagai</label>
                    <select name="level" class="form-control" required="">
                        <?php
                        echo '<option value="">- Pilih salah satu -</option>';
                        foreach (['Mitra/Pelanggan', 'Petugas', 'Agent'] as $level) {
                            echo '<option value="' . $level . '">' . $level . '</option>';
                        }
                        ?>
                    </select>
                    <br>

                    <button type="button" class="btn btn-sm btn-warning text-white" data-dismiss="modal">
                        <i class='bx bx-arrow-back'></i> Kembali
                    </button>

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class='bx bx-mail-send'></i> Kirim Email
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
                if (session()->has('alert')) {
                    $explode = explode('_', session()->get('alert'));
                    echo '
                                                				  <div class="alert alert-' .
                        $explode[0] .
                        '"><i class="bx bx-error-circle"></i> ' .
                        $explode[1] .
                        '</div>
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
