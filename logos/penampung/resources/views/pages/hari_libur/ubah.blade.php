@extends('template')

@section('title')
    Ubah Hari Libur | Helpdesk
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-plus'></i> Ubah Hari Libur</b></div>
                    <hr style="border-style: dashed;">

                    <div class="row">
                        <div class="col-md-6" align="center">
                            <img src="<?= url('logos/add.png') ?>" style="max-width: 100%;">
                        </div>

                        <div class="col-md-6">
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
                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                                action="<?= route('hari_libur.edit', $hari_libur->id_hari_libur) ?>">
                                <?= csrf_field() ?>


                                <div class="mb-3">
                                    <label>Tanggal</label>
                                    <input type="date" value="{{$hari_libur->tanggal}}" name="tanggal" class="form-control" required=""
                                        placeholder="Harap di isi ...">
                                </div>
                                <div class="mb-3">
                                    <label>Nama</label>
                                    <input type="text" name="nama" value="{{$hari_libur->nama_hari_libur}}" class="form-control" required="" maxlength="150"
                                        placeholder="Harap di isi ...">
                                </div>
                                <div class="mb-3">
                                    <label>Keterangan</label>
                                    <textarea name="keterangan"  class="form-control" id="keterangan" cols="30" rows="3">{{$hari_libur->keterangan_hari_libur}}</textarea>
                                </div>

                                <button type="button" class="btn btn-sm btn-warning" id="kembali">
                                    <i class='bx bx-arrow-back'></i> Kembali
                                </button>

                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class='bx bx-check-double'></i> Selesai
                                </button>

                            </form>
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
            loadPage('<?= route('hari_libur') ?>');
        });
    </script>

@stop
