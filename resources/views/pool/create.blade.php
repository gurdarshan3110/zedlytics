@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-25 d-flex align-items-center">
                {{$title}}  
                <div class="green-blinking ms-2" id="response-status">&nbsp;</div>
            </h3>

            <div class="mt-auto w-75 align-items-end d-flex justify-content-end">
                <div class="bank-row ms-2 p-2 rounded">Bank</div>
                <div class="party-row ms-2 p-2 rounded me-2">Party</div>
                @if(in_array('view ledger date', permissions()))
                    @if(in_array('restrict ledger date', permissions()))
                    @php
                        $yesterday = \Carbon\Carbon::now()->subDays(3)->toDateString();
                        $today = \Carbon\Carbon::today()->toDateString();
                    @endphp
                    {{ html()->date('date')
                        ->class('form-control w-25')
                        ->id('date')
                        ->attribute('min', $yesterday)
                        ->attribute('max', $today) }}
                    @else
                    @php
                    $today = \Carbon\Carbon::today()->toDateString();
                    @endphp
                    {{ html()->date('date')
                        ->class('form-control w-25')
                        ->id('date')
                        ->value($today)}}
                    @endif
                @else
                <input type="hidden" id="date" value="{{ date('Y-m-d') }}"/>
                @endif
                <a href="{{route($url.'.index')}}" class="btn btn-primary ms-1" tooltip="New">
                    <span class=" d-md-inline">Back</span>
                </a>
            </div>
                
        </div>
        <div class="row mt-1">
            @foreach($accounts as $account)
                @if(in_array($account->account_code, permissions()))
                <?php $bank = bankAccount($account->account_code);?>
                <div class="col-sm-2 mb-1">
                    <a href="/{{ $url.'/create/'.$account->id}}" class="btn btn-primary ms-1 fs-7 w-100 {{(($bankId==$bank->id)?'active-account':'')}}" tooltip="New">
                        <span class=" d-md-inline">{{$account->account_code}}</span><br>
                        <span class=" d-md-inline">{{$bank->bankBalance()}}</span>
                        <input type="hidden" id="bank_{{$bank->id}}" value="{{$bank->closingBalance(date('Y-m-d',strtotime('-1 day')))}}"/>
                    </a>
                </div>
                @endif
            @endforeach
        </div>
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="card mb-4">
            <div class="card-body">
                @include($directory.'.fields')       
            </div>
        </div>
    </div>
</main>
 @endsection               
