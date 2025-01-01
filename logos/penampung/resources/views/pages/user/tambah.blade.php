@extends('template')
@section('title')
    User
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-plus'></i> User</b></div>
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
                                action="<?= route('pelanggan.save') ?>">
                                <?= csrf_field() ?>

                                <label>NPP</label>
                                <input type="number" name="npp" value="<?= old('npp') ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" value="<?= old('nama') ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Jenis Kelamin</label><br>
                                <input type="radio" name="jenkel" value="Laki-laki" checked=""> Laki-laki
                                &nbsp;&nbsp;&nbsp;
                                <input type="radio" name="jenkel" value="Perempuan"> Perempuan
                                <br><br>
                
                                <label>No.Telp</label>
                                <input type="number" name="telp" value="<?= old('telp') ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Email</label>
                                <input type="email" name="email" value="<?= old('email') ?>" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>


                                {{-- <label>Password</label>
                                <input type="password" name="password" value="<?= old('password') ?>" id="txt_password"
                                    class="form-control" required="" maxlength="255" placeholder="Harap di isi ...">
                                <label for="" id="txt_label_password" class="text-danger"
                                    style="font-size: 12px;"></label> --}}
                                <br>

                                <label>Unit Kerja</label>
                                <select name="kantor" id="unit_kerja" class="form-control" required="">
                                    <option value="" disabled selected>- Pilih salah satu --</option>
                                    @foreach ($unit_kerja as $kantor)
                                        @if ($kantor == 'Pusat')
                                            <optgroup label="Kantor Pusat">
                                                @foreach ($kantor_pusat as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_pusat }}">
                                                        {{ $kantor }} - {{ $item->nama_kantor_pusat }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @elseif($kantor == 'Cabang')
                                            <optgroup label="kantor Cabang">
                                                @foreach ($kantor_cabang as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_cabang }}">
                                                        {{ $kantor }} - {{ $item->nama_kantor_cabang }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @elseif($kantor == 'Wilayah')
                                            <optgroup label="Kantor Wilayah">
                                                @foreach ($kantor_wilayah as $item)
                                                    <option value="{{ $kantor }},{{ $item->id_kantor_wilayah }}">
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
                                    <option value="" disabled selected>- Pilih salah satu -</option>

                                </select>
                                <br>


                                <label>Level</label>
                                <select name="level" id="level" class="form-control" required="">
                                    <option value="" disabled selected> - Pilih salah satu -</option>
                                    <option value="Staff">Staff</option>
                                    <option value="Kepala Bagian Unit Kerja">Kepala Bagian Unit Kerja</option>
                                    <option value="Kepala Unit Kerja">Kepala Unit Kerja</option>
                                </select>
                                <br>

                                <label>Status</label>
                                <select name="status" class="form-control" required="">
                                    <option value="" disabled selected>- Pilih Salah Satu -</option>
                                    <option value="Aktif">Aktif</option>
                                    <option value="Tidak Aktif">Tidak Aktif</option>
                                </select>
                                <br>

                                <label>Sebagai</label>
                                <select name="sebagai" class="form-control" required="">
                                    <option value="" disabled selected>- Pilih Salah Satu -</option>
                                    <option value="Staff">Staff</option>
                                    {{-- @if (auth()->user()->sebagai_pegawai == 'Administator') --}}
                                        <option value="PIC">PIC</option>
                                        <option value="Administrator">Administrator</option>
                                    {{-- @endif --}}
                                </select>
                                <br>

                                <div class="form-group">
                                    <label>Unggah Foto (Opsional)</label>
                                    <br>
                                    <label for="file-1">
                                        <img src="<?= url('logos/image.png') ?>" id="image-1"
                                            style="width: 150px;border-radius: 5px;">
                                        <input type="file" accept="image/*" name="foto" id="file-1"
                                            class="form-control" onchange="previewImage('image-1','file-1')"
                                            style="display: none;">
                                    </label>
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
            loadPage('<?= route('pelanggan') ?>');
        });

        $('#txt_password').on('keyup', function() {

            $('#txt_label_password').html("");

            var InputValue = $("#txt_password").val();
            var regex = new RegExp("^(?=.*[a-z])(?=.*[0-9])(?=.*[!@#\$%\^&\*])(?=.{8,})");
            $("#passwordText").text(`Password value:- ${InputValue}`);

            console.log(InputValue);

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

    <script>
        // $('#unit_kerja').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "- Pilih salah satu -",
        // });

        // $('.bagian-unit-kerja').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "- Pilih salah satu -",
        // }).on('change', function() {
        //     let val = $(this).val();
        //     $('#level').empty();
        //     const level = ['Kepala Bagian Unit Kerja', 'Staff'];
        //     if (val == 0) {
        //         $('#level').append(`<option value="Kepala Unit Kerja" >Kepala Unit Kerja</option>`);
        //     }
        //     level.forEach(function(res) {
        //         $('#level').append(`<option value="${res}" >${res}</option>`);
        //     })

        // });

        // $('#unit_kerja').on('change', function() {
        //     let value = $(this).val();

        //     let result = value.split(',')
        //     let kantor = result[0]
        //     let id = result[1]
        //     $.post("{{ route('pengaduan.get-bagian-unit') }}", {
        //         kantor: kantor,
        //         id: id,
        //         _token: '{{ csrf_token() }}'
        //     }, function(data) {
        //         $('select[name="bagian"]').empty();
        //         $('#bagian_unit_kerja').append(
        //             `<option value="0" >Semua Bagian</option>`
        //         )
        //         data.forEach(function(res) {
        //             $('#bagian_unit_kerja').append(
        //                 `<option value="${res.id_bagian}" >${res.nama_bagian}</option>`
        //             )
        //         })
        //         const level = ['Kepala Unit','Kepala Bagian Unit Kerja', 'Staff'];
        //     level.forEach(function(res) {
        //         $('#level').append(`<option value="${res}" >${res}</option>`);
        //     })
        //     }).fail(function() {
        //         alert('error');
        //     })
        // })
    </script>

@stop
