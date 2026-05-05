# Dispute Resolution Playbook

Audience: Artikul arbitrators. Last updated: 2026-05-04.

## Goal

Decide each dispute fairly within 5 business days. The MVP has no escrow yet,
so the decision affects rating only — but it sets the precedent buyers and
sellers learn from. Be predictable and well-documented.

## Step-by-step

1. Open `/admin/disputes`. Cases ordered by `sla_due_at`.
2. Read the dispute reason and description from both parties (seller and
   buyer). The form on the user side captured one of: `quality`, `deadline`,
   `communication`, `scope`, `other`.
3. Open the linked contract → tab **Chat**: read the full conversation
   end-to-end.
4. Open tab **Files** / **Milestones** / **Hours** depending on contract
   type. Cross-check claimed deliverables.
5. If anything is unclear, message both parties via the contract chat
   ("system message" = use a clearly labelled admin reply).
6. Decide and click one of the three actions:
   - **In favour of buyer** — typical when the deliverable is missing or
     materially below what was agreed.
   - **In favour of seller** — when the buyer's complaints are not
     supported by evidence or the agreed scope was met.
   - **Partial** — when both sides share fault. Set buyer / seller share
     percentages (sum to 100) and write a summary.

Always provide a **resolution summary** explaining the decision in 2–4
sentences. The text is sent to both parties.

## Effects

- The contract status flips back from `disputed` to either `cancelled`
  (buyer / seller / partial) or `in_progress` (when you mark the dispute
  itself as cancelled, e.g. duplicate filing).
- `has_dispute` resets to false.
- Rating impact (post-Phase 1 with escrow): the losing side gets a -0.3
  penalty for 90 days. On MVP this is informational; record it in the
  resolution summary.
- Both parties get a `DisputeResolvedNotification` (email + DB + telegram if
  linked).

## Escalation

For monetary disputes above 100 USD or repeated offenders, escalate to the
super admin. They have the authority to ban accounts and refer cases to
RISMENT support.

## Common patterns

- **Seller delivers low quality vs spec** → buyer wins, summary cites the
  TZ section.
- **Buyer keeps changing scope mid-contract** → seller wins or partial.
- **Communication broke down for both sides** → partial 50/50.
- **Buyer didn't pay (off-platform)** → cancel contract, ban buyer if
  repeated.
