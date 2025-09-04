# Mollie Connect Integration Plan (Donations + Paying Memberships)

This document describes the detailed plan to implement Donations and Paying Memberships in OpenAction using Mollie Connect for Platforms, incorporating application fees and reusing the existing Members Area module.


## 1. Model and Scope

- Use Mollie Connect for Platforms (OAuth). Each OpenAction customer (Organization) owns and is liable for their payments; OpenAction creates and manages payments on their behalf.
- Reuse the existing Members Area module; add a Paying Memberships mode and project-level settings to enable paid flows.
- Support Donations (one‑off + recurring) and Paying Memberships (auto‑renew via Mollie Subscriptions or one‑off first period).
- Add a configurable organization‑level Application Fee (default 1%) applied to payments and subscriptions, respecting Mollie limits.


## 2. Data Model Changes

All classes/fields are indicative; naming can be adapted to the codebase conventions.

### 2.1 Organization-level

- Integration\MollieOAuthConnection (console/src/Entity/Integration/MollieOAuthConnection.php)
  - OneToOne with Organization (required, unique per Organization)
  - Fields:
    - `id`, timestamps
    - `organization` (OneToOne)
    - `clientId` (string, optional override) – default is app config
    - `scopes` (json array)
    - `refreshToken` (text, encrypted at rest)
    - `accessToken` (text, nullable) and `accessTokenExpiresAt` (datetime)
    - `mollieOrganizationId` (string), `mollieOrganizationName` (string)
    - `testmode` (bool)
    - `capabilities` (json) or minimal booleans like `canReceivePayments` (bool)

- Organization (console/src/Entity/Organization.php)
  - Add fields:
    - `mollieAppFeeEnabled` (bool, default true)
    - `mollieAppFeePercent` (decimal(5,2), default 1.00)

### 2.2 Project-level (extend existing Project)

Embed settings directly on `Project` (no extra settings entities):

- Mollie profile and methods
  - `mollieProfileId` (string, nullable)
  - `mollieCurrency` (string, ISO 4217, nullable)
  - `mollieAllowedMethods` (json, nullable)

- Donations configuration
  - `donationRecommendedAmounts` (json array of strings/decimals)
  - `donationAllowCustom` (bool)
  - `donationCustomMin` (int, minor units, nullable)
  - `donationCustomMax` (int, minor units, nullable)
  - `donationShowRecurring` (bool)
  - `donationDefaultRecurring` (bool)

- Paying memberships (reusing Members Area module)
  - `payingMembershipsEnabled` (bool)
  - `membershipAllowCustomAmount` (bool)
  - `membershipCustomMin` (int, minor units, nullable)
  - `membershipCustomMax` (int, minor units, nullable)
  - `membershipShowAutoRenew` (bool)
  - `membershipDefaultAutoRenew` (bool)

### 2.3 Donations and Memberships

- Donation (console/src/Entity/Donation.php)
  - Fields:
    - `id`, timestamps
    - `organization` (ManyToOne Organization, required, index)
    - `project` (ManyToOne Project, nullable, index)
    - `contact` (ManyToOne Community\Contact, required, index)
    - `amount` (int minor units), `currency` (string)
    - `isRecurring` (bool), `frequency` (enum: `one_off`, `monthly`, `yearly`)
    - `status` (enum: `pending`, `open`, `paid`, `failed`, `canceled`, `refunded`)
    - `method` (string, nullable)
    - `molliePaymentId` (string, unique, nullable)
    - `mollieSubscriptionId` (string, nullable)
    - `description` (string), `metadata` (json)
    - `origin` (enum: `mollie`, `manual`, `import`)
    - `paidAt` (datetime nullable)

- MembershipPlan (console/src/Entity/MembershipPlan.php)
  - Fields:
    - `id`, timestamps
    - `project` (ManyToOne Project, index)
    - `name` (string), `description` (text nullable)
    - `price` (int minor units), `currency` (string)
    - `interval` (enum: `monthly`, `yearly`)
    - `image` (OneToOne Upload nullable)
    - `active` (bool), `sortOrder` (int)

- ContactMembership (console/src/Entity/ContactMembership.php)
  - Fields:
    - `id`, timestamps
    - `organization` (ManyToOne Organization)
    - `project` (ManyToOne Project)
    - `contact` (ManyToOne Community\Contact)
    - `plan` (ManyToOne MembershipPlan, nullable for custom)
    - `amount` (int minor units), `currency` (string)
    - `interval` (enum: `monthly`, `yearly`, `one_off`)
    - `status` (enum: `pending`, `active`, `past_due`, `canceled`, `expired`)
    - `origin` (enum: `mollie`, `manual`, `import`)
    - `autoRenew` (bool)
    - `currentPeriodStart` (datetime), `currentPeriodEnd` (datetime)
    - `mollieSubscriptionId` (string nullable)
    - `mollieCustomerId` (string nullable) – the consumer customer id under the merchant account


## 3. Application Fees (1% default)

- Configuration at Organization level: `mollieAppFeeEnabled` + `mollieAppFeePercent`.
- Apply to Payments and Subscriptions (`applicationFee` parameter) created via OAuth.
- Fee calculation:
  - `fee = roundToCents(amount * percent / 100)`
  - Enforce Mollie maximum for Payments API: reserve `€0.35 + 6% * amount` for Mollie fee → `maxFee = amount - (0.35 + 0.06*amount)`; cap `fee <= maxFee`. Min fee `€0.01`.
  - Currency selection rules per Mollie docs: use payment currency if balance available; otherwise use customer primary balance currency.
- Validate and log if configured fee cannot be applied due to max constraints.


## 4. OAuth and Client Wrapper

- OAuth flow (Console):
  - “Connect with Mollie” button when accessing Donations/Memberships and no connection exists.
  - Redirect to Mollie authorize URL with required scopes.
  - Callback exchanges authorization code for `access_token` and `refresh_token` (Tokens API). Persist in `MollieOAuthConnection` with encrypted refresh token, cached access token and expiry.
  - After connect: fetch capabilities/onboarding state; list profiles; save selected `profileId` on the `Project`.

- Scopes (minimum):
  - `organizations.read`, `profiles.read`, `payments.write`, `payments.read`, `customers.write`, `customers.read`, `subscriptions.write`, `subscriptions.read`, `mandates.read`
  - Optional: `profiles.write` (if we create profiles), `capabilities.read` or `onboarding.read` for readiness UI

- `MollieOAuthClient` service:
  - `getAccessToken(Organization)`: use refresh token to obtain new access tokens (cache in DB until expiry).
  - Wrap Mollie SDK calls with Bearer token.
  - Support `testmode` flag when configured.
  - Methods: profiles list/create, methods enable/disable (optional), customers CRUD, mandates read, payments create/get/refund, subscriptions create/get/cancel, balances/settlements/invoices (future reporting).


## 5. Flows

### 5.1 Donations

- Public flow:
  1. Present amounts: `donationRecommendedAmounts` + optional custom input within min/max.
  2. Identify/Create `Contact` in CRM.
  3. One‑off donation:
     - Create Payment via OAuth with `profileId`, `amount`, `redirectUrl`, `webhookUrl`, `metadata` (donationId, contactId, projectId), optional `method`, and `applicationFee` if enabled.
  4. Recurring donation:
     - Ensure/create merchant‐side Mollie Customer (OAuth) for the contact.
     - Create/collect mandate on first charge if required (via Payment or direct authorization per method).
     - Create Subscription (`amount`, `interval`) and set `applicationFee` on subscription.
     - Store `mollieSubscriptionId` on Donation (and optionally keep a ledger of each charge as Donation rows).

- Console:
  - Project settings for donations are embedded on Project.
  - Donations list/detail (filters: status/date/contact/project).
  - Export CSV.

### 5.2 Paying Memberships (Members Area)

- Public flow (reusing existing module, gated by `payingMembershipsEnabled`):
  1. Display `MembershipPlan`s (+ optional custom amount within bounds) and auto‑renew checkbox (if configured).
  2. If auto‑renew:
     - Ensure/create merchant‐side Mollie Customer for the contact.
     - Create Subscription (amount from plan or custom, interval monthly/yearly) with `applicationFee`.
     - Create/activate `ContactMembership` with `autoRenew=true`, store `mollieSubscriptionId`, and set `currentPeriodEnd` from subscription cycle.
  3. If not auto‑renew:
     - Create one‑off Payment; on webhook `paid`, create `ContactMembership` with one period and `autoRenew=false`.

- Console:
  - Plans CRUD under project membership settings.
  - Paying memberships settings on Project (fields above).
  - Memberships dashboard: view, cancel (call Mollie Subscriptions API), manual extend, export.


## 6. Webhooks and Reconciliation

- Endpoints (Console): new project webhooks, e.g. `POST /webhook/mollie/project/{projectUuid}/{token}` for Payments and Subscriptions.
- Handler logic:
  - Payments:
    - On `paid`: find Donation by `metadata` or `molliePaymentId`, set `status=paid`, fill `paidAt`, `method`; if tied to one‑off membership, create/activate `ContactMembership` period.
    - On `failed`/`canceled`/`expired`: update Donation status.
  - Subscriptions:
    - On new cycle charge `paid`: extend `ContactMembership.currentPeriodEnd` and optionally record a `Donation` for the charge.
    - On `canceled`/`completed`: set membership status `canceled/expired` appropriately.
- Onboarding/capabilities display: show readiness messages using Capabilities or Onboarding status.


## 7. Manual Donations and Memberships

- Manual donations:
  - Console action “Add donation” with: contact (required), project (optional), amount/currency, date, method (cash/bank/cheque/other), reference, note.
  - Persist `Donation` with `status=paid`, `origin=manual`, no Mollie IDs.
  - Import CSV: map by contact email/external id; support idempotency reference to avoid dupes.

- Manual memberships:
  - Console action “Add membership” with: contact, project, plan (optional), amount/currency, interval, `currentPeriodStart/End`, `autoRenew=false` by default, origin (manual/import), note.
  - Persist `ContactMembership` accordingly.
  - Mixed scenarios: if a contact later subscribes via Mollie, close the manual membership at current end and start Mollie‑based membership from next period (or immediate switch by admin).


## 8. UI Changes (Console & Public)

- Console
  - Organization: “Connect with Mollie” setup page; show connection status, capabilities, and disconnect.
  - Project: Donations settings panel; Paying Memberships settings panel; Mollie profile selection; allowed methods.
  - Management: Donations list; Memberships list with actions; Plans CRUD.

- Public (per project)
  - Donations page (`/donate`): amounts, custom inputs, recurring checkbox; redirects to Mollie checkout; thank‑you page.
  - Memberships: reuse existing routes/pages; add payment/renewal options when `payingMembershipsEnabled`.


## 9. Services and Validation

- `MollieOAuthClient` – token refresh, OAuth SDK wrapper, `testmode` support.
- `PaymentManager` – orchestrates donations and memberships creation using OAuth client; applies application fee; idempotency keys on create.
- Input validation: ensure amounts are within configured bounds; enforce fee maximums; block flows if capabilities not ready.


## 10. Security and Compliance

- Encrypt `refreshToken` at rest (e.g., Sodium). Do not log tokens.
- Store secrets in environment (`.env.local`), not VCS; support separate test/live modes via `testmode` flag.
- GDPR: capture consent texts on donation/membership forms; link to policies; minimize PII in payment metadata.


## 11. Migrations and Repositories

- Doctrine migrations to add:
  - `integration_mollie_oauth_connection` table
  - New Organization fields (app fee)
  - New Project fields (Mollie + donations + paying memberships)
  - `donations`, `membership_plans`, `contact_memberships` tables
- Repositories for Donation, MembershipPlan, ContactMembership, MollieOAuthConnection.


## 12. Testing Plan

- Unit tests: fee calculation, OAuth client token refresh, PaymentManager build payloads.
- Functional tests: webhook handlers for payment/subscription events; project settings forms.
- Public flows: initiate one‑off donation and subscription in testmode (mock Mollie client).


## 13. Configuration

- App config/env:
  - `MOLLIE_CONNECT_CLIENT_ID`
  - `MOLLIE_CONNECT_CLIENT_SECRET`
  - `MOLLIE_CONNECT_REDIRECT_URI`
  - `MOLLIE_CONNECT_SCOPES` (default as listed)
  - `MOLLIE_TESTMODE` (optional)
- Per Organization: `mollieAppFeeEnabled`, `mollieAppFeePercent`.


## 14. Implementation Order (Phased)

1) OAuth connect (org‑level) + profile selection per project + testmode toggle
2) One‑off donations with application fee + webhook processing
3) Recurring donations (Subscriptions) with application fee + cycle webhooks
4) Paying memberships: plans CRUD, one‑off and auto‑renew flows
5) Management UIs (lists, filters, exports) for donations/memberships
6) Manual donations/memberships + CSV import


## 15. Routes Overview (indicative)

- Console
  - `GET /console/organization/{orgUuid}/mollie` – connect/status
  - `GET/POST /console/project/{projectUuid}/payments/settings` – profile + methods
  - `GET/POST /console/project/{projectUuid}/donations/settings` – donation config
  - `GET/POST /console/project/{projectUuid}/memberships/settings` – paying memberships config
  - `GET /console/project/{projectUuid}/donations` – list
  - `GET /console/project/{projectUuid}/memberships` – list
  - `POST /console/project/{projectUuid}/memberships/{id}/cancel` – cancel subscription

- Public
  - `GET/POST /{projectSubdomain}/donate` – donation page
  - Members area existing routes – enhanced with payment steps when paying mode enabled

- Webhooks
  - `POST /webhook/mollie/project/{projectUuid}/{token}` – payment/subscription events


## 16. Open Considerations (Later Phases)

- Reporting (Balances/Settlements/Invoices APIs) for reconciliation dashboards.
- Refunds (Refunds API) from console.
- Advanced dunning/past_due handling for memberships.
- Per‑project application fee override if ever needed.


---

This plan reuses the existing Members Area, centralizes OAuth at the organization level (1:1), embeds per‑project settings, enforces application fees, and supports both automated and manual donation/membership records with robust webhook reconciliation.

## TODO

- Define env config: add `MOLLIE_CONNECT_CLIENT_ID`, `MOLLIE_CONNECT_CLIENT_SECRET`, `MOLLIE_CONNECT_REDIRECT_URI`, `MOLLIE_CONNECT_SCOPES`, `MOLLIE_TESTMODE` to `console/.env.dist` and read them in Symfony config.
- Add secure secrets handling: verify existing encryption service (Sodium/OpenSSL). If missing, add a `SecretsCipher` service for encrypting/decrypting OAuth refresh tokens.
- Create Doctrine entity `Integration\MollieOAuthConnection` with fields described (refresh/access tokens, scopes, org ids, testmode, capabilities) and OneToOne to `Organization` (unique index).
- Create Doctrine entity repositories for `MollieOAuthConnection` with simple finders (by org id, by mollieOrganizationId).
- Add new fields on `Organization`: `mollieAppFeeEnabled` (bool, default true) and `mollieAppFeePercent` (decimal(5,2), default 1.00) with getters/setters.
- Write Doctrine migration adding `integration_mollie_oauth_connection` table and the two new `organizations` columns with defaults.
- Extend `Project` entity: add `mollieProfileId`, `mollieCurrency`, `mollieAllowedMethods` (json), donation fields (`donationRecommendedAmounts`, `donationAllowCustom`, `donationCustomMin`, `donationCustomMax`, `donationShowRecurring`, `donationDefaultRecurring`), paying membership fields (`payingMembershipsEnabled`, `membershipAllowCustomAmount`, `membershipCustomMin`, `membershipCustomMax`, `membershipShowAutoRenew`, `membershipDefaultAutoRenew`), with getters/setters and sensible defaults.
- Write Doctrine migration to add the above `projects` columns with appropriate nullability and indexes where needed.
- Add `Donation` entity with required `organization`, required `contact`, nullable `project`, amounts/currency, status/fields listed, relations, indexes on `organization_id`, `project_id`, `contact_id`, and unique on `molliePaymentId`.
- Add repository for `Donation` with finders (by molliePaymentId, by subscriptionId+cycle if needed, by project with filters and pagination).
- Add `MembershipPlan` entity per project with fields listed and repository (find active, find by project ordered).
- Add `ContactMembership` entity with fields listed, indexes (org, project, contact, subscription id), and repository with helpers (active for contact/project, due to expire soon, by status).
- Implement value object or enum classes for donation status and membership status/interval/frequency to centralize allowed values.
- Create service `MollieOAuthClient`: token storage, `getAccessToken(Organization)`, refresh on expiry, wrapping Mollie SDK with Bearer token; support `testmode` parameter.
- Implement `MollieOAuthClient` methods: listProfiles, getCapabilities/Onboarding, createCustomer, getCustomer, createPayment, getPayment, refundPayment (placeholder), createSubscription, getSubscription, cancelSubscription, listMethods for profile.
- Add `ApplicationFeeCalculator` service: compute fee from amount and org settings; enforce Mollie maximum (Payments API reserve 0.35 + 6% rule) and minimal 0.01; determine currency using Balances (stub for now) or default to payment currency; unit tests for edge cases.
- Add `PaymentManager` service orchestrating domain flows: create one‑off donation payment (build metadata, redirectUrl, webhookUrl), create recurring donation subscription, create membership one‑off payment, create membership subscription; use idempotency keys per operation.
- Add idempotency support: pass deterministic keys (e.g., `donation:{uuid}`) via Mollie SDK header where supported; store last key on `Donation`/`ContactMembership` if useful.
- Build Console controller: Organization Mollie connect page with “Connect with Mollie” button (authorize URL generation) and status display (capabilities, org name/id, disconnect action).
- Build Console route for OAuth callback: process `code`, exchange tokens via Tokens API, persist encrypted refresh token to `MollieOAuthConnection`, store mollie org info, redirect back with success.
- Add disconnect action: revoke tokens (Revoke API), delete `MollieOAuthConnection`, show confirmation.
- Build Console Project Payments Settings page: select `mollieProfileId` from list, choose `mollieCurrency`, optional `mollieAllowedMethods`; validate presence of org connection first.
- Build form types and validators for the Project Payments Settings page; persist to `Project` on submit.
- Build Console Project Donations Settings page: fields for recommended amounts, allow custom, min/max, show recurring, default recurring; form, validation (bounds), persistence.
- Build Console Project Paying Memberships Settings page: toggle `payingMembershipsEnabled`, custom amount allow + bounds, show auto‑renew, default auto‑renew.
- Build CRUD UI for `MembershipPlan` under project: list, create, update, delete; upload image; validation; ordering.
- Implement Donations list in console: server-side pagination, filters (status, date range, contact email, project), export CSV; item view shows Mollie IDs and timeline.
- Implement Memberships list in console: filters (status, plan, expiring soon), actions per item (cancel Mollie subscription if origin=mollie, manual extend if origin=manual), export CSV.
- Add Public Donations controller and routes (`/donate`): GET shows form with amounts (from project settings), POST validates inputs, creates Contact (or links existing), creates Donation row (pending), calls `PaymentManager` to create Mollie Payment (with applicationFee), redirects to checkout.
- Add Public Donations thank-you route: displays success message and shows contact management link; ensures no sensitive data leaked.
- Add Public Memberships flow integration: when `payingMembershipsEnabled`, extend existing members area join/upgrade pages to include plan selection/custom amount and auto‑renew checkbox; POST creates/updates contact, triggers payment or subscription via `PaymentManager`, then redirects to checkout or confirmation.
- Implement new webhook endpoint(s) for project payments/subscriptions under `console/src/Controller/Bridge` (e.g., `POST /webhook/mollie/project/{projectUuid}/{token}`) with stateless handling.
- Implement webhook handler: verify payload via Mollie API (Get payment/subscription using OAuth), resolve domain entity via metadata/IDs, update Donation status on events; for one‑off membership payments, create/activate `ContactMembership` with computed period.
- Implement webhook handler for subscription cycles: on successful charge, extend `ContactMembership.currentPeriodEnd` accordingly and optionally log a Donation row for the cycle; on cancel, set status and end date.
- Add guards: block initiating payments if Capabilities/Onboarding do not allow payments; show link to Mollie dashboard deep link.
- Implement manual “Add donation” form in console: fields (contact, optional project, amount/currency, date, method, reference, note); persist Donation with status=paid and origin=manual.
- Implement manual “Add membership” form in console: fields (contact, project, plan optional, amount/currency, interval, start/end, autoRenew=false, origin); persist ContactMembership accordingly.
- Implement CSV importers: donations and memberships; map contacts by email/id; validate amounts/dates; dry-run preview; persist with `origin=import`.
- Implement validation constraints: ensure donation custom amount within min/max; membership custom amount within min/max; project has `mollieProfileId` before enabling paid flows.
- Implement feature gating in UI: show Donation/Paying Memberships menus only when org connected and project configured.
- Add permission checks (reuse existing project manage permissions) for accessing payments-related settings and lists.
- Write unit tests for `ApplicationFeeCalculator` (fee caps, rounding, min fee).
- Write unit tests for `MollieOAuthClient` token refresh/caching and error handling (mock SDK).
- Write unit tests for `PaymentManager` to ensure correct payloads and app fee inclusion for payments/subscriptions.
- Write functional tests for webhook controllers updating Donation/ContactMembership state transitions from simulated events.
- Write functional tests for console forms (project settings, plans CRUD) to ensure validation works and fields persist.
- Add translations for all new UI labels in `translations/` (FR/EN).
- Update navigation templates to include Donations and paying memberships management entries under project.
- Update `README.md` or `docs/` with short setup guide linking to this MOLLIE.md; add note on configuring env vars and running migrations.
- QA pass in test mode: connect sandbox org, select profile, process one‑off donation, verify webhook updates; create recurring donation, verify cycle handling.
- QA pass for paying memberships: create plan, subscribe with auto‑renew, verify membership creation and period updates; try one‑off.
- QA pass for manual entries: create manual donation and membership; verify lists and exports.
- Add basic monitoring/logging: log webhook events processed, payment/subscription errors; add Sentry breadcrumbs if available.
- Prepare production rollout checklist: confirm app developer agreement for application fees, verify scopes, confirm redirect URIs in Mollie app, and run a live small payment test.
