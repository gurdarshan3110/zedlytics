<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <?php 
                        $modules = softModules();
                        $permissions = permissions();
                    ?>

                    @php
                        $activeModule = false;
                    @endphp

                    @foreach ($modules as $i => $module)
                        @php
                            $moduleurl = str_replace('-', ' ', $module->url);
                            if ($module->url == 'margin-limit-menu') {
                                $moduleurl = 'margin limit';
                            }
                            $permissionKey = (($i == 0) ? 'dashboard' : 'view ' . $moduleurl);
                        @endphp
                        @if (in_array($permissionKey, $permissions) && request()->is($module->url . '*'))
                            @php
                                $activeModule = true;
                            @endphp
                        @endif
                    @endforeach

                    <a class="nav-link collapsed {{ $activeModule ? '' : 'collapsed' }}" href="#" data-bs-toggle="collapse" data-bs-target="#menuListCollapse" aria-expanded="{{ $activeModule ? 'true' : 'false' }}" aria-controls="menuListCollapse">
                        <div class="sb-nav-link-icon"><i class="fas fa-bars"></i></div>
                        Menu List
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>
                    <div class="collapse {{ $activeModule ? 'show' : '' }}" id="menuListCollapse" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                        <nav class="sb-sidenav-menu-nested nav">
                            @foreach ($modules as $i => $module)
                                @php
                                    $moduleurl = str_replace('-', ' ', $module->url);
                                    if ($module->url == 'margin-limit-menu') {
                                        $moduleurl = 'margin limit';
                                    }
                                    $permissionKey = (($i == 0) ? 'dashboard' : 'view ' . $moduleurl);
                                @endphp
                                @if (in_array($permissionKey, $permissions))
                                    <a class="nav-link {{ request()->is($module->url . '*') ? 'active' : '' }}" href="{{ route($module->url . '.index') }}">
                                        <div class="sb-nav-link-icon">
                                            <i class="fas {{ $module->icon }}"></i>
                                        </div>
                                        {{ $module->name }}
                                    </a>
                                @endif
                            @endforeach
                        </nav>
                    </div>
                </div>
            </div>
            <div class="sb-sidenav-footer bg-primary  px-3 text-light">
                &copy; ZedLytics {{ date('Y') }}
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        @yield('content')
        @yield('footer')
