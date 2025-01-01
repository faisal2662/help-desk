<!-- autorisasi -->
<?php
$npp = '-';
$nama = '-';
$jenkel = '-';
$telp = '-';
$email = '-';
$kantor = '-';
$divisi = '-';
$sebagai = '-';
$foto = 'logos/avatar.png';
$tgl = '-';
$status = '-';
$role = '-';
?>

@extends('template')

@section('title')
    Helpdesk - Profil
@stop

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <b>
                            Role Menu
                        </b>

                        <div style="position: absolute;top: 0;right: 0;padding-top: 20px;padding-right: 10px;">
                            <a href='#' data-toggle="modal" id="add"><button type="button" id="grid"
                                    class="btn btn-sm btn-primary">
                                    <i class="bx bx-plus-circle"></i>
                                </button></a>
                        </div>

                    </div>
                    <div id="alert" style="display: none;">
                        <div class="alert alert-success"><i class="bx bx-error-circle"></i><span id="message"></span>
                        </div>
                    </div>
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

                    <div class="row">
                        <div class="col-sm-12 col-md-12 ">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="sortable">
                                    <thead>
                                        <tr>
                                            <td align="center"><b>Menu</b></td>
                                            <td align="center"><b>Icon</b></td>
                                            <td align="center"><b>Route Name</b></td>
                                            <td align="center"><b>Type</b></td>
                                            <td align="center"><b>Action</b></td>
                                        </tr>
                                    </thead>
                                    <?php
                                    $no = 1;
                                    ?>
                                    <tbody>
                                        @foreach ($role_data as $mn)
                                            @if ($mn->type == 'TITLE')
                                                <tr style="cursor: pointer;" class="ui-state-default"
                                                    id="item-{{ $mn->id_role_menu }}"><span
                                                        class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                    {{-- <td><b>{{ $no++ }}</b></td> --}}
                                                    <td style="display: none;">{{ $mn->position }}</td>
                                                    <td><b>{{ $mn->menu }}</b></td>
                                                    <td><b>{{ $mn->icon }}</b></td>
                                                    <td><b>{{ $mn->route_name }}</b></td>
                                                    <td><b>{{ $mn->type }}</b></td>
                                                    <td><a href="#" class='btn-edit badge bg-info text-white'
                                                            data-id="{{ $mn->id_role_menu }}"><i class='bx bx-edit'></i>
                                                            Edit</a>&nbsp;|&nbsp;<a class="badge bg-danger text-white"
                                                            href="javascript:void(0);"
                                                            onclick="btnDelete({{ $mn->id_role_menu }}, '{{ $mn->menu }}')"><i
                                                                class='bx bx-trash'></i> Delete</a></td>
                                                </tr>
                                            @else
                                                <tr style="cursor: pointer;" class="ui-state-default"
                                                    id="item-{{ $mn->id_role_menu }}"><span
                                                        class="ui-icon ui-icon-arrowthick-2-n-s"></span>
                                                    {{-- <td>{{ $no++ }}</td> --}}
                                                    <td style="display: none;">{{ $mn->position }}</td>
                                                    <td>{{ $mn->menu }}</td>
                                                    <td>{{ $mn->icon }}</td>
                                                    <td>{{ $mn->route_name }}</td>
                                                    <td>{{ $mn->type }}</td>
                                                    <td><a href="#" class='btn-edit badge bg-info text-white'
                                                            data-id="{{ $mn->id_role_menu }}"><i class='bx bx-edit'></i>
                                                            Edit</a>&nbsp;|&nbsp;<a class="badge bg-danger text-white"
                                                            href="javascript:void(0);"
                                                            onclick="btnDelete({{ $mn->id_role_menu }}, '{{ $mn->menu }}')"><i
                                                                class='bx bx-trash'></i> Delete</a></td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    <tbody>

                                </table>

                            </div>
                        </div>
                    </div>


                    <div class="modal fade" id="modalMenu">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <table style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <b id="modal-title-header">

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
                                <form method="POST" enctype="multipart/form-data" onsubmit="show(true)" id="formMenu">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label><b>Menu</b></label>
                                            <input type="text" class="form-control" name="menu" id="menu"
                                                placeholder="Menu">
                                        </div>
                                        <div class="form-group">
                                            <label><b>Icon</b></label>
                                            <input type="text" class="form-control" name="icon" id="icon"
                                                placeholder="Icon">
                                        </div>
                                        <div class="form-group">
                                            <label><b>Route</b></label>
                                            <input type="text" class="form-control" name="route_name" id="route_name"
                                                placeholder="Route">
                                        </div>

                                        <div class="form-group">
                                            <label><b>Type</b></label>
                                            <select name="type" class="form-control" id="type">
                                                <option>Pilih Type</option>
                                                <option value="TITLE">Title</option>
                                                <option value="MENU">Menu</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label><b>Position</b></label>
                                            <input type="number" class="form-control" id="position" name="position">
                                        </div>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">

                    </div>
                    <p>&nbsp;</p>

                </div>
            </div>
            <p>&nbsp;</p>
        </div>

        <?php
        $bulan = date('Y-m');

        if (isset($_GET['bulan'])) {
            $bulan = $_GET['bulan'];
        }
        ?>


    </div>

@stop

@section('script')

    <script type="text/javascript">
        function previewImage(preview, source) {
            var oFReader = new FileReader();
            oFReader.readAsDataURL(document.getElementById(source).files[0]);

            oFReader.onload = function(oFREvent) {
                document.getElementById(preview).src = oFREvent.target.result;
            };
        };
    </script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#sortable tbody").sortable({
                update: function(event, ui) {
                    // Ambil ID elemen dalam urutan baru
                    var newOrder = $(this).sortable('serialize');

                    $.ajax({
                        type: "POST",
                        url: "{{ route('role.menu.update_order') }}",
                        data: newOrder + '&_token={{ csrf_token() }}',
                    }).done(function(response) {
                        console.log(response);
                        $('#alert').show().fadeIn('slow');
                        $('#message').text('Berhasil diperbarui')
                        setTimeout(() => {
                            $('#alert').hide().fadeOut('slow');

                        }, 3000);
                    });

                }
            });
        });

        function btnDelete(id, name) {
            if (confirm('Kamu yakin ingin menghapus ' + name + '?')) {
                $.ajax({
                    url: `{{ route('role.menu.delete', '') }}/${id}`,
                    type: 'GET',
                    success: function(data) {
                        alert('berhasil dihapus');
                        location.reload();
                    },
                    error: function(data) {
                        alert('Gagal dihapus')
                    }

                })
            }
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            var modalMenu = $('#modalMenu');

            function clean() {
                $('#menu').val('');
                $('#icon').val('');
                $('#route_name').val('');
                $('#type').val('');
                $('#position').val('');

            }


            $('#add').on('click', function(e) {
                $('#modal-title-header').html("Add Menu");
                clean();


                $('#formMenu').attr("action", "<?= route('role.menu.save') ?>");
                $(modalMenu).modal('show');

            });

            $('.table').on('click', '.btn-edit', function(e) {
                var id = this.getAttribute('data-id');


                $.ajax({
                    url: "{{ route('role.menu') }}/" + id + "/detail",
                    type: "GET",
                    success: function(res) {
                        $('#menu').val(res.data.menu);
                        $('#icon').val(res.data.icon);
                        $('#route_name').val(res.data.route_name);
                        $('#type').val(res.data.type);
                        $('#position').val(res.data.position);
                        $('#formMenu').attr("action", "<?= route('role.menu') ?>/" + id +
                            "/update");

                        $('#modal-title-header').html("Edit Menu");
                        $(modalMenu).modal('show');

                    }
                });

                console.log(id);
            });

        });
    </script>

    <script type="text/javascript">
        $("#bulan").datepicker({
            format: "yyyy-mm",
            viewMode: "months",
            minViewMode: "months"
        });
    </script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script src="https://www.chartjs.org/samples/2.6.0/utils.js"></script>



@stop
