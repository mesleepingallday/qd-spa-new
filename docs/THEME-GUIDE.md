# Theme guide: conventions for building screens

Read `docs/DESIGN-PLAN.md` first, because it says *what* each screen is for. This file covers *how* to build it, so every screen reads as one product.

## Layout of the repo

```
quangdang/                      ← the WordPress theme (zip this folder to install)
  functions.php                 loads inc/*.php, then every inc/features/*.php
  theme.json                    color, font and size tokens (single source; CSS reads them as variables)
  inc/                          core: helpers, menu tree, content types, services, schema…
  inc/features/{area}.php       one file per area (blog, booking, about…): meta boxes, page CSS/JS, hooks
  template-parts/site/          header pieces: brand, nav, mega panel, drawer, search
  template-parts/components/    reusable cards and blocks: service-card, post-card, category-card, booking-strip, help-card
  template-parts/{area}/        sections for one screen
  assets/css/main.css           tokens + base + shared components + site chrome
  assets/css/pages/{area}.css   page-only styles, registered through the qd_page_styles filter
  assets/js/main.js             site-wide behaviour (menu, drawer, search, scroll-spy)
  assets/images/                real design images; assets/images/_placeholders/ = generated stand-ins
dev/                            local WordPress (SQLite), seed data, screenshot script
docs/                           plan, this guide, image prompts
```

## Rules

1. **Reuse before you build.** Use the existing components first:
   - Buttons: `qd_button()`, with variants primary, outline, ghost and light.
   - Building blocks: `.chip`, `.badge`, `.card`, `.frame`, `.section`, `.section-head` (`qd_section_head()`), `.accordion` (`<details>`), `.checklist`, `.steps`, `.notice`, `.field`/`.input`/`.select`, `.breadcrumb` (`qd_breadcrumbs()`), `.page-hero`, `.with-aside`, `.scroller`, `.prose`.
   - Images: `qd_thumb()` and `qd_asset_img()`. Icons: `qd_icon()` (see `inc/icons.php` for names).
   - The reference screen is `template-parts/service/detail.php` with `assets/css/pages/service.css`. Copy its patterns.
2. **One filled teal button per view** ("Đặt lịch…"). Everything else is outline, ghost or a text link.
3. **No new colors, fonts, radii or shadows.** Use the tokens in `main.css` `:root` (`--c-*`, `--r-*`, `--shadow-float`). Shadow is only for floating layers (sticky booking card, menus, dialogs). Cards get a hairline border and no shadow.
4. **Type.** Use `.page-title` (H1, serif) and `.section-title` (H2 of a page section, serif). Everything else is Be Vietnam Pro: card titles `.card__title` at 18px/600, body 17px, articles 18px (`.prose`). Never go below 13px.
5. **Each section must earn its place.** No decorative sections and no lorem ipsum. Hide a section when it has no data (see how `detail.php` guards each block).
6. **Mobile first, tested at 375px and 1440px.** There must be no horizontal scroll. Tap targets must be at least 44px. Inputs are 48px tall with 16px text. Phones already have the sticky action bar (Gọi / Zalo / Đặt lịch), so don't duplicate a full-width booking button in the first screen on phones.
7. **Copy is Vietnamese and plain** ("Nghỉ dưỡng: Không cần", not "Downtime"). Use sentence case in headings, and no ALL CAPS except the small group labels already styled.
8. **Accessibility.**
   - Use one `<h1>` per page and keep headings in order.
   - Give every section `aria-labelledby` pointing at its heading id.
   - Images get meaningful alt text, or `alt=""` when decorative.
   - Forms need a `<label>` for every field, and errors must say what to fix.
   - Respect `prefers-reduced-motion` (already global).
9. **Escaping.** Every dynamic value goes through `esc_html`, `esc_attr`, `esc_url` or `wp_kses_post`. Prefix PHP globals and functions with `qd_`.
10. **SEO.** Use real links (no JS-only navigation) and numbered pagination (`the_posts_pagination()`). Add structured data with `qd_schema_add()` and `qd_schema_faq()`.
11. **Do not edit shared files** (`main.css`, `main.js`, `inc/*.php` outside your feature file, `header.php`, `footer.php`, existing components). If you truly need a shared change, put it in your area's file and list it in your report so the lead can promote it.

## Adding a screen

- **Page by slug.** WordPress picks `page-{slug}.php` automatically. Examples: `page-dat-lich.php`, `page-lien-he.php`, `page-doi-ngu-bac-si.php`.
- **CPT archive or single.** Use `archive-khoa-hoc.php`, `single-khoa-hoc.php`, `single-bac-si.php`, and so on.
- **Posts.** `home.php` is the posts page `/tin-tuc/`. Also available: `category.php`, `single.php`, `search.php`, `404.php`.
- **Styles.** Create `assets/css/pages/{area}.css` and register it from `inc/features/{area}.php`:
  ```php
  add_filter( 'qd_page_styles', function ( $styles ) {
      $styles['qd-blog'] = array( 'assets/css/pages/blog.css', fn() => is_home() || is_singular( 'post' ) || is_archive() );
      return $styles;
  } );
  ```
- **Scripts.** Put them in `assets/js/{area}.js`, vanilla JS with no dependencies, enqueued from your feature file with `array( 'strategy' => 'defer', 'in_footer' => true )`.
- **Meta fields.** Use `qd_meta_box( $id, $title, $post_type, $fields )` (see `inc/services.php`). Meta keys are already registered in `inc/content-types.php`.
- **Demo data.** Add `dev/seed-extra/{area}.php`. It runs inside `wp eval-file` after the core seed, so WP functions and `qd_seed_post()` / `qd_seed_image()` are available.

## Local WordPress

```bash
ln -s /home/user/qd-spa-new/node_modules node_modules       # playwright for screenshots (once, in a worktree)
QD_WP_CACHE=/home/user/wpdev-cache bash dev/setup.sh 8081   # fresh site + demo content on port 8081
bash dev/serve.sh 8081 &                                     # http://localhost:8081  (admin / admin)
node dev/screenshot.mjs --base http://localhost:8081 --full /tin-tuc/ /some-post/
```

Re-run `setup.sh` whenever you change seed data. The theme folder is symlinked, so PHP, CSS and JS edits show up live. Lint with `php -l file.php` and `node --check file.js`. Look at every screenshot you take: check for overflow, wrapping, empty states and Vietnamese diacritics.

## Placeholder data

Everything in `dev/seed-data/` is a sample: prices, doctors' names and promotions. `qd_clinic()` values (hotline, Zalo…) are placeholders set in Customize → Thông tin phòng khám. Never hard-code a phone number or address; always read it through `qd_clinic()`.
