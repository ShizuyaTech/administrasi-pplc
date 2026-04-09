<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Section;
use App\Models\Role;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Employee::with(['section', 'role']);
        
        // Filter by section for users without manage-all-sections permission
        if (!$user->canManageAllSections()) {
            $query->where('section_id', $user->section_id);
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nrp', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%");
            });
        }
        
        // Filter by section for users who can manage all sections
        if ($request->filled('section_id') && $user->canManageAllSections()) {
            $query->where('section_id', $request->section_id);
        }
        
        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }
        
        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }
        
        $employees = $query->orderBy('name')->paginate(15);
        
        $sections = $user->canManageAllSections() ? Section::orderBy('name')->get() : collect([$user->section]);
        $roles = Role::orderBy('name')->get();
        
        return view('employees.index', compact('employees', 'sections', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::orderBy('name')->get() : collect([$user->section]);
        $roles = Role::orderBy('name')->get();
        
        return view('employees.create', compact('sections', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        // Force section_id for users without manage-all-sections permission
        if (!$user->canManageAllSections()) {
            $data['section_id'] = $user->section_id;
        }
        
        Employee::create($data);
        
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // Check section access
        if (!auth()->user()->canAccessSection($employee->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan di seksi ini.');
        }
        
        $employee->load(['section', 'role']);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        // Check section access
        if (!auth()->user()->canAccessSection($employee->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan di seksi ini.');
        }
        
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::orderBy('name')->get() : collect([$user->section]);
        $roles = Role::orderBy('name')->get();
        
        return view('employees.edit', compact('employee', 'sections', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        // Check section access
        if (!auth()->user()->canAccessSection($employee->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan di seksi ini.');
        }
        
        $user = auth()->user();
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active') ? true : false;
        
        // Save old section_id for comparison
        $oldSectionId = $employee->section_id;
        
        // Users without manage-all-sections permission cannot change section
        if (!$user->canManageAllSections()) {
            unset($data['section_id']);
        }
        
        $employee->update($data);
        
        // Sync data to User if employee has a user account
        $employeeUser = \App\Models\User::where('employee_id', $employee->id)->first();
        if ($employeeUser) {
            $syncData = [];
            
            // Sync name
            if (isset($data['name']) && $data['name'] != $employeeUser->name) {
                $syncData['name'] = $data['name'];
            }
            
            // Sync section if changed
            if (isset($data['section_id']) && $data['section_id'] != $oldSectionId) {
                $syncData['section_id'] = $data['section_id'];
            }
            
            // Sync shift if changed (map enum to integer)
            if (isset($data['shift'])) {
                $shiftMapping = [
                    'Shift A' => 1,
                    'Shift B' => 2,
                    'Non Shift' => null,
                ];
                $userShift = $shiftMapping[$data['shift']] ?? null;
                if ($userShift != $employeeUser->shift) {
                    $syncData['shift'] = $userShift;
                }
            }
            
            if (!empty($syncData)) {
                $employeeUser->update($syncData);
            }
        }
        
        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        // Check section access
        if (!auth()->user()->canAccessSection($employee->section_id)) {
            abort(403, 'Anda tidak memiliki akses ke data karyawan di seksi ini.');
        }
        
        try {
            $employee->delete();
            return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('employees.index')->with('error', 'Data karyawan tidak dapat dihapus karena masih digunakan.');
        }
    }

    /**
     * Search employees by section for autocomplete
     */
    public function search(Request $request)
    {
        $user = auth()->user();
        $query = Employee::where('is_active', true);
        
        // Filter by section
        if ($request->filled('section_id')) {
            $sectionId = $request->section_id;
            
            // Check if user can access this section
            if (!$user->canManageAllSections() && $user->section_id != $sectionId) {
                return response()->json([]);
            }
            
            $query->where('section_id', $sectionId);
        } else {
            // If no section specified, use user's section
            if (!$user->canManageAllSections()) {
                $query->where('section_id', $user->section_id);
            }
        }
        
        // Search by name
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        
        $employees = $query->select('id', 'name', 'nrp', 'position')
                           ->orderBy('name')
                           ->limit(10)
                           ->get();
        
        return response()->json($employees);
    }

    /**
     * Show the Excel import form.
     */
    public function importForm()
    {
        $user = auth()->user();
        $sections = $user->canManageAllSections() ? Section::orderBy('name')->get() : collect([$user->section]);
        $roles = Role::orderBy('name')->get();
        return view('employees.import', compact('sections', 'roles'));
    }

    /**
     * Process the uploaded Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $user = auth()->user();
        $sections = Section::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);
        $roles    = Role::pluck('id', 'name')->mapWithKeys(fn($id, $name) => [strtolower($name) => $id]);

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
            if ($i === 1) continue; // skip header

            $nrp      = trim($row['A'] ?? '');
            $name     = trim($row['B'] ?? '');
            $sectionN = strtolower(trim($row['C'] ?? ''));
            $position = trim($row['D'] ?? '');
            $shift    = trim($row['E'] ?? 'Non Shift');
            $roleN    = strtolower(trim($row['F'] ?? ''));
            $isActive = isset($row['G']) ? (int) $row['G'] : 1;

            if ($nrp === '' && $name === '') continue;

            if ($nrp === '' || $name === '') {
                $skipped[] = "Baris $i: NRP atau Nama kosong";
                continue;
            }

            // Resolve section
            if ($user->canManageAllSections()) {
                $sectionId = $sections[$sectionN] ?? null;
                if (!$sectionId) {
                    $skipped[] = "Baris $i ($name): Seksi '$sectionN' tidak ditemukan";
                    continue;
                }
            } else {
                $sectionId = $user->section_id;
            }

            // Resolve role (nullable — kosongkan kolom F jika tidak ada role)
            if ($roleN === '' || $roleN === '-') {
                $roleId = null;
            } else {
                $roleId = $roles[$roleN] ?? null;
                if ($roleId === null) {
                    $skipped[] = "Baris $i ($name): Role '$roleN' tidak ditemukan";
                    continue;
                }
            }

            if (Employee::where('nrp', $nrp)->exists()) {
                $skipped[] = "Baris $i ($name): NRP $nrp sudah ada";
                continue;
            }

            Employee::create([
                'nrp'        => $nrp,
                'name'       => $name,
                'section_id' => $sectionId,
                'position'   => $position,
                'shift'      => $shift ?: 'Non Shift',
                'role_id'    => $roleId,
                'is_active'  => (bool) $isActive,
            ]);
            $imported++;
        }

        $message = "Berhasil import $imported karyawan.";
        if ($skipped) {
            $message .= ' Dilewati: ' . implode('; ', $skipped);
        }

        return redirect()->route('employees.index')->with('success', $message);
    }

    /**
     * Download the Excel import template.
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Karyawan');

        // Header
        $headers = ['NRP', 'Nama Lengkap', 'Seksi', 'Jabatan', 'Shift (Shift A/Shift B/Non Shift)', 'Role (opsional, kosongkan jika tidak ada)', 'Status Aktif (1=Aktif, 0=Nonaktif)'];
        $sheet->fromArray($headers, null, 'A1');
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setWidth(30);
        }

        // Sample rows
        $sheet->fromArray(['12345', 'Budi Santoso', 'Seksi IT', 'Staff IT', 'Non Shift', 'Staff', '1'], null, 'A2');
        $sheet->fromArray(['12346', 'Siti Rahayu', 'Seksi IT', 'Operator', 'Shift A', '', '1'], null, 'A3');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ];
        $sheet->getStyle('A1:G1')->applyFromArray($headerStyle);
        // Style sample rows lightly to distinguish them
        $sheet->getStyle('A2:G3')->getFont()->setItalic(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_karyawan.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
