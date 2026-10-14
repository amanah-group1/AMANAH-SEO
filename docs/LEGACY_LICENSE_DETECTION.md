# Legacy License Detection — Safe Handling

## Decision

The Core plugin must not silently import a legacy AI license secret from the historical `amanah_license` table. A row in that table is historical data, not proof that the current site has an official, valid entitlement.

## Behavior

- Read the encrypted Core option first.
- If no current AI key is configured, detect only whether a legacy row exists for the current WordPress user.
- The detection query reads the row ID only; it never selects or logs the secret.
- If an older release already copied the legacy key into encrypted Core storage, that key is quarantined while the legacy row exists unless Core has an explicit-save provenance marker. Its encrypted value is retained; detection does not delete or rewrite it.
- Saving a key through Settings & Access records explicit provenance and then follows the normal licensing-service validation flow.
- Do not migrate, overwrite, delete, or otherwise mutate the historical row or database during detection.
- Show `legacy_detected_not_imported` and a clear notice when a legacy record exists but no Core key is configured.
- The user must explicitly provide an official key in Settings & Access. Entitlements remain denied until the licensing service validates that key.
- If no current key and no legacy row exist, show `not_configured`; do not reuse a stale `invalid` status as if a key were configured.

## Validation

Run:

```sh
php -l plugin/includes/class-amanah-seo-module-licenses.php
php tests/module-licensing-regression.php
php tests/ai-license-entitlement-regression.php
php tests/legacy-license-detection-regression.php
```

These automated checks do not replace a read-only verification on WordPress staging. No production database changes are part of this change.
