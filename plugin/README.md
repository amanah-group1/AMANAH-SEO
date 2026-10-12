# AMANAH SEO Core — MVP foundation

This is the first runtime plugin shell for the AMANAH SEO product.

## Scope in this package
- A single WordPress plugin entry point.
- An administrator-only dashboard, findings, tasks, and event history.
- Manual findings include source, evidence/reason, priority, and uncertainty.
- Tasks include owner, success criteria, status, and a link to a finding.
- Status changes and key actions are recorded in an event table.
- Activation creates only tables owned by AMANAH SEO Core.

## Explicitly not included
- AI or ENGINE source is not loaded or migrated.
- No legacy module activation/install/uninstall hooks run.
- No GSC connection or search metrics are claimed.
- No automatic changes or publication to the website.
- No licensing server integration is claimed. Core activation remains free; module-license UI/API must be added only after the licensing contract audit is complete.

## Install for controlled testing
Copy the `plugin` directory into a clean WordPress test site's `wp-content/plugins/amanah-seo` directory, then activate **AMANAH SEO** in wp-admin. Do not replace another plugin directory and do not test on production. On activation, only the three `wp_amanah_seo_*` tables (with the site's actual table prefix) are created/updated through `dbDelta`; no existing AI/ENGINE tables are intentionally touched.

## Validation
The GitHub Actions workflow runs `php -l` over the plugin PHP files. This is a syntax gate only, not a substitute for WordPress runtime acceptance, capability/nonce testing, database backup/restore drills, or migration validation.

## Known limitations before MVP acceptance
- Website profile currently reports the WordPress site on which this plugin is installed; remote-site management is not implemented.
- GSC integration, verified source ingestion, findings editing/resolution, and remote-site management remain unfinished.
- Approval decisions are recorded, but they only change workflow state and do not execute website changes. Completion requires evidence text, and a task with pending approval cannot be marked complete.
- License management for AI and ENGINE remains unimplemented pending verified product/server contracts.
