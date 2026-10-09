// Lab performance check: LCP, CLS and transferred bytes on a phone profile with CPU and network throttling.
// This is a lab measurement (diagnostic), not a field guarantee. Budgets come from docs/REDESIGN.md.
// Usage: node dev/perf-check.mjs [--base http://localhost:8081] /path1 /path2 ...
import { chromium } from 'playwright';
import { gzipSync } from 'node:zlib';

const args = process.argv.slice(2);
const bi = args.indexOf('--base');
const base = bi > -1 ? args.splice(bi, 2)[1] : 'http://localhost:8081';
const paths = args.length ? args : ['/'];

// Budgets (docs/REDESIGN.md): CSS <= 25 KB gzip, initial fonts <= 100 KB, JS <= 20 KB gzip for the whole page.
const BUDGET = { lcp: 2500, cls: 0.1, css: 25 * 1024, fonts: 100 * 1024, js: 20 * 1024 };
const browser = await chromium.launch();
let failed = 0;

for (const path of paths) {
	const ctx = await browser.newContext({ viewport: { width: 375, height: 812 }, deviceScaleFactor: 2, isMobile: true, hasTouch: true });
	const page = await ctx.newPage();
	const cdp = await ctx.newCDPSession(page);
	await cdp.send('Network.enable');
	await cdp.send('Network.emulateNetworkConditions', { offline: false, latency: 150, downloadThroughput: (1.6 * 1024 * 1024) / 8, uploadThroughput: (750 * 1024) / 8 });
	await cdp.send('Emulation.setCPUThrottlingRate', { rate: 4 });

	const bytes = { css: 0, fonts: 0, js: 0, img: 0, other: 0 };
	// The local PHP server does not compress, so CSS and JS are measured as their gzip size (what a real host sends).
	page.on('response', async (res) => {
		try {
			const type = res.request().resourceType();
			const body = await res.body();
			if (type === 'stylesheet') bytes.css += gzipSync(body).length;
			else if (type === 'script') bytes.js += gzipSync(body).length;
			else if (type === 'font') bytes.fonts += body.length;
			else if (type === 'image') bytes.img += body.length;
			else bytes.other += body.length;
		} catch (e) { /* redirects have no body */ }
	});

	await page.addInitScript(() => {
		window.__m = { lcp: 0, cls: 0 };
		new PerformanceObserver((l) => { for (const e of l.getEntries()) window.__m.lcp = e.startTime; }).observe({ type: 'largest-contentful-paint', buffered: true });
		new PerformanceObserver((l) => { for (const e of l.getEntries()) if (!e.hadRecentInput) window.__m.cls += e.value; }).observe({ type: 'layout-shift', buffered: true });
	});
	await page.goto(base + path, { waitUntil: 'load' });
	await page.waitForTimeout(1500);
	const m = await page.evaluate(() => window.__m);
	const row = { lcp: Math.round(m.lcp), cls: +m.cls.toFixed(3), ...Object.fromEntries(Object.entries(bytes).map(([k, v]) => [k, Math.round(v)])) };
	const bad = [];
	if (row.lcp > BUDGET.lcp) bad.push('LCP');
	if (row.cls > BUDGET.cls) bad.push('CLS');
	if (row.css > BUDGET.css) bad.push('CSS');
	if (row.fonts > BUDGET.fonts) bad.push('fonts');
	if (row.js > BUDGET.js) bad.push('JS');
	failed += bad.length ? 1 : 0;
	console.log(`${bad.length ? 'FAIL' : 'ok  '} ${path}  LCP ${row.lcp} ms  CLS ${row.cls}  css ${row.css} B  fonts ${row.fonts} B  js ${row.js} B  img ${row.img} B${bad.length ? '  over: ' + bad.join(',') : ''}`);
	await ctx.close();
}

await browser.close();
process.exit(failed ? 1 : 0);
