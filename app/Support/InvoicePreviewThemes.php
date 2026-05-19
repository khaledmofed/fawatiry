<?php

namespace App\Support;

/**
 * Tailwind class sets for invoice preview (A4). Each key is used by resources/views/invoices/preview/frame.blade.php
 */
class InvoicePreviewThemes
{
    /** @var array<string, array<string, string>> */
    private static array $themes = [];

    public static function has(string $slug): bool
    {
        return array_key_exists($slug, self::definitions());
    }

    /**
     * @return array<string, string>
     */
    public static function for(string $slug): array
    {
        $defs = self::definitions();

        return $defs[$slug] ?? $defs['clean-white'];
    }

    /**
     * Raw CSS inline-style values for DomPDF PDF rendering.
     * These bypass Tailwind compilation so dark themes render correctly.
     *
     * Keys: wrapStyle, cardStyle, thStyle, tdStyle, totalsStyle, grandStyle
     *
     * @return array<string, string>
     */
    public static function pdfColors(string $slug): array
    {
        // topBarStyle: applied to the top bar div for themes where the hero bar has a colored/dark background.
        // For simple themes without a hero bar, topBarStyle is '' (empty — just a bottom border applies via class).
        $map = [
            'clean-white' => [
                'wrapStyle'    => 'background:#ffffff; color:#0f172a;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#f8fafc; border:1px solid #e2e8f0;',
                'thStyle'      => 'background:#f8fafc; color:#475569; border-bottom:1px solid #e2e8f0;',
                'tdStyle'      => 'border-bottom:1px solid #f1f5f9;',
                'totalsStyle'  => 'background:#f8fafc; border:1px solid #e2e8f0;',
                'grandStyle'   => 'border-top:1px solid #e2e8f0; color:#0f172a;',
            ],
            'luxury-black-gold' => [
                'wrapStyle'    => 'background:#0c0c0c; color:#e5e5e5;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#111111; border:1px solid #2a2a2a;',
                'thStyle'      => 'background:#1a1a1a; color:#c9a84a; border-bottom:1px solid #333333;',
                'tdStyle'      => 'border-bottom:1px solid #2a2a2a;',
                'totalsStyle'  => 'background:#141414; border:1px solid #333333;',
                'grandStyle'   => 'border-top:1px solid #333333; color:#d4af37;',
            ],
            'corporate-blue' => [
                'wrapStyle'    => 'background:#ffffff; color:#0f172a;',
                'topBarStyle'  => 'background:#1e3a6e; color:#ffffff; border-radius:8px; padding:20px 24px; margin-bottom:24px;',
                'cardStyle'    => 'background:#f8fafc; border:1px solid #e2e8f0;',
                'thStyle'      => 'background:#1e3a6e; color:#ffffff; border-bottom:1px solid #1e3a6e;',
                'tdStyle'      => 'border-bottom:1px solid #f1f5f9;',
                'totalsStyle'  => 'background:#eff6ff; border:1px solid #bfdbfe;',
                'grandStyle'   => 'border-top:1px solid #bfdbfe; color:#1e3a6e;',
            ],
            'modern-minimal' => [
                'wrapStyle'    => 'background:#ffffff; color:#171717;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#f5f5f5; border:none;',
                'thStyle'      => 'background:transparent; color:#737373; border-bottom:1px solid #e5e5e5;',
                'tdStyle'      => 'border-bottom:1px solid #f5f5f5;',
                'totalsStyle'  => 'background:transparent; border:none;',
                'grandStyle'   => 'border-top:1px solid #e5e5e5; color:#171717;',
            ],
            'elegant-serif' => [
                'wrapStyle'    => 'background:#fffdf7; color:#1c1917;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#ffffff; border:1px solid #d6d3d1;',
                'thStyle'      => 'background:#f5f5f4; color:#57534e; border-bottom:1px solid #d6d3d1;',
                'tdStyle'      => 'border-bottom:1px solid #e7e5e4;',
                'totalsStyle'  => 'background:#ffffff; border:1px solid #d6d3d1;',
                'grandStyle'   => 'border-top:1px solid #d6d3d1; color:#1c1917;',
            ],
            'arabic-rtl-professional' => [
                'wrapStyle'    => 'background:#ffffff; color:#0f172a;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#f0fdf9; border:1px solid #e2e8f0;',
                'thStyle'      => 'background:#f0fdfa; color:#134e4a; border-bottom:1px solid #99f6e4;',
                'tdStyle'      => 'border-bottom:1px solid #f1f5f9;',
                'totalsStyle'  => 'background:#ffffff; border:1px solid #e2e8f0;',
                'grandStyle'   => 'border-top:1px solid #99f6e4; color:#134e4a;',
            ],
            'creative-agency' => [
                'wrapStyle'    => 'background:#ffffff; color:#2e1065;',
                'topBarStyle'  => 'background:#7c3aed; color:#ffffff; border-radius:10px; padding:20px 24px; margin-bottom:24px;',
                'cardStyle'    => 'background:#f5f3ff; border:1px solid #ddd6fe;',
                'thStyle'      => 'background:#7c3aed; color:#ffffff; border-bottom:1px solid #6d28d9;',
                'tdStyle'      => 'border-bottom:1px solid #f5f3ff;',
                'totalsStyle'  => 'background:#faf5ff; border:1px solid #ddd6fe;',
                'grandStyle'   => 'border-top:1px solid #ddd6fe; color:#4c1d95;',
            ],
            'dark-modern' => [
                'wrapStyle'    => 'background:#121212; color:#f5f5f5;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#1a1a1a; border:1px solid #262626;',
                'thStyle'      => 'background:#171717; color:#d4d4d4; border-bottom:1px solid #262626;',
                'tdStyle'      => 'border-bottom:1px solid #262626;',
                'totalsStyle'  => 'background:#171717; border:1px solid #262626;',
                'grandStyle'   => 'border-top:1px solid #404040; color:#ffffff;',
            ],
            'premium-gold' => [
                'wrapStyle'    => 'background:#1c1410; color:#f5e6c8;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#221c16; border:1px solid #3d3428;',
                'thStyle'      => 'background:#2a2218; color:#f0d78c; border-bottom:1px solid #3d3428;',
                'tdStyle'      => 'border-bottom:1px solid #2a241c;',
                'totalsStyle'  => 'background:#1a1510; border:1px solid #3d3428;',
                'grandStyle'   => 'border-top:1px solid #c9a22740; color:#f0d78c;',
            ],
            'soft-elegant' => [
                'wrapStyle'    => 'background:#ffffff; color:#1e293b;',
                'topBarStyle'  => '',
                'cardStyle'    => 'background:#fff1f2; border:1px solid #fecdd3;',
                'thStyle'      => 'background:#fff1f2; color:#881337; border-bottom:1px solid #fecdd3;',
                'tdStyle'      => 'border-bottom:1px solid #fff1f2;',
                'totalsStyle'  => 'background:#ffffff; border:1px solid #fecdd3;',
                'grandStyle'   => 'border-top:1px solid #fecdd3; color:#0f172a;',
            ],
        ];

        return $map[$slug] ?? $map['clean-white'];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private static function definitions(): array
    {
        if (self::$themes !== []) {
            return self::$themes;
        }

        $b = fn (string $page, string $card, string $accent, string $muted, string $tableHead, string $border) => compact(
            'page', 'card', 'accent', 'muted', 'tableHead', 'border'
        );

        self::$themes = [
            'clean-white' => $b(
                'bg-white text-slate-900 shadow-clay-card',
                'rounded-lg border border-slate-200/90 bg-white',
                'text-slate-900',
                'text-slate-500',
                'bg-slate-50 text-slate-600 uppercase tracking-wide text-[10px] font-semibold',
                'border-slate-200'
            ) + [
                'wrap' => 'bg-white text-slate-900',
                'topBar' => 'border-b border-slate-200 pb-6',
                'title' => 'text-3xl font-bold tracking-tight text-slate-900',
                'badgeWrap' => 'rounded-full px-3 py-1 text-xs font-semibold ring-1',
                'badgePaid' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
                'badgePending' => 'bg-amber-50 text-amber-800 ring-amber-200',
                'badgeOverdue' => 'bg-rose-50 text-rose-800 ring-rose-200',
                'badgeDraft' => 'bg-slate-100 text-slate-700 ring-slate-200',
                'badgeCancelled' => 'bg-slate-100 text-slate-500 ring-slate-200',
                'sectionTitle' => 'text-[11px] font-semibold uppercase tracking-wider text-slate-400',
                'clientCard' => 'rounded-xl border border-slate-200 bg-slate-50/80 p-4',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-slate-200 bg-slate-50 px-3 py-2.5 text-start font-semibold text-slate-600',
                'td' => 'border-b border-slate-100 px-3 py-2.5 align-middle',
                'input' => 'w-full rounded-md border-0 bg-transparent px-0 py-0.5 text-sm text-slate-900 focus:ring-2 focus:ring-slate-300/60',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-xl border border-slate-200 bg-slate-50/50 p-4 text-sm',
                'grand' => 'border-t border-slate-200 pt-2 text-base font-bold text-slate-900',
                'notes' => 'mt-8 rounded-xl border border-slate-200 bg-slate-50/40 p-4 text-sm text-slate-600',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-slate-200 pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-slate-400 pt-2 text-center text-xs text-slate-500',
                'footer' => 'mt-10 border-t border-slate-200 pt-4 text-center text-[11px] text-slate-400',
            ],

            'luxury-black-gold' => $b(
                'bg-[#0c0c0c] text-neutral-200 shadow-[0_25px_80px_rgba(0,0,0,0.55)]',
                'rounded-sm border border-[#2a2a2a] bg-[#111]',
                'text-[#d4af37]',
                'text-neutral-400',
                'bg-[#1a1a1a] text-[#d4af37]/90 uppercase tracking-[0.2em] text-[10px] font-semibold',
                'border-[#333]'
            ) + [
                'wrap' => 'bg-[#0c0c0c] text-neutral-200',
                'topBar' => 'border-b border-[#2a2a2a] pb-6',
                'title' => 'text-3xl font-light tracking-[0.12em] text-[#d4af37]',
                'badgeWrap' => 'rounded-sm px-3 py-1 text-xs font-medium ring-1 ring-[#d4af37]/40',
                'badgePaid' => 'bg-[#14532d]/40 text-emerald-300',
                'badgePending' => 'bg-[#713f12]/40 text-amber-200',
                'badgeOverdue' => 'bg-[#7f1d1d]/40 text-rose-200',
                'badgeDraft' => 'bg-neutral-800 text-neutral-300',
                'badgeCancelled' => 'bg-neutral-800 text-neutral-500',
                'sectionTitle' => 'text-[10px] font-semibold uppercase tracking-[0.25em] text-[#d4af37]/60',
                'clientCard' => 'rounded-sm border border-[#333] bg-[#141414] p-4',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-[#333] bg-[#1a1a1a] px-3 py-2.5 text-start font-semibold text-[#d4af37]/80',
                'td' => 'border-b border-[#2a2a2a] px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent px-0 py-0.5 text-sm text-neutral-100 focus:ring-2 focus:ring-[#d4af37]/30',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-sm border border-[#333] bg-[#141414] p-4 text-sm',
                'grand' => 'border-t border-[#333] pt-2 text-base font-semibold text-[#d4af37]',
                'notes' => 'mt-8 rounded-sm border border-[#333] bg-[#141414] p-4 text-sm text-neutral-400',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-[#333] pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-[#d4af37]/50 pt-2 text-center text-xs text-neutral-500',
                'footer' => 'mt-10 border-t border-[#2a2a2a] pt-4 text-center text-[11px] text-neutral-500',
            ],

            'corporate-blue' => $b(
                'bg-slate-50 text-slate-900 shadow-clay-card',
                'rounded-lg border border-slate-200 bg-white overflow-hidden',
                'text-blue-700',
                'text-slate-500',
                'bg-blue-900 text-white uppercase tracking-wide text-[10px] font-semibold',
                'border-slate-200'
            ) + [
                'wrap' => 'bg-white text-slate-900',
                'topBar' => 'rounded-t-lg bg-gradient-to-r from-blue-900 to-blue-800 px-6 py-5 text-white mb-6',
                'title' => 'text-2xl font-bold tracking-tight text-white',
                'badgeWrap' => 'rounded-full px-3 py-1 text-xs font-semibold bg-white/15 ring-1 ring-white/30',
                'badgePaid' => 'text-emerald-200',
                'badgePending' => 'text-amber-200',
                'badgeOverdue' => 'text-rose-200',
                'badgeDraft' => 'text-blue-100',
                'badgeCancelled' => 'text-slate-300',
                'sectionTitle' => 'text-[11px] font-semibold uppercase tracking-wider text-blue-600/80',
                'clientCard' => 'rounded-lg border border-slate-200 bg-slate-50 p-4',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-blue-900/20 bg-blue-900 px-3 py-2.5 text-start text-white font-semibold',
                'td' => 'border-b border-slate-100 px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent px-0 py-0.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-300/60',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-lg border border-slate-200 bg-blue-50/40 p-4 text-sm',
                'grand' => 'border-t border-blue-200 pt-2 text-base font-bold text-blue-900',
                'notes' => 'mt-8 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-slate-200 pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-slate-400 pt-2 text-center text-xs text-slate-500',
                'footer' => 'mt-10 border-t border-slate-200 pt-4 text-center text-[11px] text-slate-400',
            ],

            'modern-minimal' => $b(
                'bg-[#fafafa] text-neutral-900 shadow-clay-soft',
                'rounded-2xl border border-neutral-200/80 bg-white',
                'text-neutral-900',
                'text-neutral-500',
                'text-[10px] font-semibold uppercase tracking-widest text-neutral-400',
                'border-neutral-200'
            ) + [
                'wrap' => 'bg-white text-neutral-900',
                'topBar' => 'border-b border-neutral-100 pb-8',
                'title' => 'text-4xl font-semibold tracking-tight',
                'badgeWrap' => 'rounded-full px-3 py-1 text-xs font-medium bg-neutral-100 text-neutral-700',
                'badgePaid' => 'bg-emerald-50 text-emerald-800',
                'badgePending' => 'bg-amber-50 text-amber-800',
                'badgeOverdue' => 'bg-rose-50 text-rose-800',
                'badgeDraft' => 'bg-neutral-100 text-neutral-600',
                'badgeCancelled' => 'bg-neutral-100 text-neutral-500',
                'sectionTitle' => 'text-[10px] font-semibold uppercase tracking-[0.2em] text-neutral-400',
                'clientCard' => 'rounded-2xl bg-neutral-50 p-5',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-neutral-200 px-0 py-3 text-start font-medium text-neutral-500',
                'td' => 'border-b border-neutral-100 px-0 py-3 align-middle',
                'input' => 'w-full border-0 bg-transparent px-0 py-0.5 text-sm focus:ring-0 focus:outline-none',
                'totalsBox' => 'mt-8 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 text-sm text-neutral-600',
                'grand' => 'border-t border-neutral-200 pt-3 text-lg font-semibold text-neutral-900',
                'notes' => 'mt-10 text-sm leading-relaxed text-neutral-500',
                'signature' => 'mt-14 flex flex-wrap items-end justify-between gap-8',
                'sigLine' => 'mt-10 w-48 border-t border-neutral-300 pt-2 text-center text-xs text-neutral-400',
                'footer' => 'mt-12 text-center text-[11px] text-neutral-400',
            ],

            'elegant-serif' => $b(
                'bg-[#fffdf7] text-stone-900 shadow-clay-card',
                'rounded-sm border border-stone-200 bg-[#fffdf7]',
                'text-stone-800',
                'text-stone-500',
                'bg-stone-100 text-stone-600 font-serif uppercase text-[10px] tracking-widest',
                'border-stone-200'
            ) + [
                'wrap' => 'bg-[#fffdf7] text-stone-900 font-serif',
                'topBar' => 'border-b border-stone-300 pb-6',
                'title' => 'text-4xl font-normal tracking-tight',
                'badgeWrap' => 'rounded-sm px-3 py-1 text-xs border border-stone-300',
                'badgePaid' => 'text-emerald-800 bg-emerald-50/80',
                'badgePending' => 'text-amber-900 bg-amber-50/80',
                'badgeOverdue' => 'text-rose-900 bg-rose-50/80',
                'badgeDraft' => 'text-stone-600',
                'badgeCancelled' => 'text-stone-500',
                'sectionTitle' => 'text-xs font-semibold uppercase tracking-widest text-stone-400',
                'clientCard' => 'border border-stone-200 bg-white/60 p-4',
                'table' => 'w-full text-sm border-collapse font-serif',
                'th' => 'border-b border-stone-300 px-3 py-2.5 text-start font-semibold',
                'td' => 'border-b border-stone-200/80 px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent font-serif text-sm focus:ring-1 focus:ring-stone-300',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 border border-stone-200 bg-white/50 p-4 text-sm',
                'grand' => 'border-t border-stone-300 pt-2 text-lg font-semibold',
                'notes' => 'mt-8 border-s-4 border-stone-300 ps-4 text-sm italic text-stone-600',
                'signature' => 'mt-10 flex flex-wrap justify-between gap-8 border-t border-stone-300 pt-8',
                'sigLine' => 'mt-8 w-56 border-t border-stone-500 pt-2 text-center text-xs text-stone-500',
                'footer' => 'mt-8 text-center text-[11px] text-stone-400',
            ],

            'arabic-rtl-professional' => $b(
                'bg-white text-slate-900 shadow-clay-card',
                'rounded-lg border border-slate-200 bg-white',
                'text-teal-700',
                'text-slate-500',
                'bg-teal-800 text-white text-[11px] font-semibold',
                'border-slate-200'
            ) + [
                'wrap' => 'bg-white text-slate-900',
                'topBar' => 'border-b-2 border-teal-700 pb-5',
                'title' => 'text-3xl font-bold text-teal-800',
                'badgeWrap' => 'rounded-md px-3 py-1 text-xs font-semibold ring-1 ring-teal-200',
                'badgePaid' => 'bg-emerald-50 text-emerald-800',
                'badgePending' => 'bg-amber-50 text-amber-800',
                'badgeOverdue' => 'bg-rose-50 text-rose-800',
                'badgeDraft' => 'bg-slate-100 text-slate-700',
                'badgeCancelled' => 'bg-slate-100 text-slate-500',
                'sectionTitle' => 'text-xs font-bold text-teal-700/90',
                'clientCard' => 'rounded-lg border border-slate-200 bg-teal-50/30 p-4',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-teal-100 bg-teal-50 px-3 py-2.5 text-start font-semibold text-teal-900',
                'td' => 'border-b border-slate-100 px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent text-sm focus:ring-2 focus:ring-teal-200',
                'totalsBox' => 'mt-6 flex justify-start',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-lg border border-slate-200 p-4 text-sm',
                'grand' => 'border-t border-teal-200 pt-2 text-base font-bold text-teal-900',
                'notes' => 'mt-8 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-slate-200 pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-teal-600/40 pt-2 text-center text-xs text-slate-500',
                'footer' => 'mt-10 text-center text-[11px] text-slate-400',
            ],

            'creative-agency' => $b(
                'bg-gradient-to-br from-violet-50 to-fuchsia-50 text-violet-950 shadow-clay-card',
                'rounded-2xl border border-violet-200/80 bg-white/90 backdrop-blur-sm',
                'text-violet-700',
                'text-violet-600/70',
                'bg-gradient-to-r from-violet-600 to-fuchsia-600 text-white text-[10px] font-bold uppercase tracking-wide',
                'border-violet-100'
            ) + [
                'wrap' => 'bg-white/95 text-violet-950',
                'topBar' => 'relative overflow-hidden rounded-xl bg-gradient-to-r from-violet-600 via-fuchsia-600 to-pink-500 px-6 py-5 text-white mb-6',
                'title' => 'text-3xl font-black tracking-tight text-white',
                'badgeWrap' => 'rounded-full bg-white/20 px-3 py-1 text-xs font-semibold backdrop-blur',
                'badgePaid' => 'text-emerald-100',
                'badgePending' => 'text-amber-100',
                'badgeOverdue' => 'text-rose-100',
                'badgeDraft' => 'text-violet-100',
                'badgeCancelled' => 'text-white/70',
                'sectionTitle' => 'text-[11px] font-bold uppercase tracking-wider text-fuchsia-600',
                'clientCard' => 'rounded-xl border border-violet-100 bg-violet-50/40 p-4',
                'table' => 'w-full text-sm border-collapse overflow-hidden rounded-xl',
                'th' => 'px-3 py-2.5 text-start font-bold',
                'td' => 'border-b border-violet-50 px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent text-sm focus:ring-2 focus:ring-violet-300 rounded',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-xl border border-violet-100 bg-gradient-to-br from-white to-violet-50/50 p-4 text-sm',
                'grand' => 'border-t border-violet-200 pt-2 text-base font-black text-violet-900',
                'notes' => 'mt-8 rounded-xl border border-violet-100 bg-white/70 p-4 text-sm text-violet-800/80',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8',
                'sigLine' => 'mt-8 w-52 border-t-2 border-fuchsia-400 pt-2 text-center text-xs text-violet-500',
                'footer' => 'mt-8 text-center text-[11px] text-violet-400',
            ],

            'dark-modern' => $b(
                'bg-[#121212] text-neutral-100 shadow-[0_20px_60px_rgba(0,0,0,0.45)]',
                'rounded-xl border border-neutral-800 bg-[#1a1a1a]',
                'text-sky-400',
                'text-neutral-500',
                'bg-neutral-900 text-neutral-300 text-[10px] font-semibold uppercase tracking-wider',
                'border-neutral-800'
            ) + [
                'wrap' => 'bg-[#121212] text-neutral-100',
                'topBar' => 'border-b border-neutral-800 pb-6',
                'title' => 'text-3xl font-bold tracking-tight text-white',
                'badgeWrap' => 'rounded-lg bg-neutral-800 px-3 py-1 text-xs font-medium text-neutral-200',
                'badgePaid' => 'text-emerald-300',
                'badgePending' => 'text-amber-300',
                'badgeOverdue' => 'text-rose-300',
                'badgeDraft' => 'text-neutral-400',
                'badgeCancelled' => 'text-neutral-500',
                'sectionTitle' => 'text-[10px] font-semibold uppercase tracking-widest text-neutral-500',
                'clientCard' => 'rounded-lg border border-neutral-800 bg-neutral-900/50 p-4',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-neutral-800 bg-neutral-900 px-3 py-2.5 text-start',
                'td' => 'border-b border-neutral-800/80 px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent text-neutral-100 focus:ring-1 focus:ring-sky-500/50',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-lg border border-neutral-800 bg-neutral-900/40 p-4 text-sm',
                'grand' => 'border-t border-neutral-700 pt-2 text-base font-bold text-white',
                'notes' => 'mt-8 rounded-lg border border-neutral-800 bg-neutral-900/30 p-4 text-sm text-neutral-400',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-neutral-800 pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-neutral-600 pt-2 text-center text-xs text-neutral-500',
                'footer' => 'mt-10 border-t border-neutral-800 pt-4 text-center text-[11px] text-neutral-500',
            ],

            'premium-gold' => $b(
                'bg-gradient-to-b from-[#1c1410] to-[#0f0c0a] text-[#f5e6c8] shadow-2xl',
                'rounded-lg border border-[#c9a227]/30 bg-[#181210]/95',
                'text-[#e8c547]',
                'text-[#a89b8a]',
                'bg-[#2a2218] text-[#f0d78c] text-[10px] font-semibold uppercase tracking-[0.15em]',
                'border-[#3d3428]'
            ) + [
                'wrap' => 'text-[#f5e6c8]',
                'topBar' => 'border-b border-[#c9a227]/25 pb-6',
                'title' => 'text-3xl font-serif font-semibold text-[#f0d78c]',
                'badgeWrap' => 'rounded-sm border border-[#c9a227]/40 px-3 py-1 text-xs',
                'badgePaid' => 'text-emerald-300',
                'badgePending' => 'text-amber-200',
                'badgeOverdue' => 'text-rose-300',
                'badgeDraft' => 'text-[#c9a227]/70',
                'badgeCancelled' => 'text-[#6a5f52]',
                'sectionTitle' => 'text-[10px] font-semibold uppercase tracking-widest text-[#c9a227]/60',
                'clientCard' => 'rounded-md border border-[#3d3428] bg-[#221c16] p-4',
                'table' => 'w-full text-sm border-collapse font-serif',
                'th' => 'border-b border-[#3d3428] px-3 py-2.5 text-start text-[#f0d78c]/90',
                'td' => 'border-b border-[#2a241c] px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent text-[#f5e6c8] focus:ring-1 focus:ring-[#c9a227]/40',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-md border border-[#3d3428] bg-[#1a1510] p-4 text-sm',
                'grand' => 'border-t border-[#c9a227]/30 pt-2 text-lg font-semibold text-[#f0d78c]',
                'notes' => 'mt-8 rounded-md border border-[#3d3428] bg-[#221c16] p-4 text-sm text-[#a89b8a]',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-[#3d3428] pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-[#c9a227]/40 pt-2 text-center text-xs text-[#8a7d6c]',
                'footer' => 'mt-10 text-center text-[11px] text-[#6a5f52]',
            ],

            'soft-elegant' => $b(
                'bg-gradient-to-br from-rose-50 via-white to-sky-50 text-slate-800 shadow-clay-card',
                'rounded-2xl border border-rose-100/80 bg-white/90',
                'text-rose-600',
                'text-slate-500',
                'bg-rose-50 text-rose-900/80 text-[10px] font-semibold uppercase tracking-wider',
                'border-rose-100/60'
            ) + [
                'wrap' => 'bg-white/95 text-slate-800',
                'topBar' => 'border-b border-rose-100 pb-6',
                'title' => 'text-3xl font-semibold tracking-tight text-slate-900',
                'badgeWrap' => 'rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-800 ring-1 ring-rose-100',
                'badgePaid' => 'bg-emerald-50 text-emerald-800 ring-emerald-100',
                'badgePending' => 'bg-amber-50 text-amber-800',
                'badgeOverdue' => 'bg-rose-100 text-rose-900',
                'badgeDraft' => 'bg-slate-100 text-slate-700',
                'badgeCancelled' => 'bg-slate-100 text-slate-500',
                'sectionTitle' => 'text-[11px] font-semibold uppercase tracking-wider text-rose-400',
                'clientCard' => 'rounded-xl border border-rose-100 bg-gradient-to-br from-rose-50/50 to-white p-4',
                'table' => 'w-full text-sm border-collapse',
                'th' => 'border-b border-rose-100 bg-rose-50/60 px-3 py-2.5 text-start font-semibold text-rose-900/80',
                'td' => 'border-b border-rose-50 px-3 py-2.5 align-middle',
                'input' => 'w-full border-0 bg-transparent focus:ring-2 focus:ring-rose-100 rounded-md text-sm',
                'totalsBox' => 'mt-6 flex justify-end',
                'totalsInner' => 'w-full max-w-xs space-y-2 rounded-xl border border-rose-100 bg-white/80 p-4 text-sm',
                'grand' => 'border-t border-rose-100 pt-2 text-base font-bold text-slate-900',
                'notes' => 'mt-8 rounded-xl border border-rose-100 bg-rose-50/30 p-4 text-sm text-slate-600',
                'signature' => 'mt-10 flex flex-wrap items-end justify-between gap-8 border-t border-rose-100 pt-8',
                'sigLine' => 'mt-8 w-52 border-t border-rose-200 pt-2 text-center text-xs text-slate-500',
                'footer' => 'mt-10 text-center text-[11px] text-slate-400',
            ],
        ];

        return self::$themes;
    }
}
