@if (isset($_GET['informasi']))
    @include('pages.dashboard.informasi')
@endif
@extends('template')

@section('title')
    Dashboard - Helpdesk
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <h4>
                Dashboard - Helpdesk
            </h4>
            <p>&nbsp;</p>
        </div>
    </div>

    <?php
    $tahun = date('Y');

    if (isset($_GET['tahun'])) {
        if ($_GET['tahun'] == '') {
            $tahun = date('Y');
        } else {
            $tahun = $_GET['tahun'];
        }
    }
    ?>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
    </svg>
    <div class="alert alert-primary d-flex align-items-center" role="alert">
        <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Info:">
            <use xlink:href="#info-fill" />
        </svg>
        <div style="padding-left:10px;">
            Aplikasi Helpdesk yang dimiliki oleh Divisi Jaringan merupakan aplikasi yang mengakomodir <strong>kendala -
                kendala Unit
                Kerja diluar dari kendala helpdesk TI. </strong> <a href="?informasi=data" style="font-style: italic;">
                Selengkapnya...</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-calendar'></i> Filter Tahun</b></div>
                    <hr style="border-style: dashed;">
                    <form method="GET" onsubmit="show(true);">
                        <div class="input-group">
                            <input type="text" name="tahun" id="tahun" value="<?= $tahun ?>" required=""
                                readonly="" maxlength="255" placeholder="Atur Tahun ..." class="form-control">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class='bx bx-calendar-check'></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

    <div class="row">
        @foreach ($pegawai as $data_pegawai)
            @if ($data_pegawai->NamaPosisi->sebagai_posisi != 'Kepala Unit Kerja')
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title"><b><i class='bx bx-loader'></i> Pengaduan Pending</b></div>
                            <hr style="border-style: dashed;">
                            <canvas id="pengaduan-pending" style="width: 100%;"></canvas>
                        </div>
                    </div>
                    <p>&nbsp;</p>
                </div>
            @endif
        @endforeach

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-list-check'></i> Pengaduan Approve</b></div>
                    <hr style="border-style: dashed;">
                    <canvas id="pengaduan-approve" style="width: 100%;"></canvas>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-refresh'></i> Pengaduan On Progress</b></div>
                    <hr style="border-style: dashed;">
                    <canvas id="pengaduan-on-progress" style="width: 100%;"></canvas>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-check-double'></i> Pengaduan Finish</b></div>
                    <hr style="border-style: dashed;">
                    <canvas id="pengaduan-finish" style="width: 100%;"></canvas>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b>Rekapitulasi Pengaduan</b></div>
                    <hr style="border-style: dashed;">
                    <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <img src="..." class="d-block w-100" alt="...">
                          </div>
                          <div class="carousel-item">
                            <img src="..." class="d-block w-100" alt="...">
                          </div>
                          <div class="carousel-item">
                            <img src="..." class="d-block w-100" alt="...">
                          </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                          <span class="visually-hidden">Next</span>
                        </button>
                      </div>
                    {{-- <div class="row">
                        <div class="col">
                            <div class="card shadow p-3 mb-5 bg-body rounded" style="background-color: #d1e4f3">
                                <div class="card-body text-center">
                                    <ul style="list-style-type: none;margin:0;padding:0;" >
                                        <li >
                                           <span class="rounded " style="font-size: 55pt; color:white;background-color:rgb(58, 143, 240);"> <i class='bx bx-list-check'></i> </span>
                                        </li>
                                        <li>
                                           <p style="font-size: 13pt; font-weight: bold;"> {{$pengaduan->where('status_pengaduan', 'Pending')->count()}} </p>
                                        </li>
                                        <li>
                                          <p  style="font-size: 14pt; font-weight: bold;"> Pengaduan Approve </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow p-3 mb-5 bg-body rounded" style="background-color: #f0e3cd">
                                <div class="card-body text-center">
                                    <ul style="list-style-type: none;margin:0;padding:0;" >
                                        <li >
                                           <span class="rounded " style="font-size: 55pt; color:white;background-color:rgb(240, 191, 58);"> <i class='bx bx-refresh'></i> </span>
                                        </li>
                                        <li>
                                           <p style="font-size: 13pt; font-weight: bold;"> {{$pengaduan->where('status_pengaduan', 'On Progress')->count()}} </p>
                                        </li>
                                        <li>
                                          <p  style="font-size: 14pt; font-weight: bold;"> Pengaduan On Progress </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow p-3 mb-5 bg-body rounded" style="background-color: #cfeed1">
                                <div class="card-body text-center">
                                    <ul style="list-style-type: none;margin:0;padding:0;" >
                                        <li >
                                           <span class="rounded " style="font-size: 55pt; color:white;background-color:rgb(57, 236, 51);"> <i class='bx bx-check-double'></i> </span>
                                        </li>
                                        <li>
                                           <p style="font-size: 13pt; font-weight: bold;"> {{$pengaduan->where('status_pengaduan', 'Finish')->count()}} </p>
                                        </li>
                                        <li>
                                          <p  style="font-size: 14pt; font-weight: bold;"> Pengaduan Finish </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="card shadow p-3 mb-5 bg-body rounded" style="background-color: #fae7d9">
                                <div class="card-body text-center">
                                    <ul style="list-style-type: none;margin:0;padding:0;" >
                                        <li >
                                           <span class="rounded " style="font-size: 55pt; color:white;background-color:rgb(240, 111, 60);"> <i class='bx bx-history'></i> </span>
                                        </li>
                                        <li>
                                           <p style="font-size: 13pt; font-weight: bold;"> {{$pengaduan->where('status_pengaduan', 'Late')->count()}} </p>
                                        </li>
                                        <li>
                                          <p  style="font-size: 14pt; font-weight: bold;"> Pengaduan Late </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                       

                    </div> --}}
                </div>

            </div>
        </div>


    @stop

    @section('script')

        <script type="text/javascript">
            $("#tahun").datepicker({
                format: "yyyy",
                viewMode: "years",
                minViewMode: "years"
            });
        </script>

        @include('pages.dashboard.chart')

    @stop
