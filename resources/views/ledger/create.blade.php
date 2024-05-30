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
                @if(in_array('view ledger date', permissions()))
                <input type="date" id="date" class="form-control w-25" value="{{ date('Y-m-d') }}"/>
                @else
                <input type="hidden" id="date" value="{{ date('Y-m-d') }}"/>
                @endif
                @foreach($accounts as $account)
                    @if(in_array($account->account_code, permissions()))    
                    <a href="/{{ $url.'/create/'.$account->id}}" class="btn btn-primary ms-1" tooltip="New">
                        <span class=" d-md-inline">{{$account->account_code}}</span>
                    </a>
                    @endif
                @endforeach
                <a href="{{route($url.'.index')}}" class="btn btn-primary ms-1" tooltip="New">
                    <span class=" d-md-inline">Back</span>
                </a>
            </div>
                
        </div>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="/{{$url}}">Listing</a></li>
        </ol>
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
