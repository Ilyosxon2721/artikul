<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Currency;
use App\Enums\UserMode;
use App\Http\Controllers\Controller;
use App\Http\Resources\BuyerProfileResource;
use App\Http\Resources\SellerProfileResource;
use App\Http\Resources\UserResource;
use App\Models\BuyerProfile;
use App\Models\SellerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function update(Request $request): UserResource
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'min:2', 'max:120'],
            'username' => ['sometimes', 'nullable', 'string', 'min:3', 'max:32', 'regex:/^[a-z0-9_]+$/i', 'unique:users,username,'.$user->id],
            'email' => ['sometimes', 'nullable', 'email', 'max:191', 'unique:users,email,'.$user->id],
            'phone' => ['sometimes', 'nullable', 'regex:/^\+?\d{9,15}$/', 'unique:users,phone,'.$user->id],
            'country_code' => ['sometimes', 'nullable', 'string', 'size:2'],
            'city' => ['sometimes', 'nullable', 'string', 'max:120'],
            'timezone' => ['sometimes', 'string', 'max:64'],
            'locale' => ['sometimes', 'in:ru,uz,en'],
            'current_mode' => ['sometimes', 'in:buyer,seller'],
        ]);

        if (array_key_exists('phone', $data) && is_string($data['phone'])) {
            $data['phone'] = '+'.preg_replace('/\D+/', '', $data['phone']);
        }

        $user->fill($data)->save();

        return new UserResource($user->fresh());
    }

    public function updateBuyer(Request $request): BuyerProfileResource
    {
        $user = $request->user();

        $data = $request->validate([
            'company_name' => ['sometimes', 'nullable', 'string', 'max:160'],
            'description' => ['sometimes', 'nullable', 'string', 'max:2000'],
            'marketplace_codes' => ['sometimes', 'array'],
            'marketplace_codes.*' => ['string', 'exists:marketplaces,code'],
            'skus_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'revenue_range' => ['sometimes', 'nullable', 'string', 'max:32'],
            'default_currency' => ['sometimes', 'in:UZS,RUB,KZT,USD'],
        ]);

        if (isset($data['default_currency'])) {
            $data['default_currency'] = Currency::from($data['default_currency']);
        }

        $profile = BuyerProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->fill($data)->save();

        return new BuyerProfileResource($profile->fresh());
    }

    public function updateSeller(Request $request): SellerProfileResource
    {
        $user = $request->user();

        $data = $request->validate([
            'headline' => ['sometimes', 'nullable', 'string', 'max:80'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'video_intro_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'languages' => ['sometimes', 'array'],
            'languages.*' => ['string', 'max:32'],
            'hourly_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'in:UZS,RUB,KZT,USD'],
            'min_order_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'available_hours_per_week' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:168'],
            'open_to_long_term' => ['sometimes', 'boolean'],
        ]);

        if (isset($data['currency'])) {
            $data['currency'] = Currency::from($data['currency']);
        }

        $profile = SellerProfile::firstOrCreate(['user_id' => $user->id]);
        $profile->fill($data)->save();

        return new SellerProfileResource($profile->fresh()->load(['specializations', 'marketplaces']));
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        $user = $request->user();
        $user->forceFill(['avatar_url' => '/storage/'.$path])->save();

        return response()->json(['avatar_url' => $user->avatar_url]);
    }

    public function switchMode(Request $request): UserResource
    {
        $request->validate([
            'mode' => ['required', 'in:buyer,seller'],
        ]);

        $user = $request->user();
        $mode = UserMode::from((string) $request->input('mode'));

        if ($mode === UserMode::Seller) {
            SellerProfile::firstOrCreate(['user_id' => $user->id]);
        }

        $user->forceFill(['current_mode' => $mode])->save();

        return new UserResource($user->fresh());
    }
}
