<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>ZedLytics</title>
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('/apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('/site.webmanifest')}}">
        <link href="{{asset('/assets/css/styles.css')}}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="bg-dark" style="background-color:#e2d1e5;">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card-header"><h3 class="text-center font-weight-light my-4"> 
                                <img src="{{asset('/assets/images/watermark.png')}}" class="logo"/></h3></div>
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
            
                <div class="card-header">{{ __('Two factor Challenge') }}</div>

                <div class="card-body">
                    <p class="text-left">
                        {{ __('Please enter your authentication code to login.') }}
                    </p>

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="code" class="col-md-4 col-form-label text-md-right">{{ __('Code:') }}</label>

                            <div class="col-md-6">
                                <input id="code" type="code" class="form-control @error('code') is-invalid @enderror" name="code" required autocomplete="current-code">

                                @error('code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-0 mt-2 form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>


            <!-- <div class="mt-3 card">
                <div class="card-header">{{ __('Two factor Recovery Code') }}</div>

                <div class="card-body">
                    <p class="text-center">
                        {{ __('Please enter your recovery code to login.') }}
                    </p>

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="recovery_code" class="col-md-4 col-form-label text-md-right">{{ __('Recovery Code:') }}</label>

                            <div class="col-md-6">
                                <input id="recovery_code" type="recovery_code" class="form-control @error('recovery_code') is-invalid @enderror" name="recovery_code" required autocomplete="current-recovery_code">

                                @error('recovery_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-0 form-group row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Submit') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> -->
        </div>
                        </div>
                    </div>
                </main>
            </div>
            <!-- <div id="layoutAuthentication_footer">
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div> -->
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="{{asset('/assets/js/scripts.js')}}"></script>
    </body>
</html>
