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

1. **Every element earns its place.** If a section doesn't help someone choose, trust or book, it goes.
2. **Readable for a 50-year-old on a cheap phone.** Body text 17–18px, contrast ≥ 4.5:1, tap targets ≥ 44px, no auto-playing carousels, no text baked into images.
3. **One obvious next step per screen.** "Đặt lịch" is the only filled teal button. Everything else is outline or a text link.
4. **Medical calm, spa warmth.** Teal for trust, warm ivory for softness, plenty of white space, real faces.
5. **Content is the SEO engine.** Every service links to related articles, and every article links to its service.

## 3. Brand system

Colors were sampled from the fanpage posts (*Triệt lông Laser Double Cool* and *Làm việc xuyên lễ 2/9*).

| Token | Hex | Use |
| --- | --- | --- |
| `brand-600` | `#0A727A` | Primary buttons, links, active states (white text 5.7:1 ✓) |
| `brand-700` | `#085F66` | Hover/pressed |
| `brand-900` | `#0A3639` | Footer background, headings on light backgrounds |
| `brand-500` | `#178C8B` | Fanpage teal: icons, large display text, decorative lines (do not use for body text) |
| `brand-300` | `#7CD0CF` | Fanpage aqua: highlights and focus ring, never as a text color on white |
| `brand-100` | `#DCF2F1` | Chips, icon tiles |
| `brand-50` | `#F0FAFA` | Mint section background |
| `ivory-50` | `#FFFBF5` | Warm section background (from the 2/9 poster) |
| `ivory-200` | `#F1E6D8` | Warm borders and dividers on ivory |
| `accent-600` | `#B4532A` | **Promotions only**: offer badges and sale prices (white text 5.0:1 ✓) |
| `ink` | `#14292B` | Body text |
| `ink-muted` | `#52666A` | Secondary text, meta (6.1:1 ✓) |
| `line` | `#E2ECEC` | Hairline borders |

**Type**
- **Be Vietnam Pro** for UI and body. It was designed for Vietnamese, so diacritics never collide. Weights 400, 500, 600 and 700.
- **Playfair Display** for display headings only (H1 and section titles of 28px and up). It echoes the serif in the logo. Line-height is ≥ 1.25 so stacked marks (ệ, ẫ, ổ) never touch the line above.
- Scale: 14 meta · 16 UI · 18 article body · 20 card title · 24 H3 · 30/36 H2 · 36/52 H1 (mobile/desktop).

**Shape and depth.** Buttons are pills (they echo the fanpage label pills). Cards are 20px radius with a hairline border and no shadow. Shadow is used only on floating layers: the mega menu, the drawer and the sticky booking card.

**Imagery.** Soft daylight, real Vietnamese skin tones, teal and ivory backdrops, no heavy retouching, no text inside images. **Doctors, the clinic, devices and before/after photos must be real photos** (see `IMAGE-PROMPTS.md`, section "Real photos").

**Icons.** Lucide (inline SVG, 1.75 stroke) for UI. A custom set of 8 "concern" illustrations for the onboarding picker.

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
- **Utility bar** (36px, ivory): 📍 Tầng 5, TTTM Đức Tài – Tâm Đạt, Quỳnh Lưu · 🕗 8:00–20:00 hằng ngày · Zalo · Facebook.
- **Header** (76px, white, sticky; it shrinks to 64px once you scroll): logo, then the nav (Giới thiệu, Dịch vụ, Tin tức, Đào tạo), then search, hotline and **[Đặt lịch]**.
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
1. Home hero: *"Bạn đang quan tâm vấn đề gì?"* followed by 8 concern chips (Mụn, Thâm, Nám – tàn nhang, Sẹo, Xóa xăm, Triệt lông, Trẻ hóa da, Filler – Botox). Each chip goes straight to its category.
2. A secondary CTA, *"Chưa rõ? Kiểm tra da 1 phút"*, leads to `/tim-lieu-trinh/`.
3. **The quiz asks 4 steps, one question per screen, with a progress bar and a back button:**
   1. Main concern (multi-select icon chips)
   2. Area (face, back, underarm, arms/legs, bikini), shown only when it applies
   3. Skin type (oily, dry, combination, sensitive, "not sure")
   4. Priority (budget, fast results, no downtime)
4. The result screen shows 1–3 recommended services, each with a reason ("Vì bạn chọn: mụn viêm + da dầu"), its price-from and the number of sessions. It offers **[Đặt lịch tư vấn miễn phí]** (the service is pre-filled) and 2 articles to read. A disclaimer notes that the doctor confirms the plan at the visit.
5. The answers are saved in `localStorage`, so the booking form is pre-filled and the home hero can say *"Liệu trình gợi ý cho bạn"* on the next visit.

**B. Booking** (`/dat-lich/`, and a compact version on service pages):
1. Service: pre-filled from the page or quiz, or "Chưa biết – cần bác sĩ tư vấn".
2. Day (the next 14 days as chips) and time slot (Sáng / Chiều / Tối chips).
3. Full name and phone number (required, numeric keypad, Vietnamese phone validation), plus an optional note.
4. **Confirmation:** a summary, "Chúng tôi sẽ gọi xác nhận trong 15 phút (giờ làm việc)", then [Chỉ đường] [Nhắn Zalo] [Thêm vào lịch].

**C. Reading to booking.** A Google search lands on an article. Mid-article there is an inline service card ("Điều trị nám chuyên sâu – từ 1.500.000đ"). The visitor goes on to the service page and books from the sticky card or bar.

## 7. Screen specs (section order)

**Home**
1. Announcement bar (optional and dismissible). It carries a current notice such as *"Làm việc xuyên lễ 2/9"* or this month's offer.
2. Hero: the H1 *"Làn da khỏe đẹp, điều trị chuẩn y khoa"*, a subtitle, **[Đặt lịch khám]** and [Kiểm tra da 1 phút], the 8 concern chips, and a portrait on the right (it sits above the text on mobile). A trust row underneath lists 4 icon facts.
3. *Dịch vụ theo vấn đề da*: 7 category cards (image, name, number of services, price-from).
4. *Vì sao chọn Quang Đăng*: 4 pillars, each linking to its About subpage (doctors, technology, medical process, facility).
5. *Quy trình 5 bước*: Thăm khám, Soi da, Phác đồ riêng, Điều trị, Tái khám. This lowers first-visit anxiety.
6. *Bác sĩ*: the doctor card(s), using real photos.
7. *Kết quả thật*: before/after slider cards (real photos, with consent), plus a note on how results can vary.
8. *Ưu đãi đang diễn ra*: up to 3 promotion cards with countdown badges.
9. *Cẩm nang làm đẹp*: 1 featured post and 3 cards.
10. Booking strip: a mini form (name, phone, concern) alongside the address, hours and a small map link.

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
