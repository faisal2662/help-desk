@extends('template')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"> <button class="btn btn-warning btn-sm" id="kembali"><i
                                class='bx bx-arrow-back'></i> Kembali</button> <b><i class="bx bx-search-alt-2"></i> Detail Kantor
                            Pusat</b></div>
                    <hr style="border-style: dashed;">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Nama Kantor Pusat</strong></p>
                                </div>
                                <div class="col-7"> {{ $kantor_pusat->nama_kantor_pusat }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Kode Kantor Pusat</strong></p>
                                </div>
                                <div class="col-7"> {{ $kantor_pusat->kode_kantor_pusat }} </div>
                            </div>
                        </div>
                        <div class="col-md-6" align="center">
                            <img src="<?= url('logos/edit-helpdesk.png') ?>" style="max-width: 100%;">
                        </div>
                    </div>
                    <hr style="border-style: dashed;">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mb-3">
                                <div class="col"> <span><strong>Created By :</strong>
                                        {{ $kantor_pusat->created_by ? $kantor_pusat->created_by : '-' }}
                                    </span> </div>
                                <div class="col"> <span><strong>Updated By :</strong>
                                        {{ $kantor_pusat->update_by ? $kantor_pusat->update_by : '-' }}
                                    </span> </div>
                                <div class="col"> <span><strong>Deleted By :</strong>
                                        {{ $kantor_pusat->delete_by ? $kantor_pusat->delete_by : '-' }}
                                    </span> </div>
                            </div>
                            <div class="row">

                                <div class="col">

                                    <span><strong>Created Date :</strong>
                                        {{ $kantor_pusat->created_date ? \Carbon\Carbon::parse($kantor_pusat->created_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>
                                <div class="col">

                                    <span><strong>Updated Date :</strong>
                                        {{ $kantor_pusat->update_date ? \Carbon\Carbon::parse($kantor_pusat->update_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>
                                <div class="col">

                                    <span><strong>Deleted Date :</strong>
                                        {{ $kantor_pusat->delete_date ? \Carbon\Carbon::parse($kantor_pusat->delete_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

@stop

@section('script')

    <script type="text/javascript">
        $('#kembali').on('click', function() {
            loadPage('<?= route('kantor_pusat') ?>');
        });
    </script>

@stop
