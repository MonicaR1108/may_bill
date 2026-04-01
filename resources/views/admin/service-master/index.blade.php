@extends('admin.layouts.app')

@section('title', 'Service Master')

@section('content')
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h1 class="h4 mb-0">Service Master</h1>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('admin.service-master.index') }}" class="d-flex align-items-center gap-2 flex-wrap" data-auto-submit>
                <input
                    type="text"
                    name="q"
                    value="{{ $search ?? request('q', '') }}"
                    class="form-control form-control-sm"
                    placeholder="Search services..."
                >
            </form>
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addServiceMaster" aria-expanded="false" aria-controls="addServiceMaster">
                <i class="bi bi-plus-circle me-2"></i>Add Service
            </button>
        </div>
    </div>

    <div class="collapse mb-4" id="addServiceMaster">
        <div class="card card-soft rounded-4">
            <div class="card-body p-4 position-relative">
                <button
                    type="button"
                    class="btn-close position-absolute top-0 end-0 m-3"
                    data-bs-toggle="collapse"
                    data-bs-target="#addServiceMaster"
                    aria-label="Close"
                ></button>
                <form method="POST" action="{{ route('admin.service-master.store') }}" class="row g-3 align-items-end" novalidate>
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label">Service Name</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            required
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="1" @selected(old('status', '1') === '1')>Active</option>
                            <option value="0" @selected(old('status') === '0')>Inactive</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-check2-circle me-2"></i>Save Service
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card card-soft rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm table-admin align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 text-center">S.No</th>
                            <th>Service Name</th>
                            <th class="text-center">No. of Items</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($services as $service)
                            @php $token = \App\Models\Service::idToToken((int) $service->ID); @endphp
                            <tr>
                                <td class="ps-4 text-center">{{ ($services->currentPage() - 1) * $services->perPage() + $loop->iteration }}</td>
                                <td class="fw-semibold text-nowrap">{{ $service->ServiceName }}</td>
                                <td class="text-center">{{ (int) $service->items_count }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.service-master.status', ['token' => $token]) }}">
                                        @csrf
                                        @method('PUT')
                                        <select
                                            name="status"
                                            class="status-select form-select form-select-sm {{ (int) $service->Status === 1 ? 'status-active' : 'status-inactive' }}"
                                            onchange="this.form.submit()"
                                        >
                                            <option value="1" @selected((int) $service->Status === 1)>Active</option>
                                            <option value="0" @selected((int) $service->Status === 0)>Inactive</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-muted">@dmy($service->Created_on)</td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex align-items-center gap-1" aria-label="Row actions">
                                        <a class="action-btn action-view" href="{{ route('admin.service-master.show', ['token' => $token]) }}" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a class="action-btn action-edit" href="{{ route('admin.service-master.edit', ['token' => $token]) }}" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.service-master.destroy', ['token' => $token]) }}" class="js-confirm-delete" data-confirm-message="Do you want to delete this service?">
                                            @csrf
                                            @method('DELETE')
                                            <button class="action-btn action-delete" type="submit" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No services found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($services->hasPages())
            <div class="card-footer bg-white border-0 px-4 pb-4">
                {{ $services->links() }}
            </div>
        @endif
    </div>
@endsection
