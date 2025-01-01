@extends('template')


@section('title')
    Petanyaan FAQ
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <h4><i class='bx bx-comment-detail'></i> User FAQ - Helpdesk</h4>

            <p>&nbsp;</p>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
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
            <div class="table-responsive pt-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Pertanyaan</th>
                            <th>Kantor</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Tanggal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($user_faq as $item)
                            <tr>
                                <td> {{ $loop->iteration }} </td>
                                <td> {{ $item->pertanyaan_faq }} </td>
                                <td> {{ $item->kantor }} </td>
                                <td> {{ $item->nama_faq }} </td>
                                <td> {{ $item->email_faq }} </td>
                                <td> {{ \Carbon\Carbon::parse($item->tgl_faq)->translatedFormat('l, j F Y') }} </td>
                                <td><a href="{{ route('pertanyaan.delete', $item->id_faq) }}"
                                        onclick="return confirm('Kamu yakin ingin menghapus ini ?')"><span
                                            class="badge badge-danger"><i class="bx bx-trash"></i> Hapus</span></a> </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p>&nbsp;</p>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <?= $user_faq->links() ?>
                <p>&nbsp;</p>
            </div>
        </div>
    </div>
@endsection
