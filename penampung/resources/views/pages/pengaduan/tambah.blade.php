@extends('template')
@section('title')
    Pengaduan - Helpdesk
@endsection
@section('content')
   

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-plus'></i> Buat Pengaduan Baru</b></div>
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
                            <form method="POST" enctype="multipart/form-data" action="{{ route('pengaduan.save') }}"
                                id="formPengaduan">
                                @csrf


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
                                {{-- <br>

                                <label>Sub Unit Kerja</label>
                                <select name="sub_unit_kerja" class="form-control sub-unit-kerja" id="sub_unit_kerja"
                                    required="">
                                    <option value="" disabled selected>- Pilih salah satu -</option>
                                    <optgroup label="Kantor Pusat" class="Pusat">
                                        @foreach ($kantor_pusat as $item)
                                            <option value="{{ $item->id_kantor_pusat }}">{{ $item->nama_kantor_pusat }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Kantor Cabang" class="Cabang">
                                        @foreach ($kantor_cabang as $item)
                                            <option value="{{ $item->id_kantor_cabang }}">{{ $item->nama_kantor_cabang }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Kantor Wilayah" class="Wilayah">
                                        @foreach ($kantor_wilayah as $item)
                                            <option value="{{ $item->id_kantor_wilayah }}">
                                                {{ $item->nama_kantor_wilayah }}
                                            </option>
                                        @endforeach
                                    </optgroup>
                                </select> --}}
                                <br>

                                <label>Bagian Unit Kerja</label>
                                <select name="bagian" class="form-control bagian-unit-kerja" id="bagian_unit_kerja"
                                    required="">
                                    <option value="" disabled selected>- Pilih salah satu -</option>
                                    {{-- <optgroup label="Kantor Pusat" class="Pusat">
                                        @foreach ($bagian_kantor_pusat as $item)
                                            <option value="{{ $item->id_bagian_kantor_pusat }}">
                                                {{ $item->nama_bagian_kantor_pusat }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Kantor Cabang" class="Cabang">
                                        @foreach ($bagian_kantor_cabang as $item)
                                            <option value="{{ $item->id_bagian_kantor_cabang }}">
                                                {{ $item->nama_bagian_kantor_cabang }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="Kantor Wilayah" class="Wilayah">
                                        @foreach ($bagian_kantor_wilayah as $item)
                                            <option value="{{ $item->id_bagian_kantor_wilayah }}">
                                                {{ $item->nama_bagian_kantor_wilayah }}</option>
                                        @endforeach
                                    </optgroup>
                                    <option value=""></option> --}}

                                </select>
                                <br>
                                 <label for="">Kategori Pengaduan</label>
                                <select name="kategori_pengaduan" id="kategori_pengaduan" class="form-control">
                                    <option value="" disabled selected>- Pilih Salah Satu -</option>
                                    <option value="Bisnis">Bisnis</option>
                                    <option value="Klaim">Klaim</option>
                                    <option value="Peraturan">Peraturan</option>
                                    <option value="Dan Lainnya">Dan Lainnya</option>
                                </select>
                                <br>
                                <label for="" >Jenis Produk</label>
                                <select name="jenis_produk" class="form-control" id="jenis_produk" required>
                                    <option value="" disabled selected>- Pilih salah satu -</option>
                                    <optgroup label="KUR">
                                        <option value="KUR,Produk KUR">Produk KUR</option>
                                    </optgroup>
                                    <optgroup label="KBG & Suretyship">
                                        <option value="KBG & Suretyship,Custom Bond">Customer Bond</option>
                                        <option value="KBG & Suretyship,KBG">KBG</option>
                                        <option value="KBG & Suretyship,Surety Bond">Surety Bond</option>
                                        <option value="KBG & Suretyship,Payment Bond">Payment Bond</option>
                                    </optgroup>
                                    <optgroup label="Produktif">
                                        <option value="Produktif,ATMR">ATMR</option>
                                        <option value="Produktif,Keagenan Kargo">Keagenan Kargo</option>
                                        <option value="Produktif,KKPE">KKPE</option>
                                        <option value="Produktif,Kontruksi">Kontruksi</option>
                                        <option value="Produktif,Mikro">Mikro</option>
                                        <option value="Produktif,Distribusi Barang">Distribusi Barang</option>
                                        <option value="Produktif,Pembiayaan Invoice">Pembiayaan Invoice</option>
                                        <option value="Produktif,Subsidi Resi Gudang">Subsidi Resi Gudang</option>
                                        <option value="Produktif,Super Mikro">Super Mikro</option>
                                        <option value="Produktif,Umum">Umum</option>
                                    </optgroup>
                                    <optgroup label="Konsumtif">
                                        <option value="Konsumtif,FLPP">FLPP</option>
                                        <option value="Konsumtif,OTO">OTO</option>
                                        <option value="Konsumtif,KPR">KPR</option>
                                        <option value="Konsumtif,Multiguna">Multiguna</option>
                                        <option value="Konsumtif,KSM">KSM</option>
                                        <option value="Konsumtif,Mandiri">Mandiri</option>
                                        <option value="Konsumtif,Briguna">Briguna</option>
                                    </optgroup>
                                </select>
                                <br>
                                <label>Pengaduan</label>
                                <input type="text" name="nama_pengaduan" id="nama_pengaduan" class="form-control"
                                    required="" maxlength="255" placeholder="Harap di isi ...">
                                <br>

                                <label>Deskripsi</label>
                                <textarea name="keterangan" id="keterangan" class="form-control" required="" placeholder="Harap di isi ..."></textarea>
                                <br>

                                {{-- <label>Klasifikasi</label>
                        <select name="klasifikasi" class="form-control" required="">
                          <?php
                          echo '<option value="">- Pilih salah satu -</option>';
                          foreach (['High', 'Medium', 'Low'] as $klasifikasi) {
                              echo '<option value="' . $klasifikasi . '">' . $klasifikasi . '</option>';
                          }
                          ?>
                        </select>
                        <br> --}}

                                {{-- <button type="button" class="btn btn-sm btn-warning" id="kembali">
                                    <i class='bx bx-arrow-back'></i> Kembali
                                </button> --}}
                                <a href="{{ route('pengaduan') . '?filter=Semua' }}" class="btn btn-sm btn-warning"> <i
                                        class='bx bx-arrow-back'></i> Kembali</a>

                                <button type="submit" class="btn btn-sm btn-primary btn-save">
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

@section('script')
    <script>
        //         $('#sub_unit_kerja').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "- Pilih salah satu -",

        // });
        //         $('#bagian_unit_kerja').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "- Pilih salah satu -",

        // });

        $(document).ready(function() {
            // Initialize Select2
            $('#unit_kerja').select2({
                theme: 'bootstrap-5',
                placeholder: "- Pilih salah satu -",
            });
            $('#jenis_produk').select2({
                theme: 'bootstrap-5',
                placeholder: "- Pilih salah satu -",
            });
            // $('.sub-unit-kerja').select2({
            //     theme: 'bootstrap-5',
            // placeholder: "- Pilih salah satu -",
            // });
            $('.bagian-unit-kerja').select2({
                theme: 'bootstrap-5',
            placeholder: "- Pilih salah satu -",
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
                    data.forEach(function(res) {
                        $('#bagian_unit_kerja').append(
                            `<option value="${res.id_bagian}" >${res.nama_bagian}</option>`
                            )
                    })
                }).fail(function (){
                    alert('error');
                })
            })

            // // Event listener to filter based on group selection
            // $('#unit_kerja').on('change', function() {
            //     var selectedGroup = $(this).val();

            //     // Show all options first
            //     $('#sub_unit_kerja optgroup').show();

            //     if (selectedGroup !== "") {
            //         // Hide all optgroups except the selected one
            //         $('#sub_unit_kerja optgroup').not('.' + selectedGroup).hide();
            //     }

            //     // Trigger Select2 to update
            //     // $('#sub_unit_kerja').select2('open');


            //     $('#bagian_unit_kerja optgroup').show();

            //     if (selectedGroup !== "") {
            //         // Hide all optgroups except the selected one
            //         $('#bagian_unit_kerja optgroup').not('.' + selectedGroup).hide();
            //     }

            //     // Trigger Select2 to update
            //     // $('#bagian_unit_kerja').select2('open');
            // });
        });

        function initialForm() {
            $('#unit_kerja').val('');
            $('#sub_unit_kerja').val('');
            $('#bagian_unit_kerja').val('');
            $('#nama_pengaduan').val('');
            $('#keterangan').val('');
        }
        // $('#formPengaduan').on('submit', function(e) {
        //     e.preventDefault();

        //     $.ajax({
        //         url: "{{ route('pengaduan.save') }}",
        //         type: "POST",
        //         data: $('#formPengaduan').serialize(),
        //         beforeSend: function() {
        //             $('.btn-save').html("Loading...");
        //             $('.btn-save').attr("disabled", "");
        //         },
        //         error: function(res) {


        //             alert("Error");
        //             // console.log(res.status);
        //         },
        //         success: function(res) {
        //             // console.log(res.status);
        //             Swal.fire({
        //                 toast: true,
        //                 position: 'top-end', // Atur posisi toast
        //                 icon: 'success', // Jenis icon (success, error, warning, info, question)
        //                 title: 'Berhasil disimpan!', // Judul notifikasi
        //                 showConfirmButton: false, // Hilangkan tombol konfirmasi
        //                 timer: 3000, // Lama durasi toast (3 detik)
        //                 timerProgressBar: true, // Tampilkan progress bar di dalam toast
        //                 didOpen: (toast) => {
        //                     toast.addEventListener('mouseenter', Swal
        //                         .stopTimer); // Berhenti saat mouse di atas toast
        //                     toast.addEventListener('mouseleave', Swal
        //                         .resumeTimer); // Lanjutkan saat mouse keluar
        //                 }
        //             });

        //             // alert(res.status);

        //         },
        //         complete: function() {
        //             $('.btn-save').html("Save");
        //             $('.btn-save').removeAttr("disabled");
        //             initialForm();
        //             location.href = "{{ route('pengaduan') }}?filter=Semua"
        //         }
        //     });
        // });
    </script>
    @stop
@endsection

