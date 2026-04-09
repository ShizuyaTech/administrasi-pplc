<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Consumable;
use App\Models\Section;
use App\Http\Requests\StoreStockMovementRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = StockMovement::with(['consumable.section', 'creator']);
        
        // Filter by section for non-Super Admin
        if (!$user->canManageAllSections()) {
            $query->whereHas('consumable', function($q) use ($user) {
                $q->where('section_id', $user->section_id);
            });
        }
        
        // Filter by date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by consumable
        if ($request->filled('consumable_id')) {
            $query->where('consumable_id', $request->consumable_id);
        }
        
        // Filter by section for Super Admin
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->whereHas('consumable', function($q) use ($request) {
                $q->where('section_id', $request->section_id);
            });
        }
        
        $movements = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get consumables for filter
        $consumables = $user->canManageAllSections() 
            ? Consumable::orderBy('name')->get()
            : Consumable::where('section_id', $user->section_id)->orderBy('name')->get();
            
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('stock-movements.index', compact('movements', 'consumables', 'sections'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $consumables = $user->canManageAllSections() 
            ? Consumable::with('section')->orderBy('name')->get()
            : Consumable::where('section_id', $user->section_id)->orderBy('name')->get();
        
        return view('stock-movements.create', compact('consumables'));
    }

    public function store(StoreStockMovementRequest $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = auth()->user();
        $data = $request->validated();
        
        DB::transaction(function() use ($data, $currentUser) {
            $consumable = Consumable::findOrFail($data['consumable_id']);
            
            // Check section access
            if (!$currentUser->canManageAllSections() && $consumable->section_id != $currentUser->section_id) {
                abort(403);
            }
            
            $data['stock_before'] = $consumable->current_stock;
            
            // Calculate new stock
            if ($data['type'] === 'in') {
                $consumable->current_stock += $data['quantity'];
            } else {
                if ($consumable->current_stock < $data['quantity']) {
                    throw new \Exception('Stok tidak mencukupi untuk stock out.');
                }
                $consumable->current_stock -= $data['quantity'];
            }
            
            $data['stock_after'] = $consumable->current_stock;
            $data['created_by'] = $currentUser->id;
            
            // Save movement
            StockMovement::create($data);
            
            // Update consumable stock
            $consumable->save();
        });
        
        return redirect()->route('stock-movements.index')->with('success', 'Stock movement berhasil dicatat.');
    }

    public function show(StockMovement $stockMovement)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        if (!$user->isSuperAdmin() && $stockMovement->consumable->section_id != $user->section_id) {
            abort(403);
        }
        
        $stockMovement->load(['consumable.section', 'creator']);
        return view('stock-movements.show', compact('stockMovement'));
    }
    
    /**
     * Export stock movements to CSV
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = StockMovement::with(['consumable.section', 'creator']);
        
        if (!$user->canManageAllSections()) {
            $query->whereHas('consumable', function($q) use ($user) {
                $q->where('section_id', $user->section_id);
            });
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('consumable_id')) {
            $query->where('consumable_id', $request->consumable_id);
        }
        
        $movements = $query->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Stock Movements');

        $headers = ['Tanggal', 'Item', 'Seksi', 'Tipe', 'Qty', 'Stok Sebelum', 'Stok Sesudah', 'Catatan', 'Dibuat Oleh'];
        $sheet->fromArray($headers, null, 'A1');

        $row = 2;
        foreach ($movements as $movement) {
            $sheet->fromArray([
                $movement->created_at->format('d/m/Y H:i'),
                $movement->consumable->name,
                $movement->consumable->section->name,
                strtoupper($movement->type),
                number_format($movement->quantity, 0, ',', '.'),
                number_format($movement->stock_before, 0, ',', '.'),
                number_format($movement->stock_after, 0, ',', '.'),
                $movement->notes ?? '',
                $movement->creator->name,
            ], null, 'A' . $row);
            $row++;
        }

        $lastCol = 'I';
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ];
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'stock_movements_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
