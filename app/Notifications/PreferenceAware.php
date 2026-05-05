<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Channels\TelegramChannel;

/**
 * Helper trait for notifications: maps a category and the user's preferences
 * (stored in user_profiles.notification_preferences) to enabled channels.
 *
 * Usage: declare protected ?string $category = 'proposals'; in your notification
 * and call $this->channelsFor($notifiable) inside via().
 */
trait PreferenceAware
{
    /**
     * @return array<int, string>
     */
    protected function channelsFor(object $notifiable, ?string $category = null, array $defaults = ['database', 'mail']): array
    {
        $category ??= property_exists($this, 'category') ? $this->category : null;

        if (! $notifiable instanceof User || $category === null) {
            return $defaults;
        }

        $prefs = (array) ($notifiable->profile?->notification_preferences ?? []);
        $catPrefs = $prefs['categories'][$category] ?? null;

        if (! is_array($catPrefs)) {
            return $defaults;
        }

        $channels = [];

        // database = in_app
        if (($catPrefs['in_app'] ?? true)) {
            $channels[] = 'database';
        }
        if (($catPrefs['email'] ?? true)) {
            $channels[] = 'mail';
        }
        if (($catPrefs['telegram'] ?? false) && $notifiable->telegram_chat_id) {
            $channels[] = TelegramChannel::class;
        }

        return $channels;
    }
}
