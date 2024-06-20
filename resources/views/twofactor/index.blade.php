@extends('includes.app')

@section('content')
@push('jsscript')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
<main>
    <div class="container-fluid px-4">
        <div class="d-flex">
            <h3 class="mt-4 w-95">
                {{$title}}<br>
                <a class="btn btn-primary" href="{{route('two.factor')}}">Enable 2fa</a>
            </h3>
        </div>
        
    </div>
</main>  
@endsection
