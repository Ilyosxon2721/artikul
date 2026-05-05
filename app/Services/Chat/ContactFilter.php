<?php

declare(strict_types=1);

namespace App\Services\Chat;

/**
 * Detects phone numbers, email addresses and contact-sharing domains in user
 * messages so we can mask them until a deal is accepted.
 */
final class ContactFilter
{
    private const PATTERNS = [
        // International phone numbers (RU/UZ/KZ formats), with various spacers
        'phone' => '/(?:\+?\d[\d\s\-\(\)]{6,}\d)/u',
        // Email
        'email' => '/[\w\.\-+]+@[\w\.\-]+\.[a-z]{2,}/iu',
        // Contact-sharing platforms
        'link' => '/(?:t\.me|telegram\.me|wa\.me|whatsapp\.com|instagram\.com|fb\.me|facebook\.com|vk\.com|skype:)/iu',
    ];

    /**
     * @return array{matched: bool, reason: ?string, masked: string}
     */
    public function inspect(string $text): array
    {
        $matchedReason = null;
        $masked = $text;

        foreach (self::PATTERNS as $reason => $pattern) {
            if (preg_match($pattern, $text) === 1) {
                $matchedReason ??= $reason;
                $masked = preg_replace($pattern, str_repeat('•', 6), $masked) ?? $masked;
            }
        }

        return [
            'matched' => $matchedReason !== null,
            'reason' => $matchedReason,
            'masked' => $masked,
        ];
    }
}
