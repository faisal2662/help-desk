@extends('template')
@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-plus'></i> Ubah Nama Jabatan</b></div>
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
                            <form method="POST" enctype="multipart/form-data" onsubmit="show(true)"
                                action="<?= route('nama_jabatan.update', $namaPosisi->id_posisi_pegawai) ?>">
                                <?= csrf_field() ?>

                                <label>Kode Jabatan</label>
                                <input type="text" name="kode" class="form-control"
                                    value="{{ $namaPosisi->kode_posisi }}" required="" maxlength="255"
                                    placeholder="Harap di isi ...">
                                <br>
                                <label for="">Kantor Jabatan</label>
                                <select name="kantor" id="kantor" required class="form-control">
                                    <option value="Kantor Pusat" @if ($namaPosisi->level_pegawai == "Kantor Pusat") selected @endif>Kantor
                                        Pusat</option>
                                    <option value="Kantor Cabang" @if ($namaPosisi->level_pegawai == "Kantor Cabang") selected @endif>Kantor
                                        Cabang</option>
                                    <option value="Kantor Wilayah" @if ($namaPosisi->level_pegawai == "Kantor Wilayah") selected @endif>Kantor
                                        Wilayah</option>
                                </select>
                                <br>
                                <label>Nama Jabatan</label>
                                {{-- <input type="text" name="nama" class="form-control"
                                    value="{{ $namaPosisi->nama_posisi }}" required="" maxlength="255"
                                    placeholder="Harap di isi ..."> --}}
                                    <textarea name="nama" id="nama" class="form-control" required cols="3">{{ $namaPosisi->nama_posisi }}</textarea>
                                <br>

                                <label>Sebagai Jabatan</label>
                                <select name="sebagai" id="sebagai" required class="form-control">
                                    <option value="Staff" @if ($namaPosisi->sebagai_posisi == 'Staff') selected @endif>Staff</option>
                                    <option value="Kepala Bagian Unit Kerja"
                                        @if ($namaPosisi->sebagai_posisi == 'Kepala Bagian Unit Kerja') selected @endif>Kepala Bagian Unit Kerja</option>
                                    <option value="Kepala Unit Kerja" @if ($namaPosisi->sebagai_posisi == 'Kepala Unit Kerja') selected @endif>
                                        Kepala Unit Kerja</option>
                                </select>

                                <br>

                                <button type="button" class="btn btn-sm btn-warning" id="kembali">
                                    <i class='bx bx-arrow-back'></i> Kembali
                                </button>

                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class='bx bx-check-double'></i> Update
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
            loadPage('<?= route('nama_jabatan') ?>');
        });
    </script>

@stop
