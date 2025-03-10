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
                    <div class="card-title"><b>User</b> <span style="float:right;"> <a href="{{ route('pelanggan.jabatan') }}"
                                class="btn btn-sm btn-success">Jabatan </a> <button id="btn_sync"
                                class="btn btn-primary btn-sm"><i class='bx bx-sync'></i> Sync</button> <button
                                style="display: none" id="btn_sync_load" class="btn btn-sm btn-primary" type="button"
                                disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button></span> </div>
                    <p>
                        {{-- <?php echo htmlspecialchars_decode($input); ?> --}}
                    </p>

                    <span style="display:none;" id="alert">
                        <div class="alert text-center type-alert " role="alert">
                            <span id="message"></span>
                        </div>
                    </span>
                    <hr style="border-style: dashed;">
                    <p class="float-right"><strong>
                            Last sync : </strong> <span class="text-sync"></span></p>
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
                        <div id="dataTables"></div>
                        {{-- <table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <td><b>No</b></td>
                                    <td><b>NPP</b></td>
                                    <td><b>Nama Lengkap</b></td>
                                    <td><b>No.Telp</b></td>
                                    <td><b>Email</b></td>
                                    <td><b>Unit Kerja</b></td>
                                    <td><b>Bagian Unit Kerja</b></td>
                                    <td><b>Jabatan</b></td>
                                    <td><b>Sebagai</b></td>
                                    <td><b>Status</b></td>
                                    <td><b>Tanggal</b></td>
                                    <td><b>Action</b></td>
                                </tr>
                            </thead>
                        </table> --}}
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

    <script>
        new gridjs.Grid({
            columns: [{name: "npp",width: "100px"},{name: "Nama", width: '350px'},{name: 'No. Telp' , width: "200px"}, "Email", {name:"Unit Kerja", width: '300px'}, { name: "Bagian Unit Kerja", width:'350px'},
                 { name:"Level", width: "250px;"}, "Sebagai", "Status", {name:"Tanggal", width : "300px;" },{name: "Action", width:'200px'}
            ],
            server: {
                url: "<?= route('pelanggan.datatables') ?>",
                then: data => data.map(user => [
                    user.npp_pegawai,
                    gridjs.html(user.nama_pegawai),
                    user.telp_pegawai,
                    user.email_pegawai,
                    user.kantor,
                    user.bagian,

                    user.level_pegawai,
                    user.sebagai_pegawai,
                    user.status_pegawai,
                    user.tgl_pegawai,
                    gridjs.html(user.action)

                ])
            },
            pagination: {
                limit: 20
            }, // Load 50 data per halaman
            search: true,
            sort: true
        }).render(document.getElementById("dataTables"));
    </script>
    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            var table = new Tabulator("#dataTables", {
                layout: "fitColumns", // Agar kolom otomatis menyesuaikan lebar
                ajaxURL: "<?= route('pelanggan.datatables') ?>", // Ganti dengan API yang sesuai
                pagination: "remote", // Mengaktifkan pagination (bisa diganti "remote" untuk server-side)
                paginationSize: 20, // Jumlah data per halaman
                movableColumns: true, // Kolom bisa dipindah-pindah
                columns: [{
                        title: "No",
                        formatter: "rownum",
                        hozAlign: "center"
                    },
                    {
                        title: "NPP",
                        field: "npp_pegawai"
                    },
                    {
                        title: "Nama Lengkap",
                        field: "nama_pegawai",
                        formatter: "html"
                    },
                    {
                        title: "No. Telp",
                        field: "telp_pegawai"
                    },
                    {
                        title: "Email",
                        field: "email_pegawai"
                    },
                    {
                        title: "Unit Kerja",
                        field: "kantor"
                    },
                    {
                        title: "Bagian Unit Kerja",
                        field: "bagian"
                    },
                    {
                        title: "Jabatan",
                        field: "level_pegawai"
                    },
                    {
                        title: "Sebagai",
                        field: "sebagai_pegawai"
                    },
                    {
                        title: "Status",
                        field: "status_pegawai",
                        formatter: 'html'
                    },
                    {
                        title: "Tanggal",
                        field: "tgl_pegawai",
                        sorter: "date"
                    },
                    {
                        title: "Action",
                        field: "action",
                        formatter: "html",
                        width: 100
                    },
                    // {
                    //     title: "Action", field: "id", formatter: function(cell, formatterParams){
                    //         var id = cell.getValue();
                    //         return `<button onclick="editUser(${id})">Edit</button>
                //                 <button onclick="deleteUser(${id})">Delete</button>`;
                    //     }
                    // }
                ]
            });

            // Fungsi Edit
            window.editUser = function(id) {
                alert("Edit user dengan ID: " + id);
                // Bisa diisi dengan modal atau redirect
            };

            // Fungsi Delete
            window.deleteUser = function(id) {
                if (confirm("Yakin ingin menghapus user ini?")) {
                    fetch(`/api/users/${id}`, {
                            method: "DELETE"
                        })
                        .then(response => response.json())
                        .then(data => {
                            alert("User berhasil dihapus!");
                            table.replaceData("/api/users"); // Refresh data tabel
                        })
                        .catch(error => console.error("Error:", error));
                }
            };
        });
    </script> --}}

    {{-- <script type="text/javascript">
        $('#dataTables').DataTable({
            "processing": true,
            "serverSide": true,
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
    </script> --}}

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
            $('#alert').show();
            $('.type-alert').addClass('alert-warning')
            $('#message').text('Mohon Tunggu Proses Sync, Jangan Tutup Halaman');

            fetchNextPage();
        });
        var allData = [];
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
                totalPages = totalPages - 1;

                currentPage++;

                // Jika masih ada halaman, ambil halaman berikutnya
                if (currentPage <= totalPages) {
                    console.log('tes' + i++)
                    fetchNextPage();
                } else {
                    console.log('get berhasil');
                    sendDataInBatches(allData.length, allData);
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
                    // });

                    currentPage = 1; // Reset halaman saat ini
                    allData = [];
                    // Semua data telah diambil, lakukan sesuatu dengan allData
                    // console.log(allData);
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

        // Fungsi untuk melakukan permintaan API
        function makeApiRequest(page) {
            return $.ajax({
                url: 'https://sf7dev-pro.dataon.com/sfpro/?qlid=HrisUser.getEmployee', // Ganti dengan URL API yang sesuai
                method: 'POST',
                contentType: 'application/json',
                headers: {
                    'Authorization': 'Bearer ' + jwtToken,

                },
                data: JSON.stringify({
                    page_number: page
                }),
                dataType: 'json'
            }).then(function(response) {
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

        var allData = []; // Array yang berisi 1364 data
        var batchSize = 10; // Ukuran batch
        // var totalData = 1364; // Total data
        var currentBatchIndex = 0; // Indeks batch saat ini
        var j = 1;

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
                // console.log(batch);
                return $.ajax({
                    url: "{{ route('pelanggan.sync') }}",
                    type: "POST",
                    data: {
                        data: batch,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend: function() {

                        console.log('mengirim :' + j++)
                        // Tambahkan loader atau animasi sebelum pengiriman jika diperlukan
                    },
                    success: function(res) {
                        console.log(res);


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
                }
            }

            // Mulai proses pengiriman batch
            processBatches();
        }



        function lastSync() {
            $.ajax({
                url: "{{ route('pelanggan.last_sync') }}",
                method: "Get",
                contentType: 'application/json',

            }).then(function(resp) {
                console.log(resp)
                $('.text-sync').text(resp.data);

            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log('Error During login : ', textStatus, errorThrown);
            })
        }

        lastSync();
        // Fungsi login untuk mendapatkan token JWT
        function login() {
            const data = {
                USERPWD: '777FF8B018AB23EEE048D13978E0D1FCFF94D326',
                USERNAME: 'jamkrindo',
                ACCNAME: "jamkrindo",
                TIMESTAMP: getFormattedDate(),
            }
            const url =
                "https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo";
            return $.ajax({
                url: url, // Ganti dengan URL login yang sesuai
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                dataType: 'json'
            }).then(function(response) {
                console.log(response);
                // Mengembalikan token JWT dari respons
                return response.DATA.JWT_TOKEN;
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error during login:', textStatus, errorThrown);
                return null; // Mengembalikan null jika terjadi kesalahan
            });
        }

        // Mulai pengambilan data
    </script>


@stop


<?php } ?>
