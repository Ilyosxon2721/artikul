# Artikul — QA Checklist Phase 2 (Deals & Communication)

Last updated: 2026-05-04

## Proposals & contracts (Prompt 6)

- [ ] Seller can apply via the modal on `/tasks/{slug}` — duplicate, self-apply,
      and over-the-50-cap submissions are blocked.
- [ ] Unverified seller hits the 10/day proposal limit.
- [ ] Buyer sees proposals on `/tasks/{slug}/proposals` with sort by newest /
      rating / price.
- [ ] Accepting a proposal creates a contract, auto-rejects siblings, sets
      task status to `in_progress`, opens (and unlocks) the chat.
- [ ] Seller can withdraw pending proposals from `/dashboard/proposals`.
- [ ] Invitation from public seller profile creates a `via_invitation=true`
      proposal and notifies the seller.
- [ ] `/contracts/{contract}` shows Overview / Milestones / Hours / History
      tabs (Milestones for Project, Hours for Hourly).
- [ ] Buyer can add milestones, seller can submit, buyer can approve.
- [ ] Seller can log hours; buyer can dispute a log.
- [ ] Submit / approve / request-revision / cancel transitions work and update
      the underlying task status.

## Chat (Prompt 7)

- [ ] `/messages` shows conversation list sorted by `last_message_at` with
      search across participant names.
- [ ] Sending a message broadcasts on `private:conversation.{id}`; channel
      auth gates non-participants.
- [ ] Contact filter masks phone numbers, emails and contact-sharing links
      until the contract starts (banner + filtered placeholder visible).
- [ ] After a proposal is accepted, the existing conversation is unlocked
      and new messages pass through unmasked.

## Reviews (Prompt 8)

- [ ] Both sides can leave a review on `/contracts/{contract}/review`.
- [ ] Reviews stay hidden (`is_visible=false`) until the second party submits
      or until 14 days pass.
- [ ] Publishing recalculates `seller_profiles.rating_avg` /
      `buyer_profiles.rating_avg` and notifies the target.
- [ ] Authors can reply once, max 500 chars (via `ReviewService::reply`).
- [ ] Duplicate reviews from the same author are rejected.

## Disputes (Prompt 8)

- [ ] Either party can open a dispute on `/contracts/{contract}/dispute`
      (only on active contracts).
- [ ] Opening flips the contract to `disputed` and sets a 5 business day SLA.
- [ ] `/admin/disputes` shows a queue with badge count and three resolution
      actions (buyer / seller / partial with shares).
- [ ] Resolving by an admin updates the contract status and notifies both
      parties.

## Telegram (Prompt 9)

- [ ] `/profile/notifications` generates a 6-character code, gives a deep
      link to the bot.
- [ ] Bot webhook at `POST /telegram/webhook` handles `/start <code>`,
      `/link <code>`, `/unlink`, `/help`.
- [ ] Linking sets `users.telegram_chat_id`; unlinking clears it.
- [ ] TelegramChannel is registered for notifications that implement
      `toTelegram()`.

## SEO

- [ ] `/sitemap.xml` returns valid XML containing public pages, published
      tasks and seller profiles with usernames.
- [ ] `/robots.txt` allows `/`, disallows `/admin`, `/api`, `/dashboard`,
      `/onboarding`, `/messages`.

## Quality

- [ ] `vendor/bin/pest` — all tests green.
- [ ] `vendor/bin/pint --test` passes.
- [ ] `vendor/bin/phpstan analyse` passes (Larastan level 4 + baseline).

## Deploy notes

- Production needs: `TELEGRAM_BOT_TOKEN`, `TELEGRAM_BOT_USERNAME`,
  webhook URL configured via `setWebhook` to `https://artikul.uz/telegram/webhook`.
- Reverb daemon running (`php artisan reverb:start`).
- Queue worker (Redis) running for queued mail / Telegram notifications.
- `/sitemap.xml` submitted to Google Search Console and Yandex Webmaster.
