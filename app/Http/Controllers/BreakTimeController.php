<?php

namespace App\Http\Controllers;

use App\Models\BreakTime;
use App\Http\Requests\StoreBreakTimeRequest;
use App\Http\Requests\UpdateBreakTimeRequest;

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
}
