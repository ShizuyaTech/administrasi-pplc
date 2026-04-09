<?php

namespace App\Http\Controllers;

use App\Models\BreakTime;
use App\Http\Requests\StoreBreakTimeRequest;
use App\Http\Requests\UpdateBreakTimeRequest;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BreakTimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $breakTimes = BreakTime::orderBy('start_time')->paginate(15);
        
        return view('break-times.index', compact('breakTimes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('break-times.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBreakTimeRequest $request)
    {
        BreakTime::create($request->validated());
        
        return redirect()->route('break-times.index')
            ->with('success', 'Jam istirahat berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(BreakTime $breakTime)
    {
        return view('break-times.show', compact('breakTime'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BreakTime $breakTime)
    {
        return view('break-times.edit', compact('breakTime'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBreakTimeRequest $request, BreakTime $breakTime)
    {
        $breakTime->update($request->validated());
        
        return redirect()->route('break-times.index')
            ->with('success', 'Jam istirahat berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BreakTime $breakTime)
    {
        $breakTime->delete();
        
        return redirect()->route('break-times.index')
            ->with('success', 'Jam istirahat berhasil dihapus');
    }

    public function importForm()
    {
        return view('break-times.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

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

            $name      = trim($row['A'] ?? '');
            $startTime = trim($row['B'] ?? '');
            $endTime   = trim($row['C'] ?? '');
            $isActive  = isset($row['D']) ? (int) $row['D'] : 1;

            if ($name === '' && $startTime === '') continue;

            if ($name === '' || $startTime === '' || $endTime === '') {
                $skipped[] = "Baris $i: Nama, Jam Mulai, atau Jam Selesai kosong";
                continue;
            }

            // Validate time format
            if (!preg_match('/^\d{1,2}:\d{2}$/', $startTime) || !preg_match('/^\d{1,2}:\d{2}$/', $endTime)) {
                $skipped[] = "Baris $i ($name): Format jam tidak valid, gunakan HH:MM";
                continue;
            }

            BreakTime::create([
                'name'       => $name,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'is_active'  => (bool) $isActive,
            ]);
            $imported++;
        }

        $message = "Berhasil import $imported jam istirahat.";
        if ($skipped) {
            $message .= ' Dilewati: ' . implode('; ', $skipped);
        }

        return redirect()->route('break-times.index')->with('success', $message);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Jam Istirahat');

        $headers = ['Nama', 'Jam Mulai (HH:MM)', 'Jam Selesai (HH:MM)', 'Status Aktif (1=Aktif, 0=Nonaktif)'];
        $sheet->fromArray($headers, null, 'A1');
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setWidth(28);
        }

        $samples = [
            ['Istirahat Siang', '12:00', '13:00', 1],
            ['Istirahat Sore', '15:30', '15:45', 1],
        ];
        foreach ($samples as $rowIdx => $sample) {
            $sheet->fromArray($sample, null, 'A' . ($rowIdx + 2));
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
        ];
        $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_import_jam_istirahat.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
