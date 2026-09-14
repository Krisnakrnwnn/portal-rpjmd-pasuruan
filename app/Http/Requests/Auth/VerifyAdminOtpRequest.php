<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyAdminOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'regex:/^\d{6}$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'otp.required' => 'Masukkan kode verifikasi enam digit.',
            'otp.regex' => 'Kode verifikasi harus terdiri dari enam digit angka.',
        ];
    }
}
