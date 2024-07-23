<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <?php 

                        $modules = softModules();
                        $permissions = permissions();
                    ?>
                    @foreach ($modules as $i=> $module)
                        @php
                            $moduleurl = str_replace('-',' ',$module->url);
                            if($module->url=='margin-limit-menu'){
                                $moduleurl = 'margin limit';
                            }
                            $permissionKey = (($i==0)?'dashboard':'view ' . $moduleurl);
                        @endphp
                        @if (in_array($permissionKey, $permissions))
                            <a class="nav-link {{ request()->is($module->url . '*') ? 'active' : '' }}" href="{{ route($module->url.'.index') }}">
                                <div class="sb-nav-link-icon">
                                    <i class="fas {{ $module->icon}}"></i>
                                </div>
                                {{$module->name}}
                            </a>
                        @endif
                    @endforeach
                    <a class="nav-link {{ request()->is('two-factor' . '*') ? 'active' : '' }}" href="{{ route('two-factor.index') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        2FA
                    </a>
                </div>
            </div>
            <div class="sb-sidenav-footer bg-secondary px-3 text-muted">
             &copy; ZedLytics {{date('Y')}}
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        @yield('content')
        @yield('footer')