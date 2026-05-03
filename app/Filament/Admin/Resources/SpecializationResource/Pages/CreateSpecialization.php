<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SpecializationResource\Pages;

use App\Filament\Admin\Resources\SpecializationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSpecialization extends CreateRecord
{
    protected static string $resource = SpecializationResource::class;
}
