@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        
        <div class="card mt-1">
            @include('errors.flash.message')
            <div class="card-title ps-3 pt-3 text-bold d-flex">
                <h3 class="m-0 w-95">{{$title}} Active Records</h3>
                @if(in_array('create '.$directory, permissions()))
                <div class="mt-auto me-3 text-end">
                    <a href="{{route($url.'.create')}}" class="btn btn-primary" tooltip="New">
                        <span class=" d-md-inline">New</span>
                    </a>
                </div>
                @endif
            </div>
            <div class="card-body">
                @include($directory.'.table')
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-title ps-3 pt-3 text-bold">
                In-Active Records
            </div>
            <div class="card-body">
                @include($directory.'.inactives')
            </div>
        </div>
    </div>
</main>
 @endsection               
