# Artikul — Admin Guide

Audience: Artikul moderators and support staff. Last updated: 2026-05-04.

## Access

The admin panel lives at `https://artikul.uz/admin`. Only users with
`is_admin = true` can sign in. To grant admin access run:

```bash
php artisan artikul:make-admin user@example.com           # admin
php artisan artikul:make-admin user@example.com --super   # super admin
```

## Resources

| Section | Path | What you do |
|---|---|---|
| Users | `/admin/users` | Search, ban / unban, change role, view profile |
| Tasks | `/admin/tasks` | Hide spam, change status in edge cases, audit |
| Marketplaces | `/admin/marketplaces` | Add new platforms, toggle visibility |
| Categories | `/admin/categories` | Manage tree, sort order, descriptions |
| Specializations | `/admin/specializations` | Add or rename skills |
| Verifications | `/admin/verifications` | Review documents, approve / reject |
| Disputes | `/admin/disputes` | Decide buyer / seller / partial |

The dashboard widget shows DAU, today's signups, open contracts and pending
verifications at a glance.

## Daily routine

1. Open `/admin`.
2. Process the **Verifications** queue (target SLA: same business day).
3. Process the **Disputes** queue (SLA: 5 business days; check `sla_due_at`).
4. Skim **Tasks** for spam or rule violations; use the "Hide" action.
5. Review reports in the **Users** table — bans require a written reason.

## Verification flow

Each application contains an ID document, a selfie with the document, marketplace
dashboard screenshots and an optional recommendation file. Approve only if:

- The ID matches the user's profile name.
- The selfie clearly shows the same person holding the ID.
- At least one marketplace screenshot proves real selling experience (orders,
  views, conversion — financials are masked, that's fine).

Reject with a clear reason. Approving sets `seller_profiles.is_verified=true`
and posts a "Verified" badge across the catalog and search.

## Dispute flow

See `dispute-resolution-playbook.md` for a step-by-step guide.

## Bans

Bans (`is_banned=true`) hide the user from the catalog and prevent login.
Always note the reason. Bans are reversible — use the `Unban` action when
the issue is resolved.
