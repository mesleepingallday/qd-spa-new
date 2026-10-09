// End-to-end checks for the booking form and the quiz against the local site. Exits 1 on any failure.
// NOTE: successful submits create "lich-hen" posts in the LOCAL dev database; never point this at a live site.
// Usage: node dev/check-forms.mjs [--base http://localhost:8081]
import { chromium } from 'playwright';
import { execSync } from 'node:child_process';
import { existsSync } from 'node:fs';
import { homedir } from 'node:os';

const i = process.argv.indexOf('--base');
const base = i > -1 ? process.argv[i + 1] : 'http://localhost:8081';
if (!/localhost|127\.0\.0\.1/.test(base)) {
	console.error('Refusing to run against a non-local site.');
	process.exit(2);
}
// The server allows 5 accepted requests per IP per 10 minutes, so clear the local limiter first (best effort).
const wpcli = (process.env.QD_WP_CACHE || homedir() + '/.cache/qd-wp') + '/wp-cli.phar';
if (existsSync(wpcli) && existsSync('.wp')) {
	try { execSync(`php ${wpcli} --allow-root --path=.wp transient delete --all`, { stdio: 'ignore' }); } catch (e) { /* not fatal */ }
}
const browser = await chromium.launch();
let failed = 0;
const check = (ok, name, extra = '') => {
	if (!ok) failed++;
	console.log(`${ok ? 'ok  ' : 'FAIL'}  ${name}${extra ? '  ' + extra : ''}`);
};
const phoneViewport = { width: 375, height: 812 };

/* ---- 1. Booking page: validation, then a good submit that is sent exactly once ---- */
{
	const ctx = await browser.newContext({ viewport: phoneViewport, hasTouch: true });
	const page = await ctx.newPage();
	let posts = 0;
	page.on('request', (r) => { if (r.method() === 'POST' && /wp-json/.test(r.url())) posts++; });
	await page.goto(base + '/dat-lich/');

	await page.locator('[data-booking-page] button[type="submit"]').click();
	await page.waitForTimeout(300);
	const errs = await page.locator('[data-booking-page] .field__error:visible, [data-booking-page] [data-error-for]:visible').allInnerTexts();
	check(errs.length >= 3, 'booking: an empty submit shows specific errors next to the fields', `(${errs.length} shown)`);
	check(posts === 0, 'booking: an invalid form is not sent to the server');
	const invalidFocused = await page.evaluate(() => !!document.activeElement && /service|date|slot|name|phone/.test(document.activeElement.name || ''));
	check(invalidFocused, 'booking: focus moves to the first invalid field');

	await page.locator('.bk-opt', { hasText: 'Chưa biết' }).click();
	const dateInputs = page.locator('input[name="date"]');
	await dateInputs.nth(1).check({ force: true });
	await page.locator('input[name="slot"]:not(:disabled)').first().check({ force: true });
	const slotChecked = await page.locator('input[name="slot"]:checked + .bk-opt__box').evaluate((el) => getComputedStyle(el, '::before').content);
	check(slotChecked !== 'none' && slotChecked !== 'normal', 'booking: a selected slot shows a check mark, not colour alone');

	await page.fill('#bk-name', 'Nguyễn Thị Kiểm Thử');
	await page.fill('#bk-phone', '12345');
	await page.locator('[data-booking-page] button[type="submit"]').click();
	await page.waitForTimeout(300);
	const phoneErr = await page.locator('#bk-phone').evaluate((el) => el.getAttribute('aria-invalid') === 'true' && !!document.getElementById((el.getAttribute('aria-describedby') || '').split(' ')[0]));
	check(phoneErr, 'booking: a wrong phone number is flagged and linked to its message (aria-invalid + aria-describedby)');

	await page.fill('#bk-phone', '0912 345 678');
	// Double click: the second click must not send a second request.
	const submit = page.locator('[data-booking-page] button[type="submit"]');
	await submit.dblclick();
	await page.waitForSelector('[data-booking-done]:not([hidden])', { timeout: 10000 }).catch(() => {});
	const doneTitle = (await page.locator('.bk-done__title').innerText().catch(() => '')).trim();
	check(doneTitle === 'Đã nhận yêu cầu đặt lịch', 'booking: success says the request was received, not confirmed', `("${doneTitle}")`);
	check(posts === 1, 'booking: a double click sends exactly one request', `(${posts})`);
	await ctx.close();
}

/* ---- 2. Network failure keeps what was typed and says how to reach the clinic ---- */
{
	const ctx = await browser.newContext({ viewport: phoneViewport, hasTouch: true });
	const page = await ctx.newPage();
	await page.route('**/wp-json/**', (route) => route.abort());
	await page.goto(base + '/dich-vu/');
	const form = page.locator('form[data-booking-form]').first();
	await form.scrollIntoViewIfNeeded();
	await form.locator('input[name="name"]').fill('Trần Văn Mạng');
	await form.locator('input[name="phone"]').fill('0912345678');
	await form.locator('button[type="submit"]').click();
	await page.waitForTimeout(600);
	const status = (await form.locator('.booking-form__status').innerText()).trim();
	check(/Chưa gửi được/.test(status) && /vẫn còn nguyên/.test(status), 'network failure: a clear message, nothing is lost', `("${status.slice(0, 60)}…")`);
	check((await form.locator('input[name="name"]').inputValue()) === 'Trần Văn Mạng', 'network failure: the typed name is still there');
	check(await form.locator('button[type="submit"]').isEnabled(), 'network failure: the button is usable again for a retry');
	await ctx.close();
}

/* ---- 3. Without JavaScript the form still posts and lands on a received page ---- */
{
	const ctx = await browser.newContext({ viewport: phoneViewport, javaScriptEnabled: false });
	const page = await ctx.newPage();
	await page.goto(base + '/dich-vu/');
	const form = page.locator('form[data-booking-form]').first();
	await form.locator('input[name="name"]').fill('Lê Không Script');
	await form.locator('input[name="phone"]').fill('0987654321');
	await Promise.all([page.waitForNavigation(), form.locator('button[type="submit"]').click()]);
	const body = await page.locator('body').innerText();
	check(/da-gui=1/.test(page.url()) && /Đã nhận yêu cầu đặt lịch/.test(body), 'no JS: the form posts and the visitor sees the received message', page.url().replace(base, ''));
	await ctx.close();
}

/* ---- 4. Quiz: tiles are real checkboxes; a full run reaches information, not a prescription ---- */
{
	const ctx = await browser.newContext({ viewport: phoneViewport, hasTouch: true });
	const page = await ctx.newPage();
	await page.goto(base + '/tim-lieu-trinh/');
	const first = page.locator('input[name="concern"]').first();
	check((await first.getAttribute('type')) === 'checkbox', 'quiz: concern options are native checkboxes');
	check((await page.locator('.qopt__mark').count()) === 8, 'quiz: all eight options carry their concern tile');
	await page.locator('.qopt', { hasText: 'Nám' }).click();
	check(await page.locator('input[name="concern"][value="nam"]').isChecked(), 'quiz: tapping a tile selects it');
	const tick = await page.locator('input[name="concern"][value="nam"] + .qopt__card').evaluate((el) => getComputedStyle(el).boxShadow !== 'none');
	check(tick, 'quiz: the selected tile gets an outline as well as a tick');
	for (let step = 0; step < 6 && !(await page.locator('.quiz-result__title').count()); step++) {
		const stepEl = page.locator('[data-quiz-form] .quiz__step:visible');
		if (step > 0) {
			const radio = stepEl.locator('input[type="radio"]').first();
			const cb = stepEl.locator('input[type="checkbox"]:not(:checked)').first();
			if (await radio.count()) await radio.check({ force: true });
			else if (await cb.count()) await cb.check({ force: true });
		}
		await page.locator('[data-quiz-next]:visible').first().click();
		await page.waitForTimeout(300);
	}
	const title = (await page.locator('.quiz-result__title').innerText().catch(() => '')).trim();
	check(/dịch vụ bạn có thể quan tâm/i.test(title), 'quiz: the result reads as information', `("${title}")`);
	const href = await page.locator('.quiz-result__cta a.btn--primary').first().getAttribute('href').catch(() => '');
	check(!!href && /dv=/.test(href), 'quiz: the booking link pre-selects the top service', href || '');
	const note = await page.locator('[data-quiz-result]').innerText();
	check(/Bác sĩ sẽ thăm khám và xác nhận/.test(note), 'quiz: the result states that a doctor confirms the plan');
	await ctx.close();
}

await browser.close();
console.log(failed ? `\n${failed} check(s) failed` : '\nall form checks passed');
process.exit(failed ? 1 : 0);
