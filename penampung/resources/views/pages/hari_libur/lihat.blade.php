@extends('template')
@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
			<div class="card">
			  <div class="card-body">
				<div class="card-title"> <button  class="btn btn-warning btn-sm" id="kembali"><i class='bx bx-arrow-back'></i> Kembali</button> &nbsp;  <b><i class='bx bx-search-alt-2' ></i> Detail Hari Libur</b></div>
				<hr style="border-style: dashed;">

				<div class="row">

                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-4"><p><strong>Hari Libur </strong></p></div>
                            <div class="col-8"> {{$hari_libur->nama_hari_libur}} </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><p><strong>Nama Jabatan </strong></p></div>
                            <div class="col-8"> {{$hari_libur->keterangan_hari_libu}} </div>
                        </div>
                        <div class="row ">
                            <div class="col-4"><p><strong>Tanggal Hari Libur</strong></p></div>
                            <div class="col-8"> {{ \Carbon\Carbon::parse($hari_libur->tanggal)->translatedFormat('l, d F Y')}} </div>
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
                                    {{ $hari_libur->created_by ? $hari_libur->created_by : '-' }}
                                </span> </div>
                            <div class="col"> <span><strong>Updated By :</strong>
                                    {{ $hari_libur->updated_by ? $hari_libur->updated_by : '-' }}
                                </span> </div>
                            <div class="col"> <span><strong>Deleted By :</strong>
                                    {{ $hari_libur->deleted_by ? $hari_libur->deleted_by : '-' }}
                                </span> </div>
                        </div>
                        <div class="row">

                            <div class="col">

                                <span><strong>Created Date :</strong>
                                    {{ $hari_libur->created_date ? \Carbon\Carbon::parse($hari_libur->created_date)->translatedFormat('l, d F Y') : '-' }}</span>

                            </div>
                            <div class="col">

                                <span><strong>Updated Date :</strong>
                                    {{ $hari_libur->updated_date ? \Carbon\Carbon::parse($hari_libur->updated_date)->translatedFormat('l, d F Y') : '-' }}</span>

                            </div>
                            <div class="col">

                                <span><strong>Deleted Date :</strong>
                                    {{ $hari_libur->deleted_date ? \Carbon\Carbon::parse($hari_libur->deleted_date)->translatedFormat('l, d F Y') : '-' }}</span>

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
		loadPage('<?= route('nama_jabatan') ?>');
	  });
	</script>

@stop
