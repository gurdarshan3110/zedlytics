<div class="container">
    <div class="table-responsive">
        <table class="w-100 align-middle fs-6 table table-bordered" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
            <thead>
            <tr>
                <th width="10%">Id</th>
                <th width="20%">Username</th>
                <th width="30%">Parent</th>
                <th width="30%">Name</th>
                <th width="10%">PNL</th>
            </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>
@push('jsscript')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        const date = "{{ $date }}";

        var table = $('#top-table-winners').DataTable({
            "order": [[ $('#market-winners thead th').length - 1, 'desc' ]],
            "columnDefs": [
                { "width": "10%", "targets": 0 },
                { "width": "20%", "targets": 1 },
                { "width": "30%", "targets": 2 },
                { "width": "30%", "targets": 3 },
                { "width": "10%", "targets": 4 },
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: "/{{$url}}/list",
                type: 'GET',
                data: function (d) {
                    d.date = date; // Add the date filter
                }
            },
            columns: [
                { data: 'accountId', name: 'accountId' },
                { data: 'username', name: 'username' },
                { data: 'parent', name: 'parent' },
                { data: 'name', name: 'name' },
                { data: 'totalCloseProfit', name: 'totalCloseProfit' },
            ],
            pageLength: 10,
            dom: 'Blfrtip',
            "lengthMenu": [[10, 25, 50, 100, 1000, 2000], [10, 25, 50, 100, 1000, 2000]],
            scrollX: true,  // enables horizontal scrolling
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5',
            ],
            language: {
                search: '',
                searchPlaceholder: "Search {{ $title }}",
                paginate: {
                    previous: '<i class="fa fa-angle-left"></i>',
                    next: '<i class="fa fa-angle-right"></i>'
                }
            },
            drawCallback: function () {
                $('.dataTables_filter input').addClass('form-control form-control-solid w-250px');
                $('.dt-buttons button').addClass('fs-7 active-menu-item text-light border-0');
                $('.dt-buttons').addClass('mb-2');
                $('.dt-button').addClass('btn-primary');
                $('.paginate_button').addClass('fs-7');
                $('.paginate_button.current').addClass('fs-7 active-menu-item');
                $('.paginate_button.active-menu-item').removeClass('current');
            }
        });

    </script>
@endpush