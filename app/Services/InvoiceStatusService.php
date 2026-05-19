<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceStatusService
{
    public function markOverdueInvoices(): int
    {
        return DB::transaction(function (): int {
            return Invoice::query()
                ->where('status', InvoiceStatus::Pending->value)
                ->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->update(['status' => InvoiceStatus::Overdue->value]);
        });
    }
}
