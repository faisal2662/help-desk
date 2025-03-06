<?php if (!auth()->check()) {
    header('Location: ' . route('logout'));
    exit();
} else {
    $pegawai = \App\Models\Pegawai::with('NamaPosisi')
        ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
        ->get();
    $kantorCabang = DB::table('tb_kantor_cabang')->where('delete_kantor_cabang', 'N')->pluck('id_kantor_cabang', 'nama_kantor_cabang')->toArray();
    $kantorWilayah = DB::table('tb_kantor_wilayah')->where('delete_kantor_wilayah', 'N')->pluck('id_kantor_wilayah', 'nama_kantor_wilayah')->toArray();

    $kantor = '-';
    $bagian = '-';
    if ($pegawai->count() < 1) {
        header('Location: ' . route('keluar'));
        exit();
    } else {
        foreach ($pegawai as $data_pegawai);

        if ($data_pegawai->kantor_pegawai == 'Kantor Pusat') {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() > 0) {
                foreach ($unit_kerja as $data_unit_kerja) {
                    $kantor_pusat = DB::table('tb_bagian_kantor_pusat')->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')->where('tb_bagian_kantor_pusat.id_bagian_vkantor_pusat', '=', $data_unit_kerja->id_bagian_kantor_pusat)->get();
                    if ($kantor_pusat->count() > 0) {
                        foreach ($kantor_pusat as $data_kantor_pusat);
                        $kantor = $data_kantor_pusat->nama_kantor_pusat;
                        $bagian = 'Semua Bagian';
                        $namaBagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    }
                }
            } else {
                $kantor_pusat = DB::table('tb_bagian_kantor_pusat')->join('tb_kantor_pusat', 'tb_bagian_kantor_pusat.id_kantor_pusat', '=', 'tb_kantor_pusat.id_kantor_pusat')->where('tb_bagian_kantor_pusat.id_bagian_kantor_pusat', '=', $data_pegawai->id_bagian_kantor_pusat)->get();
                if ($kantor_pusat->count() > 0) {
                    foreach ($kantor_pusat as $data_kantor_pusat);
                    $kantor = $data_kantor_pusat->nama_kantor_pusat;
                    $bagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
                    $namaBagian = $data_kantor_pusat->nama_bagian_kantor_pusat;
                }
            }
        } elseif (array_key_exists($data_pegawai->kantor_pegawai, $kantorCabang)) {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();
            if ($unit_kerja->count() > 0) {
                foreach ($unit_kerja as $data_unit_kerja) {
                    $kantor_cabang = DB::table('tb_bagian_kantor_cabang')->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_unit_kerja->id_bagian_kantor_cabang)->get();

                    if ($kantor_cabang->count() > 0) {
                        foreach ($kantor_cabang as $data_kantor_cabang);
                        $kantor = $data_kantor_cabang->nama_kantor_cabang;
                        $bagian = 'Semua Bagian';
                        $namaBagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    }
                }
            } else {
                $kantor_cabang = DB::table('tb_bagian_kantor_cabang')->join('tb_kantor_cabang', 'tb_bagian_kantor_cabang.id_kantor_cabang', '=', 'tb_kantor_cabang.id_kantor_cabang')->where('tb_bagian_kantor_cabang.id_bagian_kantor_cabang', '=', $data_pegawai->id_bagian_kantor_cabang)->get();
                if ($kantor_cabang->count() > 0) {
                    foreach ($kantor_cabang as $data_kantor_cabang);
                    $kantor = $data_kantor_cabang->nama_kantor_cabang;
                    $bagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
                    $namaBagian = $data_kantor_cabang->nama_bagian_kantor_cabang;
                }
            }
        } elseif (array_key_exists($data_pegawai->kantor_pegawai, $kantorWilayah)) {
            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N'], ['tb_kepala_unit_kerja.kantor_pegawai', $data_pegawai->kantor_pegawai]])
                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                ->limit(1)
                ->get();

            if ($unit_kerja->count() > 0) {
                foreach ($unit_kerja as $data_unit_kerja) {
                    $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')->where('tb_kantor_wilayah.id_kantor_wilayah', '=', $data_unit_kerja->id_bagian_kantor_wilayah)->get();
                    if ($kantor_wilayah->count() > 0) {
                        foreach ($kantor_wilayah as $data_kantor_wilayah);
                        $kantor = $data_kantor_wilayah->nama_kantor_wilayah;
                        $bagian = 'Semua Bagian';
                        $namaBagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    }
                }
            } else {
                $kantor_wilayah = DB::table('tb_bagian_kantor_wilayah')->join('tb_kantor_wilayah', 'tb_bagian_kantor_wilayah.id_kantor_wilayah', '=', 'tb_kantor_wilayah.id_kantor_wilayah')->where('tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah', '=', $data_pegawai->id_bagian_kantor_wilayah)->get();
                if ($kantor_wilayah->count() > 0) {
                    foreach ($kantor_wilayah as $data_kantor_wilayah);
                    $kantor = $data_kantor_wilayah->nama_kantor_wilayah;
                    $bagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                    $namaBagian = $data_kantor_wilayah->nama_bagian_kantor_wilayah;
                }
            }
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <meta name="description" content="Helpdesk - Jamkrindo.">
    <title>@yield('title')</title>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('logos/icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('logos/icon.png') }}">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('template/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('template/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('template/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('template/boxicons/css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables/datatables.min.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/css/bootstrap-datepicker.css" rel="stylesheet"/>--> --}}
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css"
        integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <link rel="stylesheet" href="{{ asset('template/css/vertical-layout-light/style.css') }}">
    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    {{-- <link href="https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.min.css" rel="stylesheet"> --}}

    <link
        href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-5-theme/1.2.0/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.0/ckeditor5.css">
    <script type="text/javascript">
        function show(value) {
            document.getElementById('loader').style.display = value ? 'block' : 'none';
        }

        function loadPage(URL) {
            show(true);
            location = URL;
        }

        function newTab(URL) {
            window.open(URL, '_blank');
        }

        setTimeout(function() {
            show(false);
        }, 150);
    </script>

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

        /* From Uiverse.io by Codecite */
        .spinner-container {
            width: 150px;
            height: 150px;
            position: relative;
            margin: 30px auto;
            overflow: hidden;
        }

        .spinner {
            position: absolute;
            width: calc(100% - 9.9px);
            height: calc(100% - 9.9px);
            border: 5px solid transparent;
            border-radius: 50%;
            border-top-color: #4438f3;
            animation: spin 5s cubic-bezier(0.17, 0.49, 0.96, 0.79) infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        #ellipsis {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
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
            background: #192a56;
            border-radius: 100px;
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: #192a56;
        }
    </style>

    @yield('style')

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body>

    <div id="loader">
        <div id="center">
            <!-- From Uiverse.io by Codecite -->
            <div class="spinner-container">
                <div class="spinner">
                    <div class="spinner">
                        <div class="spinner">
                            <div class="spinner">
                                <div class="spinner">
                                    <div class="spinner"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <center>
                <img src="{{ asset('logos/loader.gif') }}" style="width: 170px;">
                <p class="text-primary">Sedang memproses ...</p>
            </center> --}}
        </div>
    </div>

    <div class="container-scroller">
        <!-- partial:../../partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="#"><img src="{{ asset('logos/logo.png') }}"
                        class="mr-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini" href="#"><img src="{{ asset('logos/logo.png') }}"
                        alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <!-- <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button> -->

                <?php if(isset($_GET['filter'])){ ?>

                <?php
                $search = '';

                if (isset($_GET['search'])) {
                    $search = $_GET['search'];
                }
                ?>

                <ul class="navbar-nav mr-lg-2">
                    <li class="nav-item nav-search d-none d-lg-block">

                        <form method="GET" onsubmit="show(true)" id="form-search">

                            <input type="hidden" name="filter" value="<?= $_GET['filter'] ?>" readonly=""
                                required="">

                            <div class="input-group">
                                <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                                    <span class="input-group-text" id="search">
                                        <i class="icon-search"></i>
                                    </span>
                                </div>
                                <input type="text" name="search" hidden class="form-control"
                                    id="navbar-search-input" value="<?= $search ?>" placeholder="Ketik kata kunci ..."
                                    aria-label="search" aria-describedby="search">
                            </div>

                        </form>

                    </li>
                </ul>

                <?php } ?>

                <?php
                $all_notifikasi = DB::table('tb_notifikasi')
                    ->where([['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery']])
                    ->get();
                ?>

                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                            <i class="icon-bell mx-0"></i>
                            <?php if($all_notifikasi->count() > 0){ ?>
                            <span class="count"></span>
                            <?php } ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            aria-labelledby="notificationDropdown">
                            <p class="mb-0 font-weight-normal float-left dropdown-header">Notifikasi</p>


                            <?php if($data_pegawai->NamaPosisi->sebagai_posisi == 'Kepala Unit Kerja'){ ?>

                            <?php
                            $notifikasi_checked = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Checked']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Checked" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-warning">
                                        <i class='bx bx-message-alt-check mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Checked</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_checked->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-warning">' . $notifikasi_checked->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php }else{ ?>

                            <?php
                            $notifikasi_pending = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Pending']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Pending" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-warning">
                                        <i class='bx bx-loader mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Pending</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_pending->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-warning">' . $notifikasi_pending->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php
                            $notifikasi_checked = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Checked']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Checked" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-warning">
                                        <i class='bx bx-message-alt-check mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Checked</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_checked->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-warning">' . $notifikasi_checked->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php } ?>


                            <?php
                            $notifikasi_approve = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Approve']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Approve" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-info">
                                        <i class='bx bx-list-check mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Approve</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_approve->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-info">' . $notifikasi_approve->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php
                            $notifikasi_read = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Read']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Read" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-info">
                                        <i class='bx bx-check-double mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Read</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_read->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-info">' . $notifikasi_read->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php
                            $notifikasi_on_progress = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan On Progress']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=On Progress"
                                class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-primary">
                                        <i class='bx bx-refresh mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan On Progress</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_on_progress->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-primary">' . $notifikasi_on_progress->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>
                            <?php
                            $notifikasi_on_progress = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Solved']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=On Progress"
                                class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-primary">
                                        <i class='bx bx-checkbox-checked mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Solved</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_on_progress->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-primary">' . $notifikasi_on_progress->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php
                            $notifikasi_moving = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Moving']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Moving" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-danger">
                                        <i class='bx bx-redo mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Moving</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_moving->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-danger">' . $notifikasi_moving->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>

                            <?php
                            $notifikasi_finish = DB::table('tb_notifikasi')
                                ->where([['tb_notifikasi.delete_notifikasi', 'N'], ['tb_notifikasi.status_notifikasi', 'Delivery'], ['tb_notifikasi.id_pegawai', $data_pegawai->id_pegawai], ['tb_notifikasi.nama_notifikasi', 'Pengaduan Finish']])
                                ->get();
                            ?>

                            <a href="<?= route('notifikasi') ?>?status=Finish" class="dropdown-item preview-item">
                                <div class="preview-thumbnail">
                                    <div class="preview-icon bg-success">
                                        <i class='bx bx-flag mx-0'></i>
                                    </div>
                                </div>
                                <div class="preview-item-content">
                                    <h6 class="preview-subject font-weight-normal">Pengaduan Finish</h6>
                                    <p class="small-text mb-0 text-muted">
                                        <?php
                                        if ($notifikasi_finish->count() < 1) {
                                            echo 'Belum ada notifikasi';
                                        } else {
                                            echo '<b class="text-success">' . $notifikasi_finish->count() . ' Pengaduan</b>';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </a>
                        </div>
                    </li>
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown"
                            id="profileDropdown">
                            <img src="{{ asset('logos/avatar.png') }}" alt="Foto - Profil" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a class="dropdown-item" style="padding-bottom: 0px;background: transparent;">
                                <b>Hai, Admin!</b>
                            </a>
                            <a class="dropdown-item"
                                style="font-size: 10px;padding-top: 0px;background: transparent;">
                                {{ auth()->user()->departmen_name }}
                            </a>
                            <a class="dropdown-item" href="{{ route('profil') }}">
                                <i class='bx bx-user'></i> Profil Saya
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class='bx bx-power-off'></i> Keluar
                            </a>
                        </div>
                    </li>
                    <!-- <li class="nav-item nav-settings d-none d-lg-flex">
                            <a class="nav-link" href="#">
                            <i class="icon-ellipsis"></i>
                            </a>
                        </li> -->
                    <li class="nav-item nav-settings d-lg-flex">
                        <p style="margin-top: 10px;">
                            <span
                                style="display: block; padding-top: 5px;margin-bottom:-5px;">{{ auth()->user()->employee_name }}</span>
                            <span
                                style="display: block; font-size: 9px;margin-bottom:-5px;">{{ $kantor . ' - ' . $namaBagian }}</span>
                            <span
                                style="display: block; font-size: 9px;">{{ $data_pegawai->NamaPosisi->sebagai_posisi . ' - ' . auth()->user()->sebagai_pegawai }}</span>
                        </p>

                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            {{-- sidebar --}}
            @include('sidebar')
            {{-- end-sidebar --}}
            <div class="main-panel">
                <div class="content-wrapper">

                    @yield('content')

                </div>
                <!-- content-wrapper ends -->
                <!-- partial:../../partials/_footer.html -->
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        Copyright &copy; 2024 Helpdesk - Jamkrindo.
                    </div>
                </footer>
                <!-- partial -->
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <?php if(!Request::is('chat')){ ?>


    <div style="position: fixed;bottom: 0;right: 0;padding: 15px;z-index: 17;">
        <div class="row">
            <div class="col-md-12">
                <!--<div class="card" style="cursor: pointer;box-shadow:20px 20px 50px grey;" onclick="loadPage();">-->
                <div class="card" style="cursor: pointer;box-shadow:20px 20px 50px grey;"
                    onclick="loadPage('{{ route('chat') }}');">
                    <div class="card-body">
                        <h4 class="text-primary" style="padding-bottom: 0;margin-bottom: 0;">
                            <b>
                                <i class='bx bx-chat'></i> Chat - Helpdesk
                                <span class="badge badge-danger" id="chat-notifikasi" style="display: none;">
                                    0
                                </span>
                            </b>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function chat_notifikasi() {
            var http = new XMLHttpRequest();
            var url = '<?= route('chat.notifikasi') ?>';
            var params = '_token=<?= csrf_token() ?>';
            http.open('POST', url, true);

            //Send the proper header information along with the request
            http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

            http.onreadystatechange = function() { //Call a function when the state changes.
                if (http.readyState == 4 && http.status == 200) {
                    if (http.responseText == '0') {
                        $('#chat-notifikasi').hide();
                    } else {
                        $('#chat-notifikasi').show();
                        document.getElementById('chat-notifikasi').innerHTML = http.responseText;
                        suara_chat();
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
                        chat_notifikasi();
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
    </script>

    <?php } ?>


    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="{{ asset('/template/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('/template/js/off-canvas.js') }}"></script>
    <script src="{{ asset('/template/js/hoverable-collapse.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"
    integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <script src="{{ asset('template/js/jquery-3.7.1.min.js')}}"></script>
    {{-- <script src="template/js/template.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> --}}
    <script src="{{ asset('datatables/datatables.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    {{-- <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/2.1.2/js/dataTables.min.js"></script> --}}


    <?php if(isset($_GET['filter'])){ ?>

    <script type="text/javascript">
        document.getElementById('navbar-search-input').addEventListener("keyup", (event) => {
            if (event.key === "Enter") {
                show(true);
                document.getElementById('form-search').submit();
            }
        });

        document.getElementById('search').addEventListener("keyup", (event) => {
            if (event.key === "Enter") {
                show(true);
                document.getElementById('form-search').submit();
            }
        });
    </script>

    <?php } ?>
    <script>
        function cek() {
            const data = {
                '_token': '{{ csrf_token() }}'
            }
            $.ajax({
                url: "{{ route('cek_resolve') }}",
                type: 'POST',
                data: data,
                success: function(data) {

                },
                error: function(response) {
                    // alert(' failed!');
                    console.log(response);
                }
            })
        }

        function cek_sla() {
            const data = {
                '_token': '{{ csrf_token() }}'
            }

            $.ajax({
                url: "{{ route('cek_sla') }}",
                type: 'POST',
                data: data,

                success: function(data) {

                },
                error: function(response) {
                    // alert(' failed!');
                    console.log(response);
                }
            })
        }

        function cek_habis_sla() {
            const data = {
                '_token': '{{ csrf_token() }}'
            }

            $.ajax({
                url: "{{ route('cek_habis_sla') }}",
                type: 'POST',
                data: data,
                success: function(data) {
                    console.log(data)
                    if (data.status == true) {
                        if ((data.klasifikasi_pengaduan == 'Low' || data.klasifikasi_pengaduan == '') &&
                            '{{ auth()->user()->level_pegawai }}' == 'Staff') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                title: "Ada Pengaduan yang belum diselesaikan",
                                text: 'Kode : ' + data.kode_pengaduan,
                                icon: "warning"
                            });

                        } else if (data.klasifikasi_pengaduan == 'Medium' &&
                            '{{ auth()->user()->level_pegawai }}' == 'Kepala Bagian Unit Kerja') {

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                title: "Ada Pengaduan yang belum diselesaikan",
                                text: 'Kode : ' + data.kode_pengaduan,
                                icon: "warning"
                            });
                        } else {

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                title: "Ada Pengaduan yang belum diselesaikan",
                                text: 'Kode : ' + data.kode_pengaduan,
                                icon: "warning"
                            });
                        }
                    }
                },
                error: function(response) {
                    // alert(' failed!');
                    // console.log(response);
                }
            })
        }

        function cek_tanggapan() {
            const data = {
                '_token': '{{ csrf_token() }}'
            }

            $.ajax({
                url: "{{ route('cek_tanggapan') }}",
                type: 'POST',
                data: data,

                success: function(data) {
                    // console.log(data)
                },
                error: function(response) {
                    // alert(' failed!');
                    console.log(response);
                }
            })
        }
        setInterval(cek_tanggapan, 3600000);
        setInterval(cek, 3600000);
        setInterval(cek_sla, 3600000);
        setInterval(cek_habis_sla, 3600000);
    </script>
    @yield('script')

</body>

</html>

<audio id="ringtone">
    <source src="<?= url('audio/notification.mp3') ?>" type="audio/mpeg">
</audio>

<script>
    var audio = document.getElementById('ringtone');

    function suara_chat() {
        var http = new XMLHttpRequest();
        var url = '<?= route('chat.suara_chat') ?>';
        var params = '_token=<?= csrf_token() ?>';
        http.open('POST', url, true);

        //Send the proper header information along with the request
        http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

        http.onreadystatechange = function() { //Call a function when the state changes.
            if (http.readyState == 4 && http.status == 200) {
                if (http.responseText == 'Play') {
                    audio.autoplay = true;
                    audio.load();
                }
            }
        }
        http.send(params);
    }
</script>
