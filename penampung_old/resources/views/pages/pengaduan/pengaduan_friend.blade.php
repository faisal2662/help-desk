@php

$status_klasifikasi = array(
            'High' => 'danger',
            'Medium' => 'warning',
            'Low' => 'info',
        );


@endphp
@extends('template')

@section('title')
    Pengaduan - Helpdesk
@stop

<?php if(isset($_GET['create'])){ ?>

@include('pages.pengaduan.tambah')

<?php }else if(isset($_GET['update'])){ ?>

@include('pages.pengaduan.ubah')

<?php }else if(isset($_GET['view'])){ ?>

@include('pages.pengaduan.lihat_friend')

<?php }else if(isset($_GET['lampiran'])){ ?>

@include('pages.pengaduan.lampiran')

<?php }else if(isset($_GET['alihkan'])){ ?>

@include('pages.pengaduan.alihkan')

<?php }else{ ?>


@section('content')


    <div class="row">
        <div class="col-md-12">

            <!-- list & grid -->
            <div style="position: absolute;top: 0;right: 0;padding-top: 20px;padding-right: 10px;">
                <button type="button" id="grid" class="btn btn-sm btn-primary">
                    <i class='bx bx-grid-alt'></i>
                </button>
                <button type="button" id="list" class="btn btn-sm btn-outline-primary">
                    <i class='bx bx-list-ul'></i>
                </button>
            </div>
            <!-- end list & grid -->

            <p>&nbsp;</p>
            {{-- <h4>
					<i class='bx bxs-coupon'></i> Pengaduan - <?= str_replace('Holding', 'Pengaduan SLA', $_GET['filter']) ?>
				</h4> --}}


            <p>&nbsp;</p>
        </div>
    </div>

    <div id="data_pagination">
        <!-- data pagination -->

    </div>


    <script>
        $(document).ready(function() {

            // Event handler untuk klik pada pagination
            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault(); // Mencegah reload halaman
                var page = $(this).attr('href').split('page=')[1]; // Mendapatkan nomor halaman dari URL
                fetch_data(page); // Memanggil fungsi fetch_data dengan parameter page
            });

            // Fungsi untuk mengambil data menggunakan AJAX
            function fetch_data(page) {
                // Menampilkan preloader
                $('#data_pagination').html(
                    '<div class="card">' +
                    '<div class="card-body" align="center">' +
                    '<img src="<?= url('logos/loader.gif') ?>" style="width: 150px;">' +
                    '<p class="text-primary">Sedang memproses ...</p>' +
                    '</div></div>'
                );

                // Membuat URL berdasarkan ada tidaknya parameter search
                var url = '';
                <?php if(isset($_GET['search'])) { ?>
                url =
                    '<?= route('pangaduan.friend.data_grid') ?>?filter=<?= $_GET['filter'] ?>&search=<?= htmlspecialchars($_GET['search']) ?>&page=' +
                    page;
                <?php } else { ?>
                url = '<?= route('pangaduan.friend.data_grid') ?>?filter=<?= $_GET['filter'] ?>&page=' + page;
                <?php } ?>

                // Mengirim request AJAX dengan jQuery
                $.ajax({
                    url: url,
                    method: "POST",
                    data: {
                        _token: '<?= csrf_token() ?>'
                    },
                    success: function(data) {
                        // Menampilkan hasil ke elemen dengan ID data_pagination
                        $('#data_pagination').html(data);
                    },
                    error: function(xhr, status, error) {
                        console.log('Terjadi kesalahan:', error);
                    }
                });
            }

            // Ambil data untuk halaman 1 saat halaman pertama kali dimuat
            fetch_data(1);

        });
    </script>


    <div class="row" id="data_list" style="display: none;">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">

                    @if ($pengaduan->count() < 1)
                        <center>
                            <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                            <p>Data saat ini tidak ditemukan.</p>
                        </center>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" id="dataTables"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <td><b>No</b></td>
                                        <td><b>Kode Pengaduan</b></td>
                                        <td><b>Pengaduan</b></td>
                                        <td><b>Dari</b></td>
                                        <td><b>Kepada</b></td>
                                        <td><b>Keterangan</b></td>
                                        <td><b>Klasifikasi</b></td>
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

                        </span>

                         <script type="text/javascript">
                            async function klasifikasi_data(id, name, defaultVal = ' ') {
                                const {
                                    value: fruit
                                } = await Swal.fire({
                                    title: "Pilih Klasifikasi",
                                    input: "select",
                                    inputOptions: {
                                        Low: "Low",
                                        Medium: "Medium",
                                        High: "High"

                                    },
                                    inputPlaceholder: "Pilih item",
                                    inputValue: defaultVal,
                                    showCancelButton: true,
                                    inputValidator: (value) => {
                                        return new Promise((resolve) => {
                                            $.post("{{ route('pengaduan.klasifikasi') }}", {
                                                klasifikasi: value,
                                                pengaduanId: id,
                                                name: name,
                                                _token: '{{ csrf_token() }}'

                                            }, function(data) {
                                                console.log(data)
                                                if (data.status == 'success') {
                                                 resolve()
                                                 location.reload()
                                                }
                                            });
                                            // console.log(value);
                                            // resolve();
                                        });
                                    }
                                });
                            }
                        </script>
                    @endif

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
            "ajax": {
                "url": "<?= route('pengaduan.datatables') ?>?filter=<?= $_GET['filter'] ?>",
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
                    data: 'kode_pengaduan'
                },
                {
                    data: 'nama_pengaduan'
                },
                {
                    data: 'dari_pengaduan'
                },
                {
                    data: 'kepada_pengaduan'
                },
                {
                    data: 'keterangan_pengaduan'
                },
                {
                    data: 'klasifikasi_pengaduan'
                },
                {
                    data: 'status_pengaduan'
                },
                {
                    data: 'tgl_pengaduan'
                },
                {
                    data: 'action'
                },
            ]
        });
    </script>

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

    <script type="text/javascript">
        $('#grid').on('click', function() {
            $('#grid').addClass('btn-primary');
            $('#grid').removeClass('btn-outline-primary');
            $('#list').removeClass('btn-primary');
            $('#list').addClass('btn-outline-primary');
            $('#data_pagination').show();
            $('#data_list').hide();
        });

        $('#list').on('click', function() {
            $('#list').addClass('btn-primary');
            $('#list').removeClass('btn-outline-primary');
            $('#grid').removeClass('btn-primary');
            $('#grid').addClass('btn-outline-primary');
            $('#data_pagination').hide();
            $('#data_list').show();
        });
    </script>

@stop
<?php } ?>
