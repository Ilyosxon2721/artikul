<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Reviewing = 'reviewing';
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
            self::Draft => 'Черновик',
            self::Published => 'Опубликована',
            self::Reviewing => 'Выбор исполнителя',
            self::InProgress => 'В работе',
            self::Submitted => 'Сдана',
            self::InReview => 'На проверке',
            self::Revision => 'На доработке',
            self::Completed => 'Завершена',
            self::Disputed => 'В споре',
            self::Cancelled => 'Отменена',
        };
    }

    public function isOpen(): bool
    {
        return in_array($this, [self::Published, self::Reviewing], true);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled], true);
    }
}
