# Artikul — QA Checklist Phase 3 (Quality & Launch)

Last updated: 2026-05-04

## Verification (Prompt 10)

- [ ] `/profile/verification` accepts ID document, selfie, marketplace
      screenshots, optional recommendation, and stores them on the local
      (private) disk.
- [ ] Passport hash deduplicates accounts across users.
- [ ] Filament `/admin/verifications` queue allows approve / reject with
      mandatory rejection reason; approve flips
      `seller_profiles.is_verified=true` and `verified_at`.

## Search (Prompt 10)

- [ ] `/search` runs against Meilisearch (indexes `tasks`, `sellers`) when
      configured; falls back to LIKE otherwise.
- [ ] Filterable / sortable attributes match `config/scout.php` declarations.
- [ ] Synonym dictionary covers RU / UZ / EN equivalents (фотограф ⇄
      photographer, дизайнер ⇄ designer, etc., 30+ pairs).
- [ ] `artikul:run-saved-search-alerts` notifies users with new matches.

## Video calls & referrals (Prompt 11)

- [ ] `JitsiService::startCall` creates a unique room and posts a system
      message with the meet.jit.si link.
- [ ] `/dashboard/referrals` shows the personal `?ref=CODE` URL, code,
      invitee count and signup history.
- [ ] Signup with `?ref=CODE` records `users.referred_by`.

## Segment landings (Prompt 11)

- [ ] `/for-sellers`, `/for-buyers` render hero + 3 benefits with localized
      copy in RU / UZ / EN.
- [ ] `/marketplaces/{code}` renders a per-marketplace landing with top 8
      sellers and a CTA into `/sellers?mp=...`.

## Final UX cross-check

- [ ] Buyer flow: register → create task → receive proposals → accept →
      contract → submit → approve → review.
- [ ] Seller flow: register → onboard → apply to task → contract →
      submit → approve → review.
- [ ] Hourly contract: log hours; buyer disputes one entry.
- [ ] Dispute: open → admin resolves (buyer / seller / partial).
- [ ] Verification: submit → admin approves → seller gains the badge.
- [ ] Telegram: link via `/profile/notifications` → bot sends
      notifications.
- [ ] Chat: filter masks contacts pre-deal; unlocked once contract starts.
- [ ] Video call: opens meet.jit.si in a new tab from the chat.

## Cross-browser

- [ ] Chrome, Firefox, Safari (mac) — desktop.
- [ ] Edge.
- [ ] iOS Safari, Chrome Android — mobile.
- [ ] Minimum width: 360 px (iPhone SE).

## Security pass

- [ ] CSRF on every web form (`/telegram/webhook` is the only allow-listed
      exception).
- [ ] SQL injection: filters use bindings, not string interpolation.
- [ ] XSS: comments / descriptions are escaped (`{{ }}`); markdown is
      rendered through trusted helpers only.
- [ ] File uploads: type and size limits validated server-side.
- [ ] Authorization: TaskPolicy guards update / delete; contracts /
      reviews / disputes guard the parties.
- [ ] Rate limiting: login (5/min), register (5/min), phone-code (3/min),
      api (60/min).
- [ ] Secrets stay in `.env`, not in the repo.
- [ ] Security headers middleware applied on every web response.

## Production deploy

- [ ] Forge site `artikul.uz` configured with PHP 8.3, OPcache, Redis
      sessions / cache / queues.
- [ ] Daemons:
  - `php artisan horizon`
  - `php artisan reverb:start`
  - `php artisan queue:work --queue=default,mail`
- [ ] Scheduler entry: `* * * * * php artisan schedule:run >> /dev/null 2>&1`.
- [ ] `.env` populated with: APP_KEY, MAIL_*, ESKIZ_*, MEILISEARCH_*,
      AWS_*, GOOGLE_*, TELEGRAM_*.
- [ ] SSL via Let's Encrypt; HTTPS forced.
- [ ] Backup daemon scheduled via `routes/console.php` (daily at 03:00,
      cleanup at 01:00).

## Production data

- [ ] `php artisan migrate --force`
- [ ] `php artisan db:seed --class=MarketplaceSeeder --force`
- [ ] `php artisan db:seed --class=CategorySeeder --force`
- [ ] `php artisan db:seed --class=SpecializationSeeder --force`
- [ ] `php artisan artikul:make-admin you@artikul.uz --super`
- [ ] `php artisan storage:link`
- [ ] `php artisan optimize` (config + routes + views cache).
- [ ] `npm run build` and copy `public/build/*`.

## SEO

- [ ] `/sitemap.xml` submitted to Google Search Console and Yandex Webmaster.
- [ ] `/robots.txt` reviewed.
- [ ] OpenGraph + canonical present on every public page.
- [ ] JSON-LD `WebSite` + `SearchAction` on the public layout.

## Monitoring

- [ ] Sentry DSN set via `SENTRY_LARAVEL_DSN`.
- [ ] UptimeRobot / Better Stack ping `https://artikul.uz/health` every 5 min.
- [ ] Log rotation enabled (`storage/logs/laravel.log`).
- [ ] Filament admin badge counts (verifications + disputes) reviewed daily.
