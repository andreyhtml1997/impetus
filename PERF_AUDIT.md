# Frontend Performance Audit (Theme: underline-store)

## Scope and constraints
- Scope: current theme directory only (`wp-content/themes/underline-store`).
- Branch target: `web-vitals`.
- No library replacement, no architecture rewrite, no plugin/core edits.
- Only low-risk, reviewable optimizations implemented.

## Runtime map (active path)
- Entry include chain: `functions.php` -> `functions/cleanup.php`, `functions/setup.php`, `functions/enqueues.php`, plus `functions/custom/*` (notably `filter.php`).
- Catalog and filter behavior is heavily driven by `functions/custom/filter.php` (`get_catalog_url`, query vars/rewrite, `front_get_products_by_filter`).
- Global frontend JS bootstrap is concentrated in `assets/js/scripts.js`.

## Prioritized findings

### P1
1. Global CSS/JS enqueue strategy had avoidable over-delivery.
- `functions/enqueues.php` loaded `jquery-ui` CSS/JS globally even when filter slider markup is absent.
- Google Fonts were loaded via blocking CSS `@import` from `assets/css/style.css`.

2. Aggressive style preload strategy could create network contention and style race conditions.
- `style_loader_tag` transformed almost all styles to preload/onload pattern.

### P2
1. Several image outputs lacked intrinsic dimensions in critical/near-critical templates.
- Header/menu media and multiple template image blocks were rendered with manual `<img>` without width/height metadata.

2. Hero video used invalid poster (`poster="#"`).
- `templates/homepage-videobanner.php` could trigger invalid behavior and was not LCP-friendly.

### P3
1. Duplicate preconnect hints for Google Fonts were emitted from multiple places.
- In both template head markup and enqueue logic.

2. Product-card thumbnail fetch priority path was prepared but not fully applied.
- `templates/product-item-by-filter.php` built attrs but did not pass all of them consistently.

## What was implemented in this run (safe)
- Conditional `jquery-ui` load for catalog context only.
- Fonts moved from CSS `@import` to enqueue handle.
- Resource hints deduplicated and centralized.
- Style preload narrowed to non-critical handles only.
- Removed duplicated manual Google Fonts preconnect from `header.php`.
- Replaced several manual image tags with `wp_get_attachment_image(...)` (intrinsic sizing and responsive attrs).
- Corrected hero video poster handling and preload mode.

## Intentionally left unimplemented (risk-managed)
1. Deep refactor of `functions/custom/filter.php` query architecture (`get_products`, large in-PHP filtering loops).
- High impact and medium/high regression risk without broader staging and profiling.

2. Plugin/server/site-root changes.
- Service worker, cache server config, CDN/header tuning, object-cache strategy outside theme are out of scope for this run.

3. Tracking/payment flow rewrites.
- Pixel/eSputnik/NovaPoshta/checkout logic was not changed beyond safe frontend guards.
