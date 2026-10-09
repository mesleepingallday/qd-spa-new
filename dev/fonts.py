#!/usr/bin/env python3
"""Build the theme's single web font: Google Sans Flex (SIL OFL 1.1), subset for Vietnamese + Latin.

Why not Google's own Vietnamese/Latin files: their `unicode-range` slices omit the combining marks
U+0302 (circumflex), U+0306 (breve) and U+031B (horn). Precomposed text (NFC, what Vietnamese
keyboards produce) is fine, but decomposed text (NFD, e.g. pasted from some macOS sources) falls
back to a serif. This build keeps those marks, so NFC and NFD render identically.

Output: quangdang/assets/fonts/qd-sans.woff2 (one file, variable `opsz` 12-80 and `wght` 400-700).

Usage:  pip install fonttools brotli   &&   python3 dev/fonts.py
The source file is pinned by commit and verified by SHA-256.
"""
import hashlib
import os
import sys
import tempfile
import urllib.request

from fontTools import subset
from fontTools.ttLib import TTFont
from fontTools.varLib import instancer

COMMIT = "62e55e586fc0c29d428ee47b7c359cd33143f93d"  # github.com/google/fonts
SRC_URL = (
    "https://raw.githubusercontent.com/google/fonts/%s/ofl/googlesansflex/"
    "GoogleSansFlex%%5BGRAD,ROND,opsz,slnt,wdth,wght%%5D.ttf" % COMMIT
)
SRC_SHA256 = "c31a482fbecbf2e07e6890134d20078723aadf732c9b9c6c9a44f86f8265b6fe"
OFL_URL = "https://raw.githubusercontent.com/google/fonts/%s/ofl/googlesansflex/OFL.txt" % COMMIT

REPO = os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
OUT = os.path.join(REPO, "quangdang", "assets", "fonts", "qd-sans.woff2")

UNICODES = (
    list(range(0x20, 0x7F))                        # Basic Latin
    + list(range(0xA0, 0x100))                     # Latin-1
    + [0x102, 0x103, 0x110, 0x111, 0x128, 0x129, 0x168, 0x169, 0x1A0, 0x1A1, 0x1AF, 0x1B0]
    + [0x300, 0x301, 0x302, 0x303, 0x306, 0x309, 0x31B, 0x323]   # combining marks (NFD)
    + list(range(0x1EA0, 0x1EFA))                  # Vietnamese precomposed
    + [0x2013, 0x2014, 0x2018, 0x2019, 0x201C, 0x201D, 0x2022, 0x2026, 0x2039, 0x203A, 0x2212, 0x20AC, 0x2122]
)
FEATURES = ["kern", "mark", "mkmk", "ccmp", "locl", "liga", "calt", "tnum", "case"]
# Fixed axes pinned to their defaults; opsz/wght kept as a range.
LIMITS = {"wdth": 100, "GRAD": 0, "ROND": 0, "slnt": 0, "wght": (400, 700), "opsz": (12, 80)}


def fetch(url):
    with urllib.request.urlopen(url, timeout=120) as r:  # noqa: S310 (pinned https URL)
        return r.read()


def main():
    data = fetch(SRC_URL)
    digest = hashlib.sha256(data).hexdigest()
    if digest != SRC_SHA256:
        sys.exit("Source font changed (sha256 %s). Review before rebuilding." % digest)

    with tempfile.TemporaryDirectory() as tmp:
        src = os.path.join(tmp, "src.ttf")
        mid = os.path.join(tmp, "subset.ttf")
        open(src, "wb").write(data)

        opts = subset.Options()
        opts.layout_features = FEATURES
        opts.notdef_outline = True
        opts.hinting = False
        opts.name_IDs = [0, 1, 2, 3, 4, 6, 13, 14]
        font = TTFont(src)
        sub = subset.Subsetter(opts)
        sub.populate(unicodes=UNICODES)
        sub.subset(font)
        font.save(mid)

        font = instancer.instantiateVariableFont(TTFont(mid), LIMITS, inplace=False)
        font.flavor = "woff2"
        font.save(OUT)

    cmap = TTFont(OUT).getBestCmap()
    missing = [hex(c) for c in UNICODES if c not in cmap and c not in (0x2032, 0x2033)]
    print("wrote %s (%d bytes, %d glyphs)" % (OUT, os.path.getsize(OUT), len(cmap)))
    print("NFD marks present:", all(c in cmap for c in (0x302, 0x306, 0x31B)))
    if missing:
        print("not in font (falls back):", ", ".join(missing))

    ofl = os.path.join(REPO, "quangdang", "assets", "fonts", "OFL-GoogleSansFlex.txt")
    open(ofl, "wb").write(fetch(OFL_URL))
    print("wrote", ofl)


if __name__ == "__main__":
    main()
