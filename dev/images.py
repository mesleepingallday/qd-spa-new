#!/usr/bin/env python3
"""Image manifest → labelled placeholders + docs/IMAGE-PROMPTS.md.

Every image the design uses is listed once below. Running this script:
  1. writes a placeholder (right aspect ratio, labelled) for each image,
  2. regenerates docs/IMAGE-PROMPTS.md with the GPT prompt for each one.

Real images replace placeholders by being saved at the same path WITHOUT "_placeholders/"
(any of .webp/.jpg/.png). Theme code and the seed pick the real file automatically.

Usage: python3 dev/images.py
"""
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parent.parent
THEME_IMG = ROOT / "quangdang/assets/images"
DEMO_IMG = ROOT / "dev/demo-images"
FONT = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"
FONT_BOLD = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"

PHOTO = (
    "Photorealistic editorial photo, soft natural window daylight, calm clinical-luxury mood. "
    "Color palette: teal #087A82, bright aqua #19C6BE, mist white #F2F7F7, clean white. "
    "Realistic Vietnamese / East Asian skin with natural texture, no plastic retouching. "
    "Clean minimal background, shallow depth of field, 50mm lens. No text, no logos, no watermark."
)
ILLUS = (
    "Minimal line illustration, single 2px-equivalent stroke, teal #087A82 lines with a soft aqua #19C6BE "
    "fill accent, rounded line caps, centered with generous empty padding, TRANSPARENT background "
    "(PNG with alpha), no text, no shadows. Part of a matching set of 8 icons — keep identical style."
)

# (group, path-without-ext, width, height, label, prompt or None for REAL photo, note)
M = []


def add(group, path, w, h, label, prompt=None, note=""):
    M.append((group, path, w, h, label, prompt, note))


# --- Theme design assets (shipped with the theme) --------------------------------------------
T = "theme"
add(T, "hero/home-portrait", 1200, 1500, "Ảnh hero trang chủ",
    "Portrait of a Vietnamese woman in her early 30s with clear, healthy, softly glowing skin and a gentle "
    "natural smile, hair neatly tied back, wearing a soft white knit top, one hand lightly touching her cheek. "
    "Background: softly blurred bright clinic interior in mint and white tones with a hint of teal. "
    "Keep the face in the upper-right two thirds so both the 5:6 desktop crop and the 4:3 phone crop keep it; nothing is overlaid on the photo. Vertical 4:5. This is a stand-in: the site needs an approved clinic photograph. " + PHOTO)
add(T, "og/default", 1200, 630, "Ảnh chia sẻ mặc định (Facebook/Zalo)",
    "Wide banner background: soft mint-to-white gradient with subtle pearly light, a blurred bright clinic "
    "interior with teal accents on the right third; the left 60% is calm, empty space for a logo and headline "
    "to be added later. 1200x630. " + PHOTO)
add(T, "misc/not-found", 800, 600, "Minh họa trang 404",
    "A hand mirror lying next to a small green leaf and a tiny question mark sparkle, friendly and calm. " + ILLUS.replace("Part of a matching set of 8 icons — keep identical style.", ""))
for n, svc in [(1, "Trị mụn"), (2, "Điều trị nám"), (3, "Trị thâm nách")]:
    add(T, f"results/{n}-truoc", 800, 800, f"Trước – {svc}", None, "ẢNH THẬT của khách hàng, có đồng ý bằng văn bản. Cùng góc chụp, cùng ánh sáng với ảnh Sau.")
    add(T, f"results/{n}-sau", 800, 800, f"Sau – {svc}", None, "ẢNH THẬT, cùng khách hàng và góc chụp với ảnh Trước.")
for key, label in [
    ("about/le-tan", "Quầy lễ tân"),
    ("about/phong-dieu-tri", "Phòng điều trị"),
    ("about/phong-tu-van", "Phòng tư vấn / soi da"),
    ("about/may-picosure", "Máy Laser Picosure"),
    ("about/may-double-cool", "Máy triệt lông Double Cool"),
    ("about/doi-ngu", "Ảnh tập thể đội ngũ"),
]:
    add(T, key, 1200, 900, label, None, "ẢNH THẬT chụp tại phòng khám (hoặc ảnh thiết bị từ nhà sản xuất).")

# --- Demo content (imported by dev/seed.php; on the live site staff upload these in WordPress) --
D = "demo"
cats = {
    "dieu-tri-mun": "Close-up of a young Vietnamese woman's cheek and jawline with mild acne, a gloved hand gently cleansing with a cotton pad",
    "dieu-tri-tham": "Tasteful close-up of a woman's smooth underarm and shoulder, arm raised, wearing a white tank top",
    "dieu-tri-nam": "Side-profile close-up of a Vietnamese woman in her 40s, cheekbone area with even skin tone, soft daylight",
    "dieu-tri-seo": "Close-up of cheek skin texture being examined with a handheld dermatoscope by a doctor's gloved hand",
    "xoa-xam": "Forearm with a small faded tattoo, technician's gloved hands holding a laser handpiece",
    "cham-soc-da": "Woman relaxing on a treatment bed wearing a hydrating sheet mask and a white towel headband",
    "noi-khoa-tham-my": "Doctor's gloved hand holding a small syringe near (not touching) a woman's chin area, precise and safe",
}
for slug, subject in cats.items():
    add(D, f"categories/{slug}", 1200, 900, f"Nhóm: {slug}", subject + ". Horizontal 4:3. " + PHOTO)
svcs = {
    "tri-mun-khang-khuan-da-tang": "a woman lying on a treatment bed under a soft blue LED light therapy panel, eyes covered with protective pads",
    "tri-tham-sau-mun": "close-up of a cheek with faint red post-acne marks, a serum dropper above the skin",
    "tri-mun-viem-o-lung": "a woman's upper back with a few small blemishes, lying face down on a treatment bed, towel draped",
    "tri-mun-viem-o-tay-chan": "close-up of a forearm with small bumps being treated with a cotton pad by a gloved hand",
    "tri-tham-nach": "a woman with arm raised, technician applying a soothing gel to the underarm with a spatula",
    "tri-tham-mat": "close-up of the under-eye area of a woman with closed eyes, a small cool roller beside",
    "tri-tham-ben-mong": "abstract tasteful still life: folded white towels, a jar of brightening cream and a pump bottle on an ivory tray",
    "tri-tham-vung-kin": "tasteful still life: a private treatment room with a curtain, folded white towels and a small plant",
    "dieu-tri-nam-chuyen-sau": "a doctor operating a laser handpiece on a woman's cheek, both wearing protective glasses",
    "dieu-tri-tan-nhang": "close-up of a woman's nose and cheeks with light freckles, soft daylight",
    "tri-seo-loi": "close-up of a small raised scar on a shoulder with a silicone gel tube beside it",
    "tri-seo-lom": "a doctor using a microneedling pen on a patient's cheek, gloved hands, clinical setting",
    "xoa-xam-long-may": "close-up of a woman's eyebrows with faded old brow tattoo, eyes closed, technician preparing a laser",
    "xoa-xam-mi-mat": "close-up of closed eyes with protective eye shields, a gloved hand holding a small laser tip nearby",
    "tre-hoa-da-cong-nghe-cao": "a woman with eyes closed while a smooth radio-frequency handpiece glides along her jawline",
    "phuc-hoi-da": "hands applying a soothing cream to a calm, slightly pink cheek",
    "triet-long": "a technician's gloved hand gliding a white laser hair removal handpiece along a woman's smooth lower leg, cooling gel visible",
    "xoa-not-ruoi": "a doctor examining a small mole on a forearm with a magnifying dermatoscope",
    "kiem-dau-tre-hoa-laser-picosure": "a sleek picosecond laser machine with articulated arm in a bright clinic room",
    "filler": "close-up of a woman's lips and chin in profile, a gloved hand holding a small syringe nearby, not injecting",
    "botox": "close-up of a woman's forehead and eyes, doctor marking points with a white skin pencil",
    "cang-chi": "a doctor in gloves measuring a woman's cheek contour with a small ruler, consultation scene",
    "meso": "a tray with small glass vials of serum and a mesotherapy gun, ivory towel background",
}
for slug, subject in svcs.items():
    add(D, f"services/{slug}", 1200, 900, f"Dịch vụ: {slug}", subject[0].upper() + subject[1:] + ". Horizontal 4:3. " + PHOTO)
posts = {
    "mun-an-la-gi-nguyen-nhan-va-cach-xu-ly-dung-cach": "a young Vietnamese woman looking closely at her forehead in a bathroom mirror, morning light",
    "triet-long-bang-laser-co-dau-khong-4-cau-hoi-thuong-gap": "a laser hair removal handpiece resting on a white towel next to a bottle of cooling gel",
    "nam-da-phan-biet-nam-mang-nam-chan-sau-va-tan-nhang": "a woman in her 40s applying sunscreen on her cheek outdoors in shade",
    "quy-trinh-cham-soc-da-co-ban-4-buoc-cho-nguoi-moi-bat-dau": "flat lay of four skincare bottles (cleanser, toner, moisturizer, sunscreen) in a neat row on ivory linen",
    "tham-nach-vi-sao-bi-va-dieu-tri-bao-lau-thi-cai-thien": "a woman in a white t-shirt stretching her arms, tasteful, bright room",
    "seo-ro-sau-mun-co-tri-dut-diem-duoc-khong": "macro close-up of skin texture with small atrophic scars, soft light, clinical",
    "tiem-filler-can-luu-y-gi-de-an-toan-huong-dan-chi-tiet-tu-bac-si-da-lieu-cho-nguoi-lan-dau-lam-dep-khong-phau-thuat": "a doctor showing a sealed filler box with its authenticity label to a patient during consultation",
}
for slug, subject in posts.items():
    add(D, f"posts/{slug}", 1280, 720, f"Ảnh bài viết: {slug[:32]}…", subject[0].upper() + subject[1:] + ". Horizontal 16:9. " + PHOTO)
promos = {
    "uu-dai-thang-10-triet-long-nach-chi-99-000d-buoi": "a laser hair removal handpiece on a white towel with a teal ribbon, festive but clean",
    "tri-an-sinh-vien-giam-20-lieu-trinh-tri-mun": "two Vietnamese female college students smiling with fresh clear skin, campus-like bright background",
    "thong-bao-phong-kham-lam-viec-xuyen-le-2-9": "bright clinic reception with a small Vietnamese flag on the desk, warm welcoming light",
}
for slug, subject in promos.items():
    add(D, f"promos/{slug}", 1280, 720, f"Ảnh ưu đãi: {slug[:32]}…", subject[0].upper() + subject[1:] + ". Horizontal 16:9, keep the left third calm for a badge. " + PHOTO)
for slug, subject in {
    "khoa-hoc-cham-soc-da-co-ban": "skincare trainees in teal uniforms practicing a facial on a classmate under an instructor's guidance",
    "khoa-hoc-cham-soc-da-chuyen-sau": "an instructor demonstrating a skin analysis device to two trainees in a modern clinic",
}.items():
    add(D, f"courses/{slug}", 1280, 720, f"Khóa học: {slug}", subject[0].upper() + subject[1:] + ". Horizontal 16:9. " + PHOTO)
for n in (1, 2):
    add(D, f"doctors/{n}", 960, 1200, f"Bác sĩ {n}", None, "ẢNH THẬT của bác sĩ: áo blouse trắng, nền sáng trơn, khoảng 4:5.")


def placeholder(path: Path, w: int, h: int, label: str, real: bool, transparent: bool):
    path.parent.mkdir(parents=True, exist_ok=True)
    pw, ph = max(320, w // 2), max(320, h // 2)
    if transparent:
        img = Image.new("RGBA", (pw, ph), (0, 0, 0, 0))
        d = ImageDraw.Draw(img)
        r = min(pw, ph) * 0.42
        d.ellipse((pw / 2 - r, ph / 2 - r, pw / 2 + r, ph / 2 + r), fill=(220, 242, 241, 255), outline=(124, 208, 207, 255), width=4)
    else:
        img = Image.new("RGB", (pw, ph))
        d = ImageDraw.Draw(img)
        top, bottom = (242, 247, 247), (255, 255, 255)
        for y in range(ph):
            t = y / ph
            d.line([(0, y), (pw, y)], fill=tuple(int(top[i] + (bottom[i] - top[i]) * t) for i in range(3)))
        d.ellipse((pw * 0.55, -ph * 0.25, pw * 1.25, ph * 0.55), fill=(226, 244, 243))
    size = max(14, pw // 26)
    f = ImageFont.truetype(FONT_BOLD, size)
    fs = ImageFont.truetype(FONT, max(11, int(size * 0.72)))
    lines = [(label, f, (8, 122, 130)), (f"{w}×{h}", fs, (82, 102, 106))]
    if real:
        lines.insert(0, ("ẢNH THẬT", fs, (138, 90, 0)))
    # Shrink any line that is wider than the image.
    for i, (text, fo, color) in enumerate(lines):
        sz = fo.size
        while d.textbbox((0, 0), text, font=fo)[2] > pw * 0.9 and sz > 8:
            sz -= 1
            fo = ImageFont.truetype(fo.path, sz)
        lines[i] = (text, fo, color)
    total = sum(d.textbbox((0, 0), t, font=fo)[3] + 8 for t, fo, _ in lines)
    y = (ph - total) / 2
    for text, fo, color in lines:
        box = d.textbbox((0, 0), text, font=fo)
        d.text(((pw - box[2]) / 2, y), text, font=fo, fill=color)
        y += box[3] + 8
    img.save(path, "WEBP", quality=72, method=6)


def main():
    for group, rel, w, h, label, prompt, note in M:
        base = THEME_IMG if group == T else DEMO_IMG
        placeholder(base / "_placeholders" / f"{rel}.webp", w, h, label, prompt is None, rel.startswith("misc/"))

    out = [
        "# Image list and GPT prompts",
        "",
        "Generated by `dev/images.py`. Edit that file, not this one, then run `python3 dev/images.py`.",
        "",
        "**How to use**",
        "",
        "1. Paste a prompt into ChatGPT (image generation). For a consistent set, generate one group in one conversation.",
        "2. Save the result with **exactly the file name shown** (`.png`, `.jpg` or `.webp` are all fine).",
        "3. Put it in the folder shown. Theme images go in `quangdang/assets/images/…`, demo content in `dev/demo-images/…`.",
        "   The theme finds the real file automatically and stops using the placeholder. No code change is needed.",
        "4. **ẢNH THẬT (real photo)** rows must not be AI images. Doctors, the clinic, devices and before/after results",
        "   are trust and legal claims: use real photos, and for customers get written consent.",
        "",
        "Every prompt already includes the shared style: soft daylight, teal/aqua/mint/white palette, natural Vietnamese skin, no text.",
        "",
    ]
    titles = {T: "Theme images → `quangdang/assets/images/`", D: "Demo content → `dev/demo-images/` (on the live site, upload these as Featured image in WordPress)"}
    for group in (T, D):
        out += [f"## {titles[group]}", ""]
        section = None
        for g, rel, w, h, label, prompt, note in M:
            if g != group:
                continue
            folder = rel.split("/")[0]
            if folder != section:
                section = folder
                out += [f"### {folder}/", ""]
            ratio = f"{w}×{h}"
            out.append(f"**`{rel}.png`** · {label} · {ratio}" + ("  \n**ẢNH THẬT, không dùng AI.** " + note if prompt is None else ""))
            if prompt:
                if note:
                    out.append(f"_{note}_")
                out += ["", "```text", prompt, "```"]
            out.append("")
    (ROOT / "docs/IMAGE-PROMPTS.md").write_text("\n".join(out), encoding="utf-8")
    print(f"{len(M)} images → placeholders + docs/IMAGE-PROMPTS.md")


if __name__ == "__main__":
    main()
