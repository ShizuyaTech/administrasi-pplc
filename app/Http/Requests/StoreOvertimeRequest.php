<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'type' => ['required', 'in:regular,additional'],
            'employees' => ['required', 'array', 'min:1'],
            'employees.*.name' => ['required', 'string', 'max:255'],
            'employees.*.work_description' => ['required', 'string', 'max:1000'],
        ];

        if (auth()->user()?->canManageAllSections()) {
            $rules['section_id'] = ['required', 'exists:sections,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'employees.required' => 'Minimal satu karyawan harus diisi.',
            'employees.*.name.required' => 'Nama karyawan harus diisi.',
            'employees.*.work_description.required' => 'Deskripsi pekerjaan harus diisi.',
        ];
    }
}
