@php
    /** @var array<string, string> $theme */
    $t   = $theme;
    $dir = $direction ?? 'ltr';
    $rtl = $dir === 'rtl';
    $slug = $slug ?? 'clean-white';

    $hasHeroBar = in_array($slug, ['corporate-blue', 'creative-agency'], true);
    $isDark     = in_array($slug, ['luxury-black-gold', 'dark-modern', 'premium-gold'], true);

    // Inline colour map so dark / special themes always render correctly
    // regardless of Tailwind JIT compilation.
    $colors = \App\Support\InvoicePreviewThemes::pdfColors($slug);
    $wrapBg    = $colors['wrapStyle'];
    $heroBg    = $colors['topBarStyle'];
    $cardBg    = $colors['cardStyle'];
    $thStyle   = $colors['thStyle'];
    $totBg     = $colors['totalsStyle'];
    $grandSt   = $colors['grandStyle'];

    $rowBorder   = $isDark ? 'border-bottom:1px solid rgba(255,255,255,0.06);' : 'border-bottom:1px solid rgba(0,0,0,0.05);';
    $dividerClr  = $isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.07)';
    $mutedOpacity = 0.6;
@endphp
<div class="flex h-full w-full items-center justify-center overflow-hidden px-1 py-2" dir="{{ $dir }}">
    {{-- A4 card: 210 × 297 scaled to fill the card preview area --}}
    <div
        class="relative overflow-hidden rounded-md shadow-lg ring-1 ring-black/10"
        style="width:210px; height:297px; transform:scale(1.0); transform-origin:top center; {{ $wrapBg }}"
    >

        {{-- ── HEADER ─────────────────────────────────────────── --}}
        @if($hasHeroBar)
            {{-- Hero bar (corporate-blue / creative-agency) --}}
            <div style="{{ $heroBg }}; padding:7px 9px 6px; margin-bottom:0; border-radius:0;">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div style="display:flex; gap:5px; align-items:center;">
                        <div style="width:18px; height:18px; background:rgba(255,255,255,0.25); border-radius:3px; flex-shrink:0;"></div>
                        <div>
                            <div style="font-size:5px; font-weight:700; opacity:0.75; text-transform:uppercase; letter-spacing:0.08em; color:white;">INVOICE</div>
                            <div style="font-size:10px; font-weight:800; color:white; line-height:1.1; letter-spacing:-0.01em;">Invoice</div>
                        </div>
                    </div>
                    <div style="text-align:{{ $rtl ? 'left' : 'right' }};">
                        <div style="font-size:6px; font-weight:600; color:rgba(255,255,255,0.8);">#1001</div>
                        <div style="font-size:5px; background:rgba(255,255,255,0.2); border-radius:20px; padding:1px 5px; margin-top:2px; color:white; display:inline-block;">Paid</div>
                    </div>
                </div>
            </div>
        @else
            {{-- Standard header --}}
            <div style="padding:7px 9px 5px; border-bottom:1px solid {{ $dividerClr }}; display:flex; justify-content:space-between; align-items:flex-start;">
                <div style="display:flex; gap:5px; align-items:center;">
                    <div style="width:18px; height:18px; background:rgba(128,128,128,0.12); border:1px solid rgba(0,0,0,0.09); border-radius:3px; flex-shrink:0;"></div>
                    <div>
                        <div class="{{ $t['sectionTitle'] }}" style="font-size:5px; margin-bottom:1px;">INVOICE</div>
                        <div class="{{ $t['title'] }}" style="font-size:10px; line-height:1.1;">Invoice</div>
                    </div>
                </div>
                <div style="text-align:{{ $rtl ? 'left' : 'right' }};">
                    <div style="font-size:6px; opacity:0.65; font-weight:600;">#1001</div>
                    <div class="{{ $t['badgeWrap'] }} {{ $t['badgePaid'] }}" style="font-size:5px; padding:1px 5px; display:inline-block; margin-top:2px; border-radius:20px;">Paid</div>
                </div>
            </div>
        @endif

        {{-- ── FROM + BILL TO ──────────────────────────────────── --}}
        <div style="padding:5px 9px; display:flex; gap:5px; {{ $rtl ? 'flex-direction:row-reverse;' : '' }}">
            {{-- FROM --}}
            <div style="flex:1; min-width:0;">
                <div class="{{ $t['sectionTitle'] }}" style="font-size:4.5px; margin-bottom:2px; letter-spacing:0.08em;">FROM</div>
                <div style="font-size:6.5px; font-weight:700; line-height:1.2;">Acme Invoicing</div>
                <div style="font-size:5px; line-height:1.4; opacity:{{ $mutedOpacity }};">
                    123 Business Rd<br>
                    London<br>
                    +44 20 0000 0000
                </div>
            </div>
            {{-- BILL TO --}}
            <div style="flex:1; min-width:0; {{ $cardBg }}; border-radius:4px; padding:4px 5px;">
                <div class="{{ $t['sectionTitle'] }}" style="font-size:4.5px; margin-bottom:2px; letter-spacing:0.08em;">BILL TO</div>
                <div style="font-size:6.5px; font-weight:700; line-height:1.2;">Jane Client</div>
                <div style="font-size:5px; line-height:1.4; opacity:{{ $mutedOpacity }};">
                    Client Co.<br>
                    1 Client Street<br>
                    jane@client.test
                </div>
            </div>
        </div>

        {{-- ── INVOICE DETAILS ─────────────────────────────────── --}}
        <div style="padding:3px 9px 4px; border-top:1px solid {{ $dividerClr }}; border-bottom:1px solid {{ $dividerClr }};">
            <div class="{{ $t['sectionTitle'] }}" style="font-size:4.5px; margin-bottom:2px; letter-spacing:0.08em;">INVOICE DETAILS</div>
            <div style="display:flex; justify-content:space-between; font-size:5px; opacity:0.75; margin-bottom:1px;">
                <span>Issue date</span><span style="font-weight:600;">2026-05-19</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:5px; opacity:0.75; margin-bottom:1px;">
                <span>Due date</span><span style="font-weight:600;">2026-06-02</span>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:5px; opacity:0.75;">
                <span>Currency</span><span style="font-weight:600;">USD</span>
            </div>
        </div>

        {{-- ── ITEMS TABLE ──────────────────────────────────────── --}}
        <div style="margin:4px 9px 0; border:1px solid {{ $dividerClr }}; border-radius:3px; overflow:hidden;">
            <table style="width:100%; border-collapse:collapse; font-size:5px; table-layout:fixed;">
                <thead>
                    <tr>
                        <th style="{{ $thStyle }}; padding:3px 4px; text-align:{{ $rtl ? 'right' : 'left' }}; font-size:5px; width:auto;">Item</th>
                        <th style="{{ $thStyle }}; padding:3px 2px; text-align:right; width:18px; font-size:5px;">Qty</th>
                        <th style="{{ $thStyle }}; padding:3px 2px; text-align:right; width:26px; font-size:5px;">Price</th>
                        <th style="{{ $thStyle }}; padding:3px 4px; text-align:right; width:26px; font-size:5px;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="{{ $rowBorder }}; padding:3px 4px;">Professional services</td>
                        <td style="{{ $rowBorder }}; padding:3px 2px; text-align:right;">1</td>
                        <td style="{{ $rowBorder }}; padding:3px 2px; text-align:right;">1200</td>
                        <td style="{{ $rowBorder }}; padding:3px 4px; text-align:right; font-weight:600;">1380</td>
                    </tr>
                    <tr>
                        <td style="{{ $rowBorder }}; padding:3px 4px;">Platform license</td>
                        <td style="{{ $rowBorder }}; padding:3px 2px; text-align:right;">1</td>
                        <td style="{{ $rowBorder }}; padding:3px 2px; text-align:right;">2400</td>
                        <td style="{{ $rowBorder }}; padding:3px 4px; text-align:right; font-weight:600;">2645</td>
                    </tr>
                    <tr>
                        <td style="padding:3px 4px;">Consulting</td>
                        <td style="padding:3px 2px; text-align:right;">2</td>
                        <td style="padding:3px 2px; text-align:right;">150</td>
                        <td style="padding:3px 4px; text-align:right; font-weight:600;">360</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- ── TOTALS ───────────────────────────────────────────── --}}
        <div style="padding:3px 9px 0; display:flex; justify-content:{{ $rtl ? 'flex-start' : 'flex-end' }};">
            <div style="{{ $totBg }}; border-radius:4px; padding:4px 6px; width:95px; font-size:5px;">
                <div style="display:flex; justify-content:space-between; opacity:0.7; margin-bottom:1.5px;">
                    <span>Subtotal</span><span>4700</span>
                </div>
                <div style="display:flex; justify-content:space-between; opacity:0.7; margin-bottom:1.5px;">
                    <span>Tax</span><span>705</span>
                </div>
                <div style="display:flex; justify-content:space-between; opacity:0.7; margin-bottom:1.5px;">
                    <span>Discount</span><span>100</span>
                </div>
                <div style="{{ $grandSt }}; display:flex; justify-content:space-between; font-size:6px; font-weight:700; padding-top:2px; margin-top:1px;">
                    <span>Total</span><span>5305</span>
                </div>
            </div>
        </div>

        {{-- ── THANK YOU + SIGNATURE ────────────────────────────── --}}
        <div style="padding:4px 9px 0; display:flex; justify-content:space-between; align-items:flex-end; border-top:1px solid {{ $dividerClr }}; margin-top:5px;">
            <div>
                <div class="{{ $t['sectionTitle'] }}" style="font-size:4.5px; margin-bottom:2px;">THANK YOU</div>
                <div style="font-size:5px; opacity:0.65;">Thank you for your business.</div>
            </div>
            <div style="text-align:{{ $rtl ? 'left' : 'right' }};">
                <div class="{{ $t['sectionTitle'] }}" style="font-size:4.5px; margin-bottom:2px;">SIGNATURE</div>
                <div style="font-size:5px; opacity:0.65; font-style:italic;">Authorized signature</div>
                <div style="margin-top:3px; width:45px; border-top:1px solid {{ $dividerClr }}; {{ $rtl ? 'margin-right:auto;' : 'margin-left:auto;' }}"></div>
            </div>
        </div>

        {{-- ── FOOTER ──────────────────────────────────────────── --}}
        <div style="padding:4px 9px 0; margin-top:4px; border-top:1px solid {{ $dividerClr }}; font-size:4.5px; text-align:center; opacity:0.45;">
            Payment is due within the agreed terms. Please include the invoice number on your payment.
        </div>

    </div>
</div>
