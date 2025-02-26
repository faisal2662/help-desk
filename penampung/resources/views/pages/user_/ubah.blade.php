<?php

if ($pegawai->count() < 1) {
    header('Location: ' . route('pelanggan'));
    exit();
} else {
    // foreach ($pegawai as $pegawai);
    $pegawai = $pegawai;
    $id_kantor = 0;
    $id_bagian = 0;
    $class_kantor = '-';
    $nama_kantor = '-';
    $nama_bagian = '-';
    $class_bagian = '-';

}
?>
@extends('template')
@section('title')
    Ubah - User
@stop
@section('content')

        <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-edit'></i> Perbarui User</b></div>
                    <hr style="border-style: dashed;">

                    <div class="row">
                        <div class="col-md-6" align="center">
                            <img src="<?= url('logos/edit.png') ?>" style="max-width: 100%;">
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
                                action="<?= route('pelanggan.update') ?>">
                                <?= csrf_field() ?>

                                <label>NPP</label>
                                <input type="number" name="npp" value="<?= htmlspecialchars($pegawai->npp_pegawai) ?>"
                                    class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= htmlspecialchars($pegawai->nama_pegawai) ?>"
                                    class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Jenis Kelamin</label><br>
                                <input type="radio" name="jenkel" value="Laki-laki"
                                    <?= $pegawai->jenkel_pegawai == 'Laki-laki' ? 'checked' : '' ?>> Laki-laki
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="jenkel" value="Perempuan"
                                    <?= $pegawai->jenkel_pegawai == 'Perempuan' ? 'checked' : '' ?>> Perempuan
                                <br><br>

                                <label>No.Telp</label>
                                <input type="number" name="telp" value="<?= htmlspecialchars($pegawai->telp_pegawai) ?>"
                                    class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Email</label>
                                <input type="email" name="email"
                                    value="<?= htmlspecialchars($pegawai->email_pegawai) ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Password Baru (Opsional)</label>
                                <input type="password" name="password" id="txt_password" class="form-control"
                                    maxlength="255" placeholder="Opsional ...">
                                <label for="" id="txt_label_password" class="text-danger"
                                    style="font-size: 12px;"></label>
                                <br>
                                <label>Unit Kerja</label>
                                <select name="kantor" id="unit_kerja" class="form-control" required="">
                                    <option value="" disabled selected>- Pilih salah satu --</option>
                                    @foreach ($unit_kerja as $kantor)
                                        @if ($kantor == 'Pusat')
                                            <optgroup label="Kantor Pusat">
                                                @foreach ($kantor_pusat as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_pusat }}"
                                                        @if ($id_unit_kerja == $kantor . ',' . $item->id_kantor_pusat) selected @endif>
                                                        {{ $kantor }} - {{ $item->nama_kantor_pusat }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @elseif($kantor == 'Cabang')
                                            <optgroup label="kantor Cabang">
                                                @foreach ($kantor_cabang as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_cabang }}"
                                                        @if ($id_unit_kerja == $kantor . ',' . $item->id_kantor_cabang) selected @endif>
                                                        {{ $kantor }} - {{ $item->nama_kantor_cabang }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @elseif($kantor == 'Wilayah')
                                            <optgroup label="Kantor Wilayah">
                                                @foreach ($kantor_wilayah as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_wilayah }}"
                                                        @if ($id_unit_kerja == $kantor . ',' . $item->id_kantor_wilayah) selected @endif>
                                                        {{ $kantor }} - {{ $item->nama_kantor_wilayah }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                                <br>

                                <label>Bagian Unit Kerja</label>
                                <select name="bagian" class="form-control bagian-unit-kerja" id="bagian_unit_kerja"
                                    required="">
                                    <option value="" disabled>- Pilih salah satu -</option>
                                    <option value="{{ $id_bagian }}">{{ $bagian_unit }} </option>
                                </select>
                                <br>


                                <label>Level</label>
                                <select name="level" id="level" class="form-control" required="">
                                    <option value="{{ $pegawai->NamaPosisi->sebagai_posisi }}"> {{$pegawai->NamaPosisi->sebagai_posisi}} </option>
                                </select>
                                <br>

                                <label>Status</label>
                                <select name="status" class="form-control" required="">
                                    <option value="" disabled>- Pilih Salah Satu -</option>
                                    <option value="Aktif" @if ($pegawai->status_pegawai == 'Aktif') selected @endif>Aktif</option>
                                    <option value="Tidak Aktif" @if ($pegawai->status_pegawai == 'Tidak Aktif') selected @endif>Tidak
                                        Aktif</option>
                                </select>
                                <br>

                                <label>Sebagai</label>
                                <select name="sebagai" class="form-control" required="">
                                    <option value="" disabled selected>- Pilih Salah Satu -</option>
                                    <option value="Staff" @if ($pegawai->sebagai_pegawai == 'Staff') selected @endif>Staff</option>
                                    {{-- @if (auth()->user()->sebagai_pegawai == 'Administator') --}}
                                    <option value="PIC" @if ($pegawai->sebagai_pegawai == 'PIC') selected @endif  >PIC</option>
                                    <option value="Administrator" @if ($pegawai->sebagai_pegawai == 'Administrator') selected @endif >Administrator</option>
                                    {{-- @endif --}}
                                </select>
                                <br>

                                <div class="form-group">
                                    <label>Unggah Foto (Opsional)</label>
                                    <br>
                                    <label for="file-1">
                                        <img src="<?= url($pegawai->foto_pegawai) ?>" id="image-1"
                                            style="width: 150px;border-radius: 5px;">
                                        <input type="file" accept="image/*" name="foto" id="file-1"
                                            class="form-control" onchange="previewImage('image-1','file-1')"
                                            style="display: none;">
                                    </label>
                                </div>

                                <button type="button" class="btn btn-sm btn-warning" id="kembali">
                                    <i class='bx bx-arrow-back'></i> Kembali
                                </button>

                                <button type="submit" name="update" value="{{ $pegawai->id_pegawai }}"
                                    class="btn btn-sm btn-primary">
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
            loadPage('<?= route('pelanggan') ?>');
        });

        $('#txt_password').on('keyup', function() {

            $('#txt_label_password').html("");

            var InputValue = $("#txt_password").val();
            var regex = new RegExp("^(?=.*[a-z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
            $("#passwordText").text(`Password value:- ${InputValue}`);


            if (!regex.test(InputValue)) {
                $('#txt_label_password').show();
                $('#txt_label_password').html("Minimal 8 digit dengan kombinasi huruf, angka dan karakter spesial");
                $('.btn-primary').attr("disabled", "disabled");
            } else {

                $('#txt_label_password').hide();
                $('.btn-primary').removeAttr('disabled');
            }

        });
    </script>

    {{-- <script>
        $('select[name="unit_kerja"] option').hide();
        $('select[name="bagian"] option').hide();

        <?php if($pegawai->multi_pegawai == 'Tidak'){ ?>

        // $('select[name="bagian"] .All').hide();
        $('select[name="level"] .Kepala_Unit_Kerja').hide();

        <?php }else{ ?>

        $('select[name="level"] option').hide();
        $('select[name="level"] .Kepala_Unit_Kerja').show();

        <?php } ?>

        <?php if($class_kantor == 'Kantor_Pusat'){ ?>

        $('select[name="unit_kerja"] .Kantor_Pusat').show();
        $('select[name="bagian"] .All').show();
        $('select[name="bagian"] .<?= $class_bagian ?>').show();

        <?php }else if($class_kantor == 'Kantor_Cabang'){ ?>

        $('select[name="unit_kerja"] .Kantor_Cabang').show();
        $('select[name="bagian"] .All').show();
        $('select[name="bagian"] .<?= $class_bagian ?>').show();

        <?php }else if($class_kantor == 'Kantor_Wilayah'){ ?>

        $('select[name="unit_kerja"] .Kantor_Wilayah').show();
        $('select[name="bagian"] .All').show();
        $('select[name="bagian"] .<?= $class_bagian ?>').show();

        <?php } ?>
    </script>

    <script>
        $('select[name="kantor"]').on('change', function() {
            if ($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Pusat') {
                $('select[name="unit_kerja"] option').hide();
                $('select[name="unit_kerja"] .Kantor_Pusat').show();
            } else if ($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Cabang') {
                $('select[name="unit_kerja"] option').hide();
                $('select[name="unit_kerja"] .Kantor_Cabang').show();
            } else if ($('select[name="kantor"] option').filter(':selected').val() == 'Kantor Wilayah') {
                $('select[name="unit_kerja"] option').hide();
                $('select[name="unit_kerja"] .Kantor_Wilayah').show();
            } else {
                $('select[name="unit_kerja"] option').show();
            }
        });

        $('select[name="unit_kerja"]').on('change', function() {
            var value = $('select[name="unit_kerja"] option').filter(':selected').val()
            $('select[name="bagian"] option').hide();
            $('select[name="bagian"] .' + value).show();
            $('select[name="bagian"] .All').show();
        });

        $('select[name="bagian"]').on('change', function() {
            if ($('select[name="bagian"] option').filter(':selected').val() == 'all') {
                $('select[name="level"] option').hide();
                $('select[name="level"] .Kepala_Unit_Kerja').show();
            } else {
                $('select[name="level"] option').hide();
                $('select[name="level"] option').show();
                $('select[name="level"] .Kepala_Unit_Kerja').hide();
            }
        });

    </script> --}}
    <script>
        $('#unit_kerja').select2({
            theme: 'bootstrap-5',
            placeholder: "- Pilih salah satu -",
        });

        $('.bagian-unit-kerja').select2({
            theme: 'bootstrap-5',
            placeholder: "- Pilih salah satu -",
        }).on('change', function() {
            let val = $(this).val();
            $('#level').empty();
            const level = ['Kepala Bagian Unit Kerja', 'Staff'];
            if (val == 0) {
                $('#level').append(`<option value="Kepala Unit Kerja" >Kepala Unit Kerja</option>`);
            }
            level.forEach(function(res) {
                $('#level').append(`<option value="${res}" >${res}</option>`);
            })

        });

        $('#unit_kerja').on('change', function() {
            let value = $(this).val();

            let result = value.split(',')
            let kantor = result[0]
            let id = result[1]
            $.post("{{ route('pengaduan.get-bagian-unit') }}", {
                kantor: kantor,
                id: id,
                _token: '{{ csrf_token() }}'
            }, function(data) {
                $('select[name="bagian"]').empty();
                $('#bagian_unit_kerja').append(
                    `<option value="0" >Semua Bagian</option>`
                )
                data.forEach(function(res) {
                    $('#bagian_unit_kerja').append(
                        `<option value="${res.id_bagian}" >${res.nama_bagian}</option>`
                    )
                })
                const level = ['Kepala Unit', 'Kepala Bagian Unit Kerja', 'Staff'];
                level.forEach(function(res) {
                    $('#level').append(`<option value="${res}" >${res}</option>`);
                })
            }).fail(function() {
                alert('error');
            })
        })
    </script>

@stop
