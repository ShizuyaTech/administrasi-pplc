<?php

namespace App\Http\Controllers;

use App\Models\BusinessTrip;
use App\Models\Section;
use App\Http\Requests\StoreBusinessTripRequest;
use App\Http\Requests\UpdateBusinessTripRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessTripController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $query = BusinessTrip::with(['section', 'creator', 'approver']);
        
        // Admin/Leader can see all data in their section or all sections
        if ($user->canManageAllSections()) {
            // Super admin can see all
        } elseif ($user->canApproveBusinessTrips() || $user->isLeaderOrForeman()) {
            // Leader/Foreman can see all data in their section
            $query->where('section_id', $user->section_id);
        } else {
            // Regular users (including drivers) only see their own data
            $query->where('created_by', $user->id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('departure_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('departure_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        $trips = $query->orderBy('departure_date', 'desc')->paginate(15);
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('business-trips.index', compact('trips', 'sections'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        // Generate nomor surat otomatis
        $lastTrip = BusinessTrip::latest('id')->first();
        $number = $lastTrip ? intval(substr($lastTrip->letter_number, -4)) + 1 : 1;
        $letterNumber = 'SPD/' . date('Y') . '/' . str_pad($number, 4, '0', STR_PAD_LEFT);
        
        return view('business-trips.create', compact('sections', 'letterNumber'));
    }

    public function store(StoreBusinessTripRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        $data = $request->validated();
        $data['created_by'] = $user->id;
        
        if (!$user->canManageAllSections()) {
            $data['section_id'] = $user->section_id;
        }
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('business-trips', $filename, 'public');
            $data['attachment'] = $path;
        }
        
        $businessTrip = BusinessTrip::create($data);
        
        // Handle card usage if using company vehicle with e-money
        if ($request->filled('card_id') && $request->filled('card_usage_amount')) {
            $card = \App\Models\Card::find($request->card_id);
            
            if ($card && $card->hasSufficientBalance($request->card_usage_amount)) {
                \App\Models\CardUsage::create([
                    'business_trip_id' => $businessTrip->id,
                    'card_id' => $request->card_id,
                    'initial_balance' => $request->card_initial_balance ?? $card->current_balance,
                    'usage_amount' => $request->card_usage_amount,
                    'final_balance' => 0, // Will be auto-calculated
                    'usage_notes' => $request->card_usage_notes,
                ]);
            }
        }
        
        return redirect()->route('business-trips.index')->with('success', 'Surat Perjalanan Dinas berhasil ditambahkan.');
    }

    public function show(BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only see their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveBusinessTrips() && 
            !$user->isLeaderOrForeman() &&
            $businessTrip->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke SPD ini.');
        }
        
        $businessTrip->load(['section', 'creator', 'approver']);
        return view('business-trips.show', compact('businessTrip'));
    }

    public function edit(BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only edit their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveBusinessTrips() && 
            !$user->isLeaderOrForeman() &&
            $businessTrip->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit SPD ini.');
        }
        
        if ($businessTrip->status === 'completed') {
            return redirect()->route('business-trips.index')->with('error', 'SPD yang sudah selesai tidak dapat diedit.');
        }
        
        $sections = $user->canManageAllSections() ? Section::all() : collect([$user->section]);
        
        return view('business-trips.edit', compact('businessTrip', 'sections'));
    }

    public function update(UpdateBusinessTripRequest $request, BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only update their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveBusinessTrips() && 
            !$user->isLeaderOrForeman() &&
            $businessTrip->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengupdate SPD ini.');
        }
        
        if ($businessTrip->status === 'completed') {
            return redirect()->route('business-trips.index')->with('error', 'SPD yang sudah selesai tidak dapat diupdate.');
        }
        
        $data = $request->validated();
        
        if (!$user->canManageAllSections()) {
            unset($data['section_id']);
        }
        
        // Handle file upload
        if ($request->hasFile('attachment')) {
            if ($businessTrip->attachment) {
                Storage::disk('public')->delete($businessTrip->attachment);
            }
            $file = $request->file('attachment');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('business-trips', $filename, 'public');
            $data['attachment'] = $path;
        }
        
        $businessTrip->update($data);
        
        // Handle card usage update
        if ($request->filled('card_id') && $request->filled('card_usage_amount')) {
            $card = \App\Models\Card::find($request->card_id);
            
            if ($card) {
                // Check if card usage exists
                $existingUsage = $businessTrip->cardUsages()->first();
                
                if ($existingUsage) {
                    // Save old card reference before updating
                    $oldCard = $existingUsage->card;
                    $oldUsageAmount = $existingUsage->usage_amount;
                    
                    // Restore previous card balance (refund the old usage)
                    $oldCard->topUp($oldUsageAmount);
                    
                    // Delete old usage record to avoid issues with auto balance update in CardUsage model
                    $existingUsage->delete();
                    
                    // Create new usage record
                    \App\Models\CardUsage::create([
                        'business_trip_id' => $businessTrip->id,
                        'card_id' => $request->card_id,
                        'initial_balance' => $request->card_initial_balance ?? $card->current_balance,
                        'usage_amount' => $request->card_usage_amount,
                        'final_balance' => 0, // Will be auto-calculated
                        'usage_notes' => $request->card_usage_notes,
                    ]);
                } else {
                    // Create new usage
                    \App\Models\CardUsage::create([
                        'business_trip_id' => $businessTrip->id,
                        'card_id' => $request->card_id,
                        'initial_balance' => $request->card_initial_balance ?? $card->current_balance,
                        'usage_amount' => $request->card_usage_amount,
                        'final_balance' => 0,
                        'usage_notes' => $request->card_usage_notes,
                    ]);
                }
            }
        }
        
        return redirect()->route('business-trips.index')->with('success', 'SPD berhasil diupdate.');
    }

    public function destroy(BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only delete their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveBusinessTrips() && 
            !$user->isLeaderOrForeman() &&
            $businessTrip->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus SPD ini.');
        }
        
        if ($businessTrip->status === 'completed') {
            return redirect()->route('business-trips.index')->with('error', 'SPD yang sudah selesai tidak dapat dihapus.');
        }
        
        if ($businessTrip->attachment) {
            Storage::disk('public')->delete($businessTrip->attachment);
        }
        
        $businessTrip->delete();
        
        return redirect()->route('business-trips.index')->with('success', 'SPD berhasil dihapus.');
    }

    public function approve(BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        if (!$user->canApproveBusinessTrips()) {
            return redirect()->route('business-trips.index')->with('error', 'Anda tidak memiliki akses untuk approve SPD.');
        }
        
        if (!$user->canAccessSection($businessTrip->section_id)) {
            abort(403);
        }
        
        $businessTrip->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
        
        return redirect()->route('business-trips.index')->with('success', 'SPD berhasil diapprove.');
    }

    public function complete(BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        if (!$user->canAccessSection($businessTrip->section_id)) {
            abort(403);
        }
        
        $businessTrip->update(['status' => 'completed']);
        
        return redirect()->route('business-trips.index')->with('success', 'SPD ditandai sebagai selesai.');
    }

    public function print(BusinessTrip $businessTrip)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        
        // Check access: user can only print their own data unless they are leader/admin
        if (!$user->canManageAllSections() && 
            !$user->canApproveBusinessTrips() && 
            !$user->isLeaderOrForeman() &&
            $businessTrip->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mencetak SPD ini.');
        }
        
        $businessTrip->load(['section', 'creator', 'approver']);
        return view('business-trips.print', compact('businessTrip'));
    }
    
    /**
     * Export business trips to CSV
     */
    public function export(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $query = BusinessTrip::with(['section', 'creator', 'approver']);
        
        // Apply same filter logic as index
        if ($user->canManageAllSections()) {
            // Super admin can see all
        } elseif ($user->canApproveBusinessTrips() || $user->isLeaderOrForeman()) {
            // Leader/Foreman can see all data in their section
            $query->where('section_id', $user->section_id);
        } else {
            // Regular users only see their own data
            $query->where('created_by', $user->id);
        }
        
        if ($request->filled('date_from')) {
            $query->where('departure_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('departure_date', '<=', $request->date_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        $trips = $query->orderBy('departure_date', 'desc')->get();
        
        $filename = 'business_trips_' . date('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($trips) {
            $file = fopen('php://output', 'w');
            
            // BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header
            fputcsv($file, ['Nomor Surat', 'Seksi', 'Nama Pegawai', 'Tujuan', 'Tanggal Berangkat', 'Tanggal Kembali', 'Keperluan', 'Transport', 'Estimasi Biaya', 'Status', 'Disetujui Oleh', 'Dibuat Oleh', 'Dibuat Pada']);
            
            // Data
            foreach ($trips as $trip) {
                fputcsv($file, [
                    $trip->letter_number,
                    $trip->section->name,
                    $trip->employee_name,
                    $trip->destination,
                    $trip->departure_date->format('d/m/Y'),
                    $trip->return_date->format('d/m/Y'),
                    $trip->purpose,
                    $trip->transport,
                    $trip->estimated_cost ? number_format($trip->estimated_cost, 0, ',', '.') : '',
                    ucfirst($trip->status),
                    $trip->approver ? $trip->approver->name : '',
                    $trip->creator->name,
                    $trip->created_at->format('d/m/Y H:i'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
