# Limbe.Net WordPress Tourism Platform

Limbe.Net is a lightweight WordPress tourism directory and destination guide for Cameroon. It is built as:

- `wp-content/themes/limbenet/`: custom block theme.
- `wp-content/plugins/limbenet-core/`: companion plugin for tourism content, forms, filters, schema, settings, and seed data.

Limbe.Net is positioned as an independent Cameroon tourism guide, not an official government portal.

## Requirements

- WordPress latest stable release.
- PHP 7.4 or newer.
- Hostinger WordPress hosting or any standard WordPress host.
- Pretty permalinks set to `Post name`.
- Recommended plugins: Polylang or WPML, Rank Math or Yoast SEO, LiteSpeed Cache, a security plugin, and Fluent Forms or Contact Form 7 if you want to replace the built-in lead forms later.

## Installation

1. Upload `dist/limbenet.zip` from `Appearance > Themes > Add New > Upload Theme`, or upload `wp-content/themes/limbenet/` by SFTP to `/wp-content/themes/limbenet/`.
2. Upload `dist/limbenet-core.zip` from `Plugins > Add New > Upload Plugin`, or upload `wp-content/plugins/limbenet-core/` by SFTP to `/wp-content/plugins/limbenet-core/`.
3. In WordPress admin, activate the `Limbe.Net` theme.
4. Activate the `Limbe.Net Core` plugin.
5. Go to `Settings > Permalinks` and choose `Post name`.
6. Go to `Limbe.Net Tourism > Settings` and configure WhatsApp, contact email, currency, affiliate disclosure, and safety disclaimer.
7. Go to `Limbe.Net Tourism > Seed Importer` and import starter content.
8. Install and configure Polylang or WPML for English, French, and Spanish.

## What The Plugin Adds

Custom post types:

- Attractions
- Destinations
- Trip Ideas
- Partners
- Deals
- Events
- Booking Requests

Taxonomies:

- Region
- City
- Attraction Type
- Travel Style
- Partner Type
- Difficulty
- Budget Range
- Safety Status

Frontend forms:

- Submit a business listing
- Request booking help
- Claim this listing
- Advertise with Limbe.Net

Submissions are stored as `Booking Requests` in WordPress admin.

## Key Shortcodes

- `[limbenet_tourism_search]`
- `[limbenet_tourism_search post_type="attraction" button_label="Filter attractions"]`
- `[limbenet_featured type="attraction" limit="6"]`
- `[limbenet_featured type="destination" limit="6"]`
- `[limbenet_travel_styles]`
- `[limbenet_plan_trip]`
- `[limbenet_ticket_help]`
- `[limbenet_travel_info]`
- `[limbenet_partner_cta]`
- `[limbenet_newsletter]`
- `[limbenet_attraction_details]`
- `[limbenet_destination_details]`
- `[limbenet_booking_form]`
- `[limbenet_partner_form]`
- `[limbenet_claim_form]`
- `[limbenet_advertise_form]`
- `[limbenet_language_switcher]`

## Multilingual Setup

Recommended URL strategy:

- `/en/`
- `/fr/`
- `/es/`

The theme and plugin use WordPress i18n functions for frontend strings. The language switcher uses Polylang when available, supports WPML permalink filtering, and falls back to `/en/`, `/fr/`, and `/es/` links.

## Safety And Ticket Policy

Do not invent ticket prices. Use `Price not yet verified.` until a reliable source is added.

Every attraction and destination includes a visible safety/advisory box with one of these labels:

- Normal travel planning
- Check current advisory before travel
- High-risk area: travel only with expert local guidance

## Compatibility Notes

- Gutenberg/block editor compatible.
- No Elementor, Divi, WPBakery, or page-builder dependency.
- WooCommerce-compatible for future ticketing and partner packages.
- Rank Math and Yoast compatible. The plugin prints basic fallback meta descriptions only when those plugins are absent.
- LiteSpeed Cache compatible: CSS and JS are minimal and dependency-free.

## Documentation

- [Hostinger deployment checklist](docs/hostinger-deployment-checklist.md)
- [Admin usage guide](docs/admin-usage-guide.md)
- [Translation instructions](docs/translation-instructions.md)
- [SEO launch checklist](docs/seo-launch-checklist.md)
- [Theme variant guide](docs/theme-variant-guide.md)

## Optional Theme Variants

Two additional standalone theme ZIPs are available in `dist/`:

- `limbenet-coastwave.zip`: bright coastal teal/coral visual direction.
- `limbenet-festivaltrail.zip`: colorful culture/highland visual direction.

Upload either ZIP from `Appearance > Themes > Add New > Upload Theme`.

## Building Upload Packages

Run `scripts/build-packages.ps1` from PowerShell to rebuild the upload-ready ZIP files in `dist/`.

The plugin package is intentionally built as a flat ZIP with `limbenet-core.php` at the archive root and forward-slash archive paths. This prevents WordPress from nesting the plugin as `limbenet-core-1/limbenet-core/limbenet-core.php` when a browser or host renames an uploaded ZIP.
