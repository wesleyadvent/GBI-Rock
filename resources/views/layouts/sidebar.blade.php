<!-- Sidebar Start -->
<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="#" class="text-nowrap logo-img">
                <img src=" {{ asset('assets/images/logos/dark-logo.svg') }}" width="180" alt="" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-8"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Home</span>
                </li>

                <!--Dashboard-->
                <li class="sidebar-item">
                    @if (Auth::check() && Auth::user()->role === 'sekretaris')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('sekretaris.dashboard') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('jadwal.published') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-calendar"></i>
                            </span>
                            <span class="hide-menu">Jadwal Pelayanan</span>
                        </a>
                    </li>
                    @elseif (Auth::check() && Auth::user()->role === 'koordinator_bidang')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('koordinator.dashboard') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('jadwal.published') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-calendar"></i>
                            </span>
                            <span class="hide-menu">Jadwal Pelayanan</span>
                        </a>
                    </li>
                    @elseif (Auth::check() && Auth::user()->role === 'penatua')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('penatua.dashboard') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('jadwal.published') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-calendar"></i>
                            </span>
                            <span class="hide-menu">Jadwal Pelayanan</span>
                        </a>
                    </li>
                    @elseif (Auth::check() && Auth::user()->role === 'pekerja')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('pekerja.dashboard') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('jadwal.published') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-calendar"></i>
                            </span>
                            <span class="hide-menu">Jadwal Pelayanan</span>
                        </a>
                    </li>
                    @elseif (Auth::check() && Auth::user()->role === 'admin')
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.dashboard') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-layout-dashboard"></i>
                            </span>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                    </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('jadwal.published') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-calendar"></i>
                            </span>
                            <span class="hide-menu">Jadwal Pelayanan</span>
                        </a>
                    </li>
                    @endif   
                </li>

                <!--Manjemen Akun-->
                @if (Auth::user()->role === 'admin')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Manajemen Akun</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.user.index') }}" aria-expanded="false">
                            <span>
                                <i class="bi bi-person-lines-fill"></i>
                            </span>
                            <span class="hide-menu">Lihat Semua Akun</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.user.create') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-user-plus"></i>
                            </span> 
                            <span class="hide-menu">Buat Akun </span>
                        </a>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Manajemen Bidang</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.bidang.index') }}" aria-expanded="false">
                            <span>
                                <i class="bi bi-diagram-3"></i>
                            </span>
                            <span class="hide-menu">Lihat Semua Bidang</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.bidang.create') }}" aria-expanded="false">
                            <span>
                                <i class="bi bi-diagram-3-fill"></i>
                            </span> 
                            <span class="hide-menu">Buat Bidang</span>
                        </a>
                    </li>
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Manajemen Jadwal</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('admin.pembicara-eksternal') }}" aria-expanded="false">
                            <span>
                                <i class="bi bi-diagram-3"></i>
                            </span>
                            <span class="hide-menu">Tambah Pembicara Eksternal</span>
                        </a>
                    </li>
                    
                @endif

                @if (Auth::user()->role === 'koordinator_bidang')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Manajemen Akun</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('koordinator.pekerja.index') }}"
                            aria-expanded="false">
                            <span>
                                <i class="bi bi-people-fill"></i>
                            </span>
                            <span class="hide-menu">Lihat Akun Pekerja</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('koordinator.pekerja.create') }}"
                            aria-expanded="false">
                            <span>
                                <i class="bi bi-person-fill-add"></i>
                            </span>
                            <span class="hide-menu">Tambah Akun Pekerja</span>
                        </a>
                    </li>

                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Tim Pelayanan</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('timPelayanan.index') }}" aria-expanded="false">
                            <span>
                                <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Buat Tim Pelayanan</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'pekerja')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Pelayanan Kebaktian</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('pekerja.index') }}" aria-expanded="false">
                            <span><i class="ti ti-cards"></i></span>
                            <span class="hide-menu">Permintaan Pelayanan</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'sekretaris')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Manage Jadwal Kebaktian</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('sekretaris.jadwal.create') }}"
                            aria-expanded="false">
                            <span><i class="ti ti-cards"></i></span>
                            <span class="hide-menu">Tambah Jadwal Kebaktian</span>
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('sekretaris.jadwal.index') }}" aria-expanded="false">
                            <span><i class="ti ti-cards"></i></span>
                            <span class="hide-menu">Lihat Pengajuan Jadwal</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role === 'penatua')
                    <li class="nav-small-cap">
                        <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                        <span class="hide-menu">Jadwal Kebaktian</span>
                    </li>
                    <li class="sidebar-item">
                        <a class="sidebar-link" href="{{ route('penatua.jadwal') }}"
                            aria-expanded="false">
                            <span>
                                <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Approval Jadwal Kebaktian</span>
                        </a>
                    </li>
                @endif

                <li class="nav-small-cap">
                    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
                    <span class="hide-menu">Log Out</span>
                </li>
                <li class="sidebar-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="sidebar-link bg-transparent border-0 p-0"
                            style="width:100%; text-align:left;">
                            <span><i class="ti ti-login"></i></span>
                            <span class="hide-menu">Log Out</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>
<!--  Sidebar End -->
