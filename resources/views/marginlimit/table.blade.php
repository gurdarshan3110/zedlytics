<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle" id="record-table">
        <thead>
        <tr class="fs-7">
            <th>SNO</th>
            <th>MARKET</th>
            <th style="width:18%">SCRIPT</th>
            <th>MIN DEAL</th>
            <th title="MAX DEAL IN SINGLE ORDER">MAX DEAL</th>
            <th>MAX QTY IN SCRIPT</th>
            <th>INTRADAY MARGIN</th>
            <th>HOLDING MARGIN</th>
            <th>INVTRY MARGIN</th>
            <th>TOT GRP LIMIT</th>
            <th>MARGIN TIME</th>
            <th style="width:5%;">Action</th>
        </tr>
        </thead>
        <tbody class="fs-6">
        
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
                ajax: "/{{$url}}/list?status=0",
                columns: [

                    {data: 'id', name: 'id'},
                    {data: 'market', name: 'market'},
                    {data: 'script', name: 'script'},
                    {data: 'minimum_deal', name: 'minimum_deal'},
                    {data: 'maximum_deal_in_single_order', name: 'maximum_deal_in_single_order'},
                    {data: 'maximum_quantity_in_script', name: 'maximum_quantity_in_script'},
                    {data: 'intraday_margin', name: 'intraday_margin'},
                    {data: 'holding_maintainence_margin', name: 'holding_maintainence_margin'},
                    {data: 'inventory_day_margin', name: 'inventory_day_margin'},
                    {data: 'total_group_limit', name: 'total_group_limit'},
                    {data: 'margin_calculation_time', name: 'margin_calculation_time'},
                    {data: 'action', name: 'action',className:'action'},
                    
                ],
                pageLength:1000,
                dom: 'Blfrtip',
                responsive: true,
                //"lengthMenu": [[1000, 2500], [1000, 2500]],
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
