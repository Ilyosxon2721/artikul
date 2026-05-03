<?php

declare(strict_types=1);

namespace App\Enums;

enum DisputeStatus: string
{
    case Open = 'open';
    case InReview = 'in_review';
    case ResolvedBuyer = 'resolved_buyer';
    case ResolvedSeller = 'resolved_seller';
    case ResolvedPartial = 'resolved_partial';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Открыт',
            self::InReview => 'На рассмотрении',
            self::ResolvedBuyer => 'В пользу заказчика',
            self::ResolvedSeller => 'В пользу исполнителя',
            self::ResolvedPartial => 'Частичное решение',
            self::Cancelled => 'Отменён',
        };
    }
}
