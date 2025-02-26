@extends('template')

@section('title')
    FAQ - Helpdesk
@stop


@section('content')

    <div class="row">
        <div class="col-md-12">
            <p>&nbsp;</p>
            <h4><i class='bx bx-comment-detail'></i> FAQ - Helpdesk</h4>
         
            <p>
                <a href=" {{ route('faq') }}">
                    <span class='badge badge-warning'>
                        <i class='bx bx-arrow-back'></i>Kembali
                    </span>
                </a>
                <?php echo htmlspecialchars_decode($input); ?>
            </p>
            <p>&nbsp;</p>
        </div>
    </div>

    <div id="data_pagination">
        <!-- data pagination -->
    </div>

    <script>
        $(document).ready(function() {

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                var page = $(this).attr('href').split('page=')[1];
                fetch_data(page);
            });

            function fetch_data(page) {
                // preloader
                document.getElementById('data_pagination').innerHTML =
                    '<div class="card"><div class="card-body" align="center"><img src="<?= url('logos/loader.gif') ?>" style="width: 150px;"><p class="text-primary">Sedang memproses ...</p></div></div>';

                var http = new XMLHttpRequest();
                var url = '<?= route('faq.pagination.question', $id ) ?>?page=' + page;
                var params = '_token=<?= csrf_token() ?>';
                http.open('POST', url, true);

                //Send the proper header information along with the request
                http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

                http.onreadystatechange = function() { //Call a function when the state changes.
                    if (http.readyState == 4 && http.status == 200) {
                        document.getElementById('data_pagination').innerHTML = http.responseText;
                    }
                }
                http.send(params);
            }

            fetch_data(1);

        });
    </script>

    <span style="display: none;">
        <form method="POST" onsubmit="show(true)" id="form-delete" action="<?= route('faq.deleteQuest') ?>">
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

@stop

@section('script')

@stop


