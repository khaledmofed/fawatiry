<?php

namespace Database\Seeders;

use App\Support\InvoiceLayoutDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceTemplateSeeder extends Seeder
{
    /**
     * @return array<string, mixed>
     */
    private static function document(string $slug, string $direction): array
    {
        return [
            'version' => 2,
            'meta' => [
                'direction' => $direction,
                'zoom' => 1,
            ],
            'custom' => InvoiceLayoutDocument::defaultCustom($slug),
        ];
    }

    public function run(): void
    {
        $rows = [
            ['slug' => 'luxury-black-gold', 'name' => 'Luxury black & gold', 'thumbnail_path' => 'img/templates/luxury.svg', 'direction' => 'ltr'],
            ['slug' => 'corporate-blue', 'name' => 'Corporate blue', 'thumbnail_path' => 'img/templates/corporate.svg', 'direction' => 'ltr'],
            ['slug' => 'modern-minimal', 'name' => 'Modern minimal', 'thumbnail_path' => 'img/templates/modern.svg', 'direction' => 'ltr'],
            ['slug' => 'elegant-serif', 'name' => 'Elegant serif', 'thumbnail_path' => 'img/templates/elegant.svg', 'direction' => 'ltr'],
            ['slug' => 'arabic-rtl-professional', 'name' => 'Arabic RTL professional', 'thumbnail_path' => 'img/templates/arabic.svg', 'direction' => 'rtl'],
            ['slug' => 'creative-agency', 'name' => 'Creative agency', 'thumbnail_path' => 'img/templates/creative.svg', 'direction' => 'ltr'],
            ['slug' => 'clean-white', 'name' => 'Clean white', 'thumbnail_path' => 'img/templates/minimal.svg', 'direction' => 'ltr'],
            ['slug' => 'dark-modern', 'name' => 'Dark modern', 'thumbnail_path' => 'img/templates/luxury.svg', 'direction' => 'ltr'],
            ['slug' => 'premium-gold', 'name' => 'Premium gold', 'thumbnail_path' => 'img/templates/luxury.svg', 'direction' => 'ltr'],
            ['slug' => 'soft-elegant', 'name' => 'Soft elegant', 'thumbnail_path' => 'img/templates/elegant.svg', 'direction' => 'ltr'],
        ];

        foreach ($rows as $row) {
            $layout = self::document(
                InvoiceLayoutDocument::resolvePreviewSlug($row['slug']),
                $row['direction']
            );

            DB::table('invoice_templates')->updateOrInsert(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'thumbnail_path' => $row['thumbnail_path'],
                    'preview_path' => null,
                    'layout_json' => json_encode($layout),
                    'direction' => $row['direction'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
