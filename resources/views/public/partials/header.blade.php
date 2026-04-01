<header class="admin-topbar" aria-label="Top bar">
    <div class="topbar-left">
        <img class="topbar-logo" src="{{ asset('assets/logo_netautocare1.png') }}" alt="May Bill Logo">

        <a class="topbar-title" href="{{ route('public.home') }}">
            {{ config('app.name', 'May Bill') }}
        </a>

        <button class="icon-btn" type="button" data-sidebar-toggle aria-label="Toggle sidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>

    <div class="topbar-right"></div>
</header>


