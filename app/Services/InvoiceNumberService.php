<?php

namespace App\Services;

use App\Models\CompanySetting;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    public function peek(): string
    {
        $settings = CompanySetting::current();

        return (string) $settings->next_invoice_number;
    }

    public function consumeNext(): string
    {
        return DB::transaction(function (): string {
            $settings = CompanySetting::query()->lockForUpdate()->whereKey(1)->first()
                ?? CompanySetting::query()->lockForUpdate()->firstOrFail();
            $number = (string) $settings->next_invoice_number;
            $settings->increment('next_invoice_number');

            return $this->ensureUnique($number);
        });
    }

    public function ensureUnique(string $number): string
    {
        $base = $number;
        $i = 0;
        while (Invoice::query()->where('invoice_number', $number)->exists()) {
            $i++;
            $number = $base.'-'.$i;
        }

        return $number;
    }
}
