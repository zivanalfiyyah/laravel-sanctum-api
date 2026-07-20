<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userParam = $this->route('user');
    
        if ($userParam instanceof \App\Models\User) {
            $userId = $userParam->id;
        } else {
            $userId = $userParam ?? auth()->id();
        }

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $userId,
            'password' => 'sometimes|required|string|min:8|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong',
            'email.email' => 'Format email-mu salah.',
            'email.unique' => 'Email ini sudah dipakai orang lain',
            'password.min' => 'Password minimal harus 8 karakter',
            'password.confirmed' => 'Konfirmasi password tida cocok',
        ];
    }
}
