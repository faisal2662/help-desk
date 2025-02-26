@extends('template')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"> <button class="btn btn-warning btn-sm" id="kembali"><i
                                class='bx bx-arrow-back'></i> Kembali</button> <b><i class="bx bx-search-alt-2"></i> Detail Pegawai</b></div>
                    <hr style="border-style: dashed;">

                    <div class="row">

                        <div class="col-md-8">
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>NPP Pegawai</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->employee_id }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Nama Pegawai</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->employee_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Kantor Pegawai</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->branch_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Tanggal Lahir</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->birthday }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Jenis Kelamin</strong></p>
                                </div>
                                @if ($pegawai->gender == 'L')

                                <div class="col-7">Laki - Laki </div>
                                @elseif($pegawai->gender == 'P')
                                <div class="col-7">Perempuan </div>
                                @endif
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>No. Telpon</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->primary_phone }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Email</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->email }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Alamat</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->primary_address }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Jabatan</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->position_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Status</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->employee_status }} </div>
                            </div>

                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Divisi</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->division_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Departemen</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->department_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Seksi</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->section_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Manajemen</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->management_name }} </div>
                            </div>
                            <div class="row ">
                                <div class="col-5">
                                    <p><strong>Fungsional</strong></p>
                                </div>
                                <div class="col-7"> {{ $pegawai->functional_name }} </div>
                            </div>
                        </div>
                        <div class="col-md-4" align="center">
                            <img src="<?= url('logos/edit-helpdesk.png') ?>" style="max-width: 100%;">
                        </div>
                    </div>
                    <hr style="border-style: dashed;">
                    <div class="row">
                        <div class="col-12">
                            <div class="row mb-3">
                                <div class="col"> <span><strong>Created By :</strong>
                                        {{ $pegawai->created_by ? $pegawai->created_by : '-' }}
                                    </span> </div>
                                <div class="col"> <span><strong>Updated By :</strong>
                                        {{ $pegawai->updated_by ? $pegawai->updated_by : '-' }}
                                    </span> </div>
                                <div class="col"> <span><strong>Deleted By :</strong>
                                        {{ $pegawai->deleted_by ? $pegawai->deleted_by : '-' }}
                                    </span> </div>
                            </div>
                            <div class="row">

                                <div class="col">

                                    <span><strong>Created Date :</strong>
                                        {{ $pegawai->created_date ? \Carbon\Carbon::parse($pegawai->created_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>
                                <div class="col">

                                    <span><strong>Updated Date :</strong>
                                        {{ $pegawai->updated_date ? \Carbon\Carbon::parse($pegawai->updated_date)->translatedFormat('l, d F Y') : '-' }}</span>

                                </div>
                                <div class="col">

                                    <span><strong>Deleted Date :</strong>
                                        {{ $pegawai->deleted_date ? \Carbon\Carbon::parse($pegawai->deleted_date)->translatedFormat('l, d F Y') : '-' }}</span>

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
            loadPage("<?= route('pegawai-sunfish') ?>");
        });
    </script>

@stop
