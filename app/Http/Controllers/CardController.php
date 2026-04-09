<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Section;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class CardController extends Controller
{
    /**
     * Display a listing of cards
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Card::with('section');
        
        // Filter by section
        if (!$user->canManageAllSections()) {
            $query->where('section_id', $user->section_id);
        } elseif ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by card type
        if ($request->filled('card_type')) {
            $query->where('card_type', $request->card_type);
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where('card_number', 'like', '%' . $request->search . '%');
        }
        
        $cards = $query->latest()->paginate(15);
        $sections = $user->canManageAllSections() ? Section::all() : collect();
        
        return view('cards.index', compact('cards', 'sections'));
    }

    /**
     * Show the form for creating a new card
     */
    public function create()
    {
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('cards.create', compact('sections'));
    }

    /**
     * Store a newly created card
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'card_number' => 'required|string|max:50|unique:cards,card_number',
            'card_type' => 'required|in:flazz,brizzi,e-toll,other',
            'current_balance' => 'required|numeric|min:0',
            'section_id' => $user->canManageAllSections() ? 'required|exists:sections,id' : 'nullable',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Set section_id for non-super admin
        if (!$user->canManageAllSections()) {
            $validated['section_id'] = $user->section_id;
        }
        
        Card::create($validated);
        
        return redirect()->route('cards.index')->with('success', 'Kartu E-Money berhasil ditambahkan.');
    }

    /**
     * Display the specified card
     */
    public function show(Card $card)
    {
        if (!auth()->user()->canAccessSection($card->section_id)) {
            abort(403);
        }
        
        $card->load(['section', 'cardUsages.businessTrip']);
        
        return view('cards.show', compact('card'));
    }

    /**
     * Show the form for editing the card
     */
    public function edit(Card $card)
    {
        if (!auth()->user()->canAccessSection($card->section_id)) {
            abort(403);
        }
        
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('cards.edit', compact('card', 'sections'));
    }

    /**
     * Update the specified card
     */
    public function update(Request $request, Card $card)
    {
        if (!auth()->user()->canAccessSection($card->section_id)) {
            abort(403);
        }
        
        $user = auth()->user();
        
        $validated = $request->validate([
            'card_number' => 'required|string|max:50|unique:cards,card_number,' . $card->id,
            'card_type' => 'required|in:flazz,brizzi,e-toll,other',
            'current_balance' => 'required|numeric|min:0',
            'section_id' => $user->canManageAllSections() ? 'required|exists:sections,id' : 'nullable',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:500',
        ]);
        
        // Prevent changing section for non-super admin
        if (!$user->canManageAllSections()) {
            unset($validated['section_id']);
        }
        
        $card->update($validated);
        
        return redirect()->route('cards.index')->with('success', 'Kartu E-Money berhasil diupdate.');
    }

    /**
     * Remove the specified card
     */
    public function destroy(Card $card)
    {
        if (!auth()->user()->canAccessSection($card->section_id)) {
            abort(403);
        }
        
        $card->delete();
        
        return redirect()->route('cards.index')->with('success', 'Kartu E-Money berhasil dihapus.');
    }

    /**
     * Top up card balance
     */
    public function topup(Request $request, Card $card)
    {
        if (!auth()->user()->canAccessSection($card->section_id)) {
            abort(403);
        }
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:10000',
        ]);
        
        $card->topUp($validated['amount']);
        
        return back()->with('success', 'Saldo kartu berhasil ditambah.');
    }

    /**
     * Get active cards for section (API for business trip form)
     */
    public function getActiveCards(Request $request)
    {
        $user = auth()->user();
        
        $sectionId = $request->section_id;
        
        // Check permission
        if (!$user->canManageAllSections() && $user->section_id != $sectionId) {
            return response()->json([]);
        }
        
        $cards = Card::where('section_id', $sectionId)
            ->active()
            ->withBalance(10000) // Minimal 10k
            ->orderByDesc('current_balance')
            ->get(['id', 'card_number', 'card_type', 'current_balance']);
        
        return response()->json($cards);
    }

    public function importForm()
    {
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::orderBy('name')->get() : collect([$user->section]);
        return view('cards.import', compact('sections'));
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
        $validTypes    = ['flazz', 'brizzi', 'e-toll', 'other'];
        $validStatuses = ['active', 'inactive'];

        foreach ($rows as $i => $row) {
            if ($i === 1) continue;

            $cardNumber = trim($row['A'] ?? '');
            $cardType   = strtolower(trim($row['B'] ?? 'other'));
            $balance    = $row['C'] ?? 0;
            $status     = strtolower(trim($row['D'] ?? 'active'));
            $sectionN   = strtolower(trim($row['E'] ?? ''));
            $notes      = trim($row['F'] ?? '');

            if ($cardNumber === '') continue;

            if (!in_array($cardType, $validTypes)) {
                $skipped[] = "Baris $i ($cardNumber): Jenis kartu '$cardType' tidak valid";
                continue;
            }

            if (!in_array($status, $validStatuses)) {
                $skipped[] = "Baris $i ($cardNumber): Status '$status' tidak valid";
                continue;
            }

            if ($user->canManageAllSections()) {
                $sectionId = $sections[$sectionN] ?? null;
                if (!$sectionId) {
                    $skipped[] = "Baris $i ($cardNumber): Seksi '$sectionN' tidak ditemukan";
                    continue;
                }
            } else {
                $sectionId = $user->section_id;
            }

            if (Card::where('card_number', $cardNumber)->exists()) {
                $skipped[] = "Baris $i: Nomor kartu $cardNumber sudah ada";
                continue;
            }

            Card::create([
                'card_number'     => $cardNumber,
                'card_type'       => $cardType,
                'current_balance' => (float) $balance,
                'status'          => $status,
                'section_id'      => $sectionId,
                'notes'           => $notes ?: null,
            ]);
            $imported++;
        }

        $message = "Berhasil import $imported kartu e-money.";
        if ($skipped) {
            $message .= ' Dilewati: ' . implode('; ', $skipped);
        }

        return redirect()->route('cards.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kartu E-Money');

        $headers = ['Nomor Kartu', 'Jenis Kartu (flazz/brizzi/e-toll/other)', 'Saldo Awal', 'Status (active/inactive)', 'Seksi', 'Catatan'];
        $sheet->fromArray($headers, null, 'A1');
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setWidth(30);
        }

        $sample = ['1234567890', 'flazz', 500000, 'active', 'Seksi IT', 'Kartu operasional'];
        $sheet->fromArray($sample, null, 'A2');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_kartu_emoney.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
