<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Menu Items</div>
                    <?php 

                        $modules = softModules();
                        $permissions = permissions();
                    ?>
                    @foreach ($modules as $module)
                        @php
                            $permissionKey = 'view ' . $module->url;
                        @endphp
                        @if (in_array($permissionKey, $permissions))
                            <a class="nav-link {{ request()->is($module->url . '*') ? 'active' : '' }}" href="{{ route($module->url.'.index') }}">
                                <div class="sb-nav-link-icon">
                                    <img src="{{ $module->icon ? asset($module->icon) : asset('/assets/images/default-icon.png') }}" class="icon">
                                </div>
                                {{$module->name}}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="sb-sidenav-footer px-3 text-muted">
                <div class="small">Logged in as:</div>
                {{Auth::user()->name}}
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        @yield('content')
        @yield('footer')