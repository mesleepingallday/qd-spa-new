// Behaviour checks for the site shell (header, nav, drawer, action bar). Exits 1 on any failure.
// Usage: node dev/check-shell.mjs [--base http://localhost:8081]
import { chromium } from 'playwright';

const i = process.argv.indexOf('--base');
const base = i > -1 ? process.argv[i + 1] : 'http://localhost:8081';
const browser = await chromium.launch();
let failed = 0;
const check = (ok, name, extra = '') => {
	if (!ok) failed++;
	console.log(`${ok ? 'ok  ' : 'FAIL'}  ${name}${extra ? '  ' + extra : ''}`);
};

/* 1. Without JavaScript: a parent item is a real link to its hub page */
{
	const ctx = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 1440, height: 900 } });
	const page = await ctx.newPage();
	await page.goto(base + '/');
	const href = await page.locator('.primary-nav__link--split', { hasText: 'Dịch vụ' }).first().getAttribute('href');
	check(!!href && /\/dich-vu\/$/.test(href), 'no-JS: "Dịch vụ" is a link to the hub page', href || '');
	await page.locator('.primary-nav__link--split', { hasText: 'Dịch vụ' }).first().click();
	check(/\/dich-vu\/$/.test(page.url()), 'no-JS: clicking it navigates', page.url());
	await ctx.close();
}

/* 2. Desktop disclosure: toggle button, Escape, focus return */
{
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const page = await ctx.newPage();
	await page.goto(base + '/');
	const toggle = page.locator('.primary-nav__item--mega .primary-nav__toggle');
	check((await toggle.getAttribute('aria-expanded')) === 'false', 'desktop: toggle starts collapsed');
	await toggle.focus();
	await page.keyboard.press('Enter');
	check((await toggle.getAttribute('aria-expanded')) === 'true', 'desktop: Enter opens the panel');
	check(await page.locator('.mega.is-open').isVisible(), 'desktop: panel visible');
	await page.keyboard.press('Escape');
	check((await toggle.getAttribute('aria-expanded')) === 'false', 'desktop: Escape closes');
	check(await toggle.evaluate((el) => el === document.activeElement), 'desktop: focus returns to the toggle');
	await ctx.close();
}

/* 3. Phone drawer: dialog semantics, inert page, Escape, focus return */
{
	const ctx = await browser.newContext({ viewport: { width: 375, height: 812 }, hasTouch: true });
	const page = await ctx.newPage();
	await page.goto(base + '/');
	const menu = page.locator('[data-drawer-open]');
	check((await menu.innerText()).trim() === 'Menu', 'phone: the menu button shows a visible "Menu" label');
	await menu.click();
	await page.waitForTimeout(400);
	check(await page.locator('#main').evaluate((el) => el.inert === true), 'phone: page behind the open sheet is inert');
	check(await page.locator('.drawer__sheet').evaluate((el) => el.contains(document.activeElement)), 'phone: focus moved into the sheet');
	await page.keyboard.press('Escape');
	await page.waitForTimeout(400);
	check(await page.locator('#main').evaluate((el) => el.inert === false), 'phone: page is interactive again after close');
	check(await menu.evaluate((el) => el === document.activeElement), 'phone: focus returns to the Menu button');
	check((await menu.getAttribute('aria-expanded')) === 'false', 'phone: aria-expanded reset');
	await ctx.close();
}

/* 4. Action bar visibility states */
{
	const ctx = await browser.newContext({ viewport: { width: 375, height: 812 }, hasTouch: true });
	const page = await ctx.newPage();
	await page.goto(base + '/dat-lich/');
	await page.waitForTimeout(300);
	check(await page.locator('.action-bar.is-hidden').count() === 1, 'phone: action bar hidden while the booking form is on screen');
	await page.goto(base + '/dich-vu/');
	await page.waitForTimeout(300);
	const bar = page.locator('.action-bar');
	check(!(await bar.evaluate((el) => el.classList.contains('is-hidden'))), 'phone: action bar visible on a normal page');
	await page.evaluate(() => { const i = document.createElement('input'); i.type = 'text'; i.id = 't'; document.body.append(i); i.focus(); });
	await page.waitForTimeout(100);
	check(await bar.evaluate((el) => el.classList.contains('is-hidden')), 'phone: action bar hides while a text field has focus');
	await page.evaluate(() => document.getElementById('t').blur());
	await page.waitForTimeout(100);
	check(!(await bar.evaluate((el) => el.classList.contains('is-hidden'))), 'phone: action bar returns on blur');
	await ctx.close();
}

/* 5. Logged-in admin bar: sticky header and the service index sit below it (local dev login admin/admin) */
{
	const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
	const page = await ctx.newPage();
	await page.goto(base + '/wp-login.php');
	await page.fill('#user_login', 'admin');
	await page.fill('#user_pass', 'admin');
	await page.click('#wp-submit');
	await page.goto(base + '/dich-vu/dieu-tri-nam/dieu-tri-nam-chuyen-sau/');
	if (await page.locator('#wpadminbar').count()) {
		const bar = await page.locator('#wpadminbar').evaluate((el) => el.getBoundingClientRect().height);
		await page.evaluate(() => window.scrollTo(0, 1200));
		await page.waitForTimeout(200);
		const headerTop = await page.locator('.site-header').evaluate((el) => el.getBoundingClientRect().top);
		check(Math.abs(headerTop - bar) < 1, 'admin bar: sticky header sits directly below it', `(bar ${bar}px, header top ${headerTop}px)`);
		const tabsTop = await page.locator('.section-tabs').evaluate((el) => el.getBoundingClientRect().top);
		const headerH = await page.locator('.site-header').evaluate((el) => el.getBoundingClientRect().height);
		check(Math.abs(tabsTop - (bar + headerH)) < 2, 'admin bar: service index sticks below the header', `(index top ${tabsTop}px)`);
		await page.goto(base + '/dich-vu/dieu-tri-nam/dieu-tri-nam-chuyen-sau/#bang-gia');
		await page.waitForTimeout(400);
		const target = await page.locator('#bang-gia h2').evaluate((el) => el.getBoundingClientRect().top);
		check(target > bar + headerH, 'admin bar: a linked section heading is not hidden under the sticky bars', `(heading top ${Math.round(target)}px)`);
	} else {
		console.log('skip  admin bar checks (could not log in; local dev only)');
	}
	await ctx.close();
}

/* 6. No horizontal overflow on the shell at common widths */
for (const w of [320, 375, 390, 768, 1024, 1440]) {
	const ctx = await browser.newContext({ viewport: { width: w, height: 800 } });
	const page = await ctx.newPage();
	await page.goto(base + '/dich-vu/');
	const over = await page.evaluate(() => document.documentElement.scrollWidth - window.innerWidth);
	check(over <= 0, `no horizontal overflow at ${w}px`, over > 0 ? `(+${over}px)` : '');
	await ctx.close();
}

await browser.close();
console.log(failed ? `\n${failed} check(s) failed` : '\nall shell checks passed');
process.exit(failed ? 1 : 0);
