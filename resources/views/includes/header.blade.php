<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>ZedLytics - {{$title}}</title>
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('/apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('/site.webmanifest')}}">
        <link href="{{asset('/assets/css/styles.css')}}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('/assets/js/activity-monitor.js') }}"></script>

    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-secondary">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="{{((in_array('employee dashboard', permissions()))?'/employee-dashboard':'/dashboard')}}">
                <img src="{{asset('/assets/images/watermark.png')}}" class="logo"/>
            </a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <a class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" href="{{((in_array('employee dashboard', permissions()))?'/employee-dashboard':'/dashboard')}}"><i class="fas fa-home" aria-hidden="true"></i></a>
            
            <!-- Sidebar Toggle-->
            <form method="POST" class="d-md-inline-block ms-auto me-0 me-md-3 my-2 my-md-0" action="{{route('logout')}}">
                        @csrf
            <span class="text-light text-end">
                {{Auth::user()->name}} - ({{((Auth::user()->user_type!='Super Admin')?Auth::user()->employee_code:'')}})
            </span>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" onclick = "return confirm('Are you sure?')" type="submit">
                <i class="fa fa-power-off fs-3 text-danger"></i>
                    </button>
            </form>
            
        </nav>
