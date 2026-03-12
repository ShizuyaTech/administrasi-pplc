@extends('layouts.app')

@section('title', 'Edit Overtime')
@section('page-title', 'Edit Data Overtime')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('overtimes.update', $overtime) }}">
            @csrf
            @method('PUT')

            @if(auth()->user()->isSuperAdmin())
            <div class="mb-6">
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">Seksi <span class="text-red-500">*</span></label>
                <select id="section_id" name="section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('section_id') border-red-500 @enderror">
                    <option value="">Pilih Seksi</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id', $overtime->section_id) == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>
                @error('section_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @else
            <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <p class="text-sm text-indigo-800"><strong>Seksi:</strong> {{ $overtime->section->name }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', $overtime->date->format('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('date') border-red-500 @enderror">
                    @error('date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="employee_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan <span class="text-red-500">*</span></label>
                    <input type="text" id="employee_name" name="employee_name" value="{{ old('employee_name', $overtime->employee_name) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('employee_name') border-red-500 @enderror">
                    @error('employee_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', substr($overtime->start_time, 0, 5)) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('start_time') border-red-500 @enderror">
                    @error('start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', substr($overtime->end_time, 0, 5)) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('end_time') border-red-500 @enderror">
                    @error('end_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Overtime <span class="text-red-500">*</span></label>
                <select id="type" name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                    <option value="regular" {{ old('type', $overtime->type) == 'regular' ? 'selected' : '' }}>Harian</option>
                    <option value="additional" {{ old('type', $overtime->type) == 'additional' ? 'selected' : '' }}>Susulan</option>
                </select>
                @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label for="work_description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                <textarea id="work_description" name="work_description" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('work_description') border-red-500 @enderror">{{ old('work_description', $overtime->work_description) }}</textarea>
                @error('work_description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('overtimes.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Update Data</button>
            </div>
        </form>
    </div>
</div>
@endsection
