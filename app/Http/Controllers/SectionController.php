<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
{
    /**
     * Display a listing of sections
     */
    public function index()
    {
        $sections = Section::withCount('users')->get();
        return view('sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new section
     */
    public function create()
    {
        return view('sections.create');
    }

    /**
     * Store a newly created section
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sections,name',
            'code' => 'nullable|string|max:50|unique:sections,code',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Section::create($request->only(['name', 'code', 'description']));

        return redirect()->route('sections.index')
            ->with('success', 'Section created successfully');
    }

    /**
     * Show the form for editing section
     */
    public function edit(Section $section)
    {
        return view('sections.edit', compact('section'));
    }

    /**
     * Update the specified section
     */
    public function update(Request $request, Section $section)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:sections,name,' . $section->id,
            'code' => 'nullable|string|max:50|unique:sections,code,' . $section->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $section->update($request->only(['name', 'code', 'description']));

        return redirect()->route('sections.index')
            ->with('success', 'Section updated successfully');
    }

    /**
     * Remove the specified section
     */
    public function destroy(Section $section)
    {
        // Check if section has users
        if ($section->users()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete section with assigned users');
        }

        $section->delete();

        return redirect()->route('sections.index')
            ->with('success', 'Section deleted successfully');
    }

    /**
     * Show section user assignments
     */
    public function assignments()
    {
        $sections = Section::withCount('users')->get();
        $supervisors = User::where('role_id', function($query) {
            $query->select('id')
                  ->from('roles')
                  ->where('name', 'Supervisor')
                  ->limit(1);
        })->with('sections')->get();

        $managers = User::where('role_id', function($query) {
            $query->select('id')
                  ->from('roles')
                  ->where('name', 'Manager')
                  ->limit(1);
        })->with('sections')->get();

        return view('sections.assignments', compact('sections', 'supervisors', 'managers'));
    }

    /**
     * Show form to assign sections to a user
     */
    public function assignForm($userId)
    {
        $user = User::with('sections', 'role')->findOrFail($userId);
        $sections = Section::all();

        return view('sections.assign', compact('user', 'sections'));
    }

    /**
     * Assign sections to a user
     */
    public function assignSections(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        $validator = Validator::make($request->all(), [
            'sections' => 'required|array|min:1',
            'sections.*' => 'exists:sections,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Sync sections (this will remove old assignments and add new ones)
        $user->sections()->sync($request->sections);

        return redirect()->route('sections.assignments')
            ->with('success', 'Sections assigned successfully to ' . $user->name);
    }

    /**
     * Remove section assignment from user
     */
    public function removeAssignment($userId, $sectionId)
    {
        $user = User::findOrFail($userId);
        $section = Section::findOrFail($sectionId);

        $user->sections()->detach($sectionId);

        return redirect()->back()
            ->with('success', 'Section removed from user');
    }
}
