# AI License Activation and Entitlement Integration

Status: implementation checkpoint; not production activation approval.

## Verified upstream service contract

The existing AI licensing client in `amanahgroup123/AMANAH-SEO-AI`, branch `controlled/licensing-004-007-e2e-validation`, defines:
- Activation endpoint: `POST https://api.amanahseoai.my.id/v1/activate`.
- Revalidation endpoint: `POST https://api.amanahseoai.my.id/v1/validate`.
- Signed request fields: `request_id`, Unix `timestamp`, `license_key`, HMAC-SHA256 `signature`, `product_id`, persistent `site_id`, and site `domain`.
- Signature payload: request ID, timestamp, uppercased endpoint path, and site ID separated by newline characters; the license key is the HMAC secret.
- Success response contract: `success: true` and a `data` object containing `status` and `entitlements`; error responses use `success: false` and an `error.code`.
- Established entitlement state accepts `ACTIVE` and `GRACE`; expiry and bounded grace state must still be enforced.
- Existing source explicitly uses `content_analyzer` for the Content Analyzer capability.

Reference source: `plugin/modules/licensing/class-license-client.php` (blob `430d4f7331299876430b2283ba12bf8fa08af618`) and `plugin/modules/licensing/class-license-entitlement.php` (blob `b4d3924ddc077347c6c7d5956f3dd831acc7f79f`).

## Core integration behavior

- The API key remains encrypted in Core-owned module-license storage; Core is not license-gated.
- The endpoint host is pinned to the existing licensing service, redirects are disabled, and raw service error messages and API keys are not logged or shown to users.
- A new key uses `/v1/activate`; saving the same key again uses `/v1/validate`.
- The service response is schema-checked before status is stored. Known rejection codes map to non-entitled states; malformed responses and transport errors fail closed.
- AI execution passes through one central gate in `AMANAH_SEO_AI_AI_Client::generate()`. Unknown tasks, missing entitlements, and non-active states are denied before a provider is called.
- A stale status is revalidated when an AI feature is invoked after the 24-hour validation interval. Existing valid access may use the upstream-compatible grace window for up to seven days on transient service failure; known license rejection never receives grace fallback.
- The AI feature-to-entitlement map is centralized in `required_entitlement_for_task()`. The existing upstream code proves `content_analyzer`; any additional task entitlement codes must match the licensing service's configured entitlement identifiers.

## Automated evidence

Verified on code commit `45e5b1a3df6760daf4e3dfcce982c96e7e8ed391`:
- Core regression workflow: PASS on PHP 8.1 and 8.2.
- AI activation/entitlement regression: 19 checks, 0 failures, including signed request fields, valid/invalid responses, network failure, grace compatibility, encrypted key storage, stale revalidation, and denied AI execution without entitlement.
- Module integration regression: 24 checks, 0 failures.
- Module licensing storage regression: 5 checks, 0 failures.
- ENGINE PHPUnit: 105 tests, 317 assertions, 0 failures/errors.
- Core PHP syntax workflow: PASS.
- WordPress + MySQL activation/reactivation smoke: PASS twice, including the mocked v1 licensing API contract and persistence of the validated AI entitlement.

## Remaining release gate

CI uses a deterministic mocked licensing response; it does not prove that a real customer key is accepted by the live external service. Before release, validate through AMANAH SEO Settings & Access on staging with a legitimate AI license key and confirm the service status/entitlement, without placing the key in logs or source control. Do not copy this branch to the active runtime until that live-service check is confirmed.
