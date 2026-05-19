<?php

namespace App\Support;

use Illuminate\Support\Str;

class InvoiceDocument
{
    public static function blank(string $direction = 'ltr'): array
    {
        return [
            'version' => 1,
            'meta' => [
                'widthMm' => 210,
                'heightMm' => 297,
                'zoom' => 1,
                'grid' => 8,
                'snap' => true,
                'direction' => $direction,
            ],
            'elements' => [
                self::headingElement(__('Invoice'), 24, 24, 320, 48, 32),
                self::textElement(__('Thank you for your business.'), 24, 260, 400, 36, 12),
            ],
        ];
    }

    public static function headingElement(string $content, float $x, float $y, float $w, float $h, int $fontSize = 24): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => 'heading',
            'content' => $content,
            'x' => $x,
            'y' => $y,
            'w' => $w,
            'h' => $h,
            'rotation' => 0,
            'fontSize' => $fontSize,
            'fontFamily' => 'ui-sans-serif, system-ui, sans-serif',
            'fontWeight' => '700',
            'color' => '#111827',
            'textAlign' => 'left',
            'backgroundColor' => 'transparent',
            'borderWidth' => 0,
            'borderColor' => '#000000',
            'borderRadius' => 0,
            'opacity' => 1,
            'padding' => 4,
            'margin' => 0,
        ];
    }

    public static function textElement(string $content, float $x, float $y, float $w, float $h, int $fontSize = 12): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => 'text',
            'content' => $content,
            'x' => $x,
            'y' => $y,
            'w' => $w,
            'h' => $h,
            'rotation' => 0,
            'fontSize' => $fontSize,
            'fontFamily' => 'ui-sans-serif, system-ui, sans-serif',
            'fontWeight' => '400',
            'color' => '#374151',
            'textAlign' => 'left',
            'backgroundColor' => 'transparent',
            'borderWidth' => 0,
            'borderColor' => '#000000',
            'borderRadius' => 0,
            'opacity' => 1,
            'padding' => 4,
            'margin' => 0,
        ];
    }

    public static function productsTableElement(float $x, float $y, float $w, float $h): array
    {
        return [
            'id' => (string) Str::uuid(),
            'type' => 'productsTable',
            'content' => '',
            'x' => $x,
            'y' => $y,
            'w' => $w,
            'h' => $h,
            'rotation' => 0,
            'fontSize' => 11,
            'fontFamily' => 'ui-sans-serif, system-ui, sans-serif',
            'fontWeight' => '400',
            'color' => '#111827',
            'textAlign' => 'left',
            'backgroundColor' => '#ffffff',
            'borderWidth' => 1,
            'borderColor' => '#e5e7eb',
            'borderRadius' => 4,
            'opacity' => 1,
            'padding' => 8,
            'margin' => 0,
        ];
    }
}
