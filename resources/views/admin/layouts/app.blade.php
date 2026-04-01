<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'May Bill')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('assets/admin.css') }}?v={{ @filemtime(public_path('assets/admin.css')) }}" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="admin-shell">
        @include('admin.partials.sidebar')
        @include('admin.partials.header')

        <main class="admin-content">
            <div class="container-fluid px-3 px-lg-4">
                @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="py-3 py-lg-4">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <div class="sidebar-backdrop" data-sidebar-backdrop></div>

    <button class="back-to-top" type="button" data-back-to-top aria-label="Back to top" title="Back to top">
        <i class="bi bi-arrow-up"></i>
    </button>

    <div class="page-loader" data-page-loader aria-hidden="true">
        <div class="spinner-border text-success" role="status" aria-label="Loading"></div>
    </div>

    <div class="modal fade modal-top" id="confirmDeleteModal" tabindex="-1" aria-hidden="true" aria-labelledby="confirmDeleteTitle">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteTitle">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0" data-confirm-message>Do you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" data-confirm-submit>Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/admin.js') }}"></script>
</body>
</html>

