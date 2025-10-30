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
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // ✅ new rule
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
            'receipt.file'    => 'The receipt must be a valid file.',
            'receipt.mimes'   => 'The receipt must be an image or PDF file.',
            'receipt.max'     => 'The receipt may not be greater than 2MB.',
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
