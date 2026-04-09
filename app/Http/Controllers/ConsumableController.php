<?php

namespace App\Http\Controllers;

use App\Models\Consumable;
use App\Models\Section;
use App\Http\Requests\StoreConsumableRequest;
use App\Http\Requests\UpdateConsumableRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    public function importForm()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::orderBy('name')->get() : collect([$user->section]);
        return view('consumables.import', compact('sections'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        /** @var \App\Models\User $user */
        $user = auth()->user();
        $sections = Section::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);

        try {
            $spreadsheet = IOFactory::load($request->file('file')->getPathname());
            $sheet = $spreadsheet->getActiveSheet();
            $rows  = $sheet->toArray(null, true, true, true);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'File tidak dapat dibaca: ' . $e->getMessage()]);
        }

        $imported = 0;
        $skipped  = [];

        foreach ($rows as $i => $row) {
            if ($i === 1) continue;

            $name     = trim($row['A'] ?? '');
            $unit     = trim($row['B'] ?? '');
            $stock    = $row['C'] ?? 0;
            $minStock = $row['D'] ?? 0;
            $sectionN = strtolower(trim($row['E'] ?? ''));

            if ($name === '' && $unit === '') continue;

            if ($name === '' || $unit === '') {
                $skipped[] = "Baris $i: Nama atau Satuan kosong";
                continue;
            }

            if ($user->canManageAllSections()) {
                $sectionId = $sections[$sectionN] ?? null;
                if (!$sectionId) {
                    $skipped[] = "Baris $i ($name): Seksi '$sectionN' tidak ditemukan";
                    continue;
                }
            } else {
                $sectionId = $user->section_id;
            }

            Consumable::create([
                'section_id'    => $sectionId,
                'name'          => $name,
                'unit'          => $unit,
                'current_stock' => (int) $stock,
                'minimum_stock' => (int) $minStock,
            ]);
            $imported++;
        }

        $message = "Berhasil import $imported item consumable.";
        if ($skipped) {
            $message .= ' Dilewati: ' . implode('; ', $skipped);
        }

        return redirect()->route('consumables.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Consumable');

        $headers = ['Nama Item', 'Satuan', 'Stok Awal', 'Stok Minimum', 'Seksi (jika Super Admin)'];
        $sheet->fromArray($headers, null, 'A1');
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setWidth(28);
        }

        $sample = ['Kertas A4', 'Rim', 100, 10, 'Seksi IT'];
        $sheet->fromArray($sample, null, 'A2');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ];
        $sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_consumable.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
