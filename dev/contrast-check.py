#!/usr/bin/env python3
"""Check the design tokens in quangdang/assets/css/main.css against WCAG 2.x contrast.

Reads the real tokens (single source), so the check cannot drift from the CSS.
  text pairs   need >= 4.5:1   (normal-size text)
  graphic pairs need >= 3.0:1  (essential icons / field boundaries / state cues)

Exits 1 if any pair fails. Run it before every phase is called done:  python3 dev/contrast-check.py
Add a pair to PAIRS whenever a new colour combination ships (hover, selected, disabled composites too;
write semi-transparent layers as blend(fg, bg, alpha)).
"""
import os
import re
import sys

CSS = os.path.join(os.path.dirname(__file__), "..", "quangdang", "assets", "css", "main.css")
CONCERNS = ["mun", "tham", "nam", "seo", "xoa-xam", "triet-long", "tre-hoa", "filler-botox"]
TEXT, GRAPHIC = 4.5, 3.0


THEME = os.path.join(os.path.dirname(__file__), "..", "quangdang", "theme.json")


def load_tokens(css):
    """Return (tokens, concern, drift). A token is `--name: #HEX;` or `--name: var(--wp--preset--color--slug, #HEX);`.
    drift lists tokens whose CSS fallback differs from the theme.json palette (editor and front end must agree)."""
    import json
    tokens, presets = {}, {}
    pattern = r"(--[a-z0-9-]+)\s*:\s*(?:var\(--wp--preset--color--([a-z0-9-]+),\s*)?(#[0-9a-fA-F]{6})\)?\s*;"
    for m in re.finditer(pattern, css):
        name, slug, hexv = m.group(1), m.group(2), m.group(3).upper()
        if name in tokens:
            continue  # first definition wins (:root)
        tokens[name] = hexv
        if slug:
            presets[name] = (slug, hexv)
    palette = {e["slug"]: e["color"].upper() for e in json.load(open(THEME, encoding="utf-8"))["settings"]["color"]["palette"]}
    drift = ["%s falls back to %s but theme.json '%s' is %s" % (n, h, s, palette.get(s)) for n, (s, h) in presets.items() if palette.get(s) != h]
    concern = {}
    for m in re.finditer(r'\[data-concern="([a-z-]+)"\]\s*\{([^}]*)\}', css):
        concern[m.group(1)] = {k: v.upper() for k, v in re.findall(r"(--concern-[a-z]+)\s*:\s*(#[0-9a-fA-F]{6})", m.group(2))}
    return tokens, concern, drift


def rgb(h):
    h = h.lstrip("#")
    return [int(h[i:i + 2], 16) for i in (0, 2, 4)]


def lum(h):
    def lin(c):
        c /= 255
        return c / 12.92 if c <= 0.03928 else ((c + 0.055) / 1.055) ** 2.4
    r, g, b = (lin(c) for c in rgb(h))
    return 0.2126 * r + 0.7152 * g + 0.0722 * b


def ratio(a, b):
    hi, lo = sorted((lum(a), lum(b)), reverse=True)
    return (hi + 0.05) / (lo + 0.05)


def blend(fg, bg, alpha):
    mixed = [round(f * alpha + b * (1 - alpha)) for f, b in zip(rgb(fg), rgb(bg))]
    return "#%02X%02X%02X" % tuple(mixed)


def main():
    css = open(CSS, encoding="utf-8").read()
    t, concern, drift = load_tokens(css)
    for line in drift:
        print("FAIL  palette drift:", line)
    if drift:
        return 1
    white = "#FFFFFF"
    pairs = [
        ("ink on paper", t["--ink"], t["--paper"], TEXT),
        ("ink on mist", t["--ink"], t["--mist"], TEXT),
        ("muted on paper", t["--muted"], t["--paper"], TEXT),
        ("muted on mist", t["--muted"], t["--mist"], TEXT),
        ("teal link text on paper", t["--teal"], t["--paper"], TEXT),
        ("teal link text on mist", t["--teal"], t["--mist"], TEXT),
        ("white on teal (button)", white, t["--teal"], TEXT),
        ("white on teal-hover (button hover)", white, t["--teal-hover"], TEXT),
        ("ink on teal-bright (appointment panel)", t["--ink"], t["--teal-bright"], TEXT),
        ("white on teal-deep", white, t["--teal-deep"], TEXT),
        ("teal-bright on teal-deep", t["--teal-bright"], t["--teal-deep"], TEXT),
        ("ink on sun (offer badge)", t["--ink"], t["--sun"], TEXT),
        ("amber text on white (promo price, required mark)", t["--c-accent-600"], t["--paper"], TEXT),
        ("amber text on its tint", t["--c-accent-600"], t["--c-accent-50"], TEXT),
        ("white on teal-deep at 80% (process copy)", blend(white, t["--teal-deep"], 0.8), t["--teal-deep"], TEXT),
    ]
    for key in CONCERNS:
        c = concern.get(key)
        if not c or len(c) != 3:
            print("MISSING concern tokens for", key)
            return 1
        pairs += [
            ("%s: concern-ink on its tint" % key, c["--concern-ink"], c["--concern-tint"], TEXT),
            ("%s: concern-ink on paper" % key, c["--concern-ink"], t["--paper"], TEXT),
            ("%s: ink (body text) on its tint" % key, t["--ink"], c["--concern-tint"], TEXT),
            ("%s: muted text on its tint" % key, t["--muted"], c["--concern-tint"], TEXT),
            ("%s: teal-hover link on its tint" % key, t["--teal-hover"], c["--concern-tint"], TEXT),
            ("%s: white glyph on tile" % key, white, c["--concern-tile"], GRAPHIC),
            ("%s: tile as outline on paper (selected state)" % key, c["--concern-tile"], t["--paper"], GRAPHIC),
        ]
    failed = 0
    for name, fg, bg, need in pairs:
        r = ratio(fg, bg)
        ok = r >= need
        failed += not ok
        print("%s %6.2f (need %.1f)  %s" % ("ok  " if ok else "FAIL", r, need, name))
    print("\n%d pairs, %d failing" % (len(pairs), failed))
    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main())
