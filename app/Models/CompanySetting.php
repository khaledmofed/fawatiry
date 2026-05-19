<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $table = 'company_settings';

    protected $fillable = [
        'company_name',
        'logo_path',
        'address',
        'vat_number',
        'phone',
        'email',
        'signature_path',
        'stamp_path',
        'default_currency',
        'default_tax_rate',
        'next_invoice_number',
    ];

    protected function casts(): array
    {
        return [
            'default_tax_rate' => 'decimal:2',
            'next_invoice_number' => 'integer',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'company_name' => config('app.name'),
                'default_currency' => 'USD',
                'default_tax_rate' => 0,
                'next_invoice_number' => 1001,
            ]
        );
    }
}
