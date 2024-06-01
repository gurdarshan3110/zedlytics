@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
            
        </div>
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="card mb-4">
            <div class="card-body">
                {{ html()->form('POST')->id('excelReportForm')->open() }}
                    <div class="row">
                        @include($directory.'.fields')
                        <div class="col-sm-12 mt-5">
                            {{ html()->submit('Export')->class('btn btn-primary btn-sm')}}
                            <a href="{{ route($directory.'.index') }}" class="btn btn-default bordered btn-sm">Cancel</a>
                        </div>
                    </div>
                {{ html()->form()->close() }}
            </div>
        </div>
    </div>
</main>
 @endsection  
 @push('jsscript')
<script>
$(document).ready(function() {
    $('#excelReportForm').on('submit', function(e) {
        e.preventDefault();

        var startDate = $('#start_date').val();
        var endDate = $('#end_date').val();
        var bank = $('#bank').val();
        //console.log(bank);
        $.ajax({
            url: '/generate-excel-report',
            type: 'POST',
            data: {
                start_date: startDate,
                end_date: endDate,
                bank: bank,
                _token: '{{ csrf_token() }}'  // Add CSRF token if using Laravel
            },
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response, status, xhr) {
                var filename = "";                   
                var disposition = xhr.getResponseHeader('Content-Disposition');
                if (disposition && disposition.indexOf('attachment') !== -1) {
                    var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    var matches = filenameRegex.exec(disposition);
                    if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
                }
                var linkElement = document.createElement('a');
                try {
                    var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
                    var url = window.URL.createObjectURL(blob);
                    linkElement.setAttribute('href', url);
                    linkElement.setAttribute("download", filename);
                    var clickEvent = new MouseEvent("click", {
                        "view": window,
                        "bubbles": true,
                        "cancelable": false
                    });
                    linkElement.dispatchEvent(clickEvent);
                } catch (ex) {
                    console.log(ex);
                }
            },
            error: function(response) {
                console.log('Error generating report:', response);
            }
        });
    });
});
</script>
@endpush
             
