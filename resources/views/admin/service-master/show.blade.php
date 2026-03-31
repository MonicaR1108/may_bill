@extends('admin.layouts.app')

@section('title', 'View Service')

@section('content')
    @php $token = \App\Models\Service::idToToken((int) $service->ID); @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 mb-0">View Service</h1>
            <div class="text-muted small">Service Master</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.service-master.index') }}">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
            <a class="btn btn-primary btn-sm" href="{{ route('admin.service-master.edit', ['token' => $token]) }}">
                <i class="bi bi-pencil-square me-2"></i>Edit
            </a>
        </div>
    </div>

    <div class="card card-soft rounded-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="text-muted small">Service Name</div>
                    <div class="fw-semibold">{{ $service->ServiceName }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Status</div>
                    <div>
                        <span class="badge {{ (int) $service->Status === 1 ? 'text-bg-success' : 'text-bg-danger' }}">
                            {{ (int) $service->Status === 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="col-12">
                    <div class="text-muted small mb-2">Service Items</div>
                    @if ($service->items->isEmpty())
                        <div class="text-muted">No items assigned.</div>
                    @else
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($service->items as $item)
                                <span class="badge text-bg-light border text-dark">{{ $item->ItemName }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="col-md-6">
                    <div class="text-muted small">Created On</div>
                    <div class="fw-semibold">@dmy($service->Created_on) <span class="text-muted">(@timehm($service->Created_on))</span></div>
                </div>
            </div>
        </div>
    </div>
@endsection
