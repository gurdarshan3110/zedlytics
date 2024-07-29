<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zedlytics - Login</title>
    <!-- Bootstrap CSS -->
    <link href="http://15.206.42.216/assets/css/styles.css" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .login-container {
            height: 100vh;
        }
        .login-image {
            background: url('your-image-url.jpg') no-repeat center center;
            background-size: cover;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px;
        }
        .social-icons a {
            font-size: 24px;
            margin: 0 10px;
            color: #000;
        }
        .col-h-100{
            height: 100vh;
        }
    </style>
</head>
<body>
    <div class="container-fluid login-container">
        <div class="row">
            <div class="col-md-6 bg-primary col-h-100 login-image overflow-hidden p-0">
                <img src="{{asset('/assets/images/ai.gif')}}" alt="ai" class="col-h-100">
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <div class="login-form w-100">
                    <div class="text-center mb-5 mt-4">
                        <img src="{{asset('/assets/images/watermark.png')}}" alt="Logo" style="width: 300px;">
                    </div>
                    @include('errors.flash.message')
                    <form  class="form w-100" novalidate="novalidate" id="login-form" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com">
                        </div>
                        @error('login')
                            <span class="text-light text-xs">{{ $message }}</span>
                        @enderror
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                        </div>
                        @error('password')
                            <span class="text-light text-xs">{{ $message }}</span>
                        @enderror
                        <div class="fv-plugins-message-container invalid-feedback pt-5 pb-5">{{session()->get('error')}}</div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <div class="text-center mt-5">
                        <p>Or sign in with</p>
                        <div class="social-icons">
                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-google"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.2.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>