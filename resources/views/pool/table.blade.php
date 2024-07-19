<div class="table-responsive bg-pool">
    <table class="table table-bordered table-striped w-100 align-middle fs-6" id="record-table">
        <thead>
        <tr>
            <th width="15%">Bank</th>
            <th width="13%">Account</th>
            <th width="8%" class="text-end">Credit</th>
            <th width="8%" class="text-end">Debit</th>
            <th width="8%" class="text-end">Balance</th>
            <th width="10%">Ledger Date</th>
            <th width="19%">Entry Date</th>
            <th width="15%">Created By</th>
            <th width="15%">Transaction Id</th>
            <th width="5%">Action</th>
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
                    {data: 'bank', name: 'bank'},
                    {data: 'account_code', name: 'account_code'},
                    {data: 'credit', name: 'credit', className: "text-end"},
                    {data: 'debit', name: 'debit', className: "text-end"},
                    {data: 'balance', name: 'balance', className: "text-end"},
                    {data: 'ledger_date', name: 'ledger_date'},
                    {data: 'entry_date', name: 'entry_date'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'transaction_id', name: 'transaction_id'},
                    {data: 'action', name: 'action'}
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
