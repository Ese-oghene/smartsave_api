<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContributeRequest extends FormRequest
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
            'amount' => 'required|numeric|min:1',
            'description' => 'nullable|string|max:255',
            // optional: allow status (default = pending)
            'status'      => 'in:pending,approved,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'Contribution amount is required.',
            'amount.numeric'  => 'Amount must be a valid number.',
            'amount.min'      => 'Amount must be at least 1.',
            'status.in'       => 'Invalid status value.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'code' => 422,
                'message' => 'Validation Failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
