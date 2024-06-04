@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-25">
                {{$title}}
            </h3>
        </div>
        <div class="row">
            @foreach($accounts as $account)
                @if(in_array($account->account_code, permissions()))
                <div class="col-sm-1 mb-1">
                    <a href="/{{ $url.'/create/'.$account->id}}" class="btn btn-primary ms-1 fs-8 w-100" tooltip="New">
                        <span class=" d-md-inline">{{$account->account_code}}</span>
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
                @include($directory.'.table')
            </div>
        </div>
    </div>
</main>
 @endsection               
