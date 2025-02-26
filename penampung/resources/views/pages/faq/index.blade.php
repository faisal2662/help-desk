@extends('template')

@section('title')
	FAQ - Helpdesk
@stop

<?php if(isset($_GET['kategori'])){ ?>

	@include('pages.faq.kategori')

<?php }else if(isset($_GET['update'])){ ?>

	@include('pages.faq.ubah')

<?php }else{ ?>

	@section('content')

		<div class="row">
			<div class="col-md-12">
				<p>&nbsp;</p>
				<h4><i class='bx bx-comment-detail' ></i> FAQ - Helpdesk</h4>
				<p>
					<?php echo htmlspecialchars_decode($input); ?>
				</p>
				<p>&nbsp;</p>
			</div>
		</div>

		<div id="data_pagination">
		  <!-- data pagination -->
		</div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title"><b><i class='bx bx-plus'></i>  Silahkan Tulis Pertanyaan</b></div>
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
                        <form method="POST" enctype="multipart/form-data" onsubmit="show(true)" action="<?= route('faq.save') ?>">
                            <?= csrf_field() ?>
                            <label>Pertanyaan</label>
                            <textarea name="pertanyaan"  class="form-control" required="" rows="3" placeholder="Harap di isi ..."><?= old('pertanyaan') ?></textarea>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label>Nama</label>
                                    <input type="text" name="nama" value="<?= old('nama') ?>" class="form-control"
                                        required="" maxlength="255" placeholder="Harap di isi ...">
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?= old('email') ?>" class="form-control"
                                        required="" maxlength="255" placeholder="Harap di isi ...">
                                    <br>
                                </div>
                                <div class="col-md-6">
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
                                </select><br>
                                </div>
                                <div class="col-md-6">

                                <label>Bagian Unit Kerja</label>
                                <select name="bagian" class="form-control bagian-unit-kerja" id="bagian_unit_kerja"
                                    required="">
                                    <option value="" disabled selected>- Pilih salah satu -</option>
                                </select>
                                <br>
                                </div>
                                {{-- <div class="col-md-6">
                                    <label>Urutan FAQ</label>
                                    <input type="number" name="urutan" value="<?= old('urutan') ?>" class="form-control"
                                        required="" maxlength="255" placeholder="Harap di isi ...">
                                    <br>
                                </div> --}}
                            </div>



                            <button type="submit" class="btn btn-sm btn-success">
                            Kirim <i class='bx bx-send'></i>
                            </button>

                        </form>
                    </div>
                </div>
                <p>&nbsp;</p>
            </div>
        </div>
		<script>
		  $(document).ready(function(){

		   $(document).on('click', '.pagination a', function(event){
		    event.preventDefault();
		    var page = $(this).attr('href').split('page=')[1];
		    fetch_data(page);
		   });

		   function fetch_data(page)
		   {
		    // preloader
		    document.getElementById('data_pagination').innerHTML = '<div class="card"><div class="card-body" align="center"><img src="<?= url('logos/loader.gif') ?>" style="width: 150px;"><p class="text-primary">Sedang memproses ...</p></div></div>';

		    var http = new XMLHttpRequest();
		    var url = '<?= route('faq.pagination') ?>?page=' + page;
		    var params = '_token=<?= csrf_token() ?>';
		    http.open('POST', url, true);

		    //Send the proper header information along with the request
		    http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

		    http.onreadystatechange = function() {//Call a function when the state changes.
		        if(http.readyState == 4 && http.status == 200) {
		            document.getElementById('data_pagination').innerHTML = http.responseText;
		        }
		    }
		    http.send(params);
		   }

		   fetch_data(1);

		  });
		</script>

		<span style="display: none;">
			<form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('faq.delete') ?>">
				<?= csrf_field() ?>
				<input type="text" name="delete" id="input-delete" readonly="" required="">
			</form>
		</span>

		<script type="text/javascript">
			function delete_data (id, name){
				var r = confirm('Hapus data '+name+'?');
				if(r == true){
				  show(true);
				  document.getElementById('input-delete').value = id;
				  document.getElementById('form-delete').submit();
				}
			}
		</script>

	@stop

	@section('script')
    <script type="text/javascript">
        $('#kembali').on('click', function() {
            loadPage('<?= route('faq') ?>');
        });
        $('#unit_kerja').select2({
            theme: 'bootstrap-5',
            placeholder: "- Pilih salah satu -",
        });

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
            }).fail(function() {
                alert('error');
            });
        });
    </script>

    <script src="//cdn.ckeditor.com/4.16.0/full/ckeditor.js"></script>
    <script type="text/javascript">
        CKEDITOR.replace('ckeditor');
    </script>
	@stop

<?php } ?>
