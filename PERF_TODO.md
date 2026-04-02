# Performance TODO (Medium/High Risk or Out of Scope)

## Medium/high-risk items (not auto-implemented)

1. Rebuild catalog filter data pipeline (`functions/custom/filter.php`).
- Current `get_products()` path performs heavy in-PHP filtering/sorting after broad data fetches.
- Recommended next step: move more filtering/ordering into indexed SQL/WP_Query strategy with controlled cache keys.
- Risk: high regression potential for category/brand/material/color logic and URL sync behavior.

2. Introduce granular response caching for AJAX filter endpoints.
- Candidate endpoints: `front_get_products_by_filter` and related facet builders.
- Suggested strategy: transient/object cache keyed by normalized filter params + language + page.
- Risk: cache invalidation complexity when product meta/term meta changes.

3. Sticky subsystem simplification.
- Current sticky code is custom and repeated in multiple blocks.
- Next step: consolidate into one helper with shared guardrails and optional IntersectionObserver fallback.
- Risk: behavior regressions on product detail layouts.

4. Critical CSS extraction/inlining.
- Could improve FCP/LCP on home/catalog templates.
- Risk: maintenance burden and style drift across template variants.

## Requires plugin/server/site-root access

1. Full page cache policy tuning (exclude cart/checkout/thanks, cache catalog safely with query normalization).
2. Object cache backend (Redis/Memcached) and persistent cache invalidation policy.
3. Brotli/gzip, HTTP cache headers, image CDN variants, and TLS/session reuse tuning.
4. Media pipeline normalization (modern formats, responsive source generation policy outside theme-level template fixes).

## Optional later improvements

1. Add controlled `fetchpriority="high"` policy for true LCP image only on each key template.
2. Add field-level poster management for all autoplay videos and explicit width/height/aspect-ratio wrappers.
3. Add lightweight runtime telemetry for long tasks and user timing marks on catalog interactions.
4. Add repeatable before/after Lighthouse and Web Vitals snapshots per template type.

## Items requiring explicit approval before implementation

1. Any deep refactor of filter/business logic in `functions/custom/filter.php`.
2. Any change to tracking/payment/esputnik/NovaPoshta behavior beyond obvious low-risk bug fixes.
3. Any server-side caching or infrastructure-level optimizations outside theme repository.
4. Any architectural change that alters existing URL/query contract for catalog filters.