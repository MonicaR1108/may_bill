<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ItemMaster;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

class MasterItemController extends Controller
{
    public function index()
    {
        $sort = (string) request('sort', '');
        $dir = strtolower((string) request('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $serviceFilter = (int) request('service', 0);
        $search = trim((string) request('q', ''));

        // Sorting is intentionally limited to S.No (ID) only.
        if ($sort !== 'id') {
            $sort = '';
        }

        $categoryColumn = Schema::hasColumn('item_master', 'Category')
            ? 'Category'
            : (Schema::hasColumn('item_master', 'category') ? 'category' : null);

        $priceColumn = Schema::hasColumn('item_master', 'Price')
            ? 'Price'
            : (Schema::hasColumn('item_master', 'price') ? 'price' : null);

        $query = ItemMaster::query()->with('service');

        if ($serviceFilter > 0) {
            $query->where('service_id', $serviceFilter);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('ItemName', 'like', "%{$search}%")
                    ->orWhere('Description', 'like', "%{$search}%");
            });
        }

        if ($sort === 'id') {
            $query->orderBy('ID', $dir);
        } else {
            // Default: newest first.
            // NOTE: Created_on can be NULL for older records; ordering by ID is the most reliable way to keep
            // newly created items at the top when using this module's "S.No" column.
            $query->orderByDesc('ID');
            $dir = 'desc';
        }

        return view('admin.master-items.index', [
            'items' => $query->paginate(15)->withQueryString(),
            'sort' => $sort,
            'dir' => $dir,
            'hasCategory' => (bool) $categoryColumn,
            'hasPrice' => (bool) $priceColumn,
            'services' => Service::query()->orderBy('ServiceName')->get(['ID', 'ServiceName', 'Status']),
            'selectedService' => $serviceFilter,
            'search' => $search,
        ]);
    }

    public function show(string $token)
    {
        $masterItem = $this->findItem($token)->load('service');

        return view('admin.master-items.show', [
            'item' => $masterItem,
        ]);
    }

    public function edit(string $token)
    {
        $masterItem = $this->findItem($token)->load('service');

        return view('admin.master-items.edit', [
            'item' => $masterItem,
            'token' => $token,
            'services' => Service::query()->orderBy('ServiceName')->get(['ID', 'ServiceName', 'Status']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:190'],
                'description' => ['nullable', 'string', 'max:100'],
                'status' => ['required', 'in:Active,Inactive'],
                'service_id' => ['required', 'integer', 'min:1', 'exists:services,ID'],
            ],
            [
                'service_id.required' => 'Please select the service.',
                'service_id.min' => 'Please select the service.',
                'service_id.exists' => 'Please select the service.',
            ]
        );

        $admin = Auth::guard('admin')->user();
        $by = $admin?->username ?? '';
        $today = now()->toDateString();

        ItemMaster::query()->create([
            'ItemName' => trim($validated['name']),
            'Description' => trim((string) ($validated['description'] ?? '')),
            'Status' => $validated['status'],
            'service_id' => (int) $validated['service_id'],
            'Created_by' => $by,
            'Created_on' => $today,
            'updated_by' => $by,
            'updated_on' => $today,
        ]);

        return redirect()->route('admin.master-items.index')->with('status', 'Item added.');
    }

    public function update(Request $request, string $token)
    {
        $masterItem = $this->findItem($token);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:Active,Inactive'],
            'service_id' => ['required', 'integer', 'min:1', 'exists:services,ID'],
        ]);

        $admin = Auth::guard('admin')->user();
        $by = $admin?->username ?? '';
        $today = now()->toDateString();

        $masterItem->update([
            'ItemName' => trim($validated['name']),
            'Description' => trim((string) ($validated['description'] ?? '')),
            'Status' => $validated['status'],
            'service_id' => (int) $validated['service_id'],
            'updated_by' => $by,
            'updated_on' => $today,
        ]);

        return redirect()->route('admin.master-items.edit', ['token' => $token])->with('status', 'Item updated.');
    }

    public function updateStatus(Request $request, string $token)
    {
        $masterItem = $this->findItem($token);

        $validated = $request->validate([
            'status' => ['required', 'in:Active,Inactive'],
        ]);

        $admin = Auth::guard('admin')->user();
        $by = $admin?->username ?? '';

        $masterItem->update([
            'Status' => $validated['status'],
            'updated_by' => $by,
        ]);

        return back()->with('status', 'Status updated.');
    }

    public function destroy(string $token)
    {
        $masterItem = $this->findItem($token);
        $masterItem->delete();

        return redirect()->route('admin.master-items.index')->with('status', 'Item deleted.');
    }

    private function findItem(string $token): ItemMaster
    {
        try {
            $id = ItemMaster::tokenToId($token);
        } catch (\Throwable) {
            abort(404);
        }

        return ItemMaster::query()->findOrFail($id);
    }
}
