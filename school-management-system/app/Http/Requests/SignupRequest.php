<?php

namespace App\Http\Requests;

use App\Enums\UserTypeEnum;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignupRequest extends FormRequest
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'confirmed', 'min:8', "regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?`~ ])[\w!@#$%^&*()_+\-=\[\]{};':\"\\|,.<>\/?`~ ]{8,}$/"],
            'type' => ['required', new Enum(UserTypeEnum::class)],
        ];
    }

    public function messages()
    {
        return [
            'password.regex' => 'Password must be at least 8 characters long and contain an uppercase letter, a lowercase letter, a digit, and a special character.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => 'validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}