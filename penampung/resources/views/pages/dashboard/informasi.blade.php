
@section('title')
    Informasi - Helpdesk
@stop
@section('content')
    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <h4>
                Informasi- Helpdesk
            </h4>

            <a href=" {{ route('dashboard') }}">
                <span class='badge badge-warning'>
                    <i class='bx bx-arrow-back'></i>Kembali
                </span>
            </a>
            <p>&nbsp;</p>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="card-title"><b><i class='bx bx-info-circle' ></i> Informasi Tambahan</b></div>
                    <hr style="border-style: dashed;">
                    <h6>
                        <p> Aplikasi Helpdesk yang dimiliki oleh Divisi Jaringan merupakan aplikasi yang mengakomodir
                           <strong>kendala - kendala Unit
                            Kerja diluar dari kendala helpdesk TI. </strong> Tidak termasuk ke dalam Helpdesk Jaringan:</p>
                    </h6>
                    <ol class="list-ticked">
                        <li>Layanan <em>troubleshooting</em> perangkat kerja</li>
                        <li>Layanan Jaringan LAN</li>
                        <li>Layanan Jaringan WAN</li>
                        <li>Layanan Jaringan Mitra</li>
                        <li>Layanan Jaringan Internet</li>
                        <li>Layanan <em>e-mail </em> Perusahaan</li>
                        <li>Layanan IT Helpdesk</li>
                        <li>Layanan Antivirus</li>
                        <li>Layanan Penyediaan Data</li>
                        <li>Layanan Perbaikan Data</li>
                        <li>Asistensi dan Sosialisasi Penerima Layanan SI</li>
                        <li>Seluruh Aplikasi</li>
                    </ol>
                </div>
            </div>
            <p>&nbsp;</p>
        </div>
    </div>
@stop
