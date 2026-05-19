<?php

namespace App\Http\Requests;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'status' => ['required', Rule::in(InvoiceStatus::values())],
            'invoice_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date'],
            'currency' => ['required', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'shipping_total' => ['nullable', 'numeric', 'min:0'],
            'direction' => ['required', Rule::in(['ltr', 'rtl'])],
        ];
    }
}
