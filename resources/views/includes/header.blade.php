<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>{{$title}}</title>
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('/apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('/favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('/site.webmanifest')}}">
        <link href="{{asset('/assets/css/styles.css')}}" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{ asset('/assets/js/activity-monitor.js') }}"></script>

    </head>
    <body class="sb-nav-fixed sb-sidenav-toggled">        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-secondary">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" href="{{((in_array('employee dashboard', permissions()))?'/employee-dashboard':'/dashboard')}}">
                <img src="{{asset('/assets/images/watermark.png')}}" class="logo"/>
            </a>
            <a class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></a>
            <a class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" href="{{((in_array('employee dashboard', permissions()))?'/employee-dashboard':'/dashboard')}}"><i class="fas fa-home" aria-hidden="true"></i></a>
            <div class="dropdown d-md-inline-block ms-auto me-0 me-md-3 my-2 my-md-0">
                <span class="chat-icon text-end me-4" type="button" aria-expanded="false">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                    <i class="chat-count">5</i>
                </span>
                <span class="bell-icon text-end me-4" type="button" aria-expanded="false">
                    <i class="fa fa-bell" aria-hidden="true"></i>
                    <i class="bell-count">23</i>
                </span>
                <span class="text-dark text-end dropdown-toggle fs-custom" type="button" id="menu1" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="avatar">{{ nameInitials(Auth::user()->name)}}</span>
                    {{Auth::user()->name}} {{((Auth::user()->user_type!='super_admin')?'- ('.Auth::user()->employee_code.')':'')}}
                </span>
                <ul class="dropdown-menu" aria-labelledby="menu1">
                    <li><a class="dropdown-item" href="{{ route('two-factor.index') }}">2FA</a></li>
                    <li class="d-flex align-items-center">
                        <form method="POST" class="dropdown-item" id="logoutForm" action="{{route('logout')}}">
                            @csrf
                            <button class=" btn-none text-dark text-decoration-none" onclick = "return confirm('Are you sure?')" type="submit">
                                <i class="fa fa-power-off text-danger"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <!-- Sidebar Toggle-->
            
            <span class="text-dark text-end">
                
            </span>
            
            
        </nav>
