<?php

declare(strict_types=1);

namespace App\Enums;

enum BudgetType: string
{
    case Fixed = 'fixed';
    case Range = 'range';
    case Negotiable = 'negotiable';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Фиксированный',
            self::Range => 'Диапазон',
            self::Negotiable => 'Договорная',
        };
    }
}
