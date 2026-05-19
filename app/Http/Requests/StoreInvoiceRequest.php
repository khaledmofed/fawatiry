<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'invoice_template_id' => ['required', 'exists:invoice_templates,id'],
            'client_id' => ['nullable', 'exists:clients,id'],
        ];
    }
}
