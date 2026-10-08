// Screenshot pages at phone and desktop widths.
// Usage: node dev/screenshot.mjs [--base http://localhost:8080] [--out dev/screenshots] [--full] /path1 /path2 ...
// Special targets: "mega" (desktop, Dịch vụ panel open), "drawer" (phone, menu → Dịch vụ panel).
import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';

const args = process.argv.slice(2);
const opt = (name, def) => {
	const i = args.indexOf(name);
	if (i === -1) return def;
	const v = args[i + 1];
	args.splice(i, 2);
	return v;
};
const base = opt('--base', 'http://localhost:8080');
const out = opt('--out', 'dev/screenshots');
const full = args.includes('--full');
const paths = args.filter((a) => a !== '--full');
if (!paths.length) paths.push('/');
mkdirSync(out, { recursive: true });

const viewports = [
	{ name: 'phone', width: 375, height: 812 },
	{ name: 'desktop', width: 1440, height: 900 },
];

const browser = await chromium.launch();
const slug = (p) => (p.replace(/^\/|\/$/g, '').replace(/[^a-z0-9]+/gi, '-') || 'home');

for (const target of paths) {
	for (const vp of viewports) {
		if (target === 'mega' && vp.name === 'phone') continue;
		if (target === 'drawer' && vp.name === 'desktop') continue;
		const page = await browser.newPage({ viewport: { width: vp.width, height: vp.height }, deviceScaleFactor: 1 });
		const url = base + (['mega', 'drawer'].includes(target) ? '/' : target);
		await page.goto(url, { waitUntil: 'networkidle' });
		await page.evaluate(() => document.fonts.ready);
		const overflow = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
		if (full) {
			// Scroll through once so lazy images load, then back to top.
			await page.evaluate(async () => {
				for (let y = 0; y < document.body.scrollHeight; y += 600) { window.scrollTo(0, y); await new Promise((r) => setTimeout(r, 60)); }
				window.scrollTo(0, 0);
			});
			await page.waitForLoadState('networkidle');
		}
		if (target === 'mega') {
			await page.click('.primary-nav__item--mega [data-nav-trigger]');
			await page.waitForTimeout(300);
		}
		if (target === 'drawer') {
			await page.click('[data-drawer-open]');
			await page.waitForTimeout(350);
			await page.click('.drawer__panel.is-active [data-drawer-go]:nth-of-type(1) >> nth=1');
			await page.waitForTimeout(350);
		}
		const file = `${out}/${slug(target)}-${vp.name}.png`;
		await page.screenshot({ path: file, fullPage: full && !['mega', 'drawer'].includes(target) });
		console.log(`${file}${overflow > 0 ? `  ⚠ horizontal overflow ${overflow}px` : ''}`);
		await page.close();
	}
}
await browser.close();
