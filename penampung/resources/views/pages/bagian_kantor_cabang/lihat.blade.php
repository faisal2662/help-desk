@extends('template')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"> <button class="btn btn-warning btn-sm" id="kembali"><i
                                class='bx bx-arrow-back'></i> Kembali</button> <b><i class="bx bx-search-alt-2"></i> Detail Bagian Kantor
                            cabang</b></div>
                    <hr style="border-style: dashed;">

                    <div class="row">

                        <div class="col-md-7">
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Nama Kantor cabang</strong></p>
                                </div>
                                <div class="col-7"> {{ $bagian_kantor_cabang->nama_kantor_cabang }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Bagian  Kantor cabang</strong></p>
                                </div>
                                <div class="col-7"> {{ $bagian_kantor_cabang->nama_kantor_cabang }} </div>
                            </div>
                        </div>
                        <div class="col-md-5" align="center">
                            <img src="<?= url('logos/edit-helpdesk.png') ?>" style="max-width: 100%;">
                        </div>
                    </div>
                    <hr style="border-style: dashed;">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mb-3">
                                <div class="col"> <span><strong>Created By :</strong>
                                        {{ $bagian_kantor_cabang->created_by ? $bagian_kantor_cabang->created_by : '-' }}
                                    </span> </div>
                                <div class="col"> <span><strong>Updated By :</strong>
                                        {{ $bagian_kantor_cabang->update_by ? $bagian_kantor_cabang->update_by : '-' }}
                                    </span> </div>
                                <div class="col"> <span><strong>Deleted By :</strong>
                                        {{ $bagian_kantor_cabang->delete_by ? $bagian_kantor_cabang->delete_by : '-' }}
                                    </span> </div>
                            </div>
                            <div class="row">

                                <div class="col">

                                    <span><strong>Created Date :</strong>
                                        {{ $bagian_kantor_cabang->created_date ? \Carbon\Carbon::parse($bagian_kantor_cabang->created_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>
                                <div class="col">

                                    <span><strong>Updated Date :</strong>
                                        {{ $bagian_kantor_cabang->update_date ? \Carbon\Carbon::parse($bagian_kantor_cabang->update_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>
                                <div class="col">

                                    <span><strong>Deleted Date :</strong>
                                        {{ $bagian_kantor_cabang->delete_date ? \Carbon\Carbon::parse($bagian_kantor_cabang->delete_date)->translatedFormat('l, d F Y') : '-' }}</span>

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
            loadPage('<?= route('bagian_kantor_cabang') ?>');
        });
    </script>

@stop
