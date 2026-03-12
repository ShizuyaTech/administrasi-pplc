@extends('layouts.app')

@section('title', 'Tambah Overtime')
@section('page-title', 'Tambah Data Overtime')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('overtimes.store') }}" x-data="overtimeForm()">
            @csrf

            @if(auth()->user()->isSuperAdmin())
            <div class="mb-6">
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">Seksi <span class="text-red-500">*</span></label>
                <select id="section_id" name="section_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('section_id') border-red-500 @enderror">
                    <option value="">Pilih Seksi</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>{{ $section->name }}</option>
                    @endforeach
                </select>
                @error('section_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            @else
            <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <p class="text-sm text-indigo-800"><strong>Seksi:</strong> {{ auth()->user()->section->name }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal <span class="text-red-500">*</span></label>
                    <input type="date" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('date') border-red-500 @enderror">
                    @error('date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Overtime <span class="text-red-500">*</span></label>
                    <select id="type" name="type" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('type') border-red-500 @enderror">
                        <option value="regular" {{ old('type') == 'regular' ? 'selected' : '' }}>Harian</option>
                        <option value="additional" {{ old('type') == 'additional' ? 'selected' : '' }}>Susulan</option>
                    </select>
                    @error('type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('start_time') border-red-500 @enderror">
                    @error('start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time') }}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('end_time') border-red-500 @enderror">
                    @error('end_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-sm font-medium text-gray-700">Daftar Karyawan & Pekerjaan <span class="text-red-500">*</span></label>
                    <button type="button" @click="addEmployee()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Karyawan
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(employee, index) in employees" :key="index">
                        <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="text-sm font-semibold text-gray-700" x-text="'Karyawan #' + (index + 1)"></h4>
                                <button type="button" @click="removeEmployee(index)" x-show="employees.length > 1" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label :for="'employee_name_' + index" class="block text-sm font-medium text-gray-700 mb-2">Nama Karyawan <span class="text-red-500">*</span></label>
                                    <input type="text" 
                                           :id="'employee_name_' + index" 
                                           :name="'employees[' + index + '][name]'" 
                                           x-model="employee.name"
                                           required 
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                           placeholder="Masukkan nama karyawan">
                                </div>
                                
                                <div>
                                    <label :for="'work_description_' + index" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Pekerjaan <span class="text-red-500">*</span></label>
                                    <textarea :id="'work_description_' + index" 
                                              :name="'employees[' + index + '][work_description]'" 
                                              x-model="employee.work_description"
                                              required 
                                              rows="2"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                                              placeholder="Deskripsi pekerjaan"></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                @error('employees')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('overtimes.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/employee-autocomplete.js') }}"></script>
<script>
function overtimeForm() {
    return {
        employees: @json(old('employees', [['name' => '', 'work_description' => '']])),
        autocompleteCleanups: [],
        
        init() {
            // Initialize autocomplete for existing inputs
            this.$nextTick(() => {
                this.initializeAutocompletes();
            });
        },
        
        initializeAutocompletes() {
            // Clean up existing autocomplete instances
            this.autocompleteCleanups.forEach(cleanup => cleanup());
            this.autocompleteCleanups = [];
            
            // Initialize autocomplete for each employee input
            this.employees.forEach((employee, index) => {
                const inputElement = document.getElementById(`employee_name_${index}`);
                if (inputElement) {
                    const cleanup = initEmployeeAutocomplete(inputElement, {
                        sectionIdGetter: () => {
                            const sectionSelect = document.getElementById('section_id');
                            return sectionSelect ? sectionSelect.value : null;
                        },
                        onSelect: (selectedEmployee) => {
                            this.employees[index].name = selectedEmployee.name;
                        }
                    });
                    this.autocompleteCleanups.push(cleanup);
                }
            });
        },
        
        addEmployee() {
            this.employees.push({ name: '', work_description: '' });
            // Re-initialize autocompletes after DOM update
            this.$nextTick(() => {
                this.initializeAutocompletes();
            });
        },
        
        removeEmployee(index) {
            if (this.employees.length > 1) {
                this.employees.splice(index, 1);
                // Re-initialize autocompletes after DOM update
                this.$nextTick(() => {
                    this.initializeAutocompletes();
                });
            }
        }
    }
}
</script>
@endsection
