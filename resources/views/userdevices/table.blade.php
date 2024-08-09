<div class="table-responsive">
    <div id="loader" class="text-center" style="display: none;">
        <i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <table class="table table-bordered table-striped w-100 align-middle" id="record-table">
        <thead>
        <tr>
            <th>Account Id</th>
            <th>Name</th>
            <th>Username</th>
            <th>Address</th>
            <th>Type</th>
            <th  style="width:5%;">Count</th>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var minDate, maxDate;
        
        var table=$('#record-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "/{{$url}}/list",
                    beforeSend: function() {
                        $('#loader').show();
                    },
                    complete: function() {
                        $('#loader').hide();
                    }
                },
                columns: [
                    {data: 'account_id', name: 'account_id'},
                    {data: 'name', name: 'name'},
                    {data: 'username', name: 'username'},
                    {data: 'address', name: 'address'},
                    {data: 'type', name: 'type'},
                    {data: 'count', name: 'count'},
                    
                ],
                pageLength:10,
                dom: 'Blfrtip',
                responsive: true,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "scrollX": true,  // enables horizontal scrolling
                buttons: [
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5',
                ],
                "language": {
                        "search": '',
                        "searchPlaceholder": "Search {{$title}}",
                        "paginate": {
                        "previous": '<i class="fa fa-angle-left"></i>',
                            "next": '<i class="fa fa-angle-right"></i>'
                    }
                },
                "drawCallback": function () {
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
                var min=$('#min').val();
                var max=$('#max').val();
                table.ajax.url( '/agencies/list?min='+min+'&max='+max ).load();
            });
    </script>
@endpush
