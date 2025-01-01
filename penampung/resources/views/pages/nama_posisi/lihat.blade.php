@extends('template')
@section('content')

	<div class="row">
		<div class="col-md-12">
			<p>&nbsp;</p>
			<div class="card">
			  <div class="card-body">
				<div class="card-title"> <button  class="btn btn-warning btn-sm" id="kembali"><i class='bx bx-arrow-back'></i> Kembali</button> &nbsp;  <b><i class='bx bx-search-alt-2' ></i> Detail Nama Posisi</b></div>
				<hr style="border-style: dashed;">

				<div class="row">

                    <div class="col-md-8">
                        <div class="row mb-3">
                            <div class="col-4"><p><strong>Kode Jabatan </strong></p></div>
                            <div class="col-8"> {{$namaPosisi->kode_posisi}} </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-4"><p><strong>Nama Jabatan </strong></p></div>
                            <div class="col-8"> {{$namaPosisi->nama_posisi}} </div>
                        </div>
                        <div class="row ">
                            <div class="col-4"><p><strong>Sebagai Posisi</strong></p></div>
                            <div class="col-8"> {{$namaPosisi->sebagai_posisi}} </div>
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
                                    {{ $namaPosisi->created_by ? $namaPosisi->created_by : '-' }}
                                </span> </div>
                            <div class="col"> <span><strong>Updated By :</strong>
                                    {{ $namaPosisi->updated_by ? $namaPosisi->updated_by : '-' }}
                                </span> </div>
                            <div class="col"> <span><strong>Deleted By :</strong>
                                    {{ $namaPosisi->deleted_by ? $namaPosisi->deleted_by : '-' }}
                                </span> </div>
                        </div>
                        <div class="row">

                            <div class="col">

                                <span><strong>Created Date :</strong>
                                    {{ $namaPosisi->created_date ? \Carbon\Carbon::parse($namaPosisi->created_date)->translatedFormat('l, d F Y') : '-' }}</span>

                            </div>
                            <div class="col">

                                <span><strong>Updated Date :</strong>
                                    {{ $namaPosisi->updated_date ? \Carbon\Carbon::parse($namaPosisi->updated_date)->translatedFormat('l, d F Y') : '-' }}</span>

                            </div>
                            <div class="col">

                                <span><strong>Deleted Date :</strong>
                                    {{ $namaPosisi->deleted_date ? \Carbon\Carbon::parse($namaPosisi->deleted_date)->translatedFormat('l, d F Y') : '-' }}</span>

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
