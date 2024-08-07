@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
        </div>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="/{{$url}}">Listing</a></li>
        </ol>
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="card mb-4">
            @include('errors.flash.message')
            <div class="card-title ps-3 pt-3 text-bold">
                User Device Records
            </div>
            <div class="card-body">
                @include($directory.'.table')
            </div>
        </div>
    </div>
</main>
 @endsection               
