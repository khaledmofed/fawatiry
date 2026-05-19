<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $totalInvoices = Invoice::query()->count();
        $paid = Invoice::query()->where('status', InvoiceStatus::Paid->value)->count();
        $pending = Invoice::query()->whereIn('status', [
            InvoiceStatus::Pending->value,
            InvoiceStatus::Draft->value,
        ])->count();
        $overdue = Invoice::query()->where('status', InvoiceStatus::Overdue->value)->count();

        $revenue = (float) Invoice::query()
            ->where('status', InvoiceStatus::Paid->value)
            ->sum('total');

        $statusCounts = [
            'draft' => Invoice::query()->where('status', InvoiceStatus::Draft->value)->count(),
            'pending' => Invoice::query()->where('status', InvoiceStatus::Pending->value)->count(),
            'paid' => $paid,
            'cancelled' => Invoice::query()->where('status', InvoiceStatus::Cancelled->value)->count(),
            'overdue' => $overdue,
        ];

        $latestInvoices = Invoice::query()->with('client')->latest()->limit(8)->get();
        $recentClients = Client::query()->latest()->limit(8)->get();

        $chartLabels = [];
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i)->startOfMonth();
            $chartLabels[] = $d->translatedFormat('M');
            $chartData[] = (float) Invoice::query()
                ->where('status', InvoiceStatus::Paid->value)
                ->whereYear('invoice_date', $d->year)
                ->whereMonth('invoice_date', $d->month)
                ->sum('total');
        }

        return view('dashboard', compact(
            'totalInvoices',
            'paid',
            'pending',
            'overdue',
            'revenue',
            'statusCounts',
            'latestInvoices',
            'recentClients',
            'chartLabels',
            'chartData',
        ));
    }
}
