<!DOCTYPE html>
<html lang="{{ $invoice->direction === 'rtl' ? 'ar' : 'en' }}" dir="{{ $invoice->direction }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page { margin: 8mm 12mm; size: A4 portrait; }
        html, body { margin: 0; padding: 0; }

        /*
         * "traditional arabic" is registered via php artisan pdf:register-arabic-font.
         * It covers Arabic Presentation Forms-B (U+FE70-U+FEFF) which our
         * ArabicPdfReshaper outputs, making Arabic script render connected.
         * DejaVu Sans is the Latin/fallback font.
         */
        body, .pdf-root {
            font-family: "Inter", "traditional arabic", DejaVu Sans, sans-serif !important;
        }

        /* Badge pill — DomPDF doesn't apply box-shadow (ring), so we use outline border instead */
        .pdf-badge {
            display: inline-block;
            border-radius: 9999px;
            padding: 3px 10px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid currentColor;
        }

        /* Ensure opacity shorthand works for section labels */
        .pdf-muted { opacity: 0.65; }

        /*
         * pdf-* classes provide structural/fallback styles for elements that
         * DomPDF doesn't render well from Tailwind alone.
         * Background and border colors are intentionally omitted here so that
         * the theme's Tailwind classes (injected via $embeddedCss) can override them
         * correctly for both light and dark themes.
         */

        /* Client card — structural fallback; theme class overrides bg/border */
        .pdf-client-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
        }

        /* Table header cell — structural fallback; theme class overrides bg/color */
        .pdf-th-cell {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 10px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Table data cell */
        .pdf-td-cell {
            border-bottom: 1px solid #f1f5f9;
            padding: 8px 10px;
            vertical-align: middle;
        }

        /* Totals box — structural fallback; theme class overrides bg/border */
        .pdf-totals-box {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 14px;
        }

        /* Grand total row */
        .pdf-grand {
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
            font-weight: 700;
            font-size: 1rem;
        }
    </style>
    @if(!empty($embeddedCss))
        <style>{!! $embeddedCss !!}</style>
    @endif
</head>
<body>
<div class="pdf-root">
    <div class="relative mx-auto min-h-[297mm] w-[210mm] overflow-visible rounded-sm bg-white shadow-2xl ring-1 ring-black/5">
        <div class="relative z-0 min-h-[297mm] overflow-hidden rounded-sm">
            @include('invoices.pdf.partials.frame-static', [
                'invoice'            => $invoice,
                'theme'              => $theme,
                'themeSlug'          => $themeSlug,
                'document'           => $document,
                'logoDataUri'        => $logoDataUri,
                'company'            => $company,
                'statusBadgeClasses' => $statusBadgeClasses,
                'statusLabel'        => $statusLabel,
            ])
        </div>
    </div>
</div>

{{--
    Stamp overlays — DomPDF CPDF backend does not support position:absolute with
    percentage-based top/left on min-height containers.  Use position:fixed instead,
    which is relative to the page viewport (after @page margins).

    A4 minus 12mm margins each side = 186mm × 273mm content area.
    Stamps are stored as % of the 210mm × 297mm editor canvas, so we scale them
    to the PDF content area for accurate placement.
--}}
@php
    $contentW = 186; // mm  (210 - 2×12)
    $contentH = 273; // mm  (297 - 2×12)
@endphp

@foreach($stampLayers as $layer)
    @php
        $leftMm  = round(($layer['left_pct']  / 100) * $contentW, 2);
        $topMm   = round(($layer['top_pct']   / 100) * $contentH, 2);
        $widthMm = round(($layer['width_pct'] / 100) * $contentW, 2);
    @endphp
    <img
        src="{{ $layer['dataUri'] }}"
        alt=""
        style="position:fixed; left:{{ $leftMm }}mm; top:{{ $topMm }}mm; width:{{ $widthMm }}mm; height:auto; z-index:50;"
    />
@endforeach

</body>
</html>
