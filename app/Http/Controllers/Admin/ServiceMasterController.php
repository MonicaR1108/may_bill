<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemMaster;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceMasterController extends Controller
{
    public function index()
    {
        $search = trim((string) request('q', ''));

        $services = Service::query()
            ->withCount('items')
            ->when($search !== '', function ($q) use ($search) {
                $q->where('ServiceName', 'like', "%{$search}%");
            })
            ->orderByDesc('ID')
            ->paginate(15)
            ->withQueryString();

        return view('admin.service-master.index', [
            'services' => $services,
            'search' => $search,
        ]);
    }

    public function show(string $token)
    {
        $service = $this->findService($token)->load('items');

        return view('admin.service-master.show', [
            'service' => $service,
        ]);
    }

    public function edit(string $token)
    {
        $service = $this->findService($token)->load('items');

        return view('admin.service-master.edit', [
            'service' => $service,
            'token' => $token,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'status' => ['required', 'in:1,0'],
        ]);

        Service::query()->create([
            'ServiceName' => trim($validated['name']),
            'Status' => (int) $validated['status'],
            'Created_on' => now(),
        ]);

        return redirect()->route('admin.service-master.index')->with('status', 'Service added.');
    }

    public function update(Request $request, string $token)
    {
        $service = $this->findService($token);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'status' => ['required', 'in:1,0'],
        ]);

        $service->update([
            'ServiceName' => trim($validated['name']),
            'Status' => (int) $validated['status'],
        ]);

        return redirect()->route('admin.service-master.edit', ['token' => $token])->with('status', 'Service updated.');
    }

    public function updateStatus(Request $request, string $token)
    {
        $service = $this->findService($token);

        $validated = $request->validate([
            'status' => ['required', 'in:1,0'],
        ]);

        $service->update([
            'Status' => (int) $validated['status'],
        ]);

        return back()->with('status', 'Status updated.');
    }

    public function destroy(string $token)
    {
        $service = $this->findService($token);
        ItemMaster::query()->where('service_id', $service->ID)->update(['service_id' => null]);
        $service->delete();

        return redirect()->route('admin.service-master.index')->with('status', 'Service deleted.');
    }

    private function findService(string $token): Service
    {
        try {
            $id = Service::tokenToId($token);
        } catch (\Throwable) {
            abort(404);
        }

        return Service::query()->findOrFail($id);
    }
}
