@extends('template')
@section('content')
    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b>User data sunfish</b> <span style="float:right;" > <button id="btn_sync"
                                class="btn btn-primary btn-sm"><i class='bx bx-sync'></i> Sync</button> <button
                                style="display: none" id="btn_sync_load" class="btn btn-sm btn-primary" type="button"
                                disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Loading...
                            </button></span></div>

                    <span style="display:none;" id="alert">
                        <div class="alert text-center type-alert " role="alert">
                            <span id="message"></span>
                        </div>
                    </span>
                    <hr style="border-style: dashed;">
                    <p class="float-right"><strong>
                            Last sync : </strong> <span class="text-sync"></span></p>
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
                                    <td><b>Divisi</b></td>
                                    <td><b>Action</b></td>
                                </tr>
                            </thead>
                        </table>
                    </div>


                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $('#dataTables').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "<?= route('pegawai-sunfish.datatables') ?>",
                "dataType": "json",
                "type": "GET",
            },
            columns: [{
                    data: 'no'
                },
                {
                    data: 'employee_id'
                },
                {
                    data: 'employee_name'
                },
                {
                    data: 'telp_pegawai'
                },
                {
                    data: 'email_pegawai'
                },
                {
                    data: 'branch_name'
                },
                {
                    data: 'department_name'
                },
                {
                    data: 'division_name'
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
            $('#alert').css('display', 'block');
            $('.type-alert').addClass('alert-warning')
            $('#message').text('Mohon Tunggu Proses Sync, Jangan Tutup Halaman');

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
                    $('#alert').css('display', 'block');
                    $('.type-alert').addClass('alert-danger')
                    $('#message').text('Sync Gagal');
                    setTimeout(() => {
                        $('.type-alert').removeClass('alert-danger')
                        $('#message').text('');
                        $('#alert').css('display', 'none');
                    }, 5000);
                    console.error('Error fetching data');
                    return false; // Jika ada error, return false
                }

                // Gabungkan data dari halaman saat ini ke allData
                allData = allData.concat(response.DATA.DATA);

                // Hitung total halaman
                totalPages = Math.ceil(response.DATA.TOTAL / perPage);
                totalPages = totalPages - 1;
                // console.log(allData);
                // return false;
                currentPage++;

                // Jika masih ada halaman, ambil halaman berikutnya
                if (currentPage <= totalPages) {
                    console.log('tes' + i++)
                    fetchNextPage();
                } else {
                    console.log('get berhasil');
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
                    console.log(allData.length);
                }
            }).fail(function() {
                console.error('Error fetching data from API');
                $('#btn_sync_load').hide('slow');
                $('#btn_sync').show('slow');
                $('#alert').css('display', 'block');
                $('.type-alert').addClass('alert-danger')
                $('#message').text('Sync Gagal');
                setTimeout(() => {
                    $('.type-alert').removeClass('alert-danger')
                    $('#message').text('');
                    $('#alert').css('display', 'none');
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
                $('#alert').css('display', 'block');
                $('.type-alert').addClass('alert-danger')
                $('#message').text('Sync Gagal');
                setTimeout(() => {
                    $('.type-alert').removeClass('alert-danger')
                    $('#message').text('');
                    $('#alert').css('display', 'none');

                }, 5000);
                return null; // Mengembalikan null jika terjadi kesalahan
            });
        }

        // // Fungsi login untuk mendapatkan token JWT
        // function login() {
        //     return $.ajax({
        //         url: 'URL_LOGIN', // Ganti dengan URL login yang sesuai
        //         method: 'POST',
        //         contentType: 'application/json',
        //         data: JSON.stringify({
        //             // Sertakan data login yang diperlukan
        //             username: 'your_username',
        //             password: 'your_password'
        //         }),
        //         dataType: 'json'
        //     }).then(function(response) {
        //         // Mengembalikan token JWT dari respons
        //         return response.DATA.JWT_TOKEN;
        //     }).fail(function(jqXHR, textStatus, errorThrown) {
        //         console.error('Error during login:', textStatus, errorThrown);
        //         return null; // Mengembalikan null jika terjadi kesalahan
        //     });
        // }



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

            // const url =
            //     "https://sf7dev-pro.dataon.com/sfpro/?ofid=sfSystem.loginUser&originapp=hris_jamkrindo";
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
                console.log(batch);
                return $.ajax({
                    url: "{{ route('pegawai-sunfish.sync') }}",
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
                            $('#alert').css('display', 'block');
                            $('.type-alert').addClass('alert-danger');
                            $('#message').text('Sync Gagal');
                            setTimeout(() => {
                                $('.type-alert').removeClass('alert-danger');
                                $('#message').text('');
                                $('#alert').css('display', 'none');
                            }, 5000);
                        }
                    },
                    error: function(err) {
                        console.error(err);
                        $('#btn_sync_load').hide('slow');
                        $('#btn_sync').show('slow');
                        $('#alert').css('display', 'block');
                        $('.type-alert').addClass('alert-danger');
                        $('#message').text('Sync Error');
                        setTimeout(() => {
                            $('.type-alert').removeClass('alert-danger');
                            $('#message').text('');
                            $('#alert').css('display', 'none');
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
                        console.log("Batch berhasil dikirim:", response);
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
                    $('#alert').css('display', 'block');
                    $('.type-alert').addClass('alert-primary');
                    $('#message').text('Sync Selesai');
                    setTimeout(() => {
                        $('.type-alert').removeClass('alert-primary');
                        $('#message').text('');
                        $('#alert').css('display', 'none');
                    }, 5000);
                    lastSync()
                }
            }

            // Mulai proses pengiriman batch
            processBatches();
        }

        function lastSync() {
            $.ajax({
                url: "{{ route('pegawai-sunfish.last_sync') }}",
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
        // // Fungsi untuk menyimpan data ke file JSON
        // function saveDataToFile(data) {
        //     // Konversi data ke format JSON
        //     var jsonData = JSON.stringify(data, null, 2); // Menggunakan 2 spasi untuk indentasi

        //     // Buat objek Blob dari data JSON
        //     var blob = new Blob([jsonData], {
        //         type: 'application/json'
        //     });

        //     // Buat URL untuk Blob
        //     var url = URL.createObjectURL(blob);

        //     // Buat elemen anchor untuk mengunduh file
        //     var a = document.createElement('a');
        //     a.href = url;
        //     a.download = 'data.json'; // Nama file yang akan diunduh

        //     // Tambahkan elemen anchor ke body (tidak terlihat)
        //     document.body.appendChild(a);

        //     // Simulasikan klik pada elemen anchor untuk memulai unduhan
        //     a.click();

        //     // Hapus elemen anchor dari DOM
        //     document.body.removeChild(a);

        //     // Lepaskan URL Blob
        //     URL.revokeObjectURL(url);
        // }
    </script>
@endsection
