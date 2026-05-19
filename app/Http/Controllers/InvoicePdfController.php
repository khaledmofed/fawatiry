<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\CompanySetting;
use App\Models\Invoice;
use App\Support\InvoiceLayoutDocument;
use App\Support\InvoicePdfAssets;
use App\Support\InvoicePreviewThemes;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

class InvoicePdfController extends Controller
{
    private static function chromePath(): string
    {
        // Linux (Docker/Render) or Windows local dev
        $linux = '/usr/bin/google-chrome-stable';
        return file_exists($linux) ? $linux : 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
    }

    public function dompdf(Invoice $invoice): Response
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'design', 'template']);
        $settings   = CompanySetting::current();
        $document   = InvoiceLayoutDocument::normalize($invoice->design?->document_json, $invoice);
        $themeSlug  = InvoiceLayoutDocument::resolvePreviewSlug($invoice->template?->slug);
        $theme      = InvoicePreviewThemes::for($themeSlug);
        $status     = InvoiceStatus::tryFrom((string) $invoice->status) ?? InvoiceStatus::Draft;
        $logoPath   = $settings->logo_path ?: ($invoice->company_snapshot['logo_path'] ?? null);

        $stampLayers = [];
        foreach ($document['stamps'] ?? [] as $stamp) {
            if (! is_array($stamp) || empty($stamp['path'])) {
                continue;
            }
            $uri = $this->publicDiskDataUri($stamp['path']);
            if ($uri) {
                $stampLayers[] = [
                    'dataUri'   => $uri,
                    'left_pct'  => (float) ($stamp['left_pct']  ?? 58),
                    'top_pct'   => (float) ($stamp['top_pct']   ?? 52),
                    'width_pct' => (float) ($stamp['width_pct'] ?? 22),
                    'rotation'  => (float) ($stamp['rotation']  ?? 0),
                ];
            }
        }

        $html = view('invoices.pdf.browsershot', [
            'invoice'            => $invoice,
            'document'           => $document,
            'theme'              => $theme,
            'themeSlug'          => $themeSlug,
            'logoDataUri'        => $this->publicDiskDataUri($logoPath),
            'stampLayers'        => $stampLayers,
            'embeddedCss'        => InvoicePdfAssets::compiledAppCss(),
            'company'            => [
                'name'        => $settings->company_name,
                'address'     => (string) ($settings->address    ?? ''),
                'phone'       => (string) ($settings->phone      ?? ''),
                'email'       => (string) ($settings->email      ?? ''),
                'vat_number'  => (string) ($settings->vat_number ?? ''),
            ],
            'statusBadgeClasses' => InvoicePdfAssets::statusBadgeClasses($theme, $invoice->status),
            'statusLabel'        => $status->label(),
            'fontRegularB64'     => $this->fontB64('inter-latin-400-normal.woff'),
            'fontSemiBoldB64'    => $this->fontB64('inter-latin-600-normal.woff'),
            'fontBoldB64'        => $this->fontB64('inter-latin-700-normal.woff'),
        ])->render();

        $isLinux = PHP_OS_FAMILY === 'Linux';

        if ($isLinux) {
            putenv('HOME=/tmp');
        }

        $chromeArgs = ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'];

        if ($isLinux) {
            $chromeArgs = array_merge($chromeArgs, [
                '--disable-gpu',
                '--no-zygote',
                '--single-process',
                '--user-data-dir=/tmp/chrome-user-data',
            ]);
        }

        $pdf = Browsershot::html($html)
            ->setChromePath(self::chromePath())
            ->setOption('args', $chromeArgs)
            ->format('A4')
            ->showBackground()
            ->margins(0, 0, 0, 0)
            ->waitUntilNetworkIdle()
            ->pdf();

        return response($pdf, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="invoice-' . $invoice->invoice_number . '.pdf"',
        ]);
    }

    private function publicDiskDataUri(?string $relativePath): ?string
    {
        if (! $relativePath || ! Storage::disk('public')->exists($relativePath)) {
            return null;
        }

        $full = Storage::disk('public')->path($relativePath);
        if (! is_file($full)) {
            return null;
        }

        $mime = @mime_content_type($full) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($full));
    }

    private function fontB64(string $filename): string
    {
        $path = base_path('node_modules/@fontsource/inter/files/' . $filename);

        return is_file($path) ? base64_encode((string) file_get_contents($path)) : '';
    }
}
