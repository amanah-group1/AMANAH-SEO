# AMANAH SEO Plugin — Baseline and Edit Starting Point

**Status:** Baseline marker only; this file does not certify that the ZIP has been fully synchronized to `main`.

## Baseline
- Source of truth for the next implementation/audit pass: the user-provided `plugin.zip` reviewed on 2026-10-15.
- Target repository: `amanahgroup123/AMANAH-SEO`, branch `main`.
- ZIP inventory at initial inspection: 381 files under the plugin package (Core + AI runtime + ENGINE runtime).
- No local WordPress runtime, database, API configuration, or license records were changed as part of the read-only comparison.

## First edit/audit checkpoint
1. Compare the ZIP versions of `amanah-seo.php`, `includes/class-amanah-seo-admin.php`, and `includes/class-amanah-seo-activator.php` against `main`.
2. Trace the Entity Profile route from menu registration through page rendering and capability checks. The previously observed symptom was `Sorry, you are not allowed to access this page` when opening an existing entity record.
3. Verify Core/AI/ENGINE menu ownership and ordering before changing routing or capabilities.
4. Run PHP lint and targeted regression checks after code changes; then review actual WordPress staging behavior before declaring the issue fixed.

## Synchronization rule
Treat the supplied ZIP as the intended plugin baseline. Do not delete unrelated repository files or alter product decisions while synchronizing. Record path-level changes and test evidence. This marker is a starting checkpoint, not proof that synchronization or runtime validation has passed.
