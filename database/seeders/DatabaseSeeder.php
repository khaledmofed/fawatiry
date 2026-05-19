<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceTemplate;
use App\Models\Product;
use App\Models\User;
use App\Services\InvoiceCalculationService;
use App\Support\InvoiceLayoutDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        CompanySetting::query()->updateOrInsert(
            ['id' => 1],
            [
                'company_name' => 'Acme Invoicing',
                'address' => "123 Business Rd\nLondon",
                'vat_number' => 'GB123456789',
                'phone' => '+44 20 0000 0000',
                'email' => 'billing@example.com',
                'default_currency' => 'GBP',
                'default_tax_rate' => 20,
                'next_invoice_number' => 1001,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->call(InvoiceTemplateSeeder::class);

        $client = Client::query()->create([
            'name' => 'Jane Client',
            'company' => 'Client Co',
            'email' => 'jane@client.test',
            'phone' => '+1 555 0100',
            'address' => '1 Client Street',
            'vat_number' => 'VAT-CLIENT-1',
        ]);

        $product = Product::query()->create([
            'name' => 'Consulting hour',
            'description' => 'Professional services',
            'price' => 150,
            'tax_rate' => 20,
            'sku' => 'SRV-1H',
            'is_active' => true,
        ]);

        $template = InvoiceTemplate::query()->where('slug', 'clean-white')->first();

        $invoice = Invoice::query()->create([
            'invoice_number' => '1001',
            'status' => InvoiceStatus::Draft->value,
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'currency' => 'GBP',
            'notes' => 'Sample draft invoice.',
            'terms' => 'Net 14.',
            'tax_total' => 0,
            'discount_total' => 0,
            'shipping_total' => 0,
            'subtotal' => 0,
            'total' => 0,
            'client_id' => $client->id,
            'invoice_template_id' => $template?->id,
            'direction' => 'ltr',
            'company_snapshot' => [
                'company_name' => 'Acme Invoicing',
                'logo_path' => null,
                'address' => "123 Business Rd\nLondon",
                'vat_number' => 'GB123456789',
                'phone' => '+44 20 0000 0000',
                'email' => 'billing@example.com',
            ],
        ]);

        $invoice->design()->create([
            'document_json' => $template ? InvoiceLayoutDocument::forNewInvoiceFromTemplate($template) : [],
            'version' => 1,
        ]);

        InvoiceItem::query()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'quantity' => 2,
            'unit_price' => $product->price,
            'tax_rate' => $product->tax_rate,
            'discount' => 0,
            'line_total' => 0,
            'sort_order' => 0,
        ]);

        app(InvoiceCalculationService::class)->recalculate($invoice->fresh());

        $paid = Invoice::query()->create([
            'invoice_number' => '1002',
            'status' => InvoiceStatus::Paid->value,
            'invoice_date' => now()->subMonth()->toDateString(),
            'due_date' => now()->subWeek()->toDateString(),
            'currency' => 'GBP',
            'notes' => null,
            'terms' => null,
            'tax_total' => 0,
            'discount_total' => 0,
            'shipping_total' => 10,
            'subtotal' => 0,
            'total' => 0,
            'client_id' => $client->id,
            'invoice_template_id' => $template?->id,
            'direction' => 'ltr',
            'company_snapshot' => [
                'company_name' => 'Acme Invoicing',
            ],
        ]);
        $paid->design()->create([
            'document_json' => $template ? InvoiceLayoutDocument::forNewInvoiceFromTemplate($template) : [],
            'version' => 1,
        ]);
        InvoiceItem::query()->create([
            'invoice_id' => $paid->id,
            'product_id' => $product->id,
            'name' => $product->name,
            'quantity' => 1,
            'unit_price' => 200,
            'tax_rate' => 20,
            'discount' => 0,
            'line_total' => 0,
            'sort_order' => 0,
        ]);
        app(InvoiceCalculationService::class)->recalculate($paid->fresh());

        CompanySetting::query()->whereKey(1)->update(['next_invoice_number' => 1003]);
    }
}
