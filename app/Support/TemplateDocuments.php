<?php

namespace App\Support;

use Illuminate\Support\Str;

class TemplateDocuments
{
    private static function baseMeta(string $direction, string $pageBackground = '#ffffff'): array
    {
        return [
            'widthMm' => 210,
            'heightMm' => 297,
            'zoom' => 1,
            'grid' => 8,
            'snap' => true,
            'direction' => $direction,
            'pageBackground' => $pageBackground,
        ];
    }

    private static function el(array $data): array
    {
        return array_merge([
            'id' => (string) Str::uuid(),
            'rotation' => 0,
            'fontFamily' => 'ui-sans-serif, system-ui, sans-serif',
            'fontWeight' => '400',
            'color' => '#111827',
            'textAlign' => 'left',
            'backgroundColor' => 'transparent',
            'borderWidth' => 0,
            'borderColor' => '#e5e7eb',
            'borderRadius' => 0,
            'opacity' => 1,
            'padding' => 4,
            'margin' => 0,
        ], $data);
    }

    public static function minimal(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('ltr', '#ffffff'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'Invoice', 'x' => 32, 'y' => 32, 'w' => 280, 'h' => 48, 'fontSize' => 32, 'fontWeight' => '700']),
                self::el(['type' => 'text', 'content' => 'Professional invoice', 'x' => 32, 'y' => 88, 'w' => 360, 'h' => 28, 'fontSize' => 14, 'color' => '#6b7280']),
                self::el(['type' => 'divider', 'content' => '', 'x' => 32, 'y' => 124, 'w' => 520, 'h' => 2, 'backgroundColor' => '#e5e7eb', 'borderWidth' => 0]),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 32, 'y' => 160, 'w' => 520, 'h' => 260, 'fontSize' => 11, 'backgroundColor' => '#ffffff', 'borderWidth' => 1]),
                self::el(['type' => 'footer', 'content' => 'Thank you for your business.', 'x' => 32, 'y' => 980, 'w' => 520, 'h' => 36, 'fontSize' => 11, 'color' => '#6b7280']),
            ],
        ];
    }

    public static function corporate(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('ltr', '#f8fafc'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'INVOICE', 'x' => 0, 'y' => 0, 'w' => 596, 'h' => 72, 'fontSize' => 26, 'fontWeight' => '700', 'color' => '#ffffff', 'backgroundColor' => '#0f172a', 'padding' => 24, 'borderRadius' => 0]),
                self::el(['type' => 'text', 'content' => 'Corporate billing statement', 'x' => 32, 'y' => 88, 'w' => 400, 'h' => 24, 'fontSize' => 13, 'color' => '#475569']),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 32, 'y' => 130, 'w' => 532, 'h' => 280, 'fontSize' => 11, 'backgroundColor' => '#ffffff', 'borderWidth' => 1, 'borderColor' => '#e2e8f0']),
                self::el(['type' => 'notes', 'content' => 'Payment terms: Net 30. Wire transfer preferred.', 'x' => 32, 'y' => 980, 'w' => 520, 'h' => 48, 'fontSize' => 11, 'color' => '#64748b']),
            ],
        ];
    }

    public static function luxuryBlackGold(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('ltr', '#0a0a0a'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'Invoice', 'x' => 40, 'y' => 40, 'w' => 400, 'h' => 56, 'fontSize' => 36, 'fontWeight' => '700', 'color' => '#d4af37']),
                self::el(['type' => 'text', 'content' => 'Luxury edition', 'x' => 40, 'y' => 100, 'w' => 360, 'h' => 28, 'fontSize' => 13, 'color' => '#a3a3a3']),
                self::el(['type' => 'divider', 'content' => '', 'x' => 40, 'y' => 136, 'w' => 200, 'h' => 2, 'backgroundColor' => '#d4af37', 'borderWidth' => 0]),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 40, 'y' => 170, 'w' => 520, 'h' => 280, 'fontSize' => 11, 'color' => '#e5e5e5', 'backgroundColor' => '#171717', 'borderWidth' => 1, 'borderColor' => '#404040']),
                self::el(['type' => 'footer', 'content' => 'With appreciation.', 'x' => 40, 'y' => 980, 'w' => 520, 'h' => 32, 'fontSize' => 11, 'color' => '#737373']),
            ],
        ];
    }

    public static function arabicRtl(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('rtl', '#ffffff'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'فاتورة', 'x' => 32, 'y' => 32, 'w' => 520, 'h' => 56, 'fontSize' => 34, 'fontWeight' => '700', 'textAlign' => 'right', 'fontFamily' => 'Tahoma, Arial, sans-serif']),
                self::el(['type' => 'text', 'content' => 'شكراً لتعاملكم معنا', 'x' => 32, 'y' => 96, 'w' => 520, 'h' => 32, 'fontSize' => 14, 'color' => '#4b5563', 'textAlign' => 'right', 'fontFamily' => 'Tahoma, Arial, sans-serif']),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 32, 'y' => 150, 'w' => 520, 'h' => 280, 'fontSize' => 11, 'textAlign' => 'right', 'fontFamily' => 'Tahoma, Arial, sans-serif']),
            ],
        ];
    }

    public static function modernClean(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('ltr', '#fafafa'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'Invoice', 'x' => 48, 'y' => 48, 'w' => 300, 'h' => 44, 'fontSize' => 28, 'fontWeight' => '600', 'color' => '#111827']),
                self::el(['type' => 'text', 'content' => 'Clean & modern', 'x' => 48, 'y' => 96, 'w' => 320, 'h' => 24, 'fontSize' => 13, 'color' => '#9ca3af']),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 48, 'y' => 150, 'w' => 500, 'h' => 300, 'fontSize' => 12, 'backgroundColor' => '#ffffff', 'borderWidth' => 0, 'borderRadius' => 12, 'padding' => 12]),
            ],
        ];
    }

    public static function elegant(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('ltr', '#fffdf7'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'Invoice', 'x' => 56, 'y' => 48, 'w' => 320, 'h' => 52, 'fontSize' => 30, 'fontWeight' => '600', 'color' => '#1f2937', 'fontFamily' => 'Georgia, Times New Roman, serif']),
                self::el(['type' => 'text', 'content' => 'Elegant serif typography', 'x' => 56, 'y' => 104, 'w' => 400, 'h' => 28, 'fontSize' => 13, 'color' => '#78716c', 'fontFamily' => 'Georgia, Times New Roman, serif']),
                self::el(['type' => 'divider', 'content' => '', 'x' => 56, 'y' => 140, 'w' => 120, 'h' => 1, 'backgroundColor' => '#d6d3d1', 'borderWidth' => 0]),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 56, 'y' => 170, 'w' => 500, 'h' => 280, 'fontSize' => 11, 'fontFamily' => 'Georgia, Times New Roman, serif', 'backgroundColor' => '#ffffff', 'borderWidth' => 1, 'borderColor' => '#e7e5e4']),
            ],
        ];
    }

    public static function creative(): array
    {
        return [
            'version' => 1,
            'meta' => self::baseMeta('ltr', '#faf5ff'),
            'elements' => [
                self::el(['type' => 'heading', 'content' => 'Invoice', 'x' => 36, 'y' => 36, 'w' => 280, 'h' => 52, 'fontSize' => 30, 'fontWeight' => '800', 'color' => '#6d28d9']),
                self::el(['type' => 'text', 'content' => 'Creative studio billing', 'x' => 36, 'y' => 92, 'w' => 380, 'h' => 28, 'fontSize' => 14, 'color' => '#7c3aed']),
                self::el(['type' => 'divider', 'content' => '', 'x' => 36, 'y' => 128, 'w' => 80, 'h' => 6, 'backgroundColor' => '#a855f7', 'borderRadius' => 4, 'borderWidth' => 0]),
                self::el(['type' => 'productsTable', 'content' => '', 'x' => 36, 'y' => 160, 'w' => 520, 'h' => 280, 'fontSize' => 11, 'backgroundColor' => '#ffffff', 'borderWidth' => 1, 'borderColor' => '#e9d5ff']),
                self::el(['type' => 'qr', 'content' => '', 'x' => 480, 'y' => 36, 'w' => 88, 'h' => 88, 'qrUrl' => 'https://']),
            ],
        ];
    }
}
