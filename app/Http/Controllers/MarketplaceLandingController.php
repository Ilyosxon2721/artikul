<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\SellerProfile;
use Illuminate\View\View;

class MarketplaceLandingController extends Controller
{
    public function __invoke(string $code): View
    {
        $marketplace = Marketplace::query()
            ->where('code', mb_strtoupper($code))
            ->where('is_active', true)
            ->firstOrFail();

        $sellers = SellerProfile::query()
            ->whereHas('marketplaces', fn ($q) => $q->where('marketplaces.id', $marketplace->id))
            ->where('is_searchable', true)
            ->orderByDesc('rating_avg')
            ->limit(8)
            ->get(['id', 'user_id', 'headline', 'rating_avg']);

        return view('landing.segments.marketplace', compact('marketplace', 'sellers'));
    }
}
