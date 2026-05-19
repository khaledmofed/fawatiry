<?php

namespace App\Support;

/**
 * Converts Arabic text stored in Unicode logical order into presentation-form
 * code points so that DomPDF (CPDF backend) can render connected Arabic script.
 *
 * DomPDF's PDF renderer does not apply OpenType GSUB tables, so each character
 * is drawn in its isolated Unicode form.  This class substitutes every Arabic
 * letter with the correct contextual variant (initial / medial / final / isolated)
 * from the Arabic Presentation Forms-A/B Unicode blocks (U+FE70–U+FEFF,
 * U+FB50–U+FDFF), matching what a browser's shaping engine would produce.
 *
 * Handles:
 *   - All common Arabic letters (U+0621–U+064A)
 *   - Lam-Alef mandatory ligatures (ﻻ ﻵ ﻷ ﻹ)
 *   - Diacritics / harakat (transparent — skip during neighbor lookup)
 *   - Non-Arabic characters pass through unchanged
 */
class ArabicPdfReshaper
{
    // [isolated, final, initial, medial]  — null = form unavailable (right-joining)
    private const FORMS = [
        0x0621 => [0xFE80, null,   null,   null  ], // ء
        0x0622 => [0xFE81, 0xFE82, null,   null  ], // آ  alef madda
        0x0623 => [0xFE83, 0xFE84, null,   null  ], // أ
        0x0624 => [0xFE85, 0xFE86, null,   null  ], // ؤ
        0x0625 => [0xFE87, 0xFE88, null,   null  ], // إ
        0x0626 => [0xFE89, 0xFE8A, 0xFE8B, 0xFE8C], // ئ
        0x0627 => [0xFE8D, 0xFE8E, null,   null  ], // ا  alef
        0x0628 => [0xFE8F, 0xFE90, 0xFE91, 0xFE92], // ب
        0x0629 => [0xFE93, 0xFE94, null,   null  ], // ة
        0x062A => [0xFE95, 0xFE96, 0xFE97, 0xFE98], // ت
        0x062B => [0xFE99, 0xFE9A, 0xFE9B, 0xFE9C], // ث
        0x062C => [0xFE9D, 0xFE9E, 0xFE9F, 0xFEA0], // ج
        0x062D => [0xFEA1, 0xFEA2, 0xFEA3, 0xFEA4], // ح
        0x062E => [0xFEA5, 0xFEA6, 0xFEA7, 0xFEA8], // خ
        0x062F => [0xFEA9, 0xFEAA, null,   null  ], // د
        0x0630 => [0xFEAB, 0xFEAC, null,   null  ], // ذ
        0x0631 => [0xFEAD, 0xFEAE, null,   null  ], // ر
        0x0632 => [0xFEAF, 0xFEB0, null,   null  ], // ز
        0x0633 => [0xFEB1, 0xFEB2, 0xFEB3, 0xFEB4], // س
        0x0634 => [0xFEB5, 0xFEB6, 0xFEB7, 0xFEB8], // ش
        0x0635 => [0xFEB9, 0xFEBA, 0xFEBB, 0xFEBC], // ص
        0x0636 => [0xFEBD, 0xFEBE, 0xFEBF, 0xFEC0], // ض
        0x0637 => [0xFEC1, 0xFEC2, 0xFEC3, 0xFEC4], // ط
        0x0638 => [0xFEC5, 0xFEC6, 0xFEC7, 0xFEC8], // ظ
        0x0639 => [0xFEC9, 0xFECA, 0xFECB, 0xFECC], // ع
        0x063A => [0xFECD, 0xFECE, 0xFECF, 0xFED0], // غ
        0x0640 => [0x0640, 0x0640, 0x0640, 0x0640], // tatweel (kashida) — always same
        0x0641 => [0xFED1, 0xFED2, 0xFED3, 0xFED4], // ف
        0x0642 => [0xFED5, 0xFED6, 0xFED7, 0xFED8], // ق
        0x0643 => [0xFED9, 0xFEDA, 0xFEDB, 0xFEDC], // ك
        0x0644 => [0xFEDD, 0xFEDE, 0xFEDF, 0xFEE0], // ل  — handled separately for lam-alef
        0x0645 => [0xFEE1, 0xFEE2, 0xFEE3, 0xFEE4], // م
        0x0646 => [0xFEE5, 0xFEE6, 0xFEE7, 0xFEE8], // ن
        0x0647 => [0xFEE9, 0xFEEA, 0xFEEB, 0xFEEC], // ه
        0x0648 => [0xFEED, 0xFEEE, null,   null  ], // و
        0x0649 => [0xFEEF, 0xFEF0, null,   null  ], // ى
        0x064A => [0xFEF1, 0xFEF2, 0xFEF3, 0xFEF4], // ي
    ];

    // Dual-joining: connect on both sides
    private const DUAL = [
        0x0626, 0x0628, 0x062A, 0x062B, 0x062C, 0x062D, 0x062E,
        0x0633, 0x0634, 0x0635, 0x0636, 0x0637, 0x0638, 0x0639,
        0x063A, 0x0640, 0x0641, 0x0642, 0x0643, 0x0644, 0x0645,
        0x0646, 0x0647, 0x064A,
    ];

    // Right-joining: connect only to right visual neighbor (= previous in logical order)
    private const RIGHT = [
        0x0621, 0x0622, 0x0623, 0x0624, 0x0625, 0x0627, 0x0629,
        0x062F, 0x0630, 0x0631, 0x0632, 0x0648, 0x0649,
    ];

    // Arabic diacritics (harakat) — transparent, ignored for connection logic
    private const DIACRITICS = [
        0x064B, 0x064C, 0x064D, 0x064E, 0x064F, 0x0650, 0x0651, 0x0652,
        0x0653, 0x0654, 0x0655, 0x0656, 0x0657, 0x0658, 0x0659, 0x065A,
        0x065B, 0x065C, 0x065D, 0x065E, 0x065F,
        0x0670, // alef wasl
    ];

    // Lam-Alef ligatures: [alef_cp => [isolated_ligature, final_ligature]]
    private const LAM_ALEF = [
        0x0622 => [0xFEF5, 0xFEF6],
        0x0623 => [0xFEF7, 0xFEF8],
        0x0625 => [0xFEF9, 0xFEFA],
        0x0627 => [0xFEFB, 0xFEFC],
    ];

    public static function isArabic(string $text): bool
    {
        foreach (self::codePoints($text) as $cp) {
            if ($cp >= 0x0600 && $cp <= 0x06FF) {
                return true;
            }
        }

        return false;
    }

    public static function reshape(string $text): string
    {
        $points = self::codePoints($text);
        $n = count($points);
        $out = [];
        $i = 0;

        while ($i < $n) {
            $cp = $points[$i];

            // Non-Arabic or non-shaped character — pass through
            if (! isset(self::FORMS[$cp])) {
                $out[] = $cp;
                $i++;
                continue;
            }

            // Diacritics are transparent — copy as-is
            if (in_array($cp, self::DIACRITICS, true)) {
                $out[] = $cp;
                $i++;
                continue;
            }

            // --- Lam-Alef mandatory ligature ---
            if ($cp === 0x0644) { // lam
                // Skip any diacritics between lam and a potential alef
                $j = $i + 1;
                while ($j < $n && in_array($points[$j], self::DIACRITICS, true)) {
                    $j++;
                }

                if ($j < $n && isset(self::LAM_ALEF[$points[$j]])) {
                    $alefCp = $points[$j];
                    $ligatures = self::LAM_ALEF[$alefCp];

                    // Is lam preceded by a dual-joining char?
                    $prevCp = self::nonDiacriticBefore($points, $i);
                    $isFinal = $prevCp !== null && in_array($prevCp, self::DUAL, true);

                    // Output any diacritics between lam and alef
                    for ($k = $i + 1; $k < $j; $k++) {
                        $out[] = $points[$k];
                    }

                    $out[] = $isFinal ? $ligatures[1] : $ligatures[0];
                    $i = $j + 1; // consume lam + (diacritics) + alef
                    continue;
                }
            }

            // --- General contextual form ---
            $forms = self::FORMS[$cp];
            $isDual = in_array($cp, self::DUAL, true);

            $prevCp = self::nonDiacriticBefore($points, $i);
            $nextCp = self::nonDiacriticAfter($points, $i);

            // Can this char connect backward (to right visual neighbor = prev in memory)?
            $connectsPrev = $prevCp !== null && in_array($prevCp, self::DUAL, true);

            // Can this char connect forward (to left visual neighbor = next in memory)?
            // Only dual-joining chars have a left connection point.
            $connectsNext = $isDual
                && $nextCp !== null
                && (in_array($nextCp, self::DUAL, true) || in_array($nextCp, self::RIGHT, true));

            if ($connectsPrev && $connectsNext && $forms[3] !== null) {
                $out[] = $forms[3]; // medial
            } elseif ($connectsPrev && $forms[1] !== null) {
                $out[] = $forms[1]; // final
            } elseif ($connectsNext && $forms[2] !== null) {
                $out[] = $forms[2]; // initial
            } else {
                $out[] = $forms[0]; // isolated
            }

            $i++;
        }

        return self::fromCodePoints($out);
    }

    /** Find the nearest non-diacritic code point before index $i, or null. */
    private static function nonDiacriticBefore(array $points, int $i): ?int
    {
        for ($k = $i - 1; $k >= 0; $k--) {
            if (! in_array($points[$k], self::DIACRITICS, true)) {
                return $points[$k];
            }
        }

        return null;
    }

    /** Find the nearest non-diacritic code point after index $i, or null. */
    private static function nonDiacriticAfter(array $points, int $i): ?int
    {
        $n = count($points);
        for ($k = $i + 1; $k < $n; $k++) {
            if (! in_array($points[$k], self::DIACRITICS, true)) {
                return $points[$k];
            }
        }

        return null;
    }

    /** @return int[] */
    private static function codePoints(string $utf8): array
    {
        $pts = [];
        $len = strlen($utf8);
        for ($i = 0; $i < $len;) {
            $b = ord($utf8[$i]);
            if ($b < 0x80) {
                $pts[] = $b;
                $i++;
            } elseif ($b < 0xE0) {
                $pts[] = (($b & 0x1F) << 6) | (ord($utf8[$i + 1]) & 0x3F);
                $i += 2;
            } elseif ($b < 0xF0) {
                $pts[] = (($b & 0x0F) << 12) | ((ord($utf8[$i + 1]) & 0x3F) << 6) | (ord($utf8[$i + 2]) & 0x3F);
                $i += 3;
            } else {
                $pts[] = (($b & 0x07) << 18) | ((ord($utf8[$i + 1]) & 0x3F) << 12) | ((ord($utf8[$i + 2]) & 0x3F) << 6) | (ord($utf8[$i + 3]) & 0x3F);
                $i += 4;
            }
        }

        return $pts;
    }

    /** @param int[] $codePoints */
    private static function fromCodePoints(array $codePoints): string
    {
        $s = '';
        foreach ($codePoints as $cp) {
            if ($cp < 0x80) {
                $s .= chr($cp);
            } elseif ($cp < 0x800) {
                $s .= chr(0xC0 | ($cp >> 6)).chr(0x80 | ($cp & 0x3F));
            } elseif ($cp < 0x10000) {
                $s .= chr(0xE0 | ($cp >> 12)).chr(0x80 | (($cp >> 6) & 0x3F)).chr(0x80 | ($cp & 0x3F));
            } else {
                $s .= chr(0xF0 | ($cp >> 18)).chr(0x80 | (($cp >> 12) & 0x3F)).chr(0x80 | (($cp >> 6) & 0x3F)).chr(0x80 | ($cp & 0x3F));
            }
        }

        return $s;
    }
}
