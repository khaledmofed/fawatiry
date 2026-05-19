<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InvoiceTemplate extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'thumbnail_path',
        'preview_path',
        'layout_json',
        'direction',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'layout_json' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'invoice_template_id');
    }
}
