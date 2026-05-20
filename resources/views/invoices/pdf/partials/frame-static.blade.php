@php
    /** @var array<string, string> $t */
    $t = $theme;
    $heroDark = in_array($themeSlug ?? '', ['corporate-blue', 'creative-agency', 'luxury-black-gold', 'dark-modern', 'premium-gold'], true);
    $pdfColors = \App\Support\InvoicePreviewThemes::pdfColors($themeSlug ?? 'clean-white');
    $custom = is_array($document['custom'] ?? null) ? $document['custom'] : [];
    $rtl = $invoice->direction === 'rtl';
    $cellLabel = $rtl ? 'right' : 'left';
    $cellValue = $rtl ? 'left' : 'right';
    $outerEnd = $rtl ? 'left' : 'right';

    // Logo scale: replicate the editor's logoImageStyle() using dimension scaling (DomPDF has no transform support)
    $logoScale = min(2.0, max(0.3, (float)($document['logo']['scale'] ?? 1)));
    $logoMaxPx = (int) round(88 * $logoScale);
    $logoOffsetX = max(-24, min(24, (float)($document['logo']['offset_x'] ?? 0)));
    $logoOffsetY = max(-24, min(24, (float)($document['logo']['offset_y'] ?? 0)));
    $logoPadL  = $logoOffsetX > 0 ? (int)$logoOffsetX : 0;
    $logoPadR  = $logoOffsetX < 0 ? (int)abs($logoOffsetX) : 0;
    $logoPadT  = $logoOffsetY > 0 ? (int)$logoOffsetY : 0;
    $logoPadB  = $logoOffsetY < 0 ? (int)abs($logoOffsetY) : 0;
@endphp

{{-- DomPDF: avoid responsive-only Tailwind (lg:/sm:) and fragile flex; use tables + explicit alignment. --}}
<div class="{{ $t['wrap'] }}" dir="{{ $invoice->direction }}" style="{{ $pdfColors['wrapStyle'] }}; padding:14px 26px 10px;">

    {{-- ═══════════════════════════════════════ TOP BAR ═══════════════════════════════════════ --}}
    @php $hasHeroBar = !empty($pdfColors['topBarStyle']); @endphp
    <div class="{{ $t['topBar'] }}" @if($hasHeroBar) style="{{ $pdfColors['topBarStyle'] }}" @endif>
        <table width="100%" style="border-collapse:collapse;">
            <tr>
                <td style="width:{{ $logoMaxPx + 20 }}px; vertical-align:top; padding:{{ $logoPadT }}px {{ $logoPadR + 12 }}px {{ $logoPadB }}px {{ $logoPadL }}px; overflow:visible;">
                    @if(!empty($logoDataUri))
                        <img
                            src="{{ $logoDataUri }}"
                            alt=""
                            style="display:block; max-width:{{ $logoMaxPx }}px; max-height:{{ $logoMaxPx }}px; width:auto; height:auto; object-fit:contain;"
                        />
                    @endif
                </td>
                <td style="vertical-align:top; padding:4px 8px 4px 0;">
                    <p class="mb-1 text-[11px] font-semibold uppercase tracking-wider" style="{{ $hasHeroBar ? 'opacity:0.7; color:inherit;' : '' }} {{ $heroDark && !$hasHeroBar ? '' : '' }}">{{ __('Invoice') }}</p>
                    <h1 class="{{ $t['title'] }}" @if($hasHeroBar) style="color:inherit;" @endif>{{ __('Invoice') }}</h1>
                </td>
                <td style="vertical-align:top; text-align:{{ $outerEnd }}; white-space:nowrap; padding:4px 0 4px 8px; {{ $hasHeroBar ? 'color:inherit;' : '' }}">
                    <p class="text-sm" style="opacity:0.8;">#<span class="font-mono font-semibold">{{ e($invoice->invoice_number) }}</span></p>
                    <div style="margin-top:8px;">
                        <span class="pdf-badge {{ $statusBadgeClasses }}" @if($hasHeroBar) style="border-color:rgba(255,255,255,0.5); color:#ffffff;" @endif>{{ $statusLabel }}</span>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════ TWO COLUMNS — matches editor layout exactly ════════════════════ --}}
    {{-- LEFT : From (company) + Invoice details                                                  --}}
    {{-- RIGHT: Bill to (client)                                                                  --}}
    <table width="100%" style="border-collapse:collapse; margin-top:16px;">
        <tr>
            {{-- LEFT COLUMN --}}
            <td style="width:50%; vertical-align:top; padding:0 14px 0 0;">
                {{-- From --}}
                <div>
                    <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('From') }}</p>
                    <div class="min-w-0 space-y-1 text-sm">
                        <p class="text-base font-bold leading-snug">{{ \App\Support\InvoicePdfAssets::pdfPlain($company['name'] ?? '') ?: '—' }}</p>
                        <p class="whitespace-pre-line opacity-90">{{ \App\Support\InvoicePdfAssets::pdfPlain($company['address'] ?? '') ?: '—' }}</p>
                        @if(!empty($company['phone']))
                            <p class="opacity-90">{{ \App\Support\InvoicePdfAssets::pdfPlain($company['phone']) }}</p>
                        @endif
                        @if(!empty($company['email']))
                            <p class="opacity-90">{{ \App\Support\InvoicePdfAssets::pdfPlain($company['email']) }}</p>
                        @endif
                        @if(!empty($company['vat_number']))
                            <p class="text-xs opacity-75">{{ __('VAT') }}: {{ \App\Support\InvoicePdfAssets::pdfPlain($company['vat_number']) }}</p>
                        @endif
                    </div>
                </div>

                {{-- Invoice details — same position as in the editor (left column, below From) --}}
                <div style="margin-top:12px;">
                    <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Invoice details') }}</p>
                    <table width="100%" style="border-collapse:collapse; font-size:0.875rem;">
                        <tr>
                            <td style="padding:5px 0; border-bottom:1px solid rgba(0,0,0,0.08); vertical-align:middle; opacity:0.75; width:25%;">{{ __('Issue date') }}</td>
                            <td style="padding:5px 0; border-bottom:1px solid rgba(0,0,0,0.08); vertical-align:middle; font-weight:500; width:25%;">{{ $invoice->invoice_date?->format('Y-m-d') ?? '—' }}</td>
                            <td style="padding:5px 0 5px 12px; border-bottom:1px solid rgba(0,0,0,0.08); vertical-align:middle; opacity:0.75; width:25%;">{{ __('Due date') }}</td>
                            <td style="padding:5px 0; border-bottom:1px solid rgba(0,0,0,0.08); vertical-align:middle; font-weight:500; width:25%;">{{ $invoice->due_date?->format('Y-m-d') ?? '—' }}</td>
                        </tr>
                    </table>
                </div>
            </td>

            {{-- RIGHT COLUMN — Bill to (client card) --}}
            <td style="width:50%; vertical-align:top; padding:0 0 0 14px;">
                <div class="pdf-client-card {{ $t['clientCard'] }}" style="{{ $pdfColors['cardStyle'] }}; border-radius:10px; padding:14px;">
                <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Bill to') }}</p>
                @if($invoice->client)
                    <div class="space-y-2 text-sm">
                        <p class="font-semibold">{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->client->name) }}</p>
                        @if($invoice->client->company)
                            <p>{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->client->company) }}</p>
                        @endif
                        @if($invoice->client->address)
                            <p class="whitespace-pre-line">{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->client->address) }}</p>
                        @endif
                        @if($invoice->client->email)
                            <p>{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->client->email) }}</p>
                        @endif
                        @if($invoice->client->phone)
                            <p>{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->client->phone) }}</p>
                        @endif
                        @if(!empty($invoice->client->vat_number))
                            <p class="text-xs opacity-75">{{ __('VAT') }}: {{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->client->vat_number) }}</p>
                        @endif
                    </div>
                @else
                    <p class="text-sm" style="opacity:0.7;">{{ __('No client assigned') }}</p>
                @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- ═══════════════════════════════════ LINE ITEMS TABLE ══════════════════════════════════ --}}
    <div style="margin-top:16px;">
        <table width="100%" style="border-collapse:collapse;">
            <thead>
                <tr>
                    <th class="pdf-th-cell {{ $t['th'] }}" style="{{ $pdfColors['thStyle'] }}; width:46%; text-align:{{ $cellLabel }}; padding:6px 10px; font-weight:600; font-size:0.78rem;">{{ __('Item') }}</th>
                    <th class="pdf-th-cell {{ $t['th'] }}" style="{{ $pdfColors['thStyle'] }}; width:18%; text-align:{{ $cellValue }}; padding:6px 10px; font-weight:600; font-size:0.78rem;">{{ __('Qty') }}</th>
                    <th class="pdf-th-cell {{ $t['th'] }}" style="{{ $pdfColors['thStyle'] }}; width:18%; text-align:{{ $cellValue }}; padding:6px 10px; font-weight:600; font-size:0.78rem;">{{ __('Price') }}</th>
                    <th class="pdf-th-cell {{ $t['th'] }}" style="{{ $pdfColors['thStyle'] }}; width:18%; text-align:{{ $cellValue }}; padding:6px 10px; font-weight:600; font-size:0.78rem;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $item)
                    <tr>
                        <td class="pdf-td-cell {{ $t['td'] }}" style="{{ $pdfColors['tdStyle'] }}; padding:6px 10px; vertical-align:middle;">
                            <span style="font-weight:500;">{!! \App\Support\InvoicePdfAssets::pdfPlain($item->name) !!}</span>
                            @if(!empty($item->description))
                                <div style="font-size:0.72rem; opacity:0.6; margin-top:2px; line-height:1.35;">{!! \App\Support\InvoicePdfAssets::pdfPlain($item->description) !!}</div>
                            @endif
                        </td>
                        <td class="pdf-td-cell {{ $t['td'] }}" style="{{ $pdfColors['tdStyle'] }}; padding:6px 10px; vertical-align:middle; text-align:{{ $cellValue }}; font-variant-numeric:tabular-nums;">{{ e(number_format((float) $item->quantity, 2, '.', '')) }}</td>
                        <td class="pdf-td-cell {{ $t['td'] }}" style="{{ $pdfColors['tdStyle'] }}; padding:6px 10px; vertical-align:middle; text-align:{{ $cellValue }}; font-variant-numeric:tabular-nums;">{{ e(number_format((float) $item->unit_price, 2, '.', '')) }}</td>
                        <td class="pdf-td-cell {{ $t['td'] }}" style="{{ $pdfColors['tdStyle'] }}; padding:6px 10px; vertical-align:middle; text-align:{{ $cellValue }}; font-weight:600; font-variant-numeric:tabular-nums;">{{ e(number_format((float) $item->line_total, 2, '.', '')) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td class="pdf-td-cell {{ $t['td'] }}" colspan="4" style="{{ $pdfColors['tdStyle'] }}; padding:6px 10px; text-align:center; opacity:0.5;">{{ __('No line items') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ════════════════════════════════════════ TOTALS ════════════════════════════════════════ --}}
    {{-- Two-column table pushes the totals box to the end (right for LTR, left for RTL).       --}}
    {{-- Avoids text-align:right + inline-block, which DomPDF misrenders with w-full classes.   --}}
    <table width="100%" style="border-collapse:collapse; margin-top:14px; page-break-inside:avoid;">
        <tr>
            @if(!$rtl)<td style="vertical-align:top;"></td>@endif
            <td style="width:300px; vertical-align:top;">
                <div style="{{ $pdfColors['totalsStyle'] }}; border-radius:10px; padding:12px; box-sizing:border-box;">
                    <table width="100%" style="border-collapse:collapse; font-size:0.875rem;">
                        <tr>
                            <td style="padding:5px 0; width:55%; opacity:0.7; text-align:{{ $cellLabel }}; vertical-align:middle;">{{ __('Subtotal') }}</td>
                            <td style="padding:5px 0; width:45%; text-align:{{ $cellValue }}; vertical-align:middle; font-variant-numeric:tabular-nums; font-weight:600;">{{ e(number_format((float) $invoice->subtotal, 2, '.', '')) }}</td>
                        </tr>
                        <tr>
                            <td style="padding:5px 0; opacity:0.7; text-align:{{ $cellLabel }}; vertical-align:middle;">{{ __('Tax') }}</td>
                            <td style="padding:5px 0; text-align:{{ $cellValue }}; vertical-align:middle; font-variant-numeric:tabular-nums; font-weight:600;">{{ e(number_format((float) $invoice->tax_total, 2, '.', '')) }}</td>
                        </tr>
                        @if((float)$invoice->discount_total > 0)
                        <tr>
                            <td style="padding:5px 0; opacity:0.7; text-align:{{ $cellLabel }}; vertical-align:middle;">{{ __('Discount') }}</td>
                            <td style="padding:5px 0; text-align:{{ $cellValue }}; vertical-align:middle; font-variant-numeric:tabular-nums; font-weight:600;">{{ e(number_format((float) $invoice->discount_total, 2, '.', '')) }}</td>
                        </tr>
                        @endif
                        @if((float)$invoice->shipping_total > 0)
                        <tr>
                            <td style="padding:5px 0; opacity:0.7; text-align:{{ $cellLabel }}; vertical-align:middle;">{{ __('Shipping') }}</td>
                            <td style="padding:5px 0; text-align:{{ $cellValue }}; vertical-align:middle; font-variant-numeric:tabular-nums; font-weight:600;">{{ e(number_format((float) $invoice->shipping_total, 2, '.', '')) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="2" style="padding:0;"><div style="{{ $pdfColors['grandStyle'] }}; margin:4px 0; border-bottom:none; border-left:none; border-right:none;"></div></td>
                        </tr>
                        <tr>
                            <td style="{{ $pdfColors['grandStyle'] }}; border:none; padding:6px 0 2px; text-align:{{ $cellLabel }}; font-weight:700; font-size:1rem; vertical-align:middle;">{{ __('Total') }}</td>
                            <td style="{{ $pdfColors['grandStyle'] }}; border:none; padding:6px 0 2px; text-align:{{ $cellValue }}; font-weight:700; font-size:1rem; vertical-align:middle; font-variant-numeric:tabular-nums;">{{ e($invoice->currency) }} {{ e(number_format((float) $invoice->total, 2, '.', '')) }}</td>
                        </tr>
                    </table>
                </div>
            </td>
            @if($rtl)<td style="vertical-align:top;"></td>@endif
        </tr>
    </table>

    {{-- ══════════════════════════════════ NOTES & TERMS ══════════════════════════════════════ --}}
    @if($invoice->notes)
        <div class="{{ $t['notes'] }}" style="margin-top:12px;">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Notes') }}</p>
            <div class="whitespace-pre-line text-sm">{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->notes) }}</div>
        </div>
    @endif

    @if($invoice->terms)
        <div style="margin-top:12px;">
            <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Terms') }}</p>
            <div class="whitespace-pre-line text-sm" style="padding:4px 0;">{{ \App\Support\InvoicePdfAssets::pdfPlain($invoice->terms) }}</div>
        </div>
    @endif

    {{-- ════════════════════════════════ SIGNATURE & THANK YOU ════════════════════════════════ --}}
    <table width="100%" style="border-collapse:collapse; margin-top:16px; border-top:1px solid rgba(0,0,0,0.12); page-break-inside:avoid;">
        <tr>
            <td style="width:50%; vertical-align:top; padding:12px 14px 6px 0;">
                <p class="{{ $t['sectionTitle'] }} mb-2">{{ __('Thank you') }}</p>
                <p class="text-sm whitespace-pre-line" style="margin:0; padding:4px 0 0; line-height:1.5;">{{ \App\Support\InvoicePdfAssets::pdfPlain($custom['thank_you'] ?? '') }}</p>
            </td>
            <td style="width:50%; vertical-align:top; padding:12px 0 6px 14px; text-align:{{ $outerEnd }};">
                <p class="{{ $t['sectionTitle'] }} mb-2" style="text-align:{{ $outerEnd }};">{{ __('Signature') }}</p>
                <p class="text-sm" style="text-align:{{ $outerEnd }}; margin:0 0 8px;">{{ \App\Support\InvoicePdfAssets::pdfPlain($custom['signature_label'] ?? '') }}</p>
                @if($rtl)
                    <div class="{{ $t['sigLine'] }}" style="max-width:220px; margin-right:0; margin-left:auto;">{{ __('Sign here') }}</div>
                @else
                    <div class="{{ $t['sigLine'] }}" style="max-width:220px; margin-left:auto; margin-right:0;">{{ __('Sign here') }}</div>
                @endif
            </td>
        </tr>
    </table>

    {{-- ══════════════════════════════════════ FOOTER ══════════════════════════════════════════ --}}
    <div class="{{ $t['footer'] }}" style="margin-top:10px;">
        <p class="mx-auto w-full max-w-xl text-center text-[11px] whitespace-pre-line">{{ \App\Support\InvoicePdfAssets::pdfPlain($custom['legal_footer'] ?? '') }}</p>
    </div>
</div>
