# SEO launch plan: vienthammyquangdang.vn

## Context
The site is new and live (custom `quangdang` theme from this repo, Rank Math installed, sitemap at `/sitemap_index.xml`, robots.txt is fine). It has about 30 service URLs and the About pages, and **0 blog posts**. The Ryze connector is connected, but every data call (Google Ads Keyword Planner, Search Console) returns `subscription_required`. The user has no credit card and wants $0 tools. So this plan:
- uses **real Google autocomplete data** (pulled 2026-10-13, below) in place of volumes. Per the skill rule, **no volumes or CPC are invented**. Those columns stay empty until a free source fills them (§1).
- lists the free tools that replace Ryze.
- gives a 90-day sequence: fix technical issues, then local SEO (the biggest lever for a single-location clinic), then service pages, then blog clusters, then reviews and links.

---

## 1. Free replacements for Ryze (no card, no fees)

| Need | Free tool | How |
|---|---|---|
| What the site ranks for, clicks, indexing | **Google Search Console** (free) | It is already verified under hainh.wdev@gmail.com. Open it directly at search.google.com/search-console. Submit `sitemap_index.xml`. Check *Pages* and *Performance → Queries* weekly. |
| Search volume (ranges) | **Google Keyword Planner** (free) | Sign in to ads.google.com → **"Switch to Expert Mode"** → **"Create an account without a campaign"**. That skips billing. Volumes show as ranges (10–100, 100–1K), which is enough to rank priorities. Location: Nghệ An. Language: Vietnamese. |
| Volume and keyword ideas | **Bing Webmaster Tools → Keyword Research** (free) | Sign in with Google, then import the site from GSC in one click. It gives exact Bing volumes. These are small for Vietnam, but the relative sizes are useful. |
| Seasonality and local interest | **Google Trends** (free) | Set region to Vietnam → Nghệ An. Compare "trị mụn" / "trị nám" / "triệt lông" (triệt lông peaks before summer, nám peaks in the sunny season). |
| Keyword ideas and questions | Google autocomplete, *People also ask*, *Related searches* | This is how the clusters below were built. |
| Local rank and reviews | **Google Business Profile** (free) | The *Performance* tab shows the searches that found the clinic on Maps. |

When Keyword Planner is set up, paste the volume ranges into the cluster tables and re-rank. I can also do this step later in this repo if you export the CSV.

---

## 2. Technical and on-page fixes (week 1, mostly WP admin, about 1 day)

Found on the live site today:

1. **Every page has the same meta description**: "Phòng khám chuyên khoa Da liễu – Thẩm mỹ tại Quỳnh Lưu…" (Rank Math default). The homepage has "Phòng khám da liễu thẩm mỹ". → In Rank Math, write a unique title and description per service using the keyword map in §4 (formula: `{Dịch vụ} tại Quỳnh Lưu, Nghệ An | giá, quy trình – Quang Đăng`, ≤60 / ≤155 characters).
2. **Duplicate About pages are indexable**: `/gioi-thieu/doi-ngu-bac-si/` and `/doi-ngu-chuyen-gia-bac-si/`, plus `/qua-trinh-hinh-thanh/`, `/tam-nhin-su-menh/`, `/cong-nghe-san-pham/` and `/co-so-vat-chat/` at the root. → Delete the root copies and add 301 redirects to the `/gioi-thieu/...` versions (Rank Math → Redirections).
3. **URL structure doesn't match the plan**: the acne services are flat (`/dich-vu/tri-mun-khang-khuan-da-tang/`), the Nám pages sit under `dieu-tri-tham/`, and the Chăm sóc da and Nội khoa services are flat with a `dich-vu-` prefix (`/dich-vu/dich-vu-filler/`). → Pick a structure **now, before Google indexes it** (recommended: the nested one from `docs/DESIGN-PLAN.md`), set the parent pages and 301 the old URLs. After launch, don't change URLs again.
4. **Footer links point to URLs that don't exist** (for example `/dich-vu/cham-soc-phuc-hoi-da/` vs the real path). → Rebuild the footer menu in Appearance → Menus.
5. **Two MedicalClinic schema nodes**. Rank Math outputs one without a phone or address, and the theme outputs `qd_schema_clinic()` (`quangdang/inc/schema.php:26`) with no `geo` and no `hasMap`. → In Rank Math → Titles & Meta → Local SEO, set it to output the Organization only (or turn its Local schema off). Small code change in this repo: add `geo` (lat/long) and `hasMap` from new Customizer fields in `inc/customizer.php`, and an `image`.
6. Empty licence number ("Giấy phép hoạt động:") in the footer. Fill it in through Customize → Thông tin phòng khám. This is a trust (E-E-A-T) signal for medical topics.
7. Typo "tatoo" in the slug and the form: `/dich-vu/xoa-xam/xoa-xam-tatoo/` → `xoa-xam-hinh-xam` (with a 301).
8. Service pages carry about 1,100 words of rendered text, most of it template chrome. The real body copy is thin. That is handled in §5.
9. Submit the sitemap in GSC and Bing, then *Request indexing* for the homepage and the 7 category pages.

### Status after the theme update (live after merge to `main` and a cPanel deploy)
Done in the theme (`inc/seo-fixes.php`):
- ✅ Booking goes through **SalonHero** (Customizer "Link đặt lịch chính"). Every booking button, including service pages and the quiz (they were 404s on `/dat-lich/`), now opens it, and the old `/dat-lich/` address forwards there.
- ✅ The missing pages **/lien-he/** and **/tim-lieu-trinh/**, plus the categories *Cẩm nang làm đẹp* and *Sự kiện – Ưu đãi*, are created automatically the first time an admin opens wp-admin after the update.
- ✅ Menu links always point to pages that exist (about 20 were 404s). Missing items are hidden; empty categories and courses show once they have content.
- ✅ 301s: the 6 root About duplicates → `/gioi-thieu/…`, and any 404 URL that matches a real page (for example `/dich-vu/noi-khoa-tham-my/filler/`).
- ✅ Each page gets its own meta description automatically. A description written in Rank Math always wins.
- ✅ The footer shows "Đánh giá trên Google" (your review link).

Still to do in wp-admin:
- [ ] Delete the 6 About pages at the root (`/doi-ngu-chuyen-gia-bac-si/`, `/qua-trinh-hinh-thanh/`, `/tam-nhin-su-menh/`, `/cong-nghe-san-pham/`, `/co-so-vat-chat/`); the redirects already cover them.
- [ ] Service "xoa-xam-tatoo": change the slug to `xoa-xam-hinh-xam` and fix "tatoo" in the title. WordPress redirects the old URL.
- [ ] Settings → General → Site Title, and Rank Math → Titles & Meta → Local SEO → name: "Phòng khám Da liễu Thẩm mỹ Quang Đăng", type **Organization** (this removes the second clinic schema node).
- [ ] Customize → Thông tin phòng khám: the licence number, latitude/longitude and the Google listing link.
- [ ] Write the 2 course posts (Đào tạo) and the first blog posts. They appear in the menu automatically.
- [ ] Search Console: submit `sitemap_index.xml`, then request indexing for `/` and the category pages.

## 3. Local SEO, the #1 lever (weeks 1–2)
Step-by-step, paste-ready setup: **`docs/GBP-SETUP.md`**.
For a single clinic in Quỳnh Lưu, most bookings will come from the **Google Maps / local pack**, not blog traffic.
- **Google Business Profile**: category "Phòng khám da liễu" (Dermatologist), secondary "Spa chăm sóc da" and "Dịch vụ xóa hình xăm". Use the exact address, the phone 0981618549, hours 8–20h every day, and the website and booking URL. Add each service with a price-from, at least 20 real photos (front of the building, reception, machines, doctor), and one post per week.
- **Reviews**: ask every customer after treatment (QR code at reception plus a Zalo message with the review link). Target 30 reviews in 90 days. Reply to all of them.
- **NAP consistency** (Name/Address/Phone identical everywhere): Facebook page, Zalo OA, Cốc Cốc Maps, Apple Maps, Bing Places, plus Vietnamese directories (trangvangvietnam, diadiemnghean, hellobacsi/isofhcare clinic listings).
- Embed the Google Map on `/lien-he/` (the theme already has a Maps link field).

## 4. Keyword clusters (from real Google VN autocomplete; volumes pending §1)

Brand note: "quang đăng" alone autocompletes to a singer (Quang Đăng Trần). Use **"Phòng khám Da liễu Quang Đăng"** / **"Thẩm mỹ Quang Đăng Quỳnh Lưu"** consistently so the brand gets its own SERP.

**A. Local money keywords → homepage + category pages (highest priority)**
| Keyword | Seen in autocomplete | Target page | Intent |
|---|---|---|---|
| phòng khám da liễu quỳnh lưu (nghệ an) | ✅ | `/` | local/commercial |
| trị mụn nghệ an · spa trị mụn ở vinh · trị mụn ở vinh | ✅ | `/dich-vu/dieu-tri-mun/` | local |
| triệt lông nghệ an · spa triệt lông nghệ an | ✅ | `/dich-vu/.../triet-long/` | local |
| trị nám ở vinh | ✅ | `/dich-vu/.../dieu-tri-nam/` | local |
| da liễu vinh nghệ an · thẩm mỹ viện vinh nghệ an | ✅ | `/` + GBP | local (Vinh is about 60 km away, a secondary area) |
| spa diễn châu | ✅ | `/` (mention the service area) | local |
Volume / CPC: *pending Keyword Planner*. The local volumes will be small (10–100/month each), but intent is close to 100% booking.

**B. Price and decision keywords → service pages (add a price table and FAQ)**
"trị thâm nách giá bao nhiêu", "trị sẹo rỗ bao nhiêu tiền", "triệt lông vĩnh viễn giá bao nhiêu / bao nhiêu lần thì hết", "xóa xăm lông mày bằng laser giá bao nhiêu / có đau không / có để lại sẹo không", "tiêm botox gọn hàm giá bao nhiêu", "căng chỉ da mặt giá bao nhiêu / giữ được bao lâu", "xóa nốt ruồi bao nhiêu tiền / có để lại sẹo không", "laser picosure giá bao nhiêu", "aqua peeling giá", "trị thâm mông bằng laser giá bao nhiêu".
→ Each one becomes an **H2 or FAQ item on the matching service page** (the theme already outputs `FAQPage` through `qd_schema_faq()`). Don't write separate posts for these.

**C. Informational clusters → blog (Cẩm nang làm đẹp), one pillar plus supporting posts each**
| Cluster (pillar) | Supporting posts (from autocomplete) | Links to service |
|---|---|---|
| Mụn | mụn lưng do đâu / ở nam / tuổi dậy thì; trị mụn đầu đen ở mũi; trị thâm sau mụn nên dùng gì; mụn lưng có tự hết không | điều trị mụn, trị mụn lưng, trị thâm sau mụn |
| Nám – tàn nhang | tàn nhang là gì / do đâu / khác gì nám; trị nám tại nhà (an toàn?); trị nám chân sâu | điều trị nám, tàn nhang |
| Thâm | trị thâm nách sau sinh / cho bầu; trị thâm mông do ngồi nhiều | trị thâm nách, mông, vùng kín |
| Sẹo | trị sẹo rỗ lâu năm có hết không; sẹo lồi vs sẹo lõm | trị sẹo rỗ / lồi |
| Xóa xăm | xóa xăm lông mày có hết hoàn toàn không / kiêng gì / có rụng lông không | xóa xăm lông mày, mí mắt |
| Triệt lông | triệt lông vĩnh viễn có thật không / là như thế nào / cho nam | triệt lông |
| Thẩm mỹ nội khoa | tiêm botox là gì / có tác dụng gì / trị hôi nách; căng chỉ có nguy hiểm không; peel da là gì / bao lâu thì bong | botox, căng chỉ, filler, meso |
| Nốt ruồi | xóa nốt ruồi có đau không; nốt ruồi nào cần đi khám (a medical-safety angle) | xóa nốt ruồi |
| Đào tạo | khóa học chăm sóc da cơ bản / chuyên sâu / bao nhiêu tiền | `/dao-tao/` courses |

Skip national head terms like "trị mụn" and "trị nám" alone (Hasaki and the big hospitals own them). Win them gradually through the long-tail clusters above.

## 5. Content production plan (weeks 2–12)
- **Weeks 2–4: upgrade the 7 category pages and the top 8 services first** (mụn, thâm nách, nám, triệt lông, xóa xăm lông mày, sẹo rỗ, botox, căng chỉ). Each gets 800–1,500 words of real body copy: who it suits / doesn't suit, the process step by step, sessions, downtime, a **price-from table**, real before/after photos (with consent), "Tham vấn y khoa: BS …" (a `bac-si` CPT with credentials), a FAQ (cluster B) and a mention of Quỳnh Lưu / Nghệ An in the H1 or intro.
- **Weeks 3–12: 2 blog posts per week**. Start one pillar per cluster, then supporting posts. Every post links to its service page (the theme supports service ↔ article links through a shared tag) and ends with the booking CTA.
- **Medical (YMYL) rules**: name a real doctor reviewer on every article, give no guaranteed results, cite sources (Bộ Y tế, AAD), and show the licence number. Google is strict about medical content.
- Use the `content-brief` skill per keyword when writing.

## 6. Authority and links (from month 2)
Facebook and Zalo posts linking to articles; local news and PR (baonghean.vn, local Quỳnh Lưu pages); partnerships with local businesses; the training courses (`/dao-tao/`) as a link magnet for students; a YouTube or TikTok before/after video embedded on service pages. Don't buy links.

## 7. Measurement (weekly, 15 min, all free)
GSC: indexed pages, impressions/clicks by query, and *striking-distance* queries (position 8–20 → improve that page). GBP: calls, direction requests, website clicks. Booking-form submissions (`lich-hen` CPT inbox). Optional: connect GA4 (free).
Realistic targets: all pages indexed by week 4; local pack top 3 for "phòng khám da liễu quỳnh lưu" by month 2–3; first long-tail blog traffic in months 3–6.

## 8. Theme support (done)
- Customize → **Thông tin phòng khám** now has **Vĩ độ / Kinh độ** (latitude/longitude) and **Khoảng giá** (price range). To get the coordinates, right-click the clinic in Google Maps and click the numbers to copy them.
- The `MedicalClinic` JSON-LD (`inc/schema.php`) now outputs `geo` (once both coordinates are filled in), `hasMap` (from the Google Maps link), `priceRange`, `image` (the logo) and `areaServed` (Quỳnh Lưu, Hoàng Mai, Diễn Châu, Nghệ An).
- After deploying, check the homepage at https://search.google.com/test/rich-results.

## Checking the admin fixes
- `curl -sI` each old duplicate URL and expect a `301`.
- Every page has its own meta description.
- In GSC → Pages, there is no "Duplicate without user-selected canonical".
