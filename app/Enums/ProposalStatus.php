<?php

declare(strict_types=1);

namespace App\Enums;

enum ProposalStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает',
            self::Accepted => 'Принят',
            self::Rejected => 'Отклонён',
            self::Withdrawn => 'Отозван',
        };
    }
}
