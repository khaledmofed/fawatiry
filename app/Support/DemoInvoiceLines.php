<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\InvoiceCalculationService;

class DemoInvoiceLines
{
    public static function seedIfEmpty(Invoice $invoice): void
    {
        if ($invoice->items()->exists()) {
            return;
        }

        $rows = [
            [
                'name'        => __('Professional services'),
                'description' => __('Consulting, requirements gathering, and project management'),
                'quantity'    => 1,
                'unit_price'  => 1200,
                'tax_rate'    => 15,
                'discount'    => 0,
            ],
            [
                'name'        => __('Implementation & onboarding'),
                'description' => __('System setup, configuration, and team training sessions'),
                'quantity'    => 8,
                'unit_price'  => 150,
                'tax_rate'    => 15,
                'discount'    => 0,
            ],
            [
                'name'        => __('Platform license (annual)'),
                'description' => __('Full-access license for 12 months, including updates and support'),
                'quantity'    => 1,
                'unit_price'  => 2400,
                'tax_rate'    => 15,
                'discount'    => 100,
            ],
        ];

        foreach ($rows as $index => $row) {
            $item = new InvoiceItem([
                'product_id'  => null,
                'name'        => $row['name'],
                'description' => $row['description'],
                'quantity'    => $row['quantity'],
                'unit_price'  => $row['unit_price'],
                'tax_rate'    => $row['tax_rate'],
                'discount'    => $row['discount'],
                'sort_order'  => $index,
            ]);
            $item->invoice()->associate($invoice);
            $item->save();
        }

        app(InvoiceCalculationService::class)->recalculate($invoice->fresh());
    }
}
