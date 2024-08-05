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
                    <div class="container">
                        <div class="table-responsive">
                            <table class="w-100 align-middle fs-6 table table-bordered" border="0" cellpadding="0" cellspacing="0" id="top-table-winners">
                                <thead>
                                <tr>
                                    <th width="70%">Name</th>
                                    <th width="30%">PNL</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previousProfit = 0; 
                                    @endphp
                                    @foreach($markets as $k=> $market)
                                    @php
                                        // Calculate growth direction
                                        $growthClass = '';
                                        if ($previousProfit !== null) {
                                            if ($market['totalCloseProfit'] > 0) {
                                                $growthClass = 'text-amount-success text-white'; // Increasing
                                            } elseif ($market['totalCloseProfit'] < 0) {
                                                $growthClass = 'text-amount-danger text-white'; // Decreasing
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="name-cell" title="{{$market['name']}}"><a href="/client-details/{{$market['id']}}" target="_blank" class="text-dark text-decoration-none">{{$market['name']}}</a></td>
                                        <td class="text-end">                               
                                            <span class="{{$growthClass}}">
                                                {{$market['totalCloseProfit']}}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection 