# Cata Co-Authors Plus

Extends [Co-Authors Plus](https://github.com/Automattic/Co-Authors-Plus/) (CAP) with common functions, configuration and compatibility fixes for use in Cata child themes. Not a fork or replacement for CAP — it must be installed and active alongside it.

Requires PHP 8.1+.

## Features

### Block editor support for Guest Authors

Edit a Guest Author profile with the block editor after initial setup of the author's meta data in the classic editor. Opt in from a theme:

```php
add_filter( 'cata_cap_use_block_editor', '__return_true' );
```

A Guest Author starts in the classic editor. Once published with a `cap-user_login` slug, the block editor takes over so the profile can be composed with blocks. A sidebar panel links to the author's archive page.

### Guest Author profile fields

- Removes the outdated AIM, Yahoo IM and Jabber contact fields.
- Adds Instagram, TikTok, Twitter and Tagline fields.

### REST API

- Registers CAP's `guest-author` post type and `author` taxonomy with custom controllers. Reading requires the capability to manage Guest Authors (`embed` context is open).
- Registers `cap-*` meta for the API, so Guest Authors can be created and updated programmatically.
- Allows authorized users to filter Guest Authors by email: `?meta_key=cap-user_email&meta_value=...`.
- Adds `profile`, `tagline` and `social_links` data to `/wp/v2/coauthors` responses. Social URLs are built from the profile's contact fields, accepting either a full URL or a bare username.

### Blocks

For author archive templates, using the `co-authors-plus/author` context from [CAP's blocks](https://github.com/Automattic/Co-Authors-Plus/tree/trunk/php/blocks):

- **Co-Author Content** (`cata-cap/content`) — the author's block editor profile content.
- **Co-Author Tagline** (`cata-cap/tagline`) — the author's tagline.
- **Social links** — a core Social Link block with no URL of its own automatically uses the author's profile URLs.

### Original author tracking

Records the user who created a post in `_original_author` meta before CAP reassigns `post_author`, and exposes `original_author_name` on `/wp/v2/posts`. Backfill existing posts from their earliest revision:

```
wp cata-cap backfill-original-author [--dry-run]
```

### Compatibility fixes

- **Jetpack** — skips Guest Author avatars in the image sitemap; their parent-post URLs 404 and cause Search Console errors.
- **WordPress VIP** — restores the `imageDimensions` editor setting so theme image sizes work in the Co-Author Featured Image block.
- **Web Stories** — removes `web-story` from CAP's supported post types.
- **oEmbed** — uses the first co-author as the author in oEmbed responses.

### Template functions

Helpers prefixed `cata_cap_`, including `cata_cap_is_guest_author()`, `cata_cap_has_queried_coauthor()`, `cata_cap_get_the_coauthor_meta()`, `cata_cap_queried_coauthor_has_post_content()` and `cata_cap_get_author_social_services()`. See [includes/global-functions.php](includes/global-functions.php). CAP's own template tags are enabled via `coauthors_auto_apply_template_tags`.

## Hooks

| Hook | Purpose |
| --- | --- |
| `cata_cap_use_block_editor` | Opt in to block editor support for Guest Authors. Default `false`. |
| `cata_cap_get_social_services` | Modify the social services used to build social links. |
| `cata-cap/tagline_block_wrapper_attributes` | Modify the Tagline block's wrapper attributes. |

## Development

Build the editor script and blocks with `@wordpress/scripts`:

```
npm install
npm run build                       # plugin editor script (src/ → build/)
npm run build --prefix blocks/tagline
npm run build --prefix blocks/content
```

Lint PHP with the [VIP coding standards](https://github.com/Automattic/VIP-Coding-Standards):

```
composer install
./vendor/bin/phpcs
```
