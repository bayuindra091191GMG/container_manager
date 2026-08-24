<div class="sidebar-wrapper" sidebar-layout="stroke-svg">
    <div>
        <div class="logo-wrapper"><a href="{{ route('dashboard') }}">
                <div class="d-flex flex-row align-items-center">
                    <img class="img-fluid for-light" src="{{ asset('assets/images/logo/logo-only.png') }}" alt=""
                        style="height: 35px;">
                    <img class="img-fluid for-dark" src="{{ asset('assets/images/logo/logo-only.png') }}" alt="">

                    <h3 class="fw-bold ms-2" style="font-family: Arsenal">CMS</h3>
                </div>
            </a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper"><a href="{{ route('dashboard') }}"><img class="img-fluid"
                    src="{{ asset('assets/images/logo/logo-only.png') }}" alt="" style="height: 35px;"></a></div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2"
                                aria-hidden="true"></i></div>
                    </li>
                    <li class="sidebar-list">
                        <a class="sidebar-link sidebar-title link-nav {{ \Illuminate\Support\Facades\Route::is('dashboard') ? '' : 'active' }}"
                            href="{{ route('dashboard') }}">
                            <svg class="stroke-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#stroke-home') }}"></use>
                            </svg>
                            <svg class="fill-icon">
                                <use href="{{ asset('assets/svg/icon-sprite.svg#fill-home') }}"></use>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    @if (auth()->user()->is_superadmin ||
                            auth()->user()->hierarchy->hasPermissionTo('branch.list')||
                            auth()->user()->hierarchy->hasPermissionTo('warehouse.list')||
                            auth()->user()->hierarchy->hasPermissionTo('distributor.list')||
                            auth()->user()->hierarchy->hasPermissionTo('consignee.list'))
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title" href="#">
                                <i data-feather="database"></i>
                                <span>Master Data</span>
                            </a>
                            <ul class="sidebar-submenu">

                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('branch.list'))
                                    <li>
                                        <a href="{{ route('branch') }}">Branch</a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('consignee.list'))
                                    <li>
                                        <a href="{{ route('consignee') }}">Consignee</a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('warehouse.list'))
                                    <li>
                                        <a href="{{ route('warehouse') }}">Warehouse</a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('distributor.list'))
                                    <li>
                                        <a href="{{ route('distributor') }}">Distributor</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (auth()->user()->is_superadmin ||
                            auth()->user()->hierarchy->hasPermissionTo('si.list') ||
                            auth()->user()->hierarchy->hasPermissionTo('do.list') ||
                            auth()->user()->hierarchy->hasPermissionTo('container.list') ||
                            auth()->user()->hierarchy->hasPermissionTo('container_history.list'))
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title" href="#">
                                <i data-feather="monitor"></i>
                                <span>Shipping Instruction</span>
                            </a>
                            <ul class="sidebar-submenu">
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('do.list'))
                                    <li>
                                        <a href="{{ route('delivery_order') }}">DO List</a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('si.list'))
                                    <li>
                                        <a href="{{ route('shipping_instruction') }}">SI List</a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('container.list'))
                                    <li>
                                        <a href="{{ route('container') }}">Container List</a>
                                    </li>
                                @endif
                                @if (auth()->user()->is_superadmin ||
                                        auth()->user()->hierarchy->hasPermissionTo('container_history.list'))
                                    <li>
                                        <a href="{{ route('container_history') }}">Container History</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if (auth()->user()->is_superadmin ||
                            auth()->user()->hierarchy->hasPermissionTo('warehouse_management.list'))
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title" href="#">
                                <i data-feather="monitor"></i>
                                <span>Warehouse Management</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li>
                                    <a href="{{ route('trx_warehouse') }}">List</a>
                                </li>
                                <li>
                                    <a href="{{ route('container.management') }}">Management</a>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @if (auth()->user()->is_superadmin)
                        <li class="sidebar-list">
                            <a class="sidebar-link sidebar-title" href="#">
                                <i data-feather="settings"></i>
                                <span>Admin</span>
                            </a>
                            <ul class="sidebar-submenu">
                                <li>
                                    <a href="{{ route('level') }}">Level</a>
                                </li>
                                <li>
                                    <a href="{{ route('access') }}">Menu Access</a>
                                </li>
                                <li>
                                    <a href="{{ route('user') }}">Users</a>
                                </li>
                            </ul>
                        </li>
                    @endif

                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>
