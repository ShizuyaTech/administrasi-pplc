<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBreakTimeRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama jam istirahat wajib diisi',
            'start_time.required' => 'Jam mulai wajib diisi',
            'start_time.date_format' => 'Format jam mulai harus HH:MM',
            'end_time.required' => 'Jam selesai wajib diisi',
            'end_time.date_format' => 'Format jam selesai harus HH:MM',
            'end_time.after' => 'Jam selesai harus setelah jam mulai',
        ];
    }
}
