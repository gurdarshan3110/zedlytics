<div class="table-responsive">
    <table class="table table-bordered table-striped w-100 align-middle" id="record-table">
        <thead>
        <tr>
            <th>Brand</th>
            <th>Account Id</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone No</th>
            <th>RM</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        
        </tbody>
    </table>
</div>
@push('jsscript')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript">
        var table = $('#record-table').DataTable({
            "columnDefs": [
                { "width": "7%", "targets": 0 },
                { "width": "10%", "targets": 1 },
                { "width": "7%", "targets": 2 },
                { "width": "20%", "targets": 3 },
                { "width": "20%", "targets": 4 },
                { "width": "7%", "targets": 5 },
                { "width": "19%", "targets": 6 },
                { "width": "10%", "targets": 7 },
            ],
            processing: true,
            serverSide: true,
            ajax: {
                url: "/{{$url}}/list",
                type: 'GET',
                data: function (d) {
                    d.status = 0; // Add the status filter
                }
            },
            columns: [
                { data: 'brand', name: 'brand' },
                { data: 'client_code', name: 'client_code' },
                { data: 'username', name: 'username' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'phone_no', name: 'phone_no' },
                { data: 'rm', name: 'rm' },
                { data: 'action', name: 'action', className: 'action', orderable: false, searchable: false },
            ],
            pageLength: 10,
            dom: 'Blfrtip',
            responsive: true,
            scrollX: true,  // enables horizontal scrolling
            language: {
                search: '',
                searchPlaceholder: "Search {{$title}}",
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

        $('#filter').click(function(){
            var min = $('#min').val();
            var max = $('#max').val();
            table.ajax.url('/{{$url}}/list?min=' + min + '&max=' + max).load();
        });
    </script>



@endpush
