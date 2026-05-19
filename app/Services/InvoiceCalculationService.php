<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;

class InvoiceCalculationService
{
    public function recalculate(Invoice $invoice): void
    {
        DB::transaction(function () use ($invoice): void {
            $invoice->load('items');
            $subtotal = 0;
            $taxTotal = 0;
            $discountTotal = 0;

            foreach ($invoice->items as $item) {
                $line = $this->lineAmounts($item);
                $subtotal += $line['net'];
                $taxTotal += $line['tax'];
                $discountTotal += $line['discount'];

                if ((string) $item->line_total !== (string) $line['line_total']) {
                    $item->forceFill(['line_total' => $line['line_total']])->saveQuietly();
                }
            }

            $shipping = (float) $invoice->shipping_total;
            $total = $subtotal + $taxTotal + $shipping;

            $invoice->update([
                'subtotal' => round($subtotal, 2),
                'tax_total' => round($taxTotal, 2),
                'discount_total' => round($discountTotal, 2),
                'total' => round($total, 2),
            ]);
        });
    }

    /**
     * @return array{net: float, tax: float, discount: float, line_total: float}
     */
    public function lineAmounts(InvoiceItem $item): array
    {
        $qty = (float) $item->quantity;
        $unit = (float) $item->unit_price;
        $discount = (float) $item->discount;
        $rate = (float) $item->tax_rate;

        $gross = $qty * $unit;
        $net = max(0, $gross - $discount);
        $tax = $net * ($rate / 100);

        return [
            'net' => $net,
            'tax' => $tax,
            'discount' => $discount,
            'line_total' => round($net + $tax, 2),
        ];
    }

    public function syncItemLineTotal(InvoiceItem $item): void
    {
        $line = $this->lineAmounts($item);
        $item->line_total = $line['line_total'];
        $item->saveQuietly();
    }
}
