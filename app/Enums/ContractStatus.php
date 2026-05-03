<?php

declare(strict_types=1);

namespace App\Enums;

enum ContractStatus: string
{
    case InProgress = 'in_progress';
    case Submitted = 'submitted';
    case InReview = 'in_review';
    case Revision = 'revision';
    case Completed = 'completed';
    case Disputed = 'disputed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::InProgress => 'В работе',
            self::Submitted => 'Сдан',
            self::InReview => 'На проверке',
            self::Revision => 'На доработке',
            self::Completed => 'Завершён',
            self::Disputed => 'В споре',
            self::Cancelled => 'Отменён',
        };
    }
}
