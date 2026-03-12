<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAbsenceRequest extends FormRequest
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
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $absence = $this->route('absence'); // Get current absence being edited
        $sectionId = $user->isSuperAdmin() ? $this->section_id : $absence->section_id;

        $rules = [
            'date' => [
                'required', 
                'date',
                function ($attribute, $value, $fail) use ($sectionId, $absence) {
                    if ($sectionId) {
                        $exists = \App\Models\Absence::where('section_id', $sectionId)
                            ->where('date', $value)
                            ->where('id', '!=', $absence->id)
                            ->exists();
                        
                        if ($exists) {
                            $fail('Data absensi untuk tanggal ini sudah ada.');
                        }
                    }
                },
            ],
            'present' => ['required', 'integer', 'min:0'],
            'sick' => ['required', 'integer', 'min:0'],
            'permission' => ['required', 'integer', 'min:0'],
            'leave' => ['required', 'integer', 'min:0'],
            'total_members' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];

        // Super admin can change section
        if ($user->canManageAllSections()) {
            $rules['section_id'] = ['required', 'exists:sections,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal harus diisi.',
            'present.required' => 'Jumlah hadir harus diisi.',
            'sick.required' => 'Jumlah sakit harus diisi.',
            'permission.required' => 'Jumlah izin harus diisi.',
            'leave.required' => 'Jumlah cuti harus diisi.',
            'total_members.required' => 'Total member harus diisi.',
            'section_id.required' => 'Seksi harus dipilih.',
        ];
    }
}
