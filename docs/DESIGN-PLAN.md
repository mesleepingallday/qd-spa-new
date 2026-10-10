# Quang Đăng website: design plan (v0.1)

**Phòng khám Da liễu Thẩm mỹ Quang Đăng** (logo: *Qbe+ Quang Đăng, Viện thẩm mỹ quốc tế*)
Tầng 5, TTTM Đức Tài – Tâm Đạt, Quỳnh Lưu, Nghệ An.

Stack: a custom WordPress theme (`quangdang/`), classic PHP templates plus `theme.json`.
Staff edit content in the normal block editor (Gutenberg). There is no page builder.
The theme uploads as a `.zip` to any WordPress hosting.

---

## 1. Brief

| | | Source |
| --- | --- | --- |
| Product | SEO blog plus showcase for a cosmetic-dermatology clinic. Its goal is to turn readers and searchers into **bookings, calls and Zalo chats** | user |
| Audience | Local people (Quỳnh Lưu and the rest of Nghệ An), middle-aged adults, college students, and clinic advisors (consultants who use the site to show customers services and prices) | user / *guess for "advisors"* |
| Main jobs | 1) Find the right treatment for my skin problem. 2) Trust the clinic (doctor, equipment, real results, price). 3) Book, call or Zalo in under a minute | user + convention |
| Platform | Mostly **mobile** (Facebook and Zalo traffic, local search), and desktop for advisors and office staff | guess: typical for local VN clinics |
| Differentiator | A medical clinic (doctor-led, medical-standard process), not a spa. The site has to show that on every service page: doctor, process and equipment | fanpage images |

## 2. Design principles

1. **Every element earns its place.** A section stays only if it answers a new question for the visitor: what can I get help with, who will help me, what happens next.
2. **Readable for a 50-year-old on a cheap phone.** Body text 17–18px, contrast ≥ 4.5:1, tap targets ≥ 44px, no auto-playing carousels, no text baked into images.
3. **One obvious next step per screen.** "Đặt lịch" is the only filled teal button, on every page, including concern-coloured ones.
4. **Bright, precise, human.** Large flat surfaces, strong type, real faces. One concentrated multicolour moment (the concern directory); everything else is white, mist, ink and teal.
5. **Content is the SEO engine.** Every service links to related articles, and every article links to its service.

The full redesign brief and its status live in `docs/REDESIGN.md`.

## 3. Brand system

The logo and teal brand are unchanged. Tokens live in `assets/css/main.css` (§1, "Tokens v2") and are checked by `python3 dev/contrast-check.py`.

| Token | Hex | Use |
| --- | --- | --- |
| `--ink` | `#0E2A2F` | Text (15.1:1 on white) |
| `--muted` | `#4B6468` | Secondary text (6.3:1 on white, 5.9:1 on mist) |
| `--paper` / `--mist` | `#FFFFFF` / `#F2F7F7` | Surfaces |
| `--teal` | `#087A82` | Buttons and links (white text 5.1:1); hover `--teal-hover` `#066A72` |
| `--teal-bright` | `#19C6BE` | One flat feature surface per page (appointment panel); ink text 7.1:1 |
| `--teal-deep` | `#052E33` | Footer and one optional dark section |
| `--sun` | `#FFC93C` | Genuine offers only; ink text 9.8:1 |

**Concern colours.** Each of the 8 concerns has `--concern-tile` (fill behind a white glyph, ≥ 3:1), `--concern-ink` (text on its own tint, ≥ 4.5:1) and `--concern-tint` (quiet surface), set by `data-concern="{key}"` (keys from `qd_concerns()`). Hue always comes with the concern's visible name and glyph: colour never carries the meaning alone. Hues never recolour actions, errors or validation.

| Concern | Tile | Ink | Tint |
| --- | --- | --- | --- |
| Mụn | `#FF452C` | `#CF1900` | `#FFECEA` |
| Thâm | `#D76F00` | `#A45500` | `#FBF1E6` |
| Nám – tàn nhang | `#FF3D74` | `#D2003C` | `#FFECF1` |
| Sẹo | `#9574F9` | `#7145F7` | `#F4F1FE` |
| Xóa xăm | `#6082FF` | `#2957FF` | `#EFF2FF` |
| Triệt lông | `#0A94CB` | `#08719A` | `#E6F4FA` |
| Trẻ hóa da | `#219E62` | `#19794B` | `#E9F5EF` |
| Filler – Botox | `#D755D7` | `#B22AB2` | `#FBEEFB` |

**Type.** One family: **Quang Dang Sans**, a Vietnamese + Latin subset of Google Sans Flex (SIL OFL), one variable file (`opsz` 12–80, `wght` 400–700), built by `dev/fonts.py`. Google's own subset files omit the combining marks U+0302, U+0306 and U+031B, so decomposed (NFD) Vietnamese would fall back to a serif; this build keeps them. Scale: H1 64–80px desktop / 36–44px phone, H2 36–48 / 28–34, H3 22–28, body 18 / 17, line-height 1.14–1.20 for headings and 1.6 for body. Default glyph forms (no stylistic sets). Sentence case, no all-caps labels, no accented word inside a headline.

**Shape and depth.** Radius by role: photographic stage 32–40px, editorial feature 24–28px, card 18–22px, field 12–14px, pills 999px. Shadow only on overlays (menus, drawer, dialogs). Large flat colour fields, no gradient washes.

**Motion.** State changes are immediate; press feedback 140–180ms; disclosures about 200ms; no stagger, springs or scroll-reveals. `prefers-reduced-motion` removes translation and scale.

**Imagery.** Soft daylight, real Vietnamese skin tones, visible skin texture, no text inside images. **Doctors, the clinic, devices and before/after photos must be real photos** (see `IMAGE-PROMPTS.md`, section "Real photos"). The home hero currently uses a generated placeholder portrait: replace it with an approved clinic photograph before release.

**Icons.** Phosphor (MIT, inline SVG, "bold" for small UI glyphs, "duotone" for feature icons) through `qd_icon()`; templates keep their old icon names via an alias table, regenerate with `python3 dev/icons.py`. The 8 concern glyphs are original artwork (`inc/concern-glyphs.php`, 32px grid, 2px round strokes).

## 4. Information architecture

### Sitemap (from *Thông tin giao diện Website*)

```
/                                   Trang chủ
/gioi-thieu/                        GIỚI THIỆU (hub)
  qua-trinh-hinh-thanh/             Quá trình hình thành
  tam-nhin-su-menh/                 Tầm nhìn & Sứ mệnh
  doi-ngu-bac-si/                   Về đội ngũ chuyên gia bác sĩ
  cong-nghe-san-pham/               Về Công nghệ & Sản phẩm
  co-so-vat-chat/                   Về Cơ sở vật chất
  quy-trinh-chuan-y-khoa/           Về Quy trình chuẩn y khoa
/dich-vu/                           DỊCH VỤ (all services, browse by concern)
  ── Chăm sóc và điều trị da
     ── Điều trị da
        dieu-tri-mun/   → tri-mun-khang-khuan-da-tang, tri-tham-sau-mun, tri-mun-viem-o-lung, tri-mun-viem-o-tay-chan
        dieu-tri-tham/  → tri-tham-nach, tri-tham-mat, tri-tham-ben-mong, tri-tham-vung-kin
        dieu-tri-nam/   → dieu-tri-nam-chuyen-sau, dieu-tri-tan-nhang
        dieu-tri-seo/   → tri-seo-loi, tri-seo-lom
        xoa-xam/        → xoa-xam-long-may, xoa-xam-mi-mat, xoa-xam-tattoo
     cham-soc-da/       → tre-hoa-da-cong-nghe-cao, phuc-hoi-da, triet-long, xoa-not-ruoi, kiem-dau-tre-hoa-laser-picosure
  noi-khoa-tham-my/     → filler, botox, cang-chi, meso
/tin-tuc/                           TIN TỨC
  cam-nang-lam-dep/                 Cẩm nang làm đẹp (category)
  su-kien-uu-dai/                   Sự kiện – Ưu đãi (category)
/{post-slug}/                       article
/dao-tao/                           ĐÀO TẠO
  khoa-hoc-cham-soc-da-co-ban/
  khoa-hoc-cham-soc-da-chuyen-sau/
── not in the menu, but needed by the flows ──
/tim-lieu-trinh/                    Skin quiz (onboarding)
/dat-lich/                          Booking
/lien-he/                           Contact, map, opening hours
```

### WordPress content model

| Content | WP type | Why |
| --- | --- | --- |
| Services | **Hierarchical CPT `dich-vu`**: a category (Điều trị mụn) is a parent post and services are its children | Gives the nested URLs above for free. Staff manage it like Pages |
| Service facts | Post meta: price from, sessions, minutes per session, downtime, "suits", FAQ list | Shown on cards, in the facts row and in the price table |
| Articles | Posts, categories `cam-nang-lam-dep` and `su-kien-uu-dai` | Standard and SEO-plugin friendly (Rank Math or Yoast) |
| Promotion dates | Post meta `start`/`end` on posts in Sự kiện – Ưu đãi | Shows status badges such as "Còn 5 ngày" or "Đã kết thúc" |
| Courses | CPT `khoa-hoc` (archive at `/dao-tao/`) | Curriculum, schedule, fee |
| Doctors | CPT `bac-si` | Reused on the About, service and article pages ("Tham vấn y khoa") |
| About pages | Pages with page templates | One template per About subpage |
| Bookings | Private CPT `lich-hen` plus an email to the clinic | The clinic gets an admin inbox with no plugin |
| Menus | WP menu `primary` drives the mega menu (CSS classes on items set the columns). A built-in fallback mirrors the spreadsheet | Staff can rename and reorder in Appearance → Menus |

### Mega menu

**Desktop (≥ 1024px)**
- No utility bar: the address and hours live in the footer, the contact page, the booking strip and the phone menu sheet.
- **Header** (72px, solid white with an optional light blur, sticky): logo, then the nav (Giới thiệu, Dịch vụ, Tin tức, Đào tạo), then search, hotline and **[Đặt lịch]**. A parent item is a real link plus a separate disclosure button (the link works without JavaScript).
- **Dịch vụ** opens a full-width panel:
  ```
  ┌ CHĂM SÓC VÀ ĐIỀU TRỊ DA ─────────────────────────────────────┬ NỘI KHOA THẨM MỸ ─┐
  │ Điều trị da                                                  │ Dịch vụ Filler     │
  │ Điều trị mụn →   Điều trị thâm →   Điều trị nám →  Xóa xăm → │ Dịch vụ Botox      │
  │  · 4 items         · 4 items         · 2 items       · 3     │ Dịch vụ Căng chỉ   │
  │                                     Điều trị sẹo →           │ Dịch vụ Meso       │
  │                                      · 2 items               │ ┌────────────────┐ │
  │ Chăm sóc da                                                  │ │ Chưa biết chọn?│ │
  │  · 5 items in 3 columns                                      │ │ Kiểm tra da 1′ │ │
  │                                                              │ └────────────────┘ │
  └──────────────────────────────────────────────────────────────┴────────────────────┘
  ```
  All 24 services are visible at once with no hover-tabs, which is easier for older users to scan. The help card is the entry point to the onboarding quiz for undecided visitors.
- **Giới thiệu, Tin tức and Đào tạo** open compact dropdowns: each link carries a one-line description (from the WP menu "Description" field).
- The menu opens on hover with a 150ms intent delay, and also on click and keyboard (Enter/Space/Esc/arrow keys). Each trigger has `aria-expanded`.

**Mobile**
- The header is 60px: menu button, logo, then a search icon.
- The **drawer slides in and drills down** instead of nesting accordions, because the services tree is 4 levels deep:
  `Dịch vụ ›` → panel with the headers *Điều trị da* (5 rows), *Chăm sóc da* (5 services) and *Nội khoa thẩm mỹ* (4 services) → `Điều trị mụn ›` → "Xem tất cả" plus 4 services. It never goes more than 3 taps deep.
- The drawer footer holds the hotline, Zalo, Đặt lịch and the address.
- A **sticky action bar** sits at the bottom of every page: `[📞 Gọi] [Zalo] [Đặt lịch ───]`. Local and middle-aged users call or Zalo far more than they fill in forms.

## 5. What each screen is for

| Screen | Visitor comes to… | They decide with… | Final action | Common practice for this kind of site *(from memory, worth checking)* |
| --- | --- | --- | --- | --- |
| Home | Check "is this place right for my problem?" | Concern shortcuts, doctor, real results, price-from, address | Pick a concern, book, or take the quiz | Hero plus concern grid near the top, trust signals before the fold ends, booking strip near the bottom |
| Services hub `/dich-vu/` | See everything the clinic does | Concern grouping, price-from, sessions | Open a category or a service | Grouped grid, not one long list |
| Category (e.g. Điều trị mụn) | Compare treatments for one problem | Who it suits, sessions, price-from, downtime | Open a service | Cards that put "suits / price / sessions" first. The education block underneath carries the SEO |
| Service detail | Decide "is this for me and how much?" | Suits / doesn't suit, process, price table, results, doctor, FAQ | **Book** (sticky card or bar) | Facts row up top, sticky booking card, FAQ with schema markup |
| Blog list | Learn about a problem | Topic, freshness, reading time | Read | Featured post plus grid, topic filters, real pagination (no infinite scroll, for SEO) |
| Article | Get an answer | Clear headings, medical reviewer | Read on, go to the linked service, book | Table of contents, "Tham vấn y khoa: BS…", an inline service card mid-article, related posts |
| Promotions | Find a deal | Discount, end date, conditions | Book with the offer pre-filled | Status badge and countdown, conditions spelled out |
| Quiz (onboarding) | "I don't know what I need" | 4 short questions | See 1–3 recommended services, then book | Chips with icons, one question per screen, progress bar |
| Booking | Get an appointment | Service, day, time slot | Submit, then see the confirmation with directions | Name and phone only are required. Time slots, not a free-form date-time field |
| About pages | Build trust | Doctor credentials, equipment, facility photos | Book or read services | Real photos, timeline, process steps |
| Training | Join a course | Curriculum, schedule, fee, certificate | Register | Course card with format, length and fee |
| Contact | Find the clinic | Map, floor in the mall, opening hours | Directions, call | Map plus a "Chỉ đường" button opening Google Maps |

## 6. Key flows

**A. First visit and onboarding.** There are no pop-ups. A modal on first visit annoys people and hurts Core Web Vitals. Onboarding happens on the page itself:
1. Home: the concern directory, *"Bạn cần hỗ trợ về vấn đề nào?"*, shows 8 coloured tiles (Mụn, Thâm, Nám – tàn nhang, Sẹo, Xóa xăm, Triệt lông, Trẻ hóa da, Filler – Botox). Each goes straight to its category.
2. A secondary link, *"Tìm dịch vụ phù hợp"* (hero and under the directory), leads to `/tim-lieu-trinh/`.
3. **The quiz asks 4 steps, one question per screen, with a progress bar and a back button:**
   1. Main concern (multi-select, the same concern tiles as the home directory, on real checkboxes)
   2. Area (face, back, underarm, arms/legs, bikini), shown only when it applies
   3. Skin type (oily, dry, combination, sensitive, "not sure")
   4. Priority (budget, fast results, no downtime)
4. The result screen shows 1–3 recommended services, each with a reason ("Vì bạn chọn: mụn viêm + da dầu"), its price-from and the number of sessions. It is titled *"Dịch vụ bạn có thể quan tâm"* (information, not a prescription: a doctor confirms the plan) and offers **[Đặt lịch tư vấn miễn phí]** (the service is pre-filled) and 2 articles to read. A disclaimer notes that the doctor confirms the plan at the visit.
5. The answers are saved in `localStorage`, so the booking form is pre-filled and the quiz offers to show the last result again.

**B. Booking** (`/dat-lich/`, and a compact version on service pages):
1. Service: pre-filled from the page or quiz, or "Chưa biết – cần bác sĩ tư vấn".
2. Day (the next 14 days as chips) and time slot (Sáng / Chiều / Tối chips).
3. Full name and phone number (required, numeric keypad, Vietnamese phone validation), plus an optional note.
4. **Confirmation:** a summary, "Chúng tôi sẽ gọi xác nhận trong 15 phút (giờ làm việc)", then [Chỉ đường] [Nhắn Zalo] [Thêm vào lịch].

**C. Reading to booking.** A Google search lands on an article. Mid-article there is an inline service card ("Điều trị nám chuyên sâu – từ 1.500.000đ"). The visitor goes on to the service page and books from the sticky card or bar.

## 7. Screen specs (section order)

**Home** (each section answers a new question; see `template-parts/home/`)
1. Announcement (optional, dismissible, quiet strip).
2. Hero: H1 *"Làn da khỏe đẹp, điều trị chuẩn y khoa"*, one supporting line, **[Đặt lịch khám]** (desktop; on phones the action bar carries it) and *Tìm dịch vụ phù hợp*, and a photograph. On phones: copy first, then the photograph; the phone hero offers *Chọn vấn đề của bạn* (jumps to the directory) instead of a second booking button.
3. **Concern directory**, *"Bạn cần hỗ trợ về vấn đề nào?"*: 8 labelled links (Mụn, Thâm, Nám, Sẹo, Xóa xăm, Triệt lông, Trẻ hóa, Filler / Botox), each a coloured tile with its own glyph. One row of 8 on desktop, 4 × 2 on phones. The one multicolour moment on the page.
4. **Service feature**: one category in depth (cover, intro, its first three services with sessions and price-from). Default: the first category with ≥ 3 services; override with the `qd_home_feature_category` filter.
5. Practitioner: real portrait, name, role, one factual line, link to the profile. Hidden without a doctor.
6. Process: the 5 first-visit steps (the only numbered list: it is a real sequence). Vertical on phones, the one dark section.
7. Results: a curved carousel of the clinic's square before/after posters, managed in wp-admin → Kết quả khách hàng (poster, service, caption, the poster's points as text, consent). Only cases marked with written consent render; the section is hidden when there are none.
8. Guidance: one featured article plus rows.
9. Offers: only when a promotion is running (sun badge, ink text).
10. Appointment: one flat `--teal-bright` panel with the booking form; the action bar hides while it is on screen.

**Service detail.** Breadcrumb, H1 with a one-line promise, then the **facts row** (Giá từ · Số buổi · Thời gian/buổi · Nghỉ dưỡng). In-page tabs follow (Tổng quan · Phù hợp · Quy trình · Bảng giá · Kết quả · Hỏi đáp), sticky under the header. The sections, in order:
- Overview (editor content)
- Suits / doesn't suit (two columns)
- Technology
- Process steps
- Price table
- Results
- Before and after care
- FAQ (accordion, with FAQPage schema)
- Responsible doctor
- Related articles and related services

On desktop a **sticky booking card** sits on the right (price-from, [Đặt lịch], hotline, Zalo). On mobile the bottom bar does the same job.

**Service category.** Breadcrumb, H1 and intro, the service cards (each shows the name, a line on who it suits, sessions, price-from and "Xem chi tiết"). Below them come "Hiểu về {mụn}" (editor content for SEO), FAQ, articles and booking.

**Blog list.** H1, category tabs (Tất cả · Cẩm nang làm đẹp · Sự kiện – Ưu đãi), then topic chips. The featured post is large, followed by a 3-column grid of cards (16:9 cover, topic, 2-line title, 2-line excerpt, date and reading time) and numbered pagination.

**Article.** Breadcrumb, topic, H1, then the meta line (*Tham vấn y khoa: BS. …* · Cập nhật 08/10/2026 · 6 phút đọc) and the cover. The TOC is sticky on the left on desktop and a collapsible box on mobile. The body is set at 18px and max 68ch. An inline service card goes after the 2nd H2. The page ends with a medical disclaimer box, share buttons (Facebook, Zalo, copy link) and related posts.

## 8. SEO

- Semantic HTML with one H1 per page. Breadcrumbs carry `BreadcrumbList` schema.
- JSON-LD: `MedicalClinic` (address, geo, opening hours, phone) on every page; `MedicalProcedure`/`Service` with `Offer` on service pages; `Article` with `reviewedBy` on articles; `FAQPage` wherever there is a FAQ; `Course` on courses.
- If Rank Math or Yoast is active, the theme yields titles, meta descriptions, the sitemap and Open Graph to it, and only adds the medical schema it doesn't cover.
- Speed: no jQuery, no carousels on load, the hero image preloaded, self-hosted woff2 fonts (Vietnamese plus Latin subsets), lazy images with fixed aspect ratios (CLS 0), and about 15KB of JS in total.
- Internal links: service ↔ articles (shared tag), category ↔ services, and every page links to booking.

## 9. Accessibility for this audience

Body text is 17px on mobile and 18px in articles. Inputs are 48px tall with 16px text, so iOS doesn't zoom in. The focus ring is visible (aqua). Phone and Zalo actions are always one tap away. Copy is in plain Vietnamese with no English jargon ("Nghỉ dưỡng: Không cần", not "Downtime: 0"). `prefers-reduced-motion` is respected.

## 10. Placeholders that must be replaced before launch

- Hotline, Zalo OA link, Facebook URL, Google Maps link, the operating licence number (*Giấy phép hoạt động*) and the doctor's name and credentials all live in **Appearance → Customize → Thông tin phòng khám** (the defaults are clearly fake).
- **All prices, session counts and durations in the demo content are samples.**
- Medical advertising: service claims avoid "100%" and "vĩnh viễn". Before/after photos must be real and used with consent. Per Vietnamese regulations, service ads may need content approval from the health department.

## 11. Build plan

| Phase | Owner | Scope |
| --- | --- | --- |
| 0 | Opus (lead) | This plan, the image prompt list, and a local WP dev setup (SQLite + WP-CLI + seed data built from the spreadsheet) |
| 1 | Opus | **Foundation**: `theme.json` tokens, fonts, base CSS, components, header + mega menu + mobile drawer + action bar + footer, CPTs and meta, the homepage, and the service detail page (the reference pattern) |
| 2 | Sonnet agents, in parallel (worktrees) | A: services hub and category view. B: blog list, article, promotions, search. C: quiz, booking and REST endpoint, contact. D: About pages, doctors, training, 404 |
| 3 | Opus (advisor) | Review each branch against this plan, fix and merge, then screenshot every template at 375px and 1440px and push |
