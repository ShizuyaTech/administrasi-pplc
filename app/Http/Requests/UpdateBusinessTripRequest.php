<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tripId = $this->route('business_trip')->id;
        
        $rules = [
            'letter_number' => ['required', 'string', Rule::unique('business_trips')->ignore($tripId), 'max:50'],
            'employee_name' => ['required', 'string', 'max:255'],
            'destination' => ['required', 'string', 'max:255'],
            'departure_date' => ['required', 'date'],
            'return_date' => ['required', 'date', 'after_or_equal:departure_date'],
            'purpose' => ['required', 'string', 'max:1000'],
            'transport' => ['required', 'string', 'max:100'],
            'estimated_cost' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,approved,completed,cancelled'],
            'attachment' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];

        if (auth()->user()?->canManageAllSections()) {
            $rules['section_id'] = ['required', 'exists:sections,id'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'return_date.after_or_equal' => 'Tanggal kembali harus sama atau setelah tanggal berangkat.',
            'attachment.max' => 'Ukuran file maksimal 2MB.',
        ];
    }
}
