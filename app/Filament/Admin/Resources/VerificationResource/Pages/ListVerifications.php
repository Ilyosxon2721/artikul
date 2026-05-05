<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\VerificationResource\Pages;

use App\Filament\Admin\Resources\VerificationResource;
use Filament\Resources\Pages\ListRecords;

class ListVerifications extends ListRecords
{
    protected static string $resource = VerificationResource::class;
}
