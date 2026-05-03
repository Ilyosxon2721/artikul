<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\UserMode;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Auth\PhoneVerificationService;
use App\Services\Auth\UserRegistrar;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request, UserRegistrar $registrar): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'regex:/^\+?\d{9,15}$/', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'mode' => ['nullable', 'in:buyer,seller'],
            'referral_code' => ['nullable', 'string', 'max:32'],
            'locale' => ['nullable', 'in:ru,uz,en'],
        ]);

        if (empty($data['email']) && empty($data['phone'])) {
            throw ValidationException::withMessages([
                'email' => __('auth.email_or_phone_required'),
            ]);
        }

        $user = $registrar->register($data, UserMode::tryFrom($data['mode'] ?? 'buyer') ?? UserMode::Buyer);
        event(new Registered($user));

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $field = str_contains($data['identifier'], '@') ? 'email' : 'phone';
        $value = $field === 'phone'
            ? '+'.preg_replace('/\D+/', '', $data['identifier'])
            : mb_strtolower($data['identifier']);

        $user = User::query()->where($field, $value)->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'identifier' => trans('auth.failed'),
            ]);
        }

        if ($user->is_banned) {
            throw ValidationException::withMessages([
                'identifier' => trans('auth.banned'),
            ]);
        }

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['ok' => true]);
    }

    public function sendPhoneCode(Request $request, PhoneVerificationService $codes): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^\+?\d{9,15}$/'],
            'purpose' => ['nullable', 'in:register,login'],
        ]);

        $codes->issue(
            $data['phone'],
            $data['purpose'] ?? PhoneVerificationService::PURPOSE_REGISTER,
            $request->ip(),
        );

        return response()->json(['ok' => true]);
    }

    public function verifyPhone(Request $request, PhoneVerificationService $codes): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^\+?\d{9,15}$/'],
            'code' => ['required', 'digits:6'],
            'purpose' => ['nullable', 'in:register,login'],
        ]);

        $ok = $codes->verify(
            $data['phone'],
            $data['code'],
            $data['purpose'] ?? PhoneVerificationService::PURPOSE_REGISTER,
        );

        return response()->json(['ok' => $ok]);
    }
}
