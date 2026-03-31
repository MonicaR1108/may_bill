<aside class="admin-sidebar" aria-label="Sidebar">
    <nav class="sidebar-nav">
        @php
            $mastersActive = request()->routeIs('admin.master-items.*') || request()->routeIs('admin.service-master.*');
        @endphp

        <a class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="bi bi-house-door"></i>
            <span class="label">Dashboard</span>
        </a>

        <div class="sidebar-group {{ $mastersActive ? 'sidebar-group-open' : '' }}">
            <button
                class="sidebar-link sidebar-group-toggle {{ $mastersActive ? 'active' : '' }}"
                type="button"
                data-sidebar-group="masters"
                aria-expanded="{{ $mastersActive ? 'true' : 'false' }}"
            >
                <i class="bi bi-grid-3x3-gap"></i>
                <span class="label">Masters</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </button>
            <div class="sidebar-subnav" data-sidebar-group-panel="masters">
                <a class="sidebar-sublink {{ request()->routeIs('admin.master-items.*') ? 'active' : '' }}" href="{{ route('admin.master-items.index') }}">
                    <i class="bi bi-box-seam"></i>
                    <span class="label">Item Master</span>
                </a>
                <a class="sidebar-sublink {{ request()->routeIs('admin.service-master.*') ? 'active' : '' }}" href="{{ route('admin.service-master.index') }}">
                    <i class="bi bi-clipboard-check"></i>
                    <span class="label">Service Master</span>
                </a>
            </div>
        </div>

        <a class="sidebar-link {{ request()->routeIs('admin.user-details.*') ? 'active' : '' }}" href="{{ route('admin.user-details.index') }}">
            <i class="bi bi-people"></i>
            <span class="label">User Details</span>
        </a>

        <a class="sidebar-link {{ request()->routeIs('admin.transactions.*') ? 'active' : '' }}" href="{{ route('admin.transactions.index') }}">
            <i class="bi bi-receipt"></i>
            <span class="label">Transactions</span>
        </a>
    </nav>
</aside>
