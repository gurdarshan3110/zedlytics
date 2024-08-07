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
                                    <th width="20%">Account Id</th>
                                    <th width="30%">Name</th>
                                    <th width="30%">Username</th>
                                    <th width="20%">PNL</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $previousProfit = 0; 
                                    @endphp
                                    @foreach($childs as $k=> $client)
                                    @php
                                        // Calculate growth direction
                                        $growthClass = '';
                                        if ($previousProfit !== null) {
                                            if ($client['totalCloseProfit'] > 0) {
                                                $growthClass = 'text-amount-success text-white'; // Increasing
                                            } elseif ($client['totalCloseProfit'] < 0) {
                                                $growthClass = 'text-amount-danger text-white'; // Decreasing
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td title="{{ucwords($client['accountId'])}}">{{ucwords($client['accountId'])}}</td>
                                        <td title="{{ucwords($client['name'])}}">{{ucwords($client['name'])}}</td>
                                        <td title="{{ucwords($client['username'])}}">{{ucwords($client['username'])}}</td>
                                        <td class="text-end">                               
                                            <span class="{{$growthClass}}">
                                                {{$client['totalCloseProfit']}}
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