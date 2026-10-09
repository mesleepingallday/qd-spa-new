// Degradation checks: the site must stay usable when an enhancement is missing. Exits 1 on any failure.
// Covers: JavaScript off (phone navigation), web font fails to load, backdrop blur unsupported,
// prefers-reduced-motion, forced colours.
// Usage: node dev/check-degrade.mjs [--base http://localhost:8081]
import { chromium } from 'playwright';

const i = process.argv.indexOf('--base');
const base = i > -1 ? process.argv[i + 1] : 'http://localhost:8081';
const browser = await chromium.launch();
let failed = 0;
const check = (ok, name, extra = '') => {
	if (!ok) failed++;
	console.log(`${ok ? 'ok  ' : 'FAIL'}  ${name}${extra ? '  ' + extra : ''}`);
};
const phone = { width: 375, height: 812 };

/* 1. JavaScript off, on a phone: there is still a way to the site's links */
{
	const ctx = await browser.newContext({ viewport: phone, javaScriptEnabled: false });
	const page = await ctx.newPage();
	await page.goto(base + '/');
	const link = page.locator('a.site-header__menu-btn[href="#site-nav"]');
	check((await link.count()) === 1 && (await link.isVisible()), 'no JS (phone): the Menu control is a link to the full link list');
	check(!(await page.locator('button.site-header__menu-btn').isVisible()), 'no JS (phone): the dead button is hidden');
	await link.click();
	check(await page.locator('#site-nav').isVisible(), 'no JS (phone): the link list is reachable');
	const links = await page.locator('#site-nav a').count();
	check(links >= 10, 'no JS (phone): the list holds the services and about links', `(${links} links)`);
	const h1 = await page.locator('h1').first().innerText();
	check(/Làn da khỏe đẹp/.test(h1), 'no JS: the home headline renders');
	await ctx.close();
}

/* 2. Web font fails: the page stays readable and nothing overflows or clips */
{
	const ctx = await browser.newContext({ viewport: phone });
	const page = await ctx.newPage();
	await page.route('**/qd-sans.woff2', (r) => r.abort());
	await page.goto(base + '/');
	await page.waitForTimeout(500);
	const over = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
	check(over <= 0, 'font failure: no horizontal overflow with the fallback font', over > 0 ? `(+${over}px)` : '');
	const h1 = await page.locator('h1').first().evaluate((el) => { const r = el.getBoundingClientRect(); return { h: r.height, fs: parseFloat(getComputedStyle(el).fontSize), lh: parseFloat(getComputedStyle(el).lineHeight) }; });
	check(h1.lh / h1.fs >= 1.12, 'font failure: headline line-height stays >= 1.12 (stacked tone marks do not collide)', `(${(h1.lh / h1.fs).toFixed(2)})`);
	await ctx.close();
}

/* 3. Blur unsupported: the sticky header is already a nearly opaque surface */
{
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const page = await ctx.newPage();
	await page.goto(base + '/');
	await page.addStyleTag({ content: '.site-header{-webkit-backdrop-filter:none!important;backdrop-filter:none!important}' });
	const alpha = await page.locator('.site-header').evaluate((el) => {
		const m = getComputedStyle(el).backgroundColor.match(/rgba?\(([^)]+)\)/)[1].split(',').map((x) => parseFloat(x));
		return m.length > 3 ? m[3] : 1;
	});
	check(alpha >= 0.85, 'no blur: the header background is >= 85% opaque, so text stays readable', `(alpha ${alpha})`);
	await ctx.close();
}

/* 4. Reduced motion: no transform or long transition remains on interactive elements */
{
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 }, reducedMotion: 'reduce' });
	const page = await ctx.newPage();
	await page.goto(base + '/');
	const dur = await page.locator('.btn').first().evaluate((el) => parseFloat(getComputedStyle(el).transitionDuration));
	check(dur <= 0.001, 'reduced motion: button transitions are effectively instant', `(${dur}s)`);
	const tile = page.locator('.concern-tile').first();
	await tile.hover();
	const tf = await tile.locator('.concern-tile__icon').evaluate((el) => getComputedStyle(el).transform);
	check(tf === 'none', 'reduced motion: a hovered concern tile does not move', `(${tf})`);
	await ctx.close();
}

/* 5. Forced colours: controls keep a visible boundary and the page does not overflow */
{
	const ctx = await browser.newContext({ viewport: phone, forcedColors: 'active' });
	const page = await ctx.newPage();
	await page.goto(base + '/dat-lich/');
	const over = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
	check(over <= 0, 'forced colours: no horizontal overflow on the booking page');
	const border = await page.locator('#bk-name').evaluate((el) => getComputedStyle(el).borderTopWidth);
	check(parseFloat(border) >= 1, 'forced colours: text fields keep a border', `(${border})`);
	await ctx.close();
}

await browser.close();
console.log(failed ? `\n${failed} check(s) failed` : '\nall degradation checks passed');
process.exit(failed ? 1 : 0);
