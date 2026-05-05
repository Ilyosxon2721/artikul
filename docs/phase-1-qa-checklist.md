# Artikul — QA Checklist Phase 1 (MVP Skeleton)

Last updated: 2026-05-04

## Prerequisites

- Laravel 12, PHP 8.3+, MySQL 8 (or sqlite locally), Redis 7 (production).
- `composer install`, `npm install`, `npm run build`, `php artisan migrate --seed`.

## Authentication (Prompt 3)

- [ ] Email registration works with intent (buyer / seller).
- [ ] Phone registration sends an SMS (Eskiz stub logs in local) and accepts the 6-digit code.
- [ ] Wrong code 3 times → 15-minute lockout.
- [ ] Login by email or phone in a single field.
- [ ] Google OAuth signs the user in (requires GOOGLE_CLIENT_ID / SECRET).
- [ ] Logout clears the session.

## Profile (Prompt 3)

- [ ] `/profile/general` saves name, username, email, phone, locale, country, timezone, avatar.
- [ ] Locale switcher in the header changes the UI language.
- [ ] Buyer/Seller mode switcher in the header redirects to onboarding the first time the user enters Seller mode.
- [ ] Seller onboarding wizard saves specializations, marketplaces with levels, hourly rate.
- [ ] Buyer profile editor saves company, marketplaces, SKUs, currency.
- [ ] Seller profile editor saves headline, bio, languages, hours/week.
- [ ] Public profile pages render at `/buyers/{username}` and `/sellers/{username}`.

## Tasks (Prompt 4)

- [ ] `/tasks/create` opens the wizard.
- [ ] Wizard saves draft on every step and on explicit "Save as draft".
- [ ] All five steps validate before publish.
- [ ] Published task gets a slug and appears at `/tasks/{slug}`.
- [ ] Attachments upload (max 10 files, 5 MB each) and can be deleted from the wizard.
- [ ] `/tasks` catalog filters: marketplace[], category, type, budget min/max, verified buyer.
- [ ] Sorting by newest / budget / deadline works.
- [ ] Search box matches by title and description (LIKE).
- [ ] Task detail shows view counter (incremented on each non-author visit).

## Sellers (Prompt 4)

- [ ] `/sellers` catalog filters: specialization[], marketplace[], rate range, min rating, country, verified-only.
- [ ] Sorting by recommended / rating / newest.
- [ ] Card links to `/sellers/{username}` profile page.

## Admin (Prompt 5)

- [ ] `/admin` is accessible only when `users.is_admin = true`.
- [ ] UserResource: list with filters, edit, ban / unban actions.
- [ ] TaskResource: list with status / type filters, hide action.
- [ ] MarketplaceResource: full CRUD.
- [ ] CategoryResource: full CRUD with parent hierarchy.
- [ ] SpecializationResource: full CRUD.
- [ ] VerificationResource: queue with approve / reject actions.
- [ ] Dashboard shows StatsOverviewWidget with active users / new today / open tasks / pending verifications.
- [ ] Dashboard lists 10 most recent tasks and 10 pending verifications.

## Landing & static pages (Prompt 5)

- [ ] `/` renders Hero, How-it-works, marketplaces, categories, top sellers, fresh tasks, pricing teaser, FAQ.
- [ ] `/about`, `/how-it-works`, `/terms`, `/privacy` render and switch language.
- [ ] All pages have `<title>`, `<meta description>`, OpenGraph, canonical link.
- [ ] JSON-LD `WebSite` + `SearchAction` is present in the public layout.

## Health & infra (Prompt 5)

- [ ] `/health` returns JSON with `status`, `checks` (app, db, cache, meilisearch).
- [ ] `/up` (Laravel framework health) returns 200.

## Localization

- [ ] Switch RU / UZ / EN via header → all interface strings update.
- [ ] `lang/{locale}/app.php` and `lang/{locale}/auth.php` are complete (no missing keys).

## Quality

- [ ] `vendor/bin/pint --test` passes (no style issues).
- [ ] `vendor/bin/phpstan analyse` passes (Larastan level 4 + baseline).
- [ ] `vendor/bin/pest` — all tests green.

## Deploy (manual)

- [ ] `php artisan key:generate`, `php artisan storage:link`.
- [ ] `npm run build` produces `public/build/manifest.json`.
- [ ] `php artisan migrate --force --seed` on first deploy.
- [ ] First admin: `php artisan tinker` → `User::find(...)->update(['is_admin' => true])`.
- [ ] Forge daemon: `php artisan queue:work` (Redis driver).
- [ ] (Production) `php artisan reverb:start`, `php artisan horizon` for Phase 2+.

## Out of scope for Phase 1

- Proposals, contracts, milestones, time logs (Phase 2 / Prompt 6).
- Real-time chat (Phase 2 / Prompt 7).
- Reviews, disputes, Telegram bot (Phase 2 / Prompts 8–9).
- Verification flow with Meilisearch facets, video calls, referrals (Phase 3 / Prompts 10–12).
