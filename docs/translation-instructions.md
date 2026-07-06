# Translation Instructions

## Recommended Plugin

Use Polylang or WPML.

Default language:

- English

Additional languages:

- French
- Spanish

Recommended URLs:

- `/en/`
- `/fr/`
- `/es/`

## Theme And Plugin Text

The `limbenet` theme uses the `limbenet` text domain.

The `limbenet-core` plugin uses the `limbenet-core` text domain.

Frontend text is wrapped in WordPress i18n functions where PHP renders the output. Block templates call PHP patterns and plugin shortcodes so navigation, CTAs, forms, safety labels, and search UI can be translated.

## Polylang Setup

1. Install Polylang.
2. Add English, French, and Spanish.
3. Set English as the default language.
4. Choose directory-style URLs.
5. Translate pages created by the seed importer.
6. Translate tourism post types and taxonomy terms.
7. Confirm the header and footer language switchers appear.

## WPML Setup

1. Install WPML core and String Translation.
2. Add English, French, and Spanish.
3. Set English as default.
4. Enable translation for custom post types and taxonomies.
5. Scan the `limbenet` theme and `limbenet-core` plugin for strings.
6. Translate settings text, page content, and tourism entries.

## Content Rules

Do not translate placeholder verification notes into confirmed claims. Keep meanings such as `Needs verification` and `Price not yet verified` intact until the information is confirmed.
