<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\InvoiceTemplate;
use Illuminate\Support\Str;

class InvoiceLayoutDocument
{
    public const VERSION = 2;

    /** @var array<string, string> */
    public const SLUG_ALIASES = [
        'minimal' => 'modern-minimal',
        'corporate' => 'corporate-blue',
        'arabic-rtl' => 'arabic-rtl-professional',
        'modern-clean' => 'clean-white',
        'elegant' => 'elegant-serif',
        'creative' => 'creative-agency',
    ];

    public static function resolvePreviewSlug(?string $slug): string
    {
        $s = $slug ?: 'clean-white';
        $s = self::SLUG_ALIASES[$s] ?? $s;

        return InvoicePreviewThemes::has($s) ? $s : 'clean-white';
    }

    /**
     * @return array<string, string>
     */
    public static function defaultCustom(?string $slug = null): array
    {
        $slug = self::resolvePreviewSlug($slug);

        return match ($slug) {
            'luxury-black-gold' => [
                'thank_you' => __('Thank you for your valued business.'),
                'legal_footer' => __('Registered company · All amounts exclude local statutory adjustments unless stated.'),
                'signature_label' => __('Authorized signature'),
            ],
            'arabic-rtl-professional' => [
                'thank_you' => __('شكراً لتعاملكم معنا.'),
                'legal_footer' => __('هذه الفاتورة صادرة إلكترونياً ولا تحتاج إلى ختم.'),
                'signature_label' => __('التوقيع المعتمد'),
            ],
            default => [
                'thank_you' => __('Thank you for your business.'),
                'legal_footer' => __('Payment is due within the agreed terms. Please include the invoice number on your remittance.'),
                'signature_label' => __('Authorized signature'),
            ],
        };
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultMeta(Invoice $invoice): array
    {
        return [
            'direction' => $invoice->direction ?? 'ltr',
            'zoom' => 1,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultLogo(): array
    {
        return [
            'offset_x' => 0,
            'offset_y' => 0,
            'scale' => 1,
        ];
    }

    /**
     * One draggable stamp overlay (percentage of page box).
     *
     * @return array<string, mixed>
     */
    public static function newStampItem(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'path' => null,
            'left_pct' => 58,
            'top_pct' => 52,
            'width_pct' => 22,
            'rotation' => -8,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forNewInvoiceFromTemplate(InvoiceTemplate $template): array
    {
        $slug = self::resolvePreviewSlug($template->slug);

        return [
            'version' => self::VERSION,
            'meta' => [
                'direction' => $template->direction,
                'zoom' => 1,
            ],
            'custom' => self::defaultCustom($slug),
            'logo' => self::defaultLogo(),
            'stamps' => [],
        ];
    }

    /**
     * @param  array<string, mixed>|null  $document
     * @return array<string, mixed>
     */
    public static function normalize(?array $document, Invoice $invoice): array
    {
        $slug = self::resolvePreviewSlug($invoice->template?->slug);
        $defaults = [
            'version' => self::VERSION,
            'meta' => self::defaultMeta($invoice),
            'custom' => self::defaultCustom($slug),
            'logo' => self::defaultLogo(),
            'stamps' => [],
        ];

        if (! is_array($document) || $document === []) {
            return $defaults;
        }

        $version = (int) ($document['version'] ?? 1);
        if ($version < self::VERSION || ! isset($document['custom']) || ! is_array($document['custom'])) {
            return $defaults;
        }

        $document['version'] = self::VERSION;
        $document['meta'] = array_replace($defaults['meta'], is_array($document['meta'] ?? null) ? $document['meta'] : []);
        $document['custom'] = array_replace($defaults['custom'], $document['custom']);
        $document['logo'] = array_replace($defaults['logo'], is_array($document['logo'] ?? null) ? $document['logo'] : []);

        $stamps = [];
        if (isset($document['stamps']) && is_array($document['stamps'])) {
            foreach ($document['stamps'] as $row) {
                if (is_array($row)) {
                    $stamps[] = self::normalizeStampRow($row);
                }
            }
        }
        if ($stamps === [] && isset($document['stamp']) && is_array($document['stamp']) && ! empty($document['stamp']['path'])) {
            $legacy = $document['stamp'];
            $stamps[] = self::normalizeStampRow(array_merge($legacy, [
                'id' => is_string($legacy['id'] ?? null) && $legacy['id'] !== ''
                    ? $legacy['id']
                    : (string) Str::uuid(),
            ]));
        }
        $document['stamps'] = $stamps;
        unset($document['stamp']);

        return $document;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public static function normalizeStampRow(array $row): array
    {
        $id = isset($row['id']) && is_string($row['id']) && $row['id'] !== ''
            ? $row['id']
            : (string) Str::uuid();

        return [
            'id' => $id,
            'path' => isset($row['path']) && is_string($row['path']) && $row['path'] !== '' ? $row['path'] : null,
            'left_pct' => self::clampNum($row['left_pct'] ?? 58, 0, 100),
            'top_pct' => self::clampNum($row['top_pct'] ?? 52, 0, 100),
            'width_pct' => self::clampNum($row['width_pct'] ?? 22, 5, 55),
            'rotation' => self::clampNum($row['rotation'] ?? -8, -180, 180),
        ];
    }

    private static function clampNum(mixed $v, float $min, float $max): float
    {
        $n = (float) $v;

        return max($min, min($max, $n));
    }
}
