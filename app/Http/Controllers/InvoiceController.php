<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use App\Services\InvoiceCalculationService;
use App\Services\InvoiceNumberService;
use App\Support\DemoInvoiceLines;
use App\Support\InvoiceLayoutDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceNumberService $invoiceNumberService,
        private InvoiceCalculationService $calculationService
    ) {
        $this->authorizeResource(Invoice::class, 'invoice', [
            'except' => ['store'],
        ]);
    }

    public function index(): View
    {
        $invoices = Invoice::query()->with('client')->latest()->paginate(20);

        return view('invoices.index', compact('invoices'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('invoice-templates.index');
    }

    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        $this->authorize('create', Invoice::class);

        $template = InvoiceTemplate::query()->findOrFail($request->validated('invoice_template_id'));
        $settings = CompanySetting::current();

        $invoice = DB::transaction(function () use ($request, $template, $settings): Invoice {
            $number = $this->invoiceNumberService->consumeNext();
            $snapshot = [
                'company_name' => $settings->company_name,
                'logo_path' => $settings->logo_path,
                'address' => $settings->address,
                'vat_number' => $settings->vat_number,
                'phone' => $settings->phone,
                'email' => $settings->email,
            ];

            $invoice = Invoice::query()->create([
                'invoice_number' => $number,
                'status' => InvoiceStatus::Draft->value,
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'currency' => $settings->default_currency,
                'notes' => null,
                'terms' => null,
                'tax_total' => 0,
                'discount_total' => 0,
                'shipping_total' => 0,
                'subtotal' => 0,
                'total' => 0,
                'client_id' => $request->validated('client_id'),
                'invoice_template_id' => $template->id,
                'direction' => $template->direction,
                'company_snapshot' => $snapshot,
            ]);

            $invoice->design()->create([
                'document_json' => InvoiceLayoutDocument::forNewInvoiceFromTemplate($template),
                'version' => 1,
            ]);

            DemoInvoiceLines::seedIfEmpty($invoice);

            return $invoice;
        });

        return redirect()->route('invoices.editor', $invoice)
            ->with('success', __('Invoice draft created.'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['client', 'items', 'design']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->load('client');
        $clients = Client::query()->orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        $invoice->update($request->validated());
        $this->calculationService->recalculate($invoice->fresh());

        return redirect()->route('invoices.show', $invoice)->with('success', __('Invoice updated.'));
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', __('Invoice deleted.'));
    }
}
