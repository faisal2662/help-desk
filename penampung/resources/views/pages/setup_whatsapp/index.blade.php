@extends('template')

@section('title')
    Setup WhatsApp | Helpdesk
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <b>
                            Setup Whatsapp
                        </b>



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
                        <div class="col-sm-12 col-md-12">
                            <table class="table table-bordered">
                                <thead>
                                    <th align="center"><b>No</b></th>
                                    <th align="center"><b>Status</b></th>
                                    <th align="center"><b>Aksi</b></th>

                                </thead>

                                <tbody>
                                    @foreach ($setupWhatsapp as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }} </td>
                                            <td>
                                                @if ($item->status_setup == 'TRUE')
                                                    <span class="btn btn-sm btn-success">Menyala</span>
                                                @else
                                                    <span class="btn btn-sm btn-danger">Mati</span>
                                                @endif
                                            </td>
                                            <td> {!! $item->action !!} </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>

                        </div>
                    </div>
                    <p>&nbsp;</p>

                </div>
            </div>
            <p>&nbsp;</p>
        </div>

        @foreach ($setupWhatsapp as $item)
            <!-- Modal -->
            <div class="modal fade" id="update_setup_whatsapp" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Perbarui Setup WhatsApp </h5>

                        </div>
                        <div class="modal-body">
                            <form action="{{ route('setup_whatsapp.update', $item->id_setup_whatsapp) }}" method="post">
                              @csrf
                                <label for="" class="form-label">Status Whatsapp</label>
                                <select name="status_setup" id="setup_whatsapp" class="form-control">
                                    <option value="TRUE" @if ($item->status_setup == 'TRUE') selected @endif>Menyala
                                    </option>
                                    <option value="FALSE" @if ($item->status_setup == 'FALSE') selected @endif>Mati</option>
                                </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <?php
        $bulan = date('Y-m');

        if (isset($_GET['bulan'])) {
            $bulan = $_GET['bulan'];
        }
        ?>


    </div>
@endsection
