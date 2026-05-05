<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\DisputeResource\Pages;

use App\Filament\Admin\Resources\DisputeResource;
use Filament\Resources\Pages\ListRecords;

class ListDisputes extends ListRecords
{
    protected static string $resource = DisputeResource::class;
}
