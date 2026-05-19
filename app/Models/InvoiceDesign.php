<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceDesign extends Model
{
    protected $fillable = [
        'invoice_id',
        'document_json',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'document_json' => 'array',
            'version' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
