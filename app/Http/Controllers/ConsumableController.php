<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\Section;
use App\Http\Requests\StoreConsumableRequest;
use App\Http\Requests\UpdateConsumableRequest;
use Illuminate\Http\Request;

class ConsumableController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = Consumable::with('section');
        
        // Filter by section for non-Super Admin
        if (!$user->isSuperAdmin()) {
            $query->where('section_id', $user->section_id);
        }
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Filter by section for Super Admin
        if ($request->filled('section_id') && $user->isSuperAdmin()) {
            $query->where('section_id', $request->section_id);
        }
        
        // Filter for low stock items
        if ($request->filled('low_stock') && $request->low_stock == '1') {
            $query->whereRaw('current_stock <= minimum_stock');
        }
        
        $consumables = $query->orderBy('name')->paginate(15);
        $sections = $user->isSuperAdmin() ? Section::all() : collect([$user->section]);
        
        return view('consumables.index', compact('consumables', 'sections'));
    }

    public function masterItems(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Get distinct item names with their units and total stock across all sections
        $query = Consumable::selectRaw('
            name,
            unit,
            SUM(current_stock) as total_stock,
            SUM(minimum_stock) as total_minimum_stock,
            COUNT(*) as section_count,
            MIN(id) as first_id
        ')
        ->groupBy('name', 'unit');
        
        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $masterItems = $query->orderBy('name')->paginate(20);
        
        return view('consumables.master-items', compact('masterItems'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('consumables.create', compact('sections'));
    }

    public function store(StoreConsumableRequest $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $data = $request->validated();
        
        if (!$currentUser->canManageAllSections()) {
            $data['section_id'] = $currentUser->section_id;
        }
        
        Consumable::create($data);
        
        return redirect()->route('consumables.index')->with('success', 'Item consumable berhasil ditambahkan.');
    }

    public function show(Consumable $consumable)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Check section access
        if (!$user->canAccessSection($consumable->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data consumable di seksi ini.');
        }
        
        $consumable->load(['section', 'stockMovements' => function($query) {
            $query->with('creator')->orderBy('created_at', 'desc')->take(10);
        }]);
        
        return view('consumables.show', compact('consumable'));
    }

    public function edit(Consumable $consumable)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Check section access
        if (!$user->canAccessSection($consumable->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data consumable di seksi ini.');
        }
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('consumables.edit', compact('consumable', 'sections'));
    }

    public function update(UpdateConsumableRequest $request, Consumable $consumable)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Check section access
        if (!$user->canAccessSection($consumable->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data consumable di seksi ini.');
        }
        
        $data = $request->validated();
        
        if (!$user->canManageAllSections()) {
            unset($data['section_id']);
        }
        
        $consumable->update($data);
        
        return redirect()->route('consumables.index')->with('success', 'Item consumable berhasil diupdate.');
    }

    public function destroy(Consumable $consumable)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        // Check section access
        if (!$user->canAccessSection($consumable->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data consumable di seksi ini.');
        }
        
        // Check if there are stock movements
        if ($consumable->stockMovements()->count() > 0) {
            return redirect()->route('consumables.index')->with('error', 'Tidak dapat menghapus item yang memiliki riwayat stock movement.');
        }
        
        $consumable->delete();
        
        return redirect()->route('consumables.index')->with('success', 'Item consumable berhasil dihapus.');
    }
}
