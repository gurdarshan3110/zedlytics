@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
            <div class="mt-auto me-3">
                <a href="{{route($url.'.index')}}" class="btn btn-primary" tooltip="New">
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
