<div class="col-xs-12 col-sm-12 col-md-2 col-lg-2 sidebar">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 site-logo-container">
            {{-- <h3 class="text-center site-logo">Penjadwalan Kuliah</h3> --}}
            <img src="{{ asset('images/Universitas Ubudiyah Indonesia.png') }}"
                style="height: 100px;width: 100px; margin-left: 50px" alt="...">
        </div>
    </div>

    @if (Auth::user()->lvl == 'admin')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <ul class="menu">
                <?php $page = Request::segment(1); ?>
                <li class="menu-link {{ ($page == 'dashboard') ? 'active' : '' }}">
                    <a href="/dashboard"><span class="fa fa-dashboard"></span><span class="text">Dashboard</span></a>
                </li>
                <li class="menu-link {{ ($page == 'rooms') ? 'active' : '' }}">
                    <a href="/rooms"><span class="fa fa-home"></span><span class="text">Ruang</span></a>
                </li>
                <li class="menu-link {{ ($page == 'courses') ? 'active' : '' }}">
                    <a href="/courses"><span class="fa fa-book"></span><span class="text">Mata kuliah</span></a>
                </li>
                <li class="menu-link {{ ($page == 'professors') ? 'active' : '' }}">
                    <a href="/professors"><span class="fa fa-graduation-cap"></span><span class="text">Dosen</span></a>
                </li>
                <li class="menu-link {{ ($page == 'classes') ? 'active' : '' }}">
                    <a href="/classes"><span class="fa fa-users"></span><span class="text">Prodi</span></a>
                </li>
                {{-- <li class="menu-link {{ ($page == 'timeslots') ? 'active' : '' }}">
                    <a href="/timeslots"><span class="fa fa-clock-o"></span><span class="text">Sesi</span></a>
                </li> --}}
                <li class="menu-link {{ ($page == 'my_account') ? 'active' : '' }}">
                    <a href="/my_account"><span class="fa fa-user"></span><span class="text">Akun</span></a>
                </li>
                <li class="menu-link">
                    <a href="/logout"><span class="fa fa-sign-out"></span><span class="text">Log Out</span></a>
                </li>
            </ul>
        </div>
    </div>
    @elseif (Auth::user()->lvl == 'mahasiswa')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <ul class="menu">
                <?php $page = Request::segment(1); ?>
                <li class="menu-link {{ ($page == 'dashboard') ? 'active' : '' }}">
                    <a href="/dashboard"><span class="fa fa-dashboard"></span><span class="text">Dashboard</span></a>
                </li>
                <li class="menu-link">
                    <a href="/logout"><span class="fa fa-sign-out"></span><span class="text">Log Out</span></a>
                </li>

            </ul>
        </div>
    </div>
    @elseif (Auth::user()->lvl == 'dosen')
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <ul class="menu">
                <?php $page = Request::segment(1); ?>
                <li class="menu-link {{ ($page == 'dashboard') ? 'active' : '' }}">
                    <a href="/dashboard"><span class="fa fa-dashboard"></span><span class="text">Dashboard</span></a>
                </li>
                <li class="menu-link">
                    <a href="/logout"><span class="fa fa-sign-out"></span><span class="text">Log Out</span></a>
                </li>

            </ul>
        </div>
    </div>
    @else
    <div class="unknown-role">
        <h2>Welcome!</h2>
        <p>Your role is not recognized.</p>
    </div>
    @endif
</div>