@extends('template')

@section('title')
    Hari Libur | Helpdesk Jamkrindo
@endsection

@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b>Hari Libur</b></div>
                    <p>
                        <?php echo htmlspecialchars_decode($input); ?>
                    </p>
                    @php

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
                        
                    @endphp
                    <hr style="border-style: dashed;">

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <td><b>No</b></td>
                                    <td><b>Nama</b></td>
                                    <td><b>Keterangan</b></td>
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
                            action="<?= route('hari_libur.delete') ?>">
                            <?= csrf_field() ?>
                            <input type="text" name="delete" id="input-delete" readonly="" required="">
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


                    </script>


                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>

@section('script')
    <script>

        $(document).ready(function(){

            $('#dataTables').DataTable({
                processing: true,
	            serverSide: true,
	            stateSave: true,
	            ajax:{
                    url: "{{ route('hari_libur.datatables') }}",
	                type: "GET"
	            },
	            columns: [
                    {'data': 'no'},
	                {'data': 'nama_hari_libur'},
	                {'data': 'keterangan_hari_libur'},
	                {'data': 'tanggal'},
	                {'data': 'action'}
	            ],

	        });
        });
    </script>
@endsection


@endsection
