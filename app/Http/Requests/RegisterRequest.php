<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (empty($this->all()) && ! empty($this->getContent())) {
            $json = json_decode($this->getContent(), true);
            if (is_array($json)) {
                $this->merge($json);
            } else {
                parse_str($this->getContent(), $parsed);
                if (is_array($parsed) && ! empty($parsed)) {
                    $this->merge($parsed);
                }
            }
        }

        // Support case-insensitive role inputs (e.g., 'Admin' -> 'admin')
        if ($this->has('role')) {
            $this->merge([
                'role' => strtolower((string) $this->input('role')),
            ]);
        }

        // Default 'active' to true if not provided or empty
        if (! $this->has('active') || $this->input('active') === null || $this->input('active') === '') {
            $this->merge([
                'active' => true,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'active' => 'sometimes|boolean',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,cashier,account',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'username.required' => 'The username field is required.',
            'username.unique' => 'The username has already been taken.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email field must be unique.',
            'active.boolean' => 'The active field must be true or false.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password field must be at least 6 characters long.',
            'role.required' => 'The role field is required.',
            'role.in' => 'The role field must be either admin, cashier, or account.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'name',
            'username' => 'username',
            'email' => 'email',
            'active' => 'active',
            'password' => 'password',
            'role' => 'role',
        ];
    }

    /**
     * Handle a failed validation attempt with debug context.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
                'debug_from_request' => [
                    'content_type' => $this->header('Content-Type'),
                    'received_body' => $this->getContent() ?: '(empty body)',
                    'parsed_inputs' => $this->all(),
                ],
            ], 422)
        );
    }
}
