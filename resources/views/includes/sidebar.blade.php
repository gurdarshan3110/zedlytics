<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-light" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <?php 
                        $modules = softModules(0);
                        $permissions = permissions();
                    ?>

                    @foreach ($modules as $module)
                        @php
                            $childModules = softModules($module->id);
                            $hasActiveChild = false;
                            foreach ($childModules as $childModule) {
                                $childModuleUrl = str_replace('-', ' ', $childModule->url);
                                if (request()->is($childModule->url . '*')) {
                                    $hasActiveChild = true;
                                    break;
                                }
                            }
                        @endphp

                        <a class="nav-link {{ $hasActiveChild ? '' : 'collapsed' }}" href="{{ count($childModules) > 0 ? '#' : route($module->url . '.index') }}" data-bs-toggle="{{ count($childModules) > 0 ? 'collapse' : '' }}" data-bs-target="#moduleCollapse{{ $module->id }}" aria-expanded="{{ $hasActiveChild ? 'true' : 'false' }}" aria-controls="moduleCollapse{{ $module->id }}">
                            <div class="sb-nav-link-icon"><i class="fas {{ $module->icon }}"></i></div>
                            {{ $module->name }}
                            @if (count($childModules) > 0)
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            @endif
                        </a>
                        @if (count($childModules) > 0)
                            <div class="collapse {{ $hasActiveChild ? 'show' : '' }}" id="moduleCollapse{{ $module->id }}" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    @foreach ($childModules as $childModule)
                                        @php
                                            $childModuleUrl = str_replace('-', ' ', $childModule->url);
                                            $childPermissionKey = 'view ' . $childModuleUrl;
                                            if($childModuleUrl=='margin limit menu'){
                                               $childPermissionKey = 'view margin limit';
                                            }
                                        @endphp
                                        @if (in_array($childPermissionKey, $permissions))
                                            <a class="nav-link sub-menu-item {{ request()->is($childModule->url . '*') ? 'active' : '' }}" href="{{ route($childModule->url . '.index') }}">
                                                <i class="fas fa-circle ms-4 me-2"></i>
                                                {{ $childModule->name }}
                                            </a>
                                        @endif
                                    @endforeach
                                </nav>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="sb-sidenav-footer bg-primary px-3 text-light">
                &copy; ZedLytics {{ date('Y') }}
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        @yield('content')
        @yield('footer')