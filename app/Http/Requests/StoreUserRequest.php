<?php

namespace App\Http\Requests;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->role === Role::ADMIN->status();
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
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required'],
            'email' => ['required', Rule::unique('users')],
            'phone' => ['nullable'],
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ];

        // bidang wajib untuk role admin dan staff
        if ($this->filled('role') && in_array($this->role, ['admin', 'staff'])) {
            $rules['bidang'] = ['required', 'string'];
        }

        return $rules;
    }
}
