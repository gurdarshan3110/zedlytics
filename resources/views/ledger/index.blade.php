@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
            @if(in_array('create '.$directory, permissions()))
                @foreach($accounts as $account)
                <div class="mt-auto me-3">
                    <a href="{{ $url.'/create/'.$account->id}}" class="btn btn-primary" tooltip="New">
                        <span class=" d-md-inline">{{$account->account_code}}</span>
                    </a>
                </div>
                @endforeach
            @endif
        </div>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="/{{$url}}">Listing</a></li>
        </ol>
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
