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
                {{ html()->model($employee)->form('PUT', route($url.'.update',$employee))->autocomplete(false)->open() }}
                    <div class="row">
                        @include($directory.'.fields')
                        <div class="col-sm-12 mt-5">
                            {{ html()->submit('Save')->class('btn btn-primary btn-sm')}}
                            <a href="{{ route($url.'.index') }}" class="btn btn-default bordered btn-sm">Cancel</a>
                        </div>
                    </div>
                {{ html()->form()->close() }}
                <div class="row">
                    <div class="col-sm-12 text-end">
                        {{ html()->model($employee)->form('DELETE', route($url.'.twofactor',$employee))->open() }}
                            @csrf
                            @if ($employee->users[0]->two_factor_secret)
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Disable 2FA</button>
                            @endif
                        {{ html()->form()->close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
 @endsection               
