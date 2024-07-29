@extends('includes.app')

@section('content')

<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-75">
                {{$title}}
            </h3>
            <h3 class="mt-4 d-flex justify-content-end w-25">
                <input type="date" id="date" value="{{$date}}" class="form-control"/> 
            </h3>
        </div>
    
        <div class="d-flex flex-shrink-0">
            
            <!--end::Create app-->
        </div>
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="card bg-fff">
                            @include($directory.'.top-ten-winners')
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="card bg-fff">
                            @include($directory.'.top-ten-lossers')
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</main>
 @endsection               
