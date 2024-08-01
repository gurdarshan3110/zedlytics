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
                @if(count($data)>0)
                    @foreach($data as $winner)
                        <tr>
                            <td class="text-start">{{$winner->accountId}}</td>
                            <td>{{$winner->client->username}}</td>
                            <td>{{$winner->client->parent->name}}</td>
                            <td>{{$winner->client->name}}</td>
                            <td class="text-end">{{$winner->totalCloseProfit}}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center">no records found</td>
                    </tr>
                @endif
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
        $(document).ready(function() {
            var table = $('#top-table-winners').DataTable({
                dom: 'Bfrtip',
                columnDefs: [
                    { width: "10%", targets: 0 },
                    { width: "20%", targets: 1 },
                    { width: "30%", targets: 2 },
                    { width: "30%", targets: 3 },
                    { width: "10%", targets: 4 }
                ],
                pageLength: 10,
                responsive: true,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                scrollX: true,
                buttons: [
                    {
                        extend: 'copyHtml5',
                        title: 'wl-report'
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'wl-report'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'wl-report'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'wl-report'
                    }
                ],
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

            function autoAdjustColumns(table) {
                var container = table.table().container();
                var resizeObserver = new ResizeObserver(function () {
                    table.columns.adjust();
                });
                resizeObserver.observe(container);
            }
            autoAdjustColumns(table);
        });
    </script>
@endpush