@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4 mt-1">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="user-details-tab" data-bs-toggle="tab" data-bs-target="#user-details" type="button" role="tab" aria-controls="User Details" aria-selected="true">User Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="timeline-tab" data-bs-toggle="tab" data-bs-target="#timeline" type="button" role="tab" aria-controls="Timeline" aria-selected="true">Timeline</button>
            </li>
            <li class="nav-item" role="presentation">
                <a href="{{route($url.'.index')}}" class="nav-link" id="new-{{$title}}-tab" tooltip="Back">
                    <span class=" d-md-inline">Back</span>
                </a>
            </li>
            
        </ul>
        <!-- Tab panes -->
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane card fade show active" id="user-details" role="tabpanel" aria-labelledby="user-details-tab">
                <div class="card-body">
                    @include($directory.'.show-details')
                </div>
            </div>
            <div class="tab-pane card fade" id="timeline" role="tabpanel" aria-labelledby="timeline-tab">
                
                <div class="card-body">
                    TimeLine
                </div>
            </div>
        </div>
    </div>
</main>
 @endsection               
