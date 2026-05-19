<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceDesign;
use Illuminate\Support\Facades\Validator;

class InvoiceDesignService
{
    public const MAX_JSON_BYTES = 512000;

    /**
     * @param  array<string, mixed>  $document
     * @return array<string, mixed>
     */
    public function validateDocument(array $document): array
    {
        $version = (int) ($document['version'] ?? 1);

        if ($version >= 2) {
            $validator = Validator::make($document, [
                'version' => 'required|integer|min:2',
                'meta' => 'required|array',
                'meta.direction' => 'nullable|in:ltr,rtl',
                'meta.zoom' => 'nullable|numeric|min:0.25|max:3',
                'custom' => 'required|array',
                'custom.thank_you' => 'nullable|string|max:5000',
                'custom.legal_footer' => 'nullable|string|max:5000',
                'custom.signature_label' => 'nullable|string|max:500',
                'logo' => 'nullable|array',
                'logo.offset_x' => 'nullable|numeric|between:-40,40',
                'logo.offset_y' => 'nullable|numeric|between:-40,40',
                'logo.scale' => 'nullable|numeric|between:0.5,1.6',
                'stamps' => 'nullable|array',
                'stamps.*.id' => 'required|string|max:64',
                'stamps.*.path' => 'nullable|string|max:500',
                'stamps.*.left_pct' => 'nullable|numeric|between:0,100',
                'stamps.*.top_pct' => 'nullable|numeric|between:0,100',
                'stamps.*.width_pct' => 'nullable|numeric|between:5,55',
                'stamps.*.rotation' => 'nullable|numeric|between:-180,180',
            ]);
            $validator->validate();
        } else {
            $document['meta'] = array_merge([
                'widthMm' => 210,
                'heightMm' => 297,
                'zoom' => 1,
                'grid' => 8,
                'snap' => true,
            ], $document['meta'] ?? []);
            $document['elements'] = $document['elements'] ?? [];

            $validator = Validator::make($document, [
                'version' => 'required|integer|min:1',
                'meta' => 'required|array',
                'elements' => 'present|array',
            ]);
            $validator->validate();
        }

        $encoded = json_encode($document);
        if ($encoded !== false && strlen($encoded) > self::MAX_JSON_BYTES) {
            throw new \InvalidArgumentException(__('Design JSON is too large.'));
        }

        return $document;
    }

    public function save(Invoice $invoice, array $document): InvoiceDesign
    {
        $document = $this->validateDocument($document);

        $design = $invoice->design()->firstOrNew([]);
        $design->document_json = $document;
        $design->version = ($design->version ?? 0) + 1;
        $design->save();

        return $design;
    }
}
