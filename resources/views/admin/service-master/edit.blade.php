@extends('admin.layouts.app')

@section('title', 'Edit Service')

@section('content')
    @php
        $token = $token ?? \App\Models\Service::idToToken((int) $service->ID);
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h1 class="h4 mb-0">Edit Service</h1>
            <div class="text-muted small">Service Master</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="{{ route('admin.service-master.index') }}">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
            <a class="btn btn-success btn-sm" href="{{ route('admin.service-master.show', ['token' => $token]) }}">
                <i class="bi bi-eye me-2"></i>View
            </a>
        </div>
    </div>

    <div class="card card-soft rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('admin.service-master.update', ['token' => $token]) }}" class="row g-3" novalidate>
                @csrf
                @method('PUT')

                <div class="col-md-6">
                    <label class="form-label">Service Name</label>
                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $service->ServiceName) }}"
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
                        <option value="1" @selected((string) old('status', $service->Status) === '1')>Active</option>
                        <option value="0" @selected((string) old('status', $service->Status) === '0')>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check2-circle me-2"></i>Save Changes
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.service-master.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
