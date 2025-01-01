@extends('template')

@section('title')
    Pengaduan - Helpdesk
@stop
{{-- @php
    dd($_GET['update']);
@endphp --}}
<?php if(isset($_GET['create'])){ ?>

@include('pages.pengaduan.tambah')

<?php }else if(isset($_GET['update'])){ ?>

@include('pages.pengaduan.ubah')

<?php }else if(isset($_GET['view'])){ ?>

@include('pages.pengaduan.lihat')

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
            <h4>
                <i class='bx bxs-coupon'></i> Pengaduan - <?= str_replace('Holding', 'Pengaduan SLA', $_GET['filter']) ?>
            </h4>

            <?php if($_GET['filter'] == 'Semua'){ ?>

            <p>
                <?php echo htmlspecialchars_decode($input); ?>
            </p>

            <?php } ?>

            <p>&nbsp;</p>
        </div>
    </div>

    <div id="data_pagination">
        <!-- data pagination -->
        {{-- <div class="row">

             @foreach ($pengaduan as $key => $item)
                 <div class="col-md-6">
                     <div class="card">
                         <div class="card-body">
                             <div class="card-title">
                                 <b>
                                     <img src="<?= url($session_pegawai->foto_pegawai) ?>"
                                         style="width: 20px;height: 20px;border-radius: 100%;">
                                     <?= htmlspecialchars($session_pegawai->nama_pegawai) ?>
                                 </b>
                             </div>
                             <p>
                                 @if (!is_null($pegawai->id_bagian_kantor_pusat))

                                 Unit Kerja : {{ $kantor->headOffice->nama_kantor_pusat}}
                                 Unit Bagian Kerja : {{ $kantor->nama_bagian_kantor_pusat }} <br>
                                 @elseif(!is_null($pegawai->id_bagian_kantor_cabang != null))
                                 Unit Kerja : {{ $kantor->branchOffice->nama_kantor_cabang}}
                                 Unit Bagian Kerja : {{ $kantor->nama_bagian_kantor_cabang }} <br>

                                 @else
                                 Unit Kerja : {{ $kantor->regionalOffice->nama_kantor_wilayah}}
                                 Unit Bagian Kerja : {{ $kantor->nama_bagian_kantor_wilayah }} <br>
                                 @endif
                             </p>
                             <hr style="border-style: dashed;">
                             <p>
                                 Kode Pengaduan :
                                 P<?= date('y') ?>-0000{{$item->id_pengaduan}}
                             </p>
                             <p>
                                 @if (auth()->user()->id_bagian_kantor_pusat != null)


                                 Kepada : <?= htmlspecialchars($kantor->nama_bagian_kantor_pusat) ?> - <?= htmlspecialchars($kantor->headOffice->nama_kantor_pusat) ?>
                                 @elseif(auth()->user()->id_bagian_kantor_cabang != null)

                                 Kepada : <?= htmlspecialchars($kantor->nama_bagian_kantor_cabang) ?> - <?= htmlspecialchars($kantor->branchOffice->nama_kantor_cabang) ?>
                                 @else
                                 Kepada : <?= htmlspecialchars($kantor->nama_bagian_kantor_wilayah) ?> - <?= htmlspecialchars($kantor->regionalOffice->nama_kantor_wilayah) ?>

                                 @endif
                             </p>
                             {{-- <p>
                                 <a href="?filter=<?= $_GET['filter'] ?>&view=<?= $item->id_pengaduan ?>"
                                     class="text-info">
                                     <b><i class='bx bxs-coupon'></i>
                                         <?= htmlspecialchars($item->nama_pengaduan) ?></b>
                                 </a>
                             </p>
                             <p>
                                 Deskripsi : <br>
                                 <?= $item->keterangan_pengaduan ?>
                             </p>
                             @if ($item->klasifikasi_pengaduan)

                             @else
                                 <p>
                                     Klasifikasi : -</b>
                                 </p>
                             @endif


                             <p>
                                 Status :
                                 <span class="badge badge-<?= $status_pengaduan[$item->status_pengaduan] ?>">


                                 </span>
                             </p>
                             <p>
                                 {{-- <i class='bx bx-time'></i> {{ time_elapsed_string($item->tgl_pengaduan) }} --}}
        {{-- </p> --}}




        {{--
                         </div>
                     </div>
                     <p>&nbsp;</p>
                 </div>
             @endforeach
         </div>

         <div class="row">
	  <div class="col-md-12">
		  <div class="table-responsive">
			{{ $pengaduan->links() }}
			<p>&nbsp;</p>
		  </div>
	  </div>  --}}
    </div>

    {{-- <div class="row">
            <div class="col-md-6">
				<div class="card">
				  <div class="card-body">
					<div class="card-title">
						<b>
							<img src="" style="width: 20px;height: 20px;border-radius: 100%;">
						</b>
					</div>
					<p>
						Unit Kerja :
					</p>
                    <p>

						Unit Bagian Kerja :
                    </p>
					<hr style="border-style: dashed;">
					<p>
						Kode Pengaduan :

					</p>
					<p>
						Kepada :
					</p>
					<p>
						<a href="" class="text-info">
							<b><i class='bx bxs-coupon' ></i> </b>
						</a>
					</p>
					<p>
						Deskripsi : <br>

					</p>

					<p>
                        Klasifikasi : <b class="text-"></b>
					</p>



					<p>
						Status :
						<span class="badge bg-primary">



						</span>
					</p>
					<p>
						<i class='bx bx-time' ></i>
					</p>



				  </div>
				</div>
				<p>&nbsp;</p>
			</div>
          </div> --}}
    {{-- </div> --}}
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
                    '<?= route('pengaduan.data_grid') ?>?filter=<?= $_GET['filter'] ?>&search=<?= htmlspecialchars($_GET['search']) ?>&page=' +
                    page;
                <?php } else { ?>
                url = '<?= route('pengaduan.data_grid') ?>?filter=<?= $_GET['filter'] ?>&page=' + page;
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

                    <?php
                    $session_pegawai = DB::table('tb_pegawai')
                        ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', auth()->user()->id_pegawai]])
                        ->get();

                    if ($session_pegawai->count() > 0) {
                        foreach ($session_pegawai as $data_session_pegawai);

                        if ($data_session_pegawai->level_pegawai == 'Administrator') {
                            if ($_GET['filter'] == 'Semua') {
                                $pengaduan = DB::table('tb_pengaduan')->where('tb_pengaduan.delete_pengaduan', '=', 'N')->orderBy('tb_pengaduan.id_pengaduan', 'DESC')->get();
                            } else {
                                $pengaduan = DB::table('tb_pengaduan')
                                    ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    ->get();
                            }
                        } elseif ($data_session_pegawai->level_pegawai == 'Staff') {
                            if ($_GET['filter'] == 'Semua') {
                                if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_pusat', 'tb_pengaduan.id_bagian_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_bagian_kantor_pusat')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_cabang', 'tb_pengaduan.id_bagian_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_bagian_kantor_cabang')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } else {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_wilayah', 'tb_pengaduan.id_bagian_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                }
                            } else {
                                if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_pusat', 'tb_pengaduan.id_from_bagian', '=', 'tb_bagian_kantor_pusat.id_bagian_kantor_pusat')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_cabang', 'tb_pengaduan.id_from_bagian', '=', 'tb_bagian_kantor_cabang.id_bagian_kantor_cabang')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } else {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_wilayah', 'tb_pengaduan.id_from_bagian', '=', 'tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                }
                            }
                        } elseif ($data_session_pegawai->level_pegawai == 'Kepala Bagian Unit Kerja') {
                            if ($_GET['filter'] == 'Semua') {
                                if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_pusat', 'tb_pengaduan.id_bagian_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_bagian_kantor_pusat')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_cabang', 'tb_pengaduan.id_bagian_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_bagian_kantor_cabang')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } else {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_wilayah', 'tb_pengaduan.id_bagian_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                }
                                // $pengaduan = DB::table('tb_pengaduan')
                                //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                                //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                //     ->get();
                            } else {
                                if ($data_session_pegawai->id_bagian_kantor_pusat != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_pusat', 'tb_pengaduan.id_bagian_kantor_pusat', '=', 'tb_bagian_kantor_pusat.id_bagian_kantor_pusat')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } elseif ($data_session_pegawai->id_bagian_kantor_cabang != 0) {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_cabang', 'tb_pengaduan.id_bagian_kantor_cabang', '=', 'tb_bagian_kantor_cabang.id_bagian_kantor_cabang')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } else {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->join('tb_bagian_kantor_wilayah', 'tb_pengaduan.id_bagian_kantor_wilayah', '=', 'tb_bagian_kantor_wilayah.id_bagian_kantor_wilayah')
                                        ->where('tb_pengaduan.id_from_bagian', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                }
                                // $pengaduan = DB::table('tb_pengaduan')
                                //     ->where('tb_pengaduan.kantor_pengaduan', '=', $data_session_pegawai->kantor_pegawai)
                                //     ->where('tb_pengaduan.id_bagian_kantor_pusat', '=', $data_session_pegawai->id_bagian_kantor_pusat)
                                //     ->where('tb_pengaduan.id_bagian_kantor_cabang', '=', $data_session_pegawai->id_bagian_kantor_cabang)
                                //     ->where('tb_pengaduan.id_bagian_kantor_wilayah', '=', $data_session_pegawai->id_bagian_kantor_wilayah)
                                //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                //     ->get();
                            }
                        } elseif ($data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
                            // if ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Kepala Unit Kerja') {
                            $unit_kerja = DB::table('tb_kepala_unit_kerja')
                                ->where([['tb_kepala_unit_kerja.id_pegawai', $data_session_pegawai->id_pegawai], ['tb_kepala_unit_kerja.delete_kepala_unit_kerja', 'N']])
                                ->orderBy('tb_kepala_unit_kerja.id_kepala_unit_kerja', 'ASC')
                                ->limit(1)
                                ->get();
                            foreach ($unit_kerja as $uker);
                            if ($unit_kerja->count() < 1) {
                                if ($_GET['filter'] == 'Semua') {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->whereRaw(
                                            '
                                                                                												tb_pengaduan.id_pegawai IN (
                                                                                													Select
                                                                                														tb_pegawai.id_pegawai
                                                                                													From
                                                                                														tb_pegawai
                                                                                													Where
                                                                                														tb_pegawai.kantor_pegawai = "' .
                                                $data_session_pegawai->kantor_pegawai .
                                                '" And
                                                                                														tb_pegawai.id_bagian_kantor_pusat = "' .
                                                $data_session_pegawai->id_bagian_kantor_pusat .
                                                '" And
                                                                                														tb_pegawai.id_bagian_kantor_cabang = "' .
                                                $data_session_pegawai->id_bagian_kantor_cabang .
                                                '" And
                                                                                														tb_pegawai.id_bagian_kantor_wilayah = "' .
                                                $data_session_pegawai->id_bagian_kantor_wilayah .
                                                '"
                                                                                												)
                                                                                											',
                                        )
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } else {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->whereRaw(
                                            '
                                                                                												tb_pengaduan.id_pegawai IN (
                                                                                													Select
                                                                                														tb_pegawai.id_pegawai
                                                                                													From
                                                                                														tb_pegawai
                                                                                													Where
                                                                                														tb_pegawai.kantor_pegawai = "' .
                                                $data_session_pegawai->kantor_pegawai .
                                                '" And
                                                                                														tb_pegawai.id_bagian_kantor_pusat = "' .
                                                $data_session_pegawai->id_bagian_kantor_pusat .
                                                '" And
                                                                                														tb_pegawai.id_bagian_kantor_cabang = "' .
                                                $data_session_pegawai->id_bagian_kantor_cabang .
                                                '" And
                                                                                														tb_pegawai.id_bagian_kantor_wilayah = "' .
                                                $data_session_pegawai->id_bagian_kantor_wilayah .
                                                '"
                                                                                												)
                                                                                											',
                                        )
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                        ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                }
                            } else {
                                if ($_GET['filter'] == 'Semua') {
                                    $pengaduan = DB::table('tb_pengaduan')
                                        ->whereRaw(
                                            '
                                                                                												tb_pengaduan.id_pegawai IN (
                                                                                													Select
                                                                                														tb_pegawai.id_pegawai
                                                                                													From
                                                                                														tb_pegawai
                                                                                													Where
                                                                                														tb_pegawai.kantor_pegawai IN (
                                                                                															SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                                                                                															delete_kepala_unit_kerja = "N" And
                                                                                															id_pegawai = "' .
                                                $data_session_pegawai->id_pegawai .
                                                '"
                                                                                														) And
                                                                                														tb_pegawai.id_bagian_kantor_pusat IN (
                                                                                															SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                                                                															delete_kepala_unit_kerja = "N" And
                                                                                															id_pegawai = "' .
                                                $data_session_pegawai->id_pegawai .
                                                '"
                                                                                														) And
                                                                                														tb_pegawai.id_bagian_kantor_cabang IN (
                                                                                															SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                                                                															delete_kepala_unit_kerja = "N" And
                                                                                															id_pegawai = "' .
                                                $data_session_pegawai->id_pegawai .
                                                '"
                                                                                														) And
                                                                                														tb_pegawai.id_bagian_kantor_wilayah IN (
                                                                                															SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                                                                															delete_kepala_unit_kerja = "N" And
                                                                                															id_pegawai = "' .
                                                $data_session_pegawai->id_pegawai .
                                                '"
                                                                                														)
                                                                                												)
                                                                                											',
                                        )
                                        ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                        ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                        ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                        ->get();
                                } else {
                                    if ($uker->id_bagian_kantor_pusat != 0) {
                                        $pengaduan = DB::table('tb_pengaduan')
                                            ->where('id_from_bagian', '=', $uker->id_bagian_kantor_pusat)
                                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                            ->get();
                                    } elseif ($uker->id_bagian_kantor_cabang != 0) {
                                        $pengaduan = DB::table('tb_pengaduan')
                                            ->where('id_from_bagian', '=', $uker->id_bagian_kantor_cabang)
                                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                            ->get();
                                    } elseif ($uker->id_bagian_kantor_wilayah != 0) {
                                        $pengaduan = DB::table('tb_pengaduan')
                                            ->where('id_from_bagian', '=', $uker->id_bagian_kantor_wilayah)
                                            ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                            ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                            ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                            ->get();
                                    }
                                    // $pengaduan = DB::table('tb_pengaduan')
                                    //     ->whereRaw(
                                    //         'tb_pengaduan.id_pegawai IN (
                                    //            Select
                                    //          tb_pegawai.id_pegawai
                                    //          From
                                    //                   tb_pegawai
                                    //                   Where
                                    //                  tb_pegawai.kantor_pegawai IN (
                                    //                  SELECT kantor_pegawai FROM tb_kepala_unit_kerja WHERE
                                    //                    delete_kepala_unit_kerja = "N" And
                                    //                      id_pegawai = "' .
                                    //             $data_session_pegawai->id_pegawai .
                                    //             '"
                                    //                 ) And
                                    //                 tb_pegawai.id_bagian_kantor_pusat IN (
                                    //                SELECT id_bagian_kantor_pusat FROM tb_kepala_unit_kerja WHERE
                                    //                 delete_kepala_unit_kerja = "N" And
                                    //                   id_pegawai = "' .
                                    //             $data_session_pegawai->id_pegawai .
                                    //             '"
                                    //                  ) And
                                    //                    tb_pegawai.id_bagian_kantor_cabang IN (SELECT id_bagian_kantor_cabang FROM tb_kepala_unit_kerja WHERE
                                    //                         		delete_kepala_unit_kerja = "N" And
                                    //                 id_pegawai = "' .
                                    //             $data_session_pegawai->id_pegawai .
                                    //             '"
                                    //       ) And
                                    //           tb_pegawai.id_bagian_kantor_wilayah IN (
                                    //             	SELECT id_bagian_kantor_wilayah FROM tb_kepala_unit_kerja WHERE
                                    //                  delete_kepala_unit_kerja = "N" And
                                    //                    	id_pegawai = "' .
                                    //             $data_session_pegawai->id_pegawai .
                                    //             '"
                                    //               	)
                                    // 	)
                                    //                         											',
                                    //     )
                                    //     ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                                    //     ->where('tb_pengaduan.status_pengaduan', '!=', 'Pending')
                                    //     ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                                    //     ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                                    //     ->get();
                                }
                            }
                            // } else {
                            //     $pengaduan = null;
                            //     if ($_GET['filter'] == 'Semua') {
                            //         $pengaduan = DB::table('tb_pengaduan')
                            //             ->whereRaw(
                            //                 '
                            // 									tb_pengaduan.id_pegawai IN (
                            // 										Select
                            // 											tb_pegawai.id_pegawai
                            // 										From
                            // 											tb_pegawai
                            // 										Where
                            // 											tb_pegawai.kantor_pegawai = "' .
                            //                     $data_session_pegawai->kantor_pegawai .
                            //                     '" And
                            // 											tb_pegawai.id_bagian_kantor_pusat = "' .
                            //                     $data_session_pegawai->id_bagian_kantor_pusat .
                            //                     '" And
                            // 											tb_pegawai.id_bagian_kantor_cabang = "' .
                            //                     $data_session_pegawai->id_bagian_kantor_cabang .
                            //                     '" And
                            // 											tb_pegawai.id_bagian_kantor_wilayah = "' .
                            //                     $data_session_pegawai->id_bagian_kantor_wilayah .
                            //                     '"
                            // 									)
                            // 								',
                            //             )
                            //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //             ->get();
                            //     } else {
                            //         $pengaduan = DB::table('tb_pengaduan')
                            //             ->whereRaw(
                            //                 '
                            // 									tb_pengaduan.id_pegawai IN (
                            // 										Select
                            // 											tb_pegawai.id_pegawai
                            // 										From
                            // 											tb_pegawai
                            // 										Where
                            // 											tb_pegawai.kantor_pegawai = "' .
                            //                     $data_session_pegawai->kantor_pegawai .
                            //                     '" And
                            // 											tb_pegawai.id_bagian_kantor_pusat = "' .
                            //                     $data_session_pegawai->id_bagian_kantor_pusat .
                            //                     '" And
                            // 											tb_pegawai.id_bagian_kantor_cabang = "' .
                            //                     $data_session_pegawai->id_bagian_kantor_cabang .
                            //                     '" And
                            // 											tb_pegawai.id_bagian_kantor_wilayah = "' .
                            //                     $data_session_pegawai->id_bagian_kantor_wilayah .
                            //                     '"
                            // 									)
                            // 								',
                            //             )
                            //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                            //             ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                            //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                            //             ->get();
                            //     }
                            // }
                        }
                        //  elseif ($data_session_pegawai->sebagai_pegawai == 'Mitra/Pelanggan' && $data_session_pegawai->level_pegawai == 'Staff') {
                        //     if ($_GET['filter'] == 'Semua') {
                        //         $pengaduan = DB::table('tb_pengaduan')
                        //             ->where('tb_pengaduan.id_pegawai', '=', $data_session_pegawai->id_pegawai)
                        //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //             ->get();
                        //     } else {
                        //         $pengaduan = DB::table('tb_pengaduan')
                        //             ->where('tb_pengaduan.id_pegawai', '=', $data_session_pegawai->id_pegawai)
                        //             ->where('tb_pengaduan.delete_pengaduan', '=', 'N')
                        //             ->where('tb_pengaduan.status_pengaduan', '=', $_GET['filter'])
                        //             ->orderBy('tb_pengaduan.id_pengaduan', 'DESC')
                        //             ->get();
                        //     }
                        // }
                    }
                    ?>

                    <?php if($pengaduan->count() < 1){ ?>

                    <center>
                        <img src="<?= url('logos/empty.png') ?>" style="width: 170px;">
                        <p>Data saat ini tidak ditemukan.</p>
                    </center>

                    <?php }else{ ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
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

                        <form method="POST" onsubmit="show(true)" id="form-delete"
                            action="<?= route('pengaduan.delete') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="delete" id="input-delete" readonly="" required="">
                        </form>

                        <form method="POST" onsubmit="show(true)" id="form-approve"
                            action="<?= route('pengaduan.approve') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="pengaduan" id="input-approve" readonly="" required="">
                        </form>

                        <form method="POST" onsubmit="show(true)" id="form-checked"
                            action="<?= route('pengaduan.checked') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="pengaduan" id="input-checked" readonly="" required="">
                        </form>

                        <form method="POST" onsubmit="show(true)" id="form-finish"
                            action="<?= route('pengaduan.finish') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="pengaduan" id="input-finish" readonly="" required="">
                        </form>
                    </span>

                    <script type="text/javascript">
                        function delete_data(id, name) {
                            var r = confirm('Hapus data ' + name + '?');
                            if (r == true) {
                                show(true);
                                document.getElementById('input-delete').value = id;
                                document.getElementById('form-delete').submit();
                            }
                        }

                        function approve_data(id, name) {
                            var r = confirm('Approve pengaduan ' + name + '?');
                            if (r == true) {
                                show(true);
                                document.getElementById('input-approve').value = id;
                                document.getElementById('form-approve').submit();
                            }
                        }

                        function checked_data(id, name) {
                            var r = confirm('Checked pengaduan ' + name + '?');
                            if (r == true) {
                                show(true);
                                document.getElementById('input-checked').value = id;
                                document.getElementById('form-checked').submit();
                            }
                        }

                        function update_data(id) {
                            show(true);
                            document.getElementById('input-update').value = id;
                            document.getElementById('form-update').submit();
                        }

                        function finish_data(id, name) {
                            var r = confirm('Selesaikan pengaduan ' + name + '?');
                            if (r == true) {
                                show(true);
                                document.getElementById('input-finish').value = id;
                                document.getElementById('form-finish').submit();
                            }
                        }
                    </script>

                    <?php } ?>

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
    <script>
        async function klasifikasi_data(id, name) {
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

@stop
<?php } ?>
