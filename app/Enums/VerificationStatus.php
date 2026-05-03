<?php

declare(strict_types=1);

namespace App\Enums;

enum VerificationStatus: string
{
    case Pending = 'pending';
    case InReview = 'in_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case MoreInfoRequired = 'more_info_required';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Ожидает',
            self::InReview => 'На рассмотрении',
            self::Approved => 'Одобрена',
            self::Rejected => 'Отклонена',
            self::MoreInfoRequired => 'Нужно больше информации',
        };
    }
}
