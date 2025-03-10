<?php
// 	$pegawai = DB::table('tb_pegawai')
// 	->where([['tb_pegawai.delete_pegawai','N'],['tb_pegawai.status_pegawai','Aktif'],['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
// 	->get();
// 	if($pegawai->count() < 1){
// 		header('Location: '.route('keluar'));
// 		exit();
// 	}else{
// 		foreach($pegawai as $data_pegawai);
// 		if($data_pegawai->level_pegawai != 'Administrator'){
// 			header('Location: '.route('dashboard'));
// 			exit();
// 		}
// 	}
?>

@extends('template')

@section('title')
    User - Helpdesk
@stop

<?php if(isset($_GET['create'])){ ?>

@include('pages.user.tambah')

<?php }else if(isset($_GET['update'])){ ?>

@include('pages.user.ubah')

<?php }else if(isset($_GET['view'])){ ?>

@include('pages.user.lihat')

<?php }else{ ?>

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b>User</b> <span style="float:right;"><button id="btn_sync"
                                class="btn btn-primary btn-sm"><i class='bx bx-sync'></i> Sync</button> <button
                                style="display: none" id="btn_sync_load" class="btn btn-sm btn-primary" type="button"
                                disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button></span></div>
                    <p>
                        {{-- <?php echo htmlspecialchars_decode($input); ?> --}}
                    </p>
                    <p class="float-right"><strong>
                        Last sync : </strong> <span class="text-sync"></span></p>
                    <span style="display:none;" id="alert">
                        <div class="alert text-center type-alert " role="alert">
                            <span id="message"></span>
                        </div>
                    </span>
                    <hr style="border-style: dashed;">
                    <?php
                    $pelanggan = DB::table('tb_pegawai')
                        ->where([['tb_pegawai.delete_pegawai', 'N']])
                        ->orderBy('tb_pegawai.id_pegawai', 'DESC')
                        ->get();
                    ?>

                    <?php if($pelanggan->count() < 1){ ?>

                    <center>
                        <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                        <p>Data saat ini tidak ditemukan.</p>
                    </center>

                    <?php }else{ ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <td><b>No</b></td>
                                    <td><b>NPP</b></td>
                                    <td><b>Nama Lengkap</b></td>
                                    <td><b>No.Telp</b></td>
                                    <td><b>Email</b></td>
                                    <td><b>Unit Kerja</b></td>
                                    <td><b>Bagian Unit Kerja</b></td>
                                    <td><b>Level</b></td>
                                    <td><b>Sebagai</b></td>
                                    <td><b>Status</b></td>
                                    <td><b>Tanggal</b></td>
                                    <td><b>Action</b></td>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <span style="display: none;">
                        <form method="GET" onsubmit="show(true)" id="form-update">
                            <input type="text" name="update" id="input-update" readonly="" required="">
                        </form>

                        <form method="POST" onsubmit="show(true)" id="form-delete"
                            action="<?= route('pelanggan.delete') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="delete" id="input-delete" readonly="" required="">
                        </form>
                    </span>

                    <script type="text/javascript">
                        function delete_data(id, name) {
                            var r = confirm('Hapus data ' + name + '?');
                            if (r == true) {
                                show(true);
                                document.getElementById('input-delete').value = id;
                                document.getElementById('form-delete').submit();
                            }
                        }

                        function update_data(id) {
                            show(true);
                            document.getElementById('input-update').value = id;
                            document.getElementById('form-update').submit();
                        }
                    </script>

                    <?php } ?>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

@stop

@section('script')

    <script type="text/javascript">
        $('#dataTables').DataTable({
            "processing": true,
            "serverSide": true,
			"stateSave": true,
            "ajax": {
                "url": "<?= route('pelanggan.datatables') ?>",
                "dataType": "json",
                "type": "POST",
                "data": {
                    _token: "<?= csrf_token() ?>"
                }
            },
            columns: [{
                    data: 'no'
                },
                {
                    data: 'npp_pegawai'
                },
                {
                    data: 'nama_pegawai'
                },
                {
                    data: 'telp_pegawai'
                },
                {
                    data: 'email_pegawai'
                },
                {
                    data: 'kantor'
                },
                {
                    data: 'bagian'
                },
                {
                    data: 'level_pegawai'
                },
                {
                    data: 'sebagai_pegawai'
                }, 
                {
                    data: 'status_pegawai'
                },
                {
                    data: 'tgl_pegawai'
                },
                {
                    data: 'action'
                },
            ]
        });
    </script>
    <script> 
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
        $('#btn_sync').on('click', function() {
            $('#btn_sync').hide('slow');
            $('#btn_sync_load').show('slow');
            
            fetchNextPage();
            
        });
        var allData = [];
        var dataFix = [];
        var currentPage = 1;
        var perPage = 10;
        var totalPages = 1;
        var jwtToken = null; // Variabel untuk menyimpan token JWT
        var i = 1;

        function fetchNextPage() {
            fetchData(currentPage).done(function(response) {
                if (!response || !response.DATA) {
                    $('#btn_sync_load').hide('slow');
                    $('#btn_sync').show('slow');
                    $('#alert').show();
                    $('.type-alert').addClass('alert-danger')
                    $('#message').text('Sync Gagal');
                    setTimeout(() => {
                        $('.type-alert').removeClass('alert-danger')
                        $('#message').text('');
                        $('#alert').hide('slow');
                    }, 5000);
                    console.error('Error fetching data');
                    return false; // Jika ada error, return false
                }

                // Gabungkan data dari halaman saat ini ke allData
                allData = allData.concat(response.DATA.DATA);

                // Hitung total halaman
                totalPages = Math.ceil(response.DATA.TOTAL / perPage);
                totalPages = totalPages - 4;
              
                // return false;
                currentPage++;

                // Jika masih ada halaman, ambil halaman berikutnya
                if (currentPage <= totalPages) {
                   console.log('tes :' + currentPage)
                    fetchNextPage();
                } else {
                   // console.log('get berhasil');
                    // $.post("{{ route('pelanggan.sync') }}", {
                    //     data: allData,
                    //     _token: "{{ csrf_token() }}"
                    // }, function(res) {
                    //     console.log(res)
                    //     if (res.status == 'success') {
                    //         $('#btn_sync_load').hide('slow');
                    //         $('#btn_sync').show('slow');
                    //         $('#alert').show();
                    //         $('.type-alert').addClass('alert-primary')
                    //         $('#message').text('Sync Selesai');
                    //         setTimeout(() => {
                    //             $('.type-alert').removeClass('alert-primary')
                    //             $('#message').text('');
                    //             $('#alert').hide('slow');
                    //         }, 5000);

                    //     } else {
                    //         $('#btn_sync_load').hide('slow');
                    //         $('#btn_sync').show('slow');
                    //         $('#alert').show();
                    //         $('.type-alert').addClass('alert-danger')
                    //         $('#message').text('Sync Gagal');
                    //         setTimeout(() => {
                    //             $('.type-alert').removeClass('alert-danger')
                    //             $('#message').text('');
                    //             $('#alert').hide('slow');
                    //         }, 5000);
                    //     }
                    // })
                    // Semua data telah diambil, lakukan sesuatu dengan allData
					// saveDataToFile(allData)
                    sendDataInBatches(allData.length, allData);
                   // console.log(allData.length);
                    currentPage = 1;
		allData  = []; 
                }
            }).fail(function() {
                console.error('Error fetching data from API');
                $('#btn_sync_load').hide('slow');
                $('#btn_sync').show('slow');
                $('#alert').show();
                $('.type-alert').addClass('alert-danger')
                $('#message').text('Sync Gagal');
                setTimeout(() => {
                    $('.type-alert').removeClass('alert-danger')
                    $('#message').text('');
                    $('#alert').hide('slow');
                }, 5000); 
            });
        }

        // Fungsi untuk mengambil data dari API
        function fetchData(page) {
            // Jika token belum ada, lakukan login
            if (!jwtToken) {
                return login().then(function(token) {
                    jwtToken = token; // Simpan token setelah login
                    return makeApiRequest(page); // Lakukan permintaan API setelah login
                });
            } else {
                return makeApiRequest(page); // Jika token sudah ada, langsung lakukan permintaan API
            }
        }
        function lastSync()
            {
                $.ajax({
                    url: "{{route('pelanggan.last_sync')}}",
                    method : "Get",
                    contentType: 'application/json',
                    dataType: 'json'

                }).then(function(resp){
                    // console.log(resp)
                    $('.text-sync').text(resp.data);

                }).fail(function(jqXHR,textStatus, errorThrown){
                    console.log('Error During login : ', textStatus, errorThrown);
                })
            }
            lastSync();

           
        // Fungsi untuk melakukan permintaan API
        function makeApiRequest(page) {
            return $.ajax({
                url: 'https://hris-pro.jamkrindo.co.id/sf7/?qlid=HrisUser.getEmployee', // Ganti dengan URL API yang sesuai
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'Authorization': 'Bearer Token ' + jwtToken,

                },
                data: JSON.stringify({
                    page_number: page
                }),
                dataType: 'json'
            }).then(function(response) {
                // console.log(response);
                // Mengembalikan data yang diterima dari API
                return response;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error fetching data:', textStatus, errorThrown);
                $('#btn_sync_load').hide('slow');
                $('#btn_sync').show('slow');
                $('#alert').show();
                $('.type-alert').addClass('alert-danger')
                $('#message').text('Sync Gagal');
                setTimeout(() => {
                    $('.type-alert').removeClass('alert-danger')
                    $('#message').text('');
                    $('#alert').hide('slow');
                }, 5000);
                return null; // Mengembalikan null jika terjadi kesalahan
            });
        }




        // Fungsi login untuk mendapatkan token JWT
        function login() {
            const data = {
                USERPWD: 'B0CF009C46FFBE549B34F241690B8AA8DCAE25A3',
                USERNAME: 'jamkrindo',
                ACCNAME: "jamkrindo",
                TIMESTAMP: getFormattedDate(),
            }
            const url =
                "https://hris-pro.jamkrindo.co.id/sf7/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo";

            // const url =
            //     "https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo";
            return $.ajax({
                url: url, // Ganti dengan URL login yang sesuai
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                dataType: 'json'
            }).then(function(response) {
               // console.log(response);
                // Mengembalikan token JWT dari respons
                return response.DATA.JWT_TOKEN;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error during login:', textStatus, errorThrown);
                return null; // Mengembalikan null jika terjadi kesalahan
            });
        }
        var allData = []; // Array yang berisi 1364 data
        var batchSize = 25; // Ukuran batch
        // var totalData = 1364; // Total data
        var currentBatchIndex = 0; // Indeks batch saat ini
		var j =1;

        // Fungsi untuk mengirim data ke controller dalam batch
        function sendDataInBatches(totalData, dataFix) {
            // Fungsi untuk mengirim batch ke controller
            function sendBatchToController(batch) {
                // return $.ajax({
                //     url: 'URL_CONTROLLER', // Ganti dengan URL controller yang sesuai
                //     method: 'POST',
                //     contentType: 'application/json',
                //     data: JSON.stringify(batch), // Kirim batch data dalam format JSON
                //     dataType: 'json'
                // });
                return $.ajax({
                    url: "{{ route('pelanggan.sync') }}",
                    type: "POST",
                    data: {
                        data: batch,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {

		//	console.log('mengirim :' + j++)
                        // Tambahkan loader atau animasi sebelum pengiriman jika diperlukan
                    },
                    success: function(res) {
                        // console.log(res);
                      

                        if (res.status == 'success') {
                            // $('#alert').show();
                            // $('.type-alert').addClass('alert-primary');
                            // $('#message').text('Sync Selesai');
                            // setTimeout(() => {
                            //     $('.type-alert').removeClass('alert-primary');
                            //     $('#message').text('');
                            //     $('#alert').hide('slow');
                            // }, 5000);
                        } else {
                            $('#alert').show();
                            $('.type-alert').addClass('alert-danger');
                            $('#message').text('Sync Gagal');
                            setTimeout(() => {
                                $('.type-alert').removeClass('alert-danger');
                                $('#message').text('');
                                $('#alert').hide('slow');
                            }, 5000);
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        $('#btn_sync_load').hide('slow');
                        $('#btn_sync').show('slow');
                        $('#alert').show();
                        $('.type-alert').addClass('alert-danger');
                        $('#message').text('Sync Error');
                        setTimeout(() => {
                            $('.type-alert').removeClass('alert-danger');
                            $('#message').text('');
                            $('#alert').hide('slow');
                        }, 5000);
                    }
                });

            }

            // Fungsi untuk mengirim semua data dalam batch
            function processBatches() {
                if (currentBatchIndex < totalData) {
                    // Ambil batch data
                    var batch = dataFix.slice(currentBatchIndex, currentBatchIndex + batchSize);

                    // Kirim batch ke controller
                    sendBatchToController(batch).done(function(response) {
                       // console.log("Batch berhasil dikirim:", response);
                        // Update indeks batch
                        currentBatchIndex += batchSize;
                        // Proses batch berikutnya
                        processBatches();
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        console.error("Error sending batch:", textStatus, errorThrown);
                    });
                } else {
                    totalData = 0;
                    console.log("Semua data telah berhasil dikirim.");
					$('#btn_sync_load').hide('slow');
                        $('#btn_sync').show('slow');
					 $('#alert').show();
                            $('.type-alert').addClass('alert-primary');
                            $('#message').text('Sync Selesai');
                            setTimeout(() => {
                                $('.type-alert').removeClass('alert-primary');
                                $('#message').text('');
                                $('#alert').hide('slow');
                            }, 5000);
                            lastSync();
                }
            }

            // Mulai proses pengiriman batch
            processBatches();
        }


    </script>

@stop

 
<?php } ?>
 
