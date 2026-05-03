<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\MarketplaceResource\Pages;

use App\Filament\Admin\Resources\MarketplaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMarketplace extends CreateRecord
{
    protected static string $resource = MarketplaceResource::class;
}
