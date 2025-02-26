@extends('template')

@section('title')
    FAQ - Helpdesk
@stop


<?php

$pegawai = DB::table('tb_pegawai')
    ->where([['tb_pegawai.delete_pegawai', 'N'], ['tb_pegawai.status_pegawai', 'Aktif'], ['tb_pegawai.id_pegawai', Session::get('id_pegawai')]])
    ->get();
if ($pegawai->count() < 1) {
    header('Location: ' . route('faq'));
    exit();
}

?>

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-plus'></i> Ubah FAQ | Kategori - {{$kategori->nama_kategori_faq}} </b></div>
                    <hr style="border-style: dashed;">
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
                    <form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('faq.updateQuest', $faq->id_faq) ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_kategori" value="{{$kategori->id_kategori_faq}}">
                        <label>Pertanyaan</label>
                        <textarea name="pertanyaan"  class="form-control" required="" rows="3" placeholder="Harap di isi ...">{{$faq->pertanyaan_faq}}</textarea>
                        <br>
                        <label>Jawaban</label>
                        <textarea name="jawaban"  class="form-control" rows="4" required="" id="ckeditor" placeholder="Harap di isi ...">{{$faq->jawaban_faq}}</textarea>
                        <br>

                        <button type="button" class="btn btn-sm btn-warning" id="kembali">
                            <i class='bx bx-arrow-back'></i> Kembali
                        </button>

                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class='bx bx-check-double'></i> Selesai
                        </button>

                    </form>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

@stop

@section('script')

    <script type="text/javascript">
        $('#kembali').on('click', function() {
            loadPage("<?= route('faq.Quest', $kategori->id_kategori_faq) ?>");
        });
        // $('#unit_kerja').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "- Pilih salah satu -",
        // });

        // $('.bagian-unit-kerja').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "- Pilih salah satu -",
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
        //         data.forEach(function(res) {
        //             $('#bagian_unit_kerja').append(
        //                 `<option value="${res.id_bagian}" >${res.nama_bagian}</option>`
        //             )
        //         })
        //     }).fail(function() {
        //         alert('error');
        //     });
        // });
    </script>

    <script src="//cdn.ckeditor.com/4.16.0/full/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('ckeditor');
    </script>

@stop
