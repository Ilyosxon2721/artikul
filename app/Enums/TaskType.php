<?php

declare(strict_types=1);

namespace App\Enums;

enum TaskType: string
{
    case Gig = 'gig';
    case Project = 'project';
    case Hourly = 'hourly';

    public function label(): string
    {
        return match ($this) {
            self::Gig => 'Разовая (Gig)',
            self::Project => 'Проект',
            self::Hourly => 'Почасовая',
        };
    }
}
