<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->role == Role::ADMIN->status() || $this->id == auth()->user()->id;
    }

    /**
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => __('model.user.name'),
            'email' => __('model.user.email'),
            'phone' => __('model.user.phone'),
            'nip'   => __('model.user.nip'),
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', Rule::unique('users')->ignore($this->id)],
            'new_password' => ['nullable', 'string', 'min:6'],
            'phone' => ['nullable'],
            'nip'   => ['nullable'],
            'is_active' => ['nullable'],
        ];
        // Tambahkan validasi password jika field diisi
        if ($this->filled('new_password')) {
            $rules['new_password'] = [
                'required',
                'string',
                'min:8',
                'confirmed' // Ini akan memvalidasi new_password_confirmation
            ];
            $rules['new_password_confirmation'] = ['required'];
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'new_password.required' => 'Password baru harus diisi jika ingin mengganti password.',
            'new_password.min' => 'Password minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
            'new_password_confirmation.required' => 'Konfirmasi password harus diisi.',
            'profile_picture.image' => 'File harus berupa gambar.',
            'profile_picture.mimes' => 'Format gambar harus JPG, JPEG, PNG, atau GIF.',
            'profile_picture.max' => 'Ukuran gambar maksimal 800KB.',
        ];
    }
}
