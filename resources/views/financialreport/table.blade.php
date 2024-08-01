<div class="table-responsive">
    <table class="table table-bordered table-striped w-100 align-middle" id="record-table">
        <thead>
        <tr>
            <th>Date</th>
            <th>Brand</th>
            <th>Deposits</th>
            <th>Withdraw</th>
            <th>Gap</th>
            <th>Pool</th>
            <th>Parking</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $data)
            <tr>
                <td>{{ $data['date'] }}</td>
                <td>{{ $data['brand'] }}</td>
                <td class="text-end">{{ $data['deposits'] }}</td>
                <td class="text-end">{{ $data['withdraw'] }}</td>
                <td class="text-end">{{ $data['gap'] }}</td>
                <td class="text-end">{{ $data['pool'] }}</td>
                <td class="text-end">{{ $data['parking'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@push('jsscript')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script> 
    <script type="text/javascript">
        var minDate, maxDate;
        $(document).ready(function() {
            var table = $('#record-table').DataTable({
                dom: 'Bfrtip',
                pageLength: 15,
                buttons: [
                    {
                        extend: 'copyHtml5',
                        title: 'financial-report'
                    },
                    {
                        extend: 'excelHtml5',
                        title: 'financial-report'
                    },
                    {
                        extend: 'csvHtml5',
                        title: 'financial-report'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'financial-report'
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
                },
            });

        });
    </script>
@endpush
