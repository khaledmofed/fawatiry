<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case Paid = 'paid';
    case Cancelled = 'cancelled';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Draft => __('Draft'),
            self::Pending => __('Pending'),
            self::Paid => __('Paid'),
            self::Cancelled => __('Cancelled'),
            self::Overdue => __('Overdue'),
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
