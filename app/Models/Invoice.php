<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'status',
        'invoice_date',
        'due_date',
        'currency',
        'notes',
        'terms',
        'tax_total',
        'discount_total',
        'shipping_total',
        'subtotal',
        'total',
        'client_id',
        'invoice_template_id',
        'direction',
        'company_snapshot',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'due_date' => 'date',
            'tax_total' => 'decimal:2',
            'discount_total' => 'decimal:2',
            'shipping_total' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total' => 'decimal:2',
            'company_snapshot' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Invoice $invoice): void {
            if ($invoice->status === InvoiceStatus::Pending->value
                && $invoice->due_date
                && $invoice->due_date->isPast()) {
                $invoice->status = InvoiceStatus::Overdue->value;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(InvoiceTemplate::class, 'invoice_template_id');
    }

    public function design(): HasOne
    {
        return $this->hasOne(InvoiceDesign::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)->orderBy('sort_order');
    }

    public function statusEnum(): InvoiceStatus
    {
        return InvoiceStatus::from($this->status);
    }
}
