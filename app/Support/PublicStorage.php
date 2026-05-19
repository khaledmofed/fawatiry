<?php

namespace App\Support;

/**
 * Host-relative URLs for public disk files (avoids APP_URL vs 127.0.0.1 mismatch).
 */
class PublicStorage
{
    public static function url(?string $relativePath): string
    {
        if (! $relativePath) {
            return '';
        }
        if (str_starts_with($relativePath, 'http://') || str_starts_with($relativePath, 'https://')) {
            return $relativePath;
        }

        return '/storage/'.ltrim($relativePath, '/');
    }
}
