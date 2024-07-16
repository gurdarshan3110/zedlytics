@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <!-- <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}
            </h3>
            @if(in_array('create '.$directory, permissions()))
            <div class="mt-auto me-3">
                <a href="{{route($url.'.create')}}" class="btn btn-primary" tooltip="New">
                    <span class=" d-md-inline">New</span>
                </a>
            </div>
            @endif
        </div>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="/{{$url}}">Listing</a></li>
        </ol>
        <div class="d-flex flex-shrink-0">
            
        </div> -->
        <div class="card mb-4 mt-1">
            @include('errors.flash.message')
            <div class="card-title ps-3 pt-3 text-bold">
                {{$title}}
            </div>
            <div class="card-body">
                @include($directory.'.table')
            </div>
        </div>
    </div>
</main>
 @endsection               
