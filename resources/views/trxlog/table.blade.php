<div class="table-responsive">
    <table class="table table-bordered table-striped w-100 align-middle fs-6" id="record-table">
        <thead>
        <tr>
            <th>Ticket Id</th>
            <th>Time</th>
            <th>Action</th>
            <th>Type</th>
            <th>Type Detail</th>
            <th>Account</th>
            <th>Parent</th>
            <th>Amount</th>
            <th>Script</th>
            <th>Price</th>
            <th>Close Price</th>
            <th>Open Commission</th>
            <th>Close Commission</th>
            <th>Total Pnl</th>
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
                ajax: "/{{$url}}/list",
                columns: [
                    {data: 'ticket_id', name: 'ticket_id'},
                    {data: 'time', name: 'time'},
                    {data: 'action', name: 'action'},
                    {data: 'type', name: 'type'},
                    {data: 'type_detail', name: 'type_detail'},
                    {data: 'account', name: 'account'},
                    {data: 'parent', name: 'parent'},
                    {data: 'amount', name: 'amount'},
                    {data: 'script', name: 'script'},
                    {data: 'price', name: 'price'},
                    {data: 'close_price', name: 'close_price'},
                    {data: 'open_commission', name: 'open_commission'},
                    {data: 'close_commission', name: 'close_commission'},
                    {data: 'total_pnl', name: 'total_pnl'},
                ],
                order: [[0, 'desc']], // Sort by ID in descending order
                columnDefs: [
                    {
                        targets: 0,
                        orderable: false 
                    }
                ],
                pageLength:10,
                dom: 'Blfrtip',
                responsive: true,
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
                    
                },
                "rowCallback": function (row, data) {
                    if (data.deleted==true) {
                        $(row).addClass('text-canceled');
                    }
                }

            });
            $('#filter').click(function(){
                var min=$('#min').val();
                var max=$('#max').val();
                table.ajax.url( '/agencies/list?min='+min+'&max='+max ).load();
            });
            function confirmDelete(rowId) {
                var remarks = prompt('Are you sure! Please enter remarks:');
                if (remarks !== null) {
                    $('#remarks'+rowId).val(remarks); 
                    $('#deleteForm'+rowId).submit(); // Submit the form
                }
            }
    </script>
@endpush
