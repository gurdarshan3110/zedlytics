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
                
            </h3>
        </div>
        <div class="card">
                <div class="card-header">{{ __('Two Factor Authentication') }}</div>

                <div class="card-body">
                    @if (session('status') == "two-factor-authentication-disabled")
                        <div class="alert alert-success" role="alert">
                            Two factor Authentication has been disabled.
                        </div>
                    @endif

                    @if (session('status') == "two-factor-authentication-enabled")
                        <div class="alert alert-success" role="alert">
                            Two factor Authentication has been enabled.
                        </div>
                    @endif


                    <form method="POST" action="/user/two-factor-authentication">
                        @csrf

                        @if (auth()->user()->two_factor_secret)
                            @method('DElETE')

                            <div class="pb-5">
                                {!! auth()->user()->twoFactorQrCodeSvg() !!}
                            </div>

                            <div>
                                <h3>Recovery Codes:</h3>

                                <ul>
                                    @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes)) as $code)
                                        <li>{{ $code }}</li>
                                    @endforeach
                                </ul>
                            </div>

                            <button class="btn btn-danger">Disable</button>
                        @else
                            <button class="btn btn-primary">Enable</button>
                        @endif
                    </form>
                </div>
            </div>
    </div>
</main>  
@endsection
