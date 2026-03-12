<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $employee = $this->route('employee');
        
        return [
            'nrp' => ['required', 'string', 'max:50', Rule::unique('employees', 'nrp')->ignore($employee->id)],
            'name' => ['required', 'string', 'max:255'],
            'section_id' => ['required', 'exists:sections,id'],
            'position' => ['required', 'string', 'max:100'],
            'shift' => ['required', 'in:Shift A,Shift B,Non Shift'],
            'role_id' => ['required', 'exists:roles,id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nrp.required' => 'NRP harus diisi.',
            'nrp.unique' => 'NRP sudah terdaftar.',
            'name.required' => 'Nama karyawan harus diisi.',
            'section_id.required' => 'Seksi harus dipilih.',
            'position.required' => 'Jabatan harus diisi.',
            'shift.required' => 'Shift harus dipilih.',
            'shift.in' => 'Shift tidak valid.',
            'role_id.required' => 'Role harus dipilih.',
        ];
    }
}
