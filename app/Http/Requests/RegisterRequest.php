<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Membersihkan spasi berlebih dari input nama
        $this->merge([
            'name' => preg_replace('/\s+/', ' ', trim($this->name)),
        ]);
    }

    public function rules()
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',      // Harus ada huruf besar
                'regex:/[a-z]/',      // Harus ada huruf kecil
                'regex:/[0-9]/',      // Harus ada angka
                'regex:/[^A-Za-z0-9]/', // Harus ada simbol
                function ($attribute, $value, $fail) {
                    // Cek agar password tidak sama/mengandung nama atau email
                    if (stripos($value, $this->name) !== false) {
                        $fail('Password tidak boleh mengandung nama Anda demi keamanan.');
                    }
                    $emailPrefix = explode('@', $this->email)[0];
                    if (stripos($value, $emailPrefix) !== false) {
                        $fail('Password tidak boleh mengandung alamat email Anda.');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'nama.required' => 'Nama pengguna wajib diisi.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
        ];
    }
}