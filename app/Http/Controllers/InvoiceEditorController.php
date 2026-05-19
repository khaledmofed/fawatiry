<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\UpdateInvoiceDesignRequest;
use App\Http\Requests\UpdateInvoiceEditorClientRequest;
use App\Http\Requests\UpdateInvoiceEditorMetaRequest;
use App\Http\Requests\UpdateInvoiceEditorTemplateRequest;
use App\Http\Requests\UpdateInvoiceItemsRequest;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceTemplate;
use App\Models\Product;
use App\Services\CompanySettingsService;
use App\Services\InvoiceCalculationService;
use App\Services\InvoiceDesignService;
use App\Support\InvoiceLayoutDocument;
use App\Support\InvoicePreviewThemes;
use App\Support\PublicStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InvoiceEditorController extends Controller
{
    public function __construct(
        private InvoiceDesignService $designService,
        private InvoiceCalculationService $calculationService,
        private CompanySettingsService $companySettingsService
    ) {}

    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);

        $invoice->load(['items', 'client', 'design', 'template']);
        $settings = CompanySetting::current();
        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $clients = Client::query()->orderBy('name')->get(['id', 'name', 'company', 'email']);
        $templates = InvoiceTemplate::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'slug', 'direction']);

        $document = InvoiceLayoutDocument::normalize($invoice->design?->document_json, $invoice);

        $logoUrl = PublicStorage::url($settings->logo_path);

        $themeSlug = InvoiceLayoutDocument::resolvePreviewSlug($invoice->template?->slug);
        $theme = InvoicePreviewThemes::for($themeSlug);

        $editorPayload = [
            'theme' => $theme,
            'routes' => [
                'design' => route('invoices.design.update', $invoice),
                'items' => route('invoices.items.update', $invoice),
                'meta' => route('invoices.editor.meta', $invoice),
                'client' => route('invoices.editor.client', $invoice),
                'template' => route('invoices.editor.template', $invoice),
                'dompdf' => route('invoices.pdf.dompdf', $invoice),
                'logo' => route('invoices.editor.logo', $invoice),
                'stamp' => route('invoices.editor.stamp', $invoice),
                'stampRemove' => route('invoices.editor.stamp.remove', $invoice),
            ],
            'document' => $document,
            'themeSlug' => $themeSlug,
            'items' => $invoice->items->map(fn (InvoiceItem $i) => [
                'id' => $i->id,
                'product_id' => $i->product_id,
                'name' => $i->name,
                'description' => $i->description,
                'quantity' => (float) $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'tax_rate' => (float) $i->tax_rate,
                'discount' => (float) $i->discount,
                'line_total' => (float) $i->line_total,
            ])->values()->all(),
            'products' => $products->map(fn (Product $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->price,
                'tax_rate' => (float) $p->tax_rate,
            ])->values()->all(),
            'clients' => $clients->map(fn (Client $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'company' => $c->company,
                'email' => $c->email,
            ])->values()->all(),
            'templates' => $templates->map(fn (InvoiceTemplate $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'slug' => $t->slug,
                'direction' => $t->direction,
            ])->values()->all(),
            'company' => [
                'name' => $settings->company_name,
                'address' => $settings->address,
                'phone' => $settings->phone,
                'email' => $settings->email,
                'vat_number' => $settings->vat_number,
                'logo_url' => $logoUrl,
                'settings_url' => route('settings.edit'),
            ],
            'client' => $invoice->client ? [
                'id' => $invoice->client->id,
                'name' => $invoice->client->name,
                'company' => $invoice->client->company,
                'email' => $invoice->client->email,
                'phone' => $invoice->client->phone,
                'address' => $invoice->client->address,
                'vat_number' => $invoice->client->vat_number,
            ] : null,
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'currency' => $invoice->currency,
                'notes' => $invoice->notes,
                'terms' => $invoice->terms,
                'shipping_total' => (float) $invoice->shipping_total,
                'direction' => $invoice->direction,
                'client_id' => $invoice->client_id,
                'invoice_template_id' => $invoice->invoice_template_id,
                'subtotal' => (float) $invoice->subtotal,
                'tax_total' => (float) $invoice->tax_total,
                'discount_total' => (float) $invoice->discount_total,
                'total' => (float) $invoice->total,
            ],
            'strings' => [
                'item' => __('Item'),
                'qty' => __('Qty'),
                'price' => __('Price'),
                'total' => __('Total'),
                'addLine' => __('Add line'),
                'pickProduct' => __('Select product'),
                'save' => __('Save'),
                'saved' => __('Saved'),
                'saving' => __('Saving…'),
                'failed' => __('Save failed'),
                'invoice' => __('Invoice'),
                'billTo' => __('Bill to'),
                'from' => __('From'),
                'meta' => __('Details'),
                'notes' => __('Notes'),
                'terms' => __('Terms'),
                'subtotal' => __('Subtotal'),
                'tax' => __('Tax'),
                'discount' => __('Discount'),
                'shipping' => __('Shipping'),
                'grand' => __('Total'),
                'emptyLine' => __('Empty line'),
                'noClient' => __('No client assigned'),
                'assignClient' => __('Assign client'),
                'template' => __('Template'),
                'applyTemplate' => __('Apply template'),
                'companyHint' => __('Company details are managed in Settings.'),
                'print' => __('Print'),
                'pdf' => __('Export PDF'),
                'zoomIn' => __('Zoom in'),
                'zoomOut' => __('Zoom out'),
                'addLogo' => __('Add logo'),
                'changeLogo' => __('Change logo'),
                'logoHint' => __('Drag slightly to nudge inside header.'),
                'stamp' => __('Stamps / seals'),
                'uploadStamp' => __('Add stamp image'),
                'removeStamp' => __('Remove selected stamp'),
                'resetStamp' => __('Reset position & size of selected'),
                'stampWidth' => __('Stamp size'),
                'stampRotate' => __('Rotation (°)'),
                'stampsHint' => __('You can add several stamps and drag each one anywhere on the page.'),
                'stampList' => __('Stamps on this invoice'),
            ],
            'statusLabels' => collect(InvoiceStatus::cases())
                ->mapWithKeys(fn (InvoiceStatus $s) => [$s->value => $s->label()])
                ->all(),
        ];

        return view('invoices.editor', [
            'invoice' => $invoice,
            'editorPayload' => $editorPayload,
            'theme' => $theme,
            'themeSlug' => $themeSlug,
        ]);
    }

    public function updateDesign(UpdateInvoiceDesignRequest $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $design = $this->designService->save($invoice, $request->validated('document'));

        return response()->json([
            'ok' => true,
            'version' => $design->version,
        ]);
    }

    public function updateItems(UpdateInvoiceItemsRequest $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $rows = $request->validated('items');

        DB::transaction(function () use ($invoice, $rows): void {
            $invoice->items()->delete();
            foreach ($rows as $index => $row) {
                $item = new InvoiceItem([
                    'product_id' => $row['product_id'] ?? null,
                    'name' => $row['name'],
                    'description' => $row['description'] ?? null,
                    'quantity' => $row['quantity'],
                    'unit_price' => $row['unit_price'],
                    'tax_rate' => $row['tax_rate'] ?? 0,
                    'discount' => $row['discount'] ?? 0,
                    'sort_order' => $index,
                ]);
                $item->invoice()->associate($invoice);
                $item->save();
            }
        });

        $this->calculationService->recalculate($invoice);
        $invoice->refresh()->load('items');

        return response()->json([
            'ok' => true,
            'invoice' => [
                'subtotal' => (float) $invoice->subtotal,
                'tax_total' => (float) $invoice->tax_total,
                'discount_total' => (float) $invoice->discount_total,
                'shipping_total' => (float) $invoice->shipping_total,
                'total' => (float) $invoice->total,
            ],
            'items' => $invoice->items->map(fn (InvoiceItem $i) => [
                'id' => $i->id,
                'product_id' => $i->product_id,
                'name' => $i->name,
                'description' => $i->description,
                'quantity' => (float) $i->quantity,
                'unit_price' => (float) $i->unit_price,
                'tax_rate' => (float) $i->tax_rate,
                'discount' => (float) $i->discount,
                'line_total' => (float) $i->line_total,
            ])->values()->all(),
        ]);
    }

    public function updateMeta(UpdateInvoiceEditorMetaRequest $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $data = $request->validated();
        $invoice->update($data);
        $this->calculationService->recalculate($invoice->fresh());

        $invoice->refresh()->load('client');

        return response()->json([
            'ok' => true,
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'invoice_date' => $invoice->invoice_date?->format('Y-m-d'),
                'due_date' => $invoice->due_date?->format('Y-m-d'),
                'currency' => $invoice->currency,
                'notes' => $invoice->notes,
                'terms' => $invoice->terms,
                'shipping_total' => (float) $invoice->shipping_total,
                'direction' => $invoice->direction,
                'client_id' => $invoice->client_id,
                'invoice_template_id' => $invoice->invoice_template_id,
                'subtotal' => (float) $invoice->subtotal,
                'tax_total' => (float) $invoice->tax_total,
                'discount_total' => (float) $invoice->discount_total,
                'total' => (float) $invoice->total,
            ],
            'client' => $invoice->client ? [
                'id' => $invoice->client->id,
                'name' => $invoice->client->name,
                'company' => $invoice->client->company,
                'email' => $invoice->client->email,
                'phone' => $invoice->client->phone,
                'address' => $invoice->client->address,
                'vat_number' => $invoice->client->vat_number,
            ] : null,
        ]);
    }

    public function updateClient(UpdateInvoiceEditorClientRequest $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        if (! $invoice->client_id) {
            return response()->json(['ok' => false, 'message' => __('Assign a client first.')], 422);
        }

        $invoice->client?->update($request->validated());
        $invoice->load('client');

        return response()->json([
            'ok' => true,
            'client' => $invoice->client ? [
                'id' => $invoice->client->id,
                'name' => $invoice->client->name,
                'company' => $invoice->client->company,
                'email' => $invoice->client->email,
                'phone' => $invoice->client->phone,
                'address' => $invoice->client->address,
                'vat_number' => $invoice->client->vat_number,
            ] : null,
        ]);
    }

    public function updateTemplate(UpdateInvoiceEditorTemplateRequest $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $template = InvoiceTemplate::query()->findOrFail($request->validated('invoice_template_id'));

        DB::transaction(function () use ($invoice, $template): void {
            $prev = InvoiceLayoutDocument::normalize($invoice->design?->document_json, $invoice);

            $invoice->update([
                'invoice_template_id' => $template->id,
                'direction' => $template->direction,
            ]);

            $document = InvoiceLayoutDocument::forNewInvoiceFromTemplate($template);
            $document['meta']['direction'] = $template->direction;
            $document['custom'] = array_replace($document['custom'], $prev['custom']);
            $document['logo'] = $prev['logo'];
            $document['stamps'] = $prev['stamps'];
            $document['meta']['zoom'] = $prev['meta']['zoom'] ?? $document['meta']['zoom'];

            $this->designService->save($invoice, $document);
        });

        return response()->json(['ok' => true, 'reload' => true]);
    }

    public function uploadLogo(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'logo' => ['required', 'image', 'max:4096'],
        ]);

        $settings = $this->companySettingsService->replaceLogo($request->file('logo'));
        $url = PublicStorage::url($settings->logo_path);

        return response()->json([
            'ok' => true,
            'logo_url' => $url,
        ]);
    }

    public function uploadStamp(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'stamp' => ['required', 'image', 'max:4096'],
        ]);

        $doc = InvoiceLayoutDocument::normalize($invoice->design?->document_json, $invoice);

        $dir = 'invoices/'.$invoice->id;
        Storage::disk('public')->makeDirectory($dir);

        $path = $request->file('stamp')->store($dir, 'public');
        $new = InvoiceLayoutDocument::newStampItem();
        $new['path'] = $path;
        $doc['stamps'][] = $new;
        $this->designService->save($invoice, $doc);

        return response()->json([
            'ok' => true,
            'document' => $doc,
            'new_stamp_id' => $new['id'],
            'stamp_url' => PublicStorage::url($path),
        ]);
    }

    public function removeStamp(Request $request, Invoice $invoice): JsonResponse
    {
        $this->authorize('update', $invoice);

        $data = $request->validate([
            'stamp_id' => ['required', 'string', 'max:64'],
        ]);

        $doc = InvoiceLayoutDocument::normalize($invoice->design?->document_json, $invoice);
        $stamps = $doc['stamps'];
        $idx = null;
        foreach ($stamps as $i => $row) {
            if (($row['id'] ?? '') === $data['stamp_id']) {
                $idx = $i;
                break;
            }
        }
        if ($idx === null) {
            return response()->json(['ok' => false, 'message' => __('Stamp not found.')], 404);
        }

        $removed = $stamps[$idx];
        $oldPath = $removed['path'] ?? null;
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        array_splice($stamps, $idx, 1);
        $doc['stamps'] = array_values($stamps);
        $this->designService->save($invoice, $doc);

        return response()->json([
            'ok' => true,
            'document' => $doc,
        ]);
    }
}
