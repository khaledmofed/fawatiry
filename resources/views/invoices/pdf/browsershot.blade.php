<!DOCTYPE html>
<html lang="{{ $invoice->direction === 'rtl' ? 'ar' : 'en' }}" dir="{{ $invoice->direction }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 8mm 10mm;
            size: A4 portrait;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 210mm;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* Inter font embedded from @fontsource/inter (woff — supported by Chromium) */
        @font-face {
            font-family: 'Inter';
            font-weight: 400;
            font-style: normal;
            src: url('data:font/woff;base64,{{ $fontRegularB64 }}') format('woff');
        }
        @font-face {
            font-family: 'Inter';
            font-weight: 600;
            font-style: normal;
            src: url('data:font/woff;base64,{{ $fontSemiBoldB64 }}') format('woff');
        }
        @font-face {
            font-family: 'Inter';
            font-weight: 700;
            font-style: normal;
            src: url('data:font/woff;base64,{{ $fontBoldB64 }}') format('woff');
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
        }
    </style>
    @if(!empty($embeddedCss))
        <style>{!! $embeddedCss !!}</style>
    @endif
</head>
<body>
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

    {{--
        Stamps — Chromium supports position:fixed (page viewport) and transform:rotate().
        Coordinates are stored as % of the 210mm × 297mm editor canvas; we scale them
        to the PDF content area (210 - 2×12 = 186mm wide, 297 - 2×10 = 277mm tall).
    --}}
    @php
        $contentW = 186;
        $contentH = 277;
    @endphp

    @foreach($stampLayers as $layer)
        @php
            $leftMm   = round(($layer['left_pct']  / 100) * $contentW, 2);
            $topMm    = round(($layer['top_pct']   / 100) * $contentH, 2);
            $widthMm  = round(($layer['width_pct'] / 100) * $contentW, 2);
            $rotation = (float)($layer['rotation'] ?? 0);
        @endphp
        <img
            src="{{ $layer['dataUri'] }}"
            alt=""
            style="position:fixed; left:{{ $leftMm }}mm; top:{{ $topMm }}mm; width:{{ $widthMm }}mm; height:auto; z-index:50; transform:rotate({{ $rotation }}deg); transform-origin:center center;"
        />
    @endforeach
</body>
</html>
