# Quang Đăng Clinic: WordPress theme

Website for **Phòng khám Da liễu Thẩm mỹ Quang Đăng** (Qbe+ Quang Đăng, Viện thẩm mỹ quốc tế), Quỳnh Lưu, Nghệ An. It is an SEO blog with service pages, a skin quiz and booking.

- `docs/DESIGN-PLAN.md`: the design plan (audience, sitemap, mega menu, flows, screen specs, SEO).
- `docs/THEME-GUIDE.md`: conventions for building screens.
- `docs/REDESIGN.md`: status, decisions and gates of the current redesign.
- `docs/IMAGE-PROMPTS.md`: every image the site uses, with a ready-to-paste GPT prompt and the exact file name.

## Install on WordPress hosting

1. Zip the theme folder: `npm run zip` (or zip `quangdang/` by hand), producing `quangdang.zip`.
2. In WordPress, go to Appearance → Themes → Add New → Upload, then activate it.
3. Settings → Permalinks → choose **Post name** and save.
4. Appearance → Customize → **Thông tin phòng khám**: fill in the real hotline, Zalo, Facebook, Google Maps link and licence number. Under **Site Identity**, upload the official logo.
5. Create the pages and services (see the sitemap in the design plan). Under Appearance → Menus, assign a menu to **Menu chính (mega menu)**. Until you do, the built-in menu mirrors the spreadsheet.

### Alternative: deploy with cPanel Git Version Control

1. cPanel → Git Version Control → Create. Clone URL `https://github.com/mesleepingallday/qd-spa-new.git` (or the SSH URL plus a read-only deploy key in GitHub if the repo is private). Repository Path `repositories/qd-spa-new`.
2. Manage → Basic Information → Checked-Out Branch: `main`.
3. `.cpanel.yml` copies `quangdang/` into `/home/vientha8/public_html/wp/wp-content/themes/quangdang-main` (the folder of the active theme). If WordPress lives in another folder (the one containing `wp-config.php`), edit `THEME_DIR` there.
4. Manage → Pull or Deploy → Update from Remote, then Deploy HEAD Commit. Do not edit files inside the clone; cPanel needs a clean working tree.

Recommended plugins: Rank Math **or** Yoast (titles, sitemap). The theme detects them and only adds the medical schema they don't cover.

## Local development

Needs PHP 8.1+ with pdo_sqlite, and Node 18+ (screenshots only).

```bash
npm install                 # playwright, for screenshots
npm run setup               # WordPress + SQLite + demo content → .wp/
npm run serve               # http://localhost:8080  (admin / admin)
npm run shots -- --full / /dich-vu/cham-soc-da/triet-long/ mega drawer
python3 dev/images.py       # regenerate image placeholders + docs/IMAGE-PROMPTS.md
python3 dev/icons.py        # regenerate inc/icons.php (Phosphor)
pip install fonttools brotli && python3 dev/fonts.py   # rebuild the web font
python3 dev/contrast-check.py   # colour-pair gate for the design tokens
```
