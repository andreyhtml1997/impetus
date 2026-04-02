# Performance Changes Implemented

## Branch and safety notes
- Implemented on branch: `web-vitals`.
- Pre-existing local modification in `functions/custom/meta-single-product.php` was preserved and not touched by this optimization set.

## Changed files
- `functions/enqueues.php`
- `assets/css/style.css`
- `header.php`
- `footer.php`
- `templates/homepage-videobanner.php`
- `templates/product-item-by-filter.php`
- `templates/homepage-about.php`
- `templates/homepage-popular-categories.php`
- `templates/homepage-subscribe.php`
- `page-catalog.php`
- `page-catalog-new.php`
- `taxonomy-product_category.php`
- `single-product.php`

## What changed and expected impact

1. `functions/enqueues.php`
- Added `mw_is_catalog_context()` and loaded `jquery-ui` CSS/JS only for catalog context.
- Added Google Fonts enqueue handle and removed reliance on CSS `@import`.
- Consolidated preconnect hints with deduplication.
- Narrowed style preload conversion to selected non-critical handles.
Expected impact: less global asset transfer, reduced main-thread pressure, better script/style scheduling.

2. `assets/css/style.css`
- Removed top-level Google Fonts `@import`.
Expected impact: lower render-blocking cost from CSS import chain.

3. `header.php`
- Removed duplicate Google Fonts preconnect tags.
- Added intrinsic dimensions to desktop logo.
- Replaced manual audience dropdown image tag with `wp_get_attachment_image(...)`.
Expected impact: lower head duplication and improved layout stability for header assets.

4. `footer.php`
- Added intrinsic dimensions for key SVG images (logo, visa, mastercard, esfirum).
Expected impact: reduced CLS risk in footer render.

5. `templates/homepage-videobanner.php`
- Replaced invalid `poster="#"` with optional real poster field handling.
- Added `preload="metadata"` to hero video.
Expected impact: safer hero video behavior and cleaner initial media loading.

6. Image-rendering templates
- Replaced manual image markup with `wp_get_attachment_image(...)` in:
  - `templates/product-item-by-filter.php`
  - `templates/homepage-about.php`
  - `templates/homepage-popular-categories.php`
  - `templates/homepage-subscribe.php`
  - `page-catalog.php`
  - `page-catalog-new.php`
  - `taxonomy-product_category.php`
  - `single-product.php`
Expected impact: better intrinsic size metadata, improved CLS profile, consistent responsive attributes.

## Manual verification checklist
1. Home page: hero section renders correctly, no video poster errors.
2. Catalog pages: filter slider works, `jquery-ui` is loaded there, product cards and image sliders still work.
3. Non-catalog pages: no `jquery-ui` requests, no console errors from missing slider APIs.
4. Product page: gallery, thumbs, fancybox, sticky blocks, and add-to-cart interactions still work.
5. Header/footer: logos and icons render with expected dimensions; no visual jumps on load.
6. Console/network: script/style requests align with context (catalog vs non-catalog) and no new frontend errors.
