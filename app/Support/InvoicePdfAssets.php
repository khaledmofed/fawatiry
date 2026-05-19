<?php

namespace App\Support;

/**
 * Vite-built Tailwind for embedding into DomPDF (same utilities as the invoice editor).
 */
class InvoicePdfAssets
{
    public static function compiledAppCss(): string
    {
        $manifestPath = public_path('build/manifest.json');
        if (! is_file($manifestPath)) {
            return '';
        }

        /** @var array<string, mixed> $manifest */
        $manifest = json_decode((string) file_get_contents($manifestPath), true);
        if (! is_array($manifest)) {
            return '';
        }

        $entry = $manifest['resources/css/app.css'] ?? null;
        if (! is_array($entry) || empty($entry['file']) || ! is_string($entry['file'])) {
            return '';
        }

        $cssPath = public_path('build/'.$entry['file']);
        if (! is_file($cssPath)) {
            return '';
        }

        return (string) file_get_contents($cssPath);
    }

    /**
     * @param  array<string, string>  $theme
     */
    /**
     * Decode HTML entities, strip tags, reshape Arabic text for DomPDF, then escape.
     *
     * DomPDF's CPDF backend cannot apply OpenType GSUB shaping tables, so Arabic
     * characters must be converted to their contextual presentation forms before
     * the HTML is handed to DomPDF.
     */
    public static function pdfPlain(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $clean = strip_tags($decoded);

        if (ArabicPdfReshaper::isArabic($clean)) {
            $clean = ArabicPdfReshaper::reshape($clean);
        }

        return e($clean);
    }

    public static function statusBadgeClasses(array $theme, string $status): string
    {
        $key = match ($status) {
            'paid' => 'badgePaid',
            'pending' => 'badgePending',
            'overdue' => 'badgeOverdue',
            'cancelled' => 'badgeCancelled',
            default => 'badgeDraft',
        };

        $wrap = $theme['badgeWrap'] ?? '';
        $tone = $theme[$key] ?? '';

        return trim($wrap.' '.$tone);
    }
}
