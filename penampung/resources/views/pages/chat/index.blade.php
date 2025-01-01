<?php
$pegawai = DB::table('tb_pegawai')
    ->join('tb_posisi_pegawai', 'tb_pegawai.id_posisi_pegawai', '=', 'tb_posisi_pegawai.id_posisi_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();
if ($pegawai->count() < 1) {
    header('Location: ' . route('keluar'));
    exit();
} else {
    foreach ($pegawai as $data_pegawai);

    $kantor = '-';
    $bagian = '-';

    if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();

        if ($unit_kerja->count() > 0) {
            foreach ($unit_kerja as $data_unit_kerja) {
                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                    ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                    ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_unit_kerja->id_bagian_kantor_pusat)
                    ->get();
                if ($kantor_pusat->count() > 0) {
                    foreach ($kantor_pusat as $data_kantor_pusat);
                    $kantor = $data_kantor_pusat->nama_kantor_pusat;
                    $bagian = 'Semua Bagian';
                }
            }
        } else {
            $kantor_pusat = DB::table('tb_bagian_kantor_pusat')
                ->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')
                ->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)
                ->get();
            if ($kantor_pusat->count() > 0) {
                foreach ($kantor_pusat as $data_kantor_pusat);
                $kantor = $data_kantor_pusat->nama_kantor_pusat;
                $bagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
            }
        }
    } elseif ($data_pegawai->kantor_pegawai == 'Kantor Cabang') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();

        if ($unit_kerja->count() > 0) {
            foreach ($unit_kerja as $data_unit_kerja) {
                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                    ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                    ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_unit_kerja->id_bagian_kantor_cabang)
                    ->get();
                if ($kantor_cabang->count() > 0) {
                    foreach ($kantor_cabang as $data_kantor_cabang);
                    $kantor = $data_kantor_cabang->nama_kantor_cabang;
                    $bagian = 'Semua Bagian';
                }
            }
        } else {
            $kantor_cabang = DB::table('tb_bagian_kantor_cabang')
                ->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')
                ->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)
                ->get();
            if ($kantor_cabang->count() > 0) {
                foreach ($kantor_cabang as $data_kantor_cabang);
                $kantor = $data_kantor_cabang->nama_kantor_cabang;
                $bagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
            }
        }
    } elseif ($data_pegawai->kantor_pegawai == 'Kantor Wilayah') {
        $unit_kerja = DB::table('tb_kepala_unit_kerja')
            ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
            ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
            ->limit(1)
            ->get();

        if ($unit_kerja->count() > 0) {
            foreach ($unit_kerja as $data_unit_kerja) {
                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                    ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                    ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_unit_kerja->id_bagian_kantor_wilayah)
                    ->get();
                if ($kantor_wilayah->count() > 0) {
                    foreach ($kantor_wilayah as $data_kantor_wilayah);
                    $kantor = $data_kantor_wilayah->nama_kantor_wilayah;
                    $bagian = 'Semua Bagian';
                }
            }
        } else {
            $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')
                ->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')
                ->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)
                ->get();
            if ($kantor_wilayah->count() > 0) {
                foreach ($kantor_wilayah as $data_kantor_wilayah);
                $kantor = $data_kantor_wilayah->nama_kantor_wilayah;
                $bagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
            }
        }
    }
}
?>
@extends('template')

@section('title')
    Chat - Helpdesk
@stop

@section('content')
<style>
        #toast {
            visibility: hidden;
            min-width: 50px;
            margin-left: -125px;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            right: 30px;
            top: 80px;
            font-size: 17px;
        }

        #toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
    </style>
    <input type="hidden" id="id-kontak" value="0" readonly="" required="">
    <input type="hidden" name="" id="status">

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div id="toast">
                <div class="alert alert-primary" role="alert">
                    <i class="bi bi-bell"></i> Masuk :
                    <span class="name-from fw-bold"></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body" style="height: 590px;overflow-y: scroll;">
                    <div class="card-title"><b><i class='bx bx-group'></i> Riwayat Chat</b></div>
                    <ul class="nav nav-tabs" id="setting-panel" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="pengaduan" data-toggle="tab" href="#pengaduan-saya"
                                role="tab" aria-controls="todo-section" aria-expanded="true">MY</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="friend" data-toggle="tab" href="#pengaduan-friend" role="tab"
                                aria-controls="chats-section">FRIENDS</a>
                        </li>
                    </ul>
                    <hr style="border-style: dashed;">
                    <div id="riwayat-chat">
                        <center>
                            <img src="<?= url('logos/loader.gif') ?>" style="width: 170px;">
                            <p class="text-primary">Sedang memproses ...</p>
                        </center>
                    </div>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
        <div class="col-md-8">
            <div id="mulai-chat">

                <div class="card">
                    <div class="card-body">
                        <div class="card-title"><b><i class='bx bx-chat'></i>Mulai Chat</b></div>
                        <hr style="border-style: dashed;">
                        <center>
                            <img src="<?= url('logos/chat.png') ?>" style="width: 170px;">
                            <p>Silahkan lakukan pengaduan terlebih dahulu untuk memulai Chat.</p>
                        </center>
                    </div>
                </div>
                <p>&nbsp;</p>

            </div>


            <div class="card" id="open-chat" style="display: none;margin-top: -60px;">
                <div class="card-body">
                    <hr style="border-style: dashed;">
                    <h4 class="text-primary" align="center" id="chat-loading" style="display: none;">
                        <i class='bx bx-mail-send'></i> Sedang mengirim pesan chat ...
                    </h4>
                    <div class="input-group" id="input-group-chat">
                        <input type="text" id="pesan-chat" class="form-control" required="" autocomplete="off"
                            maxlength="255" placeholder="Mulai chat ...">
                        <div class="input-group-append">
                            <span class="input-group-text bg-primary text-white"
                                style="border-radius: 0 10px 10px 0;cursor: pointer;" onclick="cpu_load();"><i
                                    class='bx bx-send'></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <audio id="notificationSound" src="{{ asset('audio/notification.mp3') }}" preload="auto"></audio>

            <p>&nbsp;</p>
        </div>
    </div>

@stop
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@section('script')
 <script>
        function fetchMessages() {
            let id = $('#id-kontak').val();
            let id_pegawai = '{{ auth()->user()->id_pegawai }}';

            $.ajax({
                url: "{{ route('chat.cek_pesan') }}",
                type: 'GET',
                data: {
                    id_kontak: id
                },
                success: function(res) {
                    console.log(res)
                    res.forEach(data => {
                        if (res != '') {
                            if (data.employee.id_pegawai != id_pegawai) {
                                playNotificationSound()

                                $('.name-from').text(data.employee.employee_name);
                                const toast = $('#toast');
                                toast.addClass('show');
                                setTimeout(function() {
                                    toast.removeClass('show');
                                }, 3000); // Toast will be visible for 3 seconds

                                // $('#pesan').append(data.keterangan_chat)

                                if ($('#id-kontak').val() == data.room_chat) {
                                    $('#group-chat').append(`
                                    <div class="row" id="box-chat">
                                        <div class="col-md-6" style="margin-bottom: 10px;">
                                            <label style="font-size: 12px;">
                                                <img src="logos/avatar.png"
                                                    style="width: 15px;height: 15px;border-radius: 100%;"> ${data.employee.employee_name}
                                                (${data.employee.nama_posisi.sebagai_posisi})
                                            </label><br>
                                            <span class="badge" id="pesan"
                                                style="background-color: rgb(180, 178, 178);font-size: 13px;border-radius: 10px 10px 10px 0;font-weight:normal;white-space: normal;text-align: left;">
                                               ${data.keterangan_chat}
                                            </span><br>
                                            <span style="font-size: 9px;">
                                                ${data.time}
                                            </span>
                                        </div>
                                    </div>`);
                                    scrollToBottom()
                                }
                            }
                        }
                    });
                },
                error: function(err) {
                    console.error('Error fetching messages!', err);
                }
            });
        }

        function scrollToBottom() {
            const chatContainer = document.getElementById('group-chat');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function playNotificationSound() {
            var audio = $('#notificationSound')[0]; // Mengakses elemen audio
            audio.play(); // Memutar suara
        }
        setInterval(fetchMessages, 4000);
    </script>
    <script type="text/javascript">
        function cek_riwayat_chat() {
            let status = document.getElementById('status')
            var http = new XMLHttpRequest();
            var url = '<?= route('chat.cek_riwayat_chat') ?>';
            var params = '_token=<?= csrf_token() ?>';
            http.open('POST', url, true);

            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function() { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {
                    if (http.responseText == 'new') {
                        if (status) {
                            riwayat_chat_friend();
                            mulai_chat_friend();
                        } else {
                            riwayat_chat();
                            mulai_chat();
                        }
                    }
                }
            }
            http.send(params);
        }

        function cek_cpu() {
            var http = new XMLHttpRequest();
            var url = '<?= route('cpu') ?>';
            var params = '_token=<?= csrf_token() ?>';
            http.open('GET', url, true);

            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function() { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {
                    if (parseInt(http.responseText) <= 40) {
                        cek_riwayat_chat();
                    }
                    refresh();
                }
            }
            http.send(params);
        }

        function refresh() {
            setTimeout(function() {
                cek_cpu();
            }, 5000);
        }

        refresh();

        function riwayat_chat() {
            document.getElementById('riwayat-chat').innerHTML = '';
            var http = new XMLHttpRequest();
            var url = '<?= route('chat.riwayat_chat') ?>?id_kontak=' + document.getElementById('id-kontak').value;
            var params = '_token=<?= csrf_token() ?>';
            http.open('POST', url, true);

            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function() { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {

                    document.getElementById('riwayat-chat').innerHTML = http.responseText;


                }

            }
            http.send(params);
        }

        function riwayat_chat_friend() {
            document.getElementById('riwayat-chat').innerHTML = '';
            var http = new XMLHttpRequest();
            var url = '<?= route('chat.riwayat_chat_friend') ?>?id_kontak=' + document.getElementById('id-kontak').value;
            var params = '_token=<?= csrf_token() ?>';
            http.open('POST', url, true);

            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function() { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {

                    document.getElementById('riwayat-chat').innerHTML = http.responseText;
                }

            }
            http.send(params);
        }

        $('#friend').click(function() {
            riwayat_chat_friend();
        })
        $('#pengaduan').click(function() {
            riwayat_chat();
        })
        riwayat_chat();

        function set_id_kontak(id) {
            let pegawai = @json($data_pegawai);
            let level = pegawai.sebagai_pegawai;
            document.getElementById('status').value = 0
            document.getElementById('id-kontak').value = id;
            mulai_chat();
            riwayat_chat();
              $.post("{{ route('chat_pengaduan') }}", {
                _token: '{{ csrf_token() }}',
                id: id,
            }, function(data) {
                if (data.status_pengaduan != 'Finish') {


                    if (level == 'PIC') {
                $('#open-chat').show();
            }
                }

            }).fail(function(xhr) {
                reject('Terjadi kesalahan: ' + xhr.statusText); // Tangani error
            });

        }



        function set_id_kontak_friend(id) {
            let pegawai = @json($data_pegawai);
            let level = pegawai.sebagai_posisi;
            document.getElementById('status').value = 1

            document.getElementById('id-kontak').value = id;
            mulai_chat_friend();
            riwayat_chat_friend();
         $.post("{{ route('chat_pengaduan') }}", {
                _token: '{{ csrf_token() }}',
                id: id,
            }, function(data) {
                // console.log(level)
                if (data.status_pengaduan != 'Finish') {
                    let klasifikasi = data.klasifikasi_pengaduan;
                    if ((klasifikasi == 'High' && level == 'Kepala Unit Kerja') || level == 'Staff' || level ==
                    'Staf') {
                        $('#open-chat').show();
                    } else if ((klasifikasi == 'Medium' && level == 'Kepala Bagian Unit Kerja') || level ==
                        'Staff' || level == 'Staf') {
                        $('#open-chat').show();
                    } else if (level == 'Staff') {
                        $('#open-chat').show();
                    }
                }

            }).fail(function(xhr) {
                reject('Terjadi kesalahan: ' + xhr.statusText); // Tangani error
            });
        }

        // function mulai_chat (){
        //   if(document.getElementById('id-kontak').value != '0'){
        //   	  var http = new XMLHttpRequest();
        //       var url = '<?= route('chat.mulai_chat') ?>?id_kontak=' + document.getElementById('id-kontak').value;
        //       var params = '_token=<?= csrf_token() ?>';
        //       http.open('POST', url, true);

        //       //Send the proper header information along with the request
        //       http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        //       http.onreadystatechange = function() {//Call a function when the state changes.
        //           if(http.readyState == 4 && http.status == 200) {
        //               document.getElementById('mulai-chat').innerHTML = http.responseText;
        //               $("#group-chat").scrollTop($("#group-chat")[0].scrollHeight);

        //               riwayat_chat();
        //           }
        //       }
        //       http.send(params);
        //   }
        // }
        function mulai_chat() {
            if (document.getElementById('id-kontak').value !== '0') {
                var http = new XMLHttpRequest();
                var url = '<?= route('chat.mulai_chat') ?>';
                var params = 'id_kontak=' + document.getElementById('id-kontak').value +
                    '&_token=<?= csrf_token() ?>';

                http.open('POST', url, true);
                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function() {
                    if (http.readyState === 4 && http.status === 200) {

                        document.getElementById('mulai-chat').innerHTML = http.responseText;

                        // riwayat_chat();
                        scrollToBottom()
                    }
                    // $('#group-chat').scrollTop($('#box-chat').height());
                };
                http.send(params);
            }
        }
        // Fungsi untuk scroll ke bagian paling bawah
        function scrollToBottom() {
            const chatContainer = document.getElementById('group-chat');
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        function mulai_chat_friend() {
            if (document.getElementById('id-kontak').value !== '0') {
                var http = new XMLHttpRequest();
                var url = '<?= route('chat.mulai_chat_friend') ?>';
                var params = 'id_kontak=' + document.getElementById('id-kontak').value +
                    '&_token=<?= csrf_token() ?>';

                http.open('POST', url, true);
                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function() {
                    if (http.readyState === 4 && http.status === 200) {
                        // console.log(http.responseText)
                        document.getElementById('mulai-chat').innerHTML = http.responseText;
                        // document.getElementById('group-chat').scrollTop =
                        //     document.getElementById('group-chat').scrollHeight;
                        scrollToBottom();

                    }
                };
                http.send(params);
            }
        }


        function cpu_load() {
            if (document.getElementById('pesan-chat').value == '') {
                alert('Ketik sesuatu untuk mulai chat ..');
            } else {
                $('#input-group-chat').hide();
                $('#chat-loading').show();

                var http = new XMLHttpRequest();
                var url = '<?= route('cpu') ?>';
                var params = '_token=<?= csrf_token() ?>';
                http.open('GET', url, true);

                //Send the proper header information along with the request
                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function() { //Call a function when the state changes.
                    if (http.readyState == 4 && http.status == 200) {
                        if (parseInt(http.responseText) <= 60) {
                            kirim_chat();
                        } else {

                            var r = confirm('Kirim pesan gagal, Coba lagi?');
                            if (r == true) {
                                cpu_load();
                            } else {
                                $('#input-group-chat').show();
                                $('#chat-loading').hide();
                            }

                        }
                    }
                }
                http.send(params);
            }
        }

        function kirim_chat() {
            var status = document.getElementById('status');
            if (document.getElementById('pesan-chat').value == '') {
                alert('Ketik sesuatu untuk mulai chat ..');
            } else {

                var http = new XMLHttpRequest();
                var url = '<?= route('chat.kirim_chat') ?>';
                var params = '_token=<?= csrf_token() ?>&kontak=' + document.getElementById('id-kontak').value +
                    '&keterangan=' + document.getElementById('pesan-chat').value +'&posisi=' + status.value;
                http.open('POST', url, true);

                //Send the proper header information along with the request
                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function() { //Call a function when the state changes.
                    if (http.readyState == 4 && http.status == 200) {
                        document.getElementById('pesan-chat').value = '';
                        $('#input-group-chat').show();
                        $('#chat-loading').hide();
                        if (status) {
                            mulai_chat_friend();
                        } else {
                            mulai_chat();
                        }
                    }
                }
                http.send(params);
            }
        }


        document.getElementById('pesan-chat').addEventListener("keyup", (event) => {
            if (event.key === "Enter") {
                cpu_load();
            }
        });
    </script>

@stop
