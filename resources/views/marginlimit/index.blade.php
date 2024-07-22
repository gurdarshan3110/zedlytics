@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @foreach($brands as $k => $brand)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{(($k==0)?'active':'')}}" id="active-{{$brand->id}}-tab" data-bs-toggle="tab" data-bs-target="#active-{{$brand->id}}" type="button" role="tab" aria-controls="Active {{$brand->name}}" aria-selected="true">{{$brand->name}}</button>
            </li>
            @endforeach
        </ul>
        <div class="tab-content" id="myTabContent">
            @foreach($brands as $k => $brand)
            <div class="tab-pane card fade {{(($k==0)?'show active':'')}}" id="active-{{$brand->id}}" role="tabpanel" aria-labelledby="active-{{$brand->id}}-tab">
                @include('errors.flash.message')
                <div class="card-body">
                    @include($directory.'.table')
                </div>
            </div>
            @endforeach
        </div>
    </div>
</main>
 @endsection               
