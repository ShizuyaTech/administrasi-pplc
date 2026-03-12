<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'date' => ['required', 'date'],
            'employee_name' => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'work_description' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:regular,additional'],
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
        ];
    }
}
