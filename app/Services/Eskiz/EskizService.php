<?php

declare(strict_types=1);

namespace App\Services\Eskiz;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin Eskiz REST client. In local/testing the SMS body is logged
 * instead of being sent so phone verification flows can be exercised
 * without burning through SMS credits.
 */
final class EskizService
{
    private const TOKEN_CACHE_KEY = 'eskiz.token';

    private const TOKEN_TTL_HOURS = 24;

    public function __construct(
        private readonly string $email,
        private readonly string $password,
        private readonly string $senderId,
        private readonly string $baseUrl,
    ) {}

    public function send(string $phone, string $message): bool
    {
        if ($this->isStubMode()) {
            Log::channel(config('logging.default'))->info('[Eskiz stub] SMS not sent in this env', [
                'to' => $phone,
                'sender' => $this->senderId,
                'message' => $message,
            ]);

            return true;
        }

        $response = Http::withToken($this->token())
            ->asForm()
            ->post($this->baseUrl.'/message/sms/send', [
                'mobile_phone' => preg_replace('/\D+/', '', $phone),
                'message' => $message,
                'from' => $this->senderId,
            ]);

        if (! $response->successful()) {
            Log::error('Eskiz send failed', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return false;
        }

        return true;
    }

    private function token(): string
    {
        return Cache::remember(
            self::TOKEN_CACHE_KEY,
            now()->addHours(self::TOKEN_TTL_HOURS),
            function (): string {
                $response = Http::asForm()->post($this->baseUrl.'/auth/login', [
                    'email' => $this->email,
                    'password' => $this->password,
                ]);

                $response->throw();

                return (string) $response->json('data.token');
            },
        );
    }

    private function isStubMode(): bool
    {
        return app()->environment(['local', 'testing'])
            || $this->email === ''
            || $this->password === '';
    }
}
