/**
 * Booking forms (site-wide: the booking strip appears on many pages).
 *
 * Progressive enhancement for every `form[data-booking-form]`:
 *  - friendly inline validation (aria-invalid + .field__error),
 *  - POST JSON to the REST route (window.qdBooking.rest) instead of a page reload,
 *  - strips: swap the form for a thank-you; booking page: confirmation screen with .ics.
 * The booking page (`[data-booking-page]`) also gets collapsing steps and a live summary.
 * Without JS every form still posts to admin-post.php (see inc/features/booking.php).
 */
(() => {
	'use strict';

	const cfg = window.qdBooking || {};
	const clinic = cfg.clinic || {};
	const forms = document.querySelectorAll('form[data-booking-form]');
	if (!forms.length || !cfg.rest) return;

	const CHECK_SVG =
		'<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';

	/* ---------- phone ---------- */
	function normalizePhone(raw) {
		let d = String(raw || '').replace(/[^0-9+]/g, '').replace(/^\+?84/, '0').replace(/[^0-9]/g, '');
		return /^0[35789][0-9]{8}$/.test(d) || /^02[0-9]{9}$/.test(d) ? d : '';
	}
	const formatPhone = (p) => (p.length === 10 ? `${p.slice(0, 4)} ${p.slice(4, 7)} ${p.slice(7)}` : p);

	/* ---------- errors ---------- */
	function errorHost(form, name) {
		return form.querySelector(`[data-error-for="${name}"]`);
	}

	function clearError(form, name) {
		const host = errorHost(form, name);
		if (host) {
			host.hidden = true;
			host.textContent = '';
			return;
		}
		const control = form.elements[name];
		if (!control || control.length) return;
		control.removeAttribute('aria-invalid');
		control.removeAttribute('aria-describedby');
		const msg = control.closest('.field') && control.closest('.field').querySelector('.field__error[data-auto]');
		if (msg) msg.remove();
	}

	function showError(form, name, message) {
		const host = errorHost(form, name);
		if (host) {
			host.textContent = message;
			host.hidden = false;
			return host;
		}
		const control = form.elements[name];
		if (!control || control.length) return null;
		clearError(form, name);
		const field = control.closest('.field');
		const id = (control.id || name) + '-error';
		const p = document.createElement('p');
		p.className = 'field__error';
		p.id = id;
		p.dataset.auto = '';
		p.textContent = message;
		(field || control.parentNode).append(p);
		control.setAttribute('aria-invalid', 'true');
		control.setAttribute('aria-describedby', id);
		return control;
	}

	function clearAll(form) {
		['name', 'phone', 'service', 'date', 'slot'].forEach((n) => clearError(form, n));
		const status = form.querySelector('.booking-form__status');
		if (status) {
			status.textContent = '';
			status.classList.remove('is-error');
		}
	}

	/** Show errors; returns the first element to focus. */
	function showErrors(form, errors) {
		let first = null;
		const order = ['service', 'date', 'slot', 'name', 'phone'];
		order.forEach((name) => {
			if (!errors[name]) return;
			const el = showError(form, name, errors[name]);
			if (!first) {
				const radio = form.querySelector(`input[name="${name}"]`);
				first = el && el.matches && el.matches('input,select,textarea') ? el : radio || el;
			}
		});
		if (errors.form) setStatus(form, errors.form, true);
		return first;
	}

	function setStatus(form, text, isError) {
		const status = form.querySelector('.booking-form__status');
		if (!status) return;
		status.textContent = text;
		status.classList.toggle('is-error', !!isError);
	}

	/* ---------- validation (mirrors the server) ---------- */
	function validate(form) {
		const errors = {};
		const isPage = form.hasAttribute('data-booking-page');
		const name = (form.elements.name.value || '').trim();
		if (name.length < 2) errors.name = 'Bạn vui lòng cho phòng khám biết họ tên để tiện xưng hô.';

		const phone = (form.elements.phone.value || '').trim();
		if (!phone) errors.phone = 'Bạn vui lòng nhập số điện thoại để phòng khám gọi xác nhận.';
		else if (!normalizePhone(phone)) errors.phone = 'Số điện thoại chưa đúng. Bạn nhập 10 số, ví dụ 0912 345 678.';

		if (isPage) {
			if (!form.querySelector('input[name="service"]:checked')) errors.service = 'Bạn chọn một dịch vụ, hoặc chọn “Chưa biết – cần bác sĩ tư vấn” nhé.';
			if (!form.querySelector('input[name="date"]:checked')) errors.date = 'Bạn chọn ngày muốn đến nhé.';
			if (!form.querySelector('input[name="slot"]:checked')) errors.slot = 'Bạn chọn buổi sáng, chiều hoặc tối nhé.';
		}
		return errors;
	}

	/* ---------- submit ---------- */
	async function send(form) {
		const data = {};
		new FormData(form).forEach((v, k) => {
			if (k !== 'action') data[k] = v;
		});
		const res = await fetch(cfg.rest, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
			body: JSON.stringify(data),
		});
		const json = await res.json().catch(() => null);
		return { res, json, data };
	}

	forms.forEach((form) => {
		const button = form.querySelector('button[type="submit"]');
		const buttonLabel = button && button.querySelector('span');
		const idleLabel = buttonLabel ? buttonLabel.textContent : '';

		form.addEventListener('input', (e) => e.target.name && clearError(form, e.target.name));
		form.addEventListener('change', (e) => e.target.name && clearError(form, e.target.name));

		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			clearAll(form);
			const errors = validate(form);
			if (Object.keys(errors).length) {
				const first = showErrors(form, errors);
				if (first) {
					first.scrollIntoView({ block: 'center', behavior: 'smooth' });
					if (first.focus) first.focus({ preventScroll: true });
				}
				return;
			}

			button.disabled = true;
			if (buttonLabel) buttonLabel.textContent = 'Đang gửi…';
			try {
				const { res, json, data } = await send(form);
				if (json && json.ok) {
					form.hasAttribute('data-booking-page') ? showConfirmation(form, data) : showThanks(form, data);
					return;
				}
				if (json && json.errors) {
					const first = showErrors(form, json.errors);
					if (first && first.focus) first.focus();
				} else {
					throw new Error(String(res.status));
				}
			} catch (err) {
				const tel = clinic.hotline ? ` hoặc gọi ${clinic.hotline}` : '';
				setStatus(form, `Chưa gửi được lịch hẹn. Bạn kiểm tra kết nối mạng rồi thử lại${tel} nhé.`, true);
			}
			button.disabled = false;
			if (buttonLabel) buttonLabel.textContent = idleLabel;
		});
	});

	/* ---------- strip: thank-you ---------- */
	function showThanks(form, data) {
		const first = (data.name || '').trim().split(/\s+/).pop();
		const phone = formatPhone(normalizePhone(data.phone));
		const box = document.createElement('div');
		box.className = 'booking-thanks';
		box.tabIndex = -1;
		box.setAttribute('role', 'status');
		box.innerHTML = `<span class="booking-thanks__check">${CHECK_SVG}</span>`;
		const h = document.createElement('p');
		h.className = 'booking-thanks__title';
		h.textContent = `Cảm ơn ${first}, phòng khám đã nhận yêu cầu!`;
		const p = document.createElement('p');
		p.append('Phòng khám sẽ gọi số ');
		const strong = document.createElement('strong');
		strong.textContent = phone;
		p.append(strong, ' trong 15 phút (giờ làm việc).');
		const a = document.createElement('a');
		a.className = 'link-more';
		a.href = form.dataset.bookingUrl || '/dat-lich/';
		a.textContent = 'Chọn sẵn ngày giờ cho lần đến';
		box.append(h, p, a);
		form.replaceChildren(box);
		box.focus();
	}

	/* ---------- booking page ---------- */
	const page = document.querySelector('form[data-booking-page]');
	const done = document.querySelector('[data-booking-done]');

	function selectedLabel(form, name) {
		const input = form.querySelector(`input[name="${name}"]:checked`);
		return input ? input.dataset.label : '';
	}

	function showConfirmation(form, data) {
		const rows = [
			['Dịch vụ', selectedLabel(form, 'service')],
			['Ngày', selectedLabel(form, 'date')],
			['Giờ', selectedLabel(form, 'slot')],
			['Họ tên', data.name],
			['Điện thoại', formatPhone(normalizePhone(data.phone))],
		].filter((r) => r[1]);
		const dl = done.querySelector('[data-done-summary]');
		dl.replaceChildren(
			...rows.map(([k, v]) => {
				const row = document.createElement('div');
				const dt = document.createElement('dt');
				const dd = document.createElement('dd');
				dt.textContent = k;
				dd.textContent = v;
				row.append(dt, dd);
				return row;
			})
		);
		dl.hidden = false;

		const calBtn = done.querySelector('[data-add-calendar]');
		if (data.date && data.slot && cfg.slots && cfg.slots[data.slot]) {
			calBtn.hidden = false;
			calBtn.onclick = () => downloadIcs(data, selectedLabel(form, 'service'));
		}

		form.hidden = true;
		done.hidden = false;
		done.focus({ preventScroll: true });
		done.scrollIntoView({ block: 'start', behavior: 'smooth' });
	}

	/** Calendar file. Clinic time is UTC+7 all year, so convert to UTC explicitly. */
	function downloadIcs(data, serviceLabel) {
		const slot = cfg.slots[data.slot];
		const [y, m, d] = data.date.split('-').map(Number);
		const stamp = (hhmm) => {
			const [h, min] = hhmm.split(':').map(Number);
			return new Date(Date.UTC(y, m - 1, d, h - 7, min)).toISOString().replace(/[-:]/g, '').replace(/\.\d+/, '');
		};
		const esc = (t) => String(t).replace(/([,;\\])/g, '\\$1').replace(/\n/g, '\\n');
		const lines = [
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//Quang Dang//Dat lich//VI',
			'BEGIN:VEVENT',
			`UID:${Date.now()}@${location.hostname}`,
			`DTSTAMP:${new Date().toISOString().replace(/[-:]/g, '').replace(/\.\d+/, '')}`,
			`DTSTART:${stamp(slot.start)}`,
			`DTEND:${stamp(slot.end)}`,
			`SUMMARY:${esc('Lịch hẹn tại ' + (clinic.name || 'phòng khám'))}`,
			`LOCATION:${esc(clinic.address || '')}`,
			`DESCRIPTION:${esc((serviceLabel ? 'Dịch vụ: ' + serviceLabel + '. ' : '') + 'Giờ dự kiến, phòng khám sẽ gọi xác nhận.')}`,
			'BEGIN:VALARM',
			'TRIGGER:-PT1H',
			'ACTION:DISPLAY',
			'DESCRIPTION:Sắp đến giờ hẹn',
			'END:VALARM',
			'END:VEVENT',
			'END:VCALENDAR',
		];
		const blob = new Blob([lines.join('\r\n')], { type: 'text/calendar;charset=utf-8' });
		const a = document.createElement('a');
		a.href = URL.createObjectURL(blob);
		a.download = 'lich-hen-quang-dang.ics';
		document.body.append(a);
		a.click();
		setTimeout(() => {
			URL.revokeObjectURL(a.href);
			a.remove();
		}, 500);
	}

	if (!page) return;

	const steps = {};
	page.querySelectorAll('.bk-step[data-step]').forEach((el) => (steps[el.dataset.step] = el));
	const sums = {};
	page.querySelectorAll('[data-sum]').forEach((el) => (sums[el.dataset.sum] = el));
	const today = page.dataset.today;

	function setCollapsed(step, collapsed, valueText) {
		const body = step.querySelector('[data-step-body]');
		const edit = step.querySelector('[data-step-edit]');
		const value = step.querySelector('[data-step-value]');
		if (!edit) return;
		body.hidden = collapsed;
		edit.hidden = !collapsed;
		edit.setAttribute('aria-expanded', String(!collapsed));
		if (collapsed) {
			value.textContent = valueText || '';
			step.classList.add('is-done');
		} else {
			value.textContent = '';
			step.classList.remove('is-done');
		}
	}

	Object.values(steps).forEach((step) => {
		const edit = step.querySelector('[data-step-edit]');
		if (!edit) return;
		edit.addEventListener('click', () => {
			setCollapsed(step, false);
			const first = step.querySelector('input:checked') || step.querySelector('input:not([disabled])');
			if (first) first.focus({ preventScroll: true });
			step.scrollIntoView({ block: 'start', behavior: 'smooth' });
		});
	});

	const whenText = () => [selectedLabel(page, 'date'), selectedLabel(page, 'slot')].filter(Boolean).join(' · ');

	function updateSummary() {
		const set = (key, text) => {
			sums[key].textContent = text || sums[key].dataset.empty;
			sums[key].classList.toggle('is-set', !!text);
		};
		set('service', selectedLabel(page, 'service'));
		set('date', selectedLabel(page, 'date'));
		set('slot', selectedLabel(page, 'slot'));
	}

	/** On "today", hide time slots that already passed (times come from the server's clock). */
	function syncSlots() {
		const dateInput = page.querySelector('input[name="date"]:checked');
		const isToday = dateInput && dateInput.value === today;
		page.querySelectorAll('input[name="slot"]').forEach((input) => {
			const off = isToday && input.dataset.passed === '1';
			input.disabled = off;
			if (off && input.checked) input.checked = false;
			input.closest('.bk-opt').classList.toggle('is-off', off);
		});
	}

	const scrollTo = (el) => el.scrollIntoView({ block: 'start', behavior: 'smooth' });

	page.addEventListener('change', (e) => {
		const name = e.target.name;
		if (name === 'service') {
			setCollapsed(steps.service, true, selectedLabel(page, 'service'));
			const hint = page.querySelector('.bk-hint');
			if (hint) hint.remove();
			if (!(page.querySelector('input[name="date"]:checked') && page.querySelector('input[name="slot"]:checked'))) scrollTo(steps.when);
		}
		if (name === 'date') syncSlots();
		if ((name === 'date' || name === 'slot') && page.querySelector('input[name="date"]:checked') && page.querySelector('input[name="slot"]:checked')) {
			setCollapsed(steps.when, true, whenText());
			scrollTo(steps.info);
			page.elements.name.focus({ preventScroll: true });
		}
		updateSummary();
	});

	/* Initial state */
	syncSlots();
	if (page.querySelector('input[name="service"]:checked')) setCollapsed(steps.service, true, selectedLabel(page, 'service'));
	if (page.querySelector('input[name="date"]:checked') && page.querySelector('input[name="slot"]:checked')) setCollapsed(steps.when, true, whenText());

	// No service in the link? Offer the one from the skin quiz (a hint, easy to change).
	if (!page.querySelector('input[name="service"]:checked')) {
		try {
			const saved = JSON.parse(localStorage.getItem('qd-quiz') || 'null');
			const input = saved && saved.top && page.querySelector(`input[name="service"][value="${saved.top.slug}"]`);
			if (input && Date.now() - saved.at < 30 * 24 * 3600 * 1000) {
				input.checked = true;
				const hint = document.createElement('p');
				hint.className = 'bk-hint';
				hint.textContent = `Đã chọn sẵn “${saved.top.title}” theo kết quả kiểm tra da của bạn. Bạn có thể đổi bên dưới.`;
				steps.service.querySelector('[data-step-body]').prepend(hint);
			}
		} catch (e) {
			/* storage unavailable: nothing to prefill */
		}
	}
	updateSummary();

	// A server-side error (no-JS post) lands us here with the form pre-filled: jump to it.
	if (page.querySelector('[aria-invalid="true"], .bk__alert')) page.scrollIntoView({ block: 'start' });
})();
