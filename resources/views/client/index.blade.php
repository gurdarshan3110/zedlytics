@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4 mt-1">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-{{$title}}-tab" data-bs-toggle="tab" data-bs-target="#active-{{$title}}" type="button" role="tab" aria-controls="Active {{$title}}" aria-selected="true">Active {{$title}}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="inactive-{{$title}}-tab" data-bs-toggle="tab" data-bs-target="#inactive-{{$title}}" type="button" role="tab" aria-controls="In Active {{$title}}" aria-selected="true">In Active {{$title}}</button>
            </li>
            @if(in_array('create '.$directory, permissions()))
            <li class="nav-item" role="presentation">
                <a href="{{route($url.'.create')}}" class="nav-link" id="new-{{$title}}-tab" tooltip="New">
                    <span class=" d-md-inline">New</span>
                </a>
            </li>
            @endif
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane card fade show active" id="active-{{$title}}" role="tabpanel" aria-labelledby="active-{{$title}}-tab">
                @include('errors.flash.message')
                <div class="card-body">
                    @include($directory.'.table')
                </div>
            </div>
            <div class="tab-pane card fade" id="inactive-{{$title}}" role="tabpanel" aria-labelledby="inactive-{{$title}}-tab">
                <div class="card-body">
                    @include($directory.'.inactives')
                </div>
            </div>
        </div>
    </div>
</main>
 @endsection               
