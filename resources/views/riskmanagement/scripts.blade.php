@extends('includes.app')

@section('content')
<script>
    function autoReload() {
        setTimeout(function() {
            location.reload();
        }, 120000); 
    }
    window.onload = autoReload;
</script>
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-75">
                {{$title}}
            </h3>
            <h3 class="mt-4 d-flex justify-content-end w-25">
                <!-- <input type="date" id="date" value="{{$date}}" class="form-control"/>  -->
            </h3>
        </div>
    
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card bg-fff p-2">
                    <div class="table-responsive">
                        <table class="w-100 align-middle fs-6 table table-bordered" id="market-winners">
                            <thead>
                            <tr>
                                <th width="75%">Name</th>
                                <th width="25%">PNL</th>
                            </tr>
                            </thead>
                            <tbody>
                                @php
                                    $previousProfit = 0; 
                                @endphp
                                @if(count($scripts)>0)
                                    @foreach($scripts as $winner)
                                        @php
                                            // Calculate growth direction
                                            $growthClass = '';
                                            if ($previousProfit !== null) {
                                                if ($winner['totalCloseProfit'] > 0) {
                                                    $growthClass = 'text-amount-success text-white'; // Increasing
                                                } elseif ($winner['totalCloseProfit'] < 0) {
                                                    $growthClass = 'text-amount-danger text-white'; // Decreasing
                                                }
                                            }
                                        @endphp
                                        <tr>
                                            @if($winner->currencyId==562)
                                                <td class="name-cell" title="{{$winner->currency->name}}"><a href="/market-details/{{$winner->currencyId}}" target="_blank" class="text-dark text-decoration-none">{{$winner->currency->name}}</a></td>
                                            @else
                                                <td class="name-cell" title="{{$winner->currency->name}}"><a href="/client-details/{{$winner->currencyId}}" target="_blank" class="text-dark text-decoration-none">{{$winner->currency->name}}</a></td>
                                            @endif
                                            <td class="text-end">                              
                                                <span class="{{$growthClass}}">
                                                    {{$winner['totalCloseProfit']}}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center">no records found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@push('jsscript')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js"></script>
    <script type="text/javascript">
        var table = $('#market-winners').DataTable({
            "order": [[ $('#market-winners thead th').length - 1, 'desc' ]]
        });
    </script>
@endpush
@endsection 