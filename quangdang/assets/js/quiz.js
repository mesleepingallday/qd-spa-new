/**
 * Skin quiz (/tim-lieu-trinh/): one question per screen → 1–3 recommended services.
 *
 * The questions are server-rendered real inputs; this file only
 *  1) walks through the steps (skipping the "area" step when it does not apply),
 *  2) scores services from the answers (RULES below — edit them to change recommendations),
 *  3) renders the result and saves it in localStorage ("qd-quiz") for the booking page.
 */
(() => {
	'use strict';

	const root = document.querySelector('[data-quiz]');
	const dataEl = document.getElementById('quiz-data');
	if (!root || !dataEl) return;

	const STORE_KEY = 'qd-quiz';
	const STORE_MAX_AGE = 30 * 24 * 3600 * 1000; // 30 days
	const services = JSON.parse(dataEl.textContent).services;
	const bySlug = Object.fromEntries(services.map((s) => [s.slug, s]));

	/* ------------------------------------------------------------------
	 * Rules: which service answers which concern, where on the body, for which skin.
	 *   concerns : quiz concerns the service addresses (a service may serve two)
	 *   areas    : body areas it treats (the area question only shows areas listed here)
	 *   skin     : skin types it suits especially well (small bonus)
	 * ---------------------------------------------------------------- */
	const RULES = {
		'tri-mun-khang-khuan-da-tang': { concerns: ['mun'], areas: ['mat'], skin: ['dau', 'hon-hop'] },
		'tri-tham-sau-mun': { concerns: ['mun', 'tham'], areas: ['mat'], skin: ['nhay-cam'] },
		'tri-mun-viem-o-lung': { concerns: ['mun'], areas: ['lung'], skin: ['dau'] },
		'tri-mun-viem-o-tay-chan': { concerns: ['mun'], areas: ['tay-chan'], skin: [] },

		'tri-tham-nach': { concerns: ['tham'], areas: ['nach'], skin: [] },
		'tri-tham-mat': { concerns: ['tham'], areas: ['mat'], skin: [] },
		'tri-tham-ben-mong': { concerns: ['tham'], areas: ['bikini'], skin: [] },
		'tri-tham-vung-kin': { concerns: ['tham'], areas: ['bikini'], skin: ['nhay-cam'] },

		'dieu-tri-nam-chuyen-sau': { concerns: ['nam'], areas: ['mat'], skin: [] },
		'dieu-tri-tan-nhang': { concerns: ['nam'], areas: ['mat'], skin: [] },

		'tri-seo-loi': { concerns: ['seo'], areas: ['mat', 'lung', 'tay-chan'], skin: [] },
		'tri-seo-lom': { concerns: ['seo'], areas: ['mat'], skin: [] },

		'xoa-xam-long-may': { concerns: ['xoa-xam'], areas: ['mat'], skin: [] },
		'xoa-xam-mi-mat': { concerns: ['xoa-xam'], areas: ['mat'], skin: [] },
		'xoa-xam-tattoo': { concerns: ['xoa-xam'], areas: ['lung', 'tay-chan'], skin: [] },

		'triet-long': { concerns: ['triet-long'], areas: ['mat', 'nach', 'tay-chan', 'bikini'], skin: [] },

		'tre-hoa-da-cong-nghe-cao': { concerns: ['tre-hoa'], areas: ['mat'], skin: ['kho', 'hon-hop'] },
		'phuc-hoi-da': { concerns: ['tre-hoa'], areas: ['mat'], skin: ['nhay-cam', 'kho'] },
		'kiem-dau-tre-hoa-laser-picosure': { concerns: ['tre-hoa'], areas: ['mat'], skin: ['dau', 'hon-hop'] },

		filler: { concerns: ['filler-botox'], areas: ['mat'], skin: [] },
		botox: { concerns: ['filler-botox'], areas: ['mat'], skin: [] },
		'cang-chi': { concerns: ['filler-botox'], areas: ['mat'], skin: [] },
		meso: { concerns: ['filler-botox'], areas: ['mat'], skin: ['kho'] },
	};

	/** Areas relevant to a concern = union of its services' areas. */
	const concernAreas = (concern) => {
		const set = new Set();
		Object.values(RULES).forEach((r) => {
			if (r.concerns.includes(concern)) r.areas.forEach((a) => set.add(a));
		});
		return set;
	};
	/** A concern is "area-aware" when it treats more than just the face. */
	const isAreaAware = (concern) => concernAreas(concern).size > 1;

	/* ------------------------------------------------------------------
	 * Scoring
	 * ---------------------------------------------------------------- */
	const minNumber = (text) => {
		const m = /\d+/.exec(text || '');
		return m ? parseInt(m[0], 10) : null;
	};
	const noDowntime = (s) => /^không/i.test(s.downtime || '');
	const lightDowntime = (s) => /nhẹ|vài giờ/i.test(s.downtime || '');

	function score(slug, a, maxPrice) {
		const rule = RULES[slug];
		const s = bySlug[slug];
		if (!rule || !s) return null;

		const matched = rule.concerns.filter((c) => a.concerns.includes(c));
		if (!matched.length) return null;

		let points = matched.length * 10;
		const why = { concerns: matched, area: null, skin: null, priority: null };

		// Body area: bonus when it matches, penalty when the visitor chose other areas.
		const aware = matched.some(isAreaAware);
		if (aware && a.areas.length) {
			const hit = rule.areas.filter((x) => a.areas.includes(x));
			if (hit.length) {
				points += 6;
				why.area = hit;
			} else {
				points -= 5;
			}
		}

		if (a.skin && rule.skin.includes(a.skin)) {
			points += 2;
			why.skin = a.skin;
		}

		// Priority orders services that are otherwise equal.
		if (a.priority === 'tiet-kiem' && s.price_from && maxPrice) {
			points += (1 - s.price_from / maxPrice) * 3;
			why.priority = 'tiet-kiem';
		} else if (a.priority === 'nhanh') {
			const n = minNumber(s.sessions);
			if (n) points += (1 - Math.min(n, 12) / 12) * 3;
			why.priority = 'nhanh';
		} else if (a.priority === 'khong-nghi') {
			if (noDowntime(s)) {
				points += 3;
				why.priority = 'khong-nghi';
			} else if (lightDowntime(s)) {
				points += 1;
			}
		}
		return { slug, points, why };
	}

	/** Best services: the top one of each chosen concern first (variety), then by score. Max 3. */
	function recommend(a) {
		const maxPrice = Math.max(0, ...services.map((s) => s.price_from || 0));
		const scored = Object.keys(RULES)
			.map((slug) => score(slug, a, maxPrice))
			.filter(Boolean)
			.sort((x, y) => y.points - x.points);

		const picks = [];
		a.concerns.forEach((c) => {
			const best = scored.find((r) => RULES[r.slug].concerns.includes(c) && !picks.includes(r));
			if (best) picks.push(best);
		});
		scored.forEach((r) => {
			if (!picks.includes(r)) picks.push(r);
		});
		return picks.sort((x, y) => y.points - x.points).slice(0, 3);
	}

	/* ------------------------------------------------------------------
	 * Steps & answers
	 * ---------------------------------------------------------------- */
	const form = root.querySelector('[data-quiz-form]');
	const stepEls = Object.fromEntries([...form.querySelectorAll('[data-step]')].map((el) => [el.dataset.step, el]));
	const resultEl = root.querySelector('[data-quiz-result]');
	const topEl = root.querySelector('[data-quiz-top]');
	const labelEl = root.querySelector('[data-quiz-label]');
	const fillEl = root.querySelector('[data-quiz-fill]');
	const barEl = fillEl.parentElement;
	const backBtn = root.querySelector('[data-quiz-back]');
	const nextBtn = root.querySelector('[data-quiz-next]');
	const errorEl = root.querySelector('[data-quiz-error]');
	const restoreLink = root.querySelector('[data-quiz-restore]');

	const checked = (name) => [...form.querySelectorAll(`input[name="${name}"]:checked`)];
	const answers = () => ({
		concerns: checked('concern').map((i) => i.value),
		areas: checked('area').map((i) => i.value),
		skin: (checked('skin')[0] || {}).value || '',
		priority: (checked('priority')[0] || {}).value || '',
	});
	const labelOf = (name, value) => {
		const input = form.querySelector(`input[name="${name}"][value="${value}"]`);
		return input ? input.dataset.label : value;
	};

	/** Areas shown = union of areas for chosen concerns, and only for area-aware concerns. */
	function relevantAreas() {
		const set = new Set();
		answers().concerns.filter(isAreaAware).forEach((c) => concernAreas(c).forEach((x) => set.add(x)));
		return set;
	}

	/** Steps that apply to the current answers, in order. */
	function activeSteps() {
		const list = ['concern'];
		// Until a concern is picked we assume the area question applies, so the count does not jump.
		if (!answers().concerns.length || relevantAreas().size) list.push('area');
		list.push('skin', 'priority');
		return list;
	}

	let current = 'concern';

	function safeStore(fn) {
		try {
			return fn();
		} catch (e) {
			return null;
		}
	}

	function show(stepKey, moveFocus) {
		current = stepKey;
		const steps = activeSteps();
		const index = steps.indexOf(stepKey);

		if (stepKey === 'area') {
			const allowed = relevantAreas();
			stepEls.area.querySelectorAll('[data-area]').forEach((label) => {
				const ok = allowed.has(label.dataset.area);
				label.hidden = !ok;
				if (!ok) label.querySelector('input').checked = false;
			});
		}

		Object.entries(stepEls).forEach(([key, el]) => {
			el.hidden = key !== stepKey;
		});
		labelEl.textContent = `Bước ${index + 1}/${steps.length}`;
		fillEl.style.width = `${((index + 1) / steps.length) * 100}%`;
		barEl.setAttribute('aria-valuemax', String(steps.length));
		barEl.setAttribute('aria-valuenow', String(index + 1));
		backBtn.hidden = index === 0;
		nextBtn.querySelector('span').textContent = index === steps.length - 1 ? 'Xem gợi ý cho tôi' : 'Tiếp tục';
		errorEl.hidden = true;
		topEl.hidden = false;
		form.hidden = false;
		resultEl.hidden = true;

		if (moveFocus) {
			const heading = stepEls[stepKey].querySelector('.quiz__q');
			heading.focus({ preventScroll: true });
			root.scrollIntoView({ block: 'start', behavior: 'smooth' });
		}
	}

	function next() {
		const group = checked(current);
		if (!group.length) {
			const messages = {
				concern: 'Bạn chọn ít nhất một vấn đề để chúng tôi gợi ý nhé.',
				area: 'Bạn chọn ít nhất một vùng để chúng tôi gợi ý nhé.',
				skin: 'Bạn chọn một loại da, hoặc “Không rõ” nếu chưa chắc.',
				priority: 'Bạn chọn điều bạn ưu tiên nhất nhé.',
			};
			errorEl.textContent = messages[current];
			errorEl.hidden = false;
			return;
		}
		const steps = activeSteps();
		const i = steps.indexOf(current);
		if (i === steps.length - 1) {
			finish();
		} else {
			show(steps[i + 1], true);
		}
	}

	function back() {
		const steps = activeSteps();
		const i = steps.indexOf(current);
		if (i > 0) show(steps[i - 1], true);
	}

	/* ------------------------------------------------------------------
	 * Result
	 * ---------------------------------------------------------------- */
	function el(tag, attrs, ...children) {
		const node = document.createElement(tag);
		Object.entries(attrs || {}).forEach(([k, v]) => {
			if (v === false || v == null) return;
			if (k === 'class') node.className = v;
			else node.setAttribute(k, v === true ? '' : v);
		});
		children.flat().forEach((c) => {
			if (c == null || c === false) return;
			node.append(c.nodeType ? c : document.createTextNode(c));
		});
		return node;
	}

	const lower = (t) => t.charAt(0).toLowerCase() + t.slice(1);

	function reasonText(r, a) {
		const parts = r.why.concerns.map((c) => lower(labelOf('concern', c)));
		if (r.why.area) parts.push(...r.why.area.map((x) => 'vùng ' + lower(labelOf('area', x))));
		if (r.why.skin && a.skin !== 'khong-ro') parts.push(lower(labelOf('skin', r.why.skin)));
		let text = 'Vì bạn chọn: ' + parts.join(' + ');
		if (r.why.priority === 'khong-nghi') text += ' · không cần nghỉ dưỡng';
		if (r.why.priority === 'tiet-kiem') text += ' · chi phí hợp lý';
		if (r.why.priority === 'nhanh') text += ' · ít buổi';
		return text;
	}

	function card(r, a, isTop) {
		const s = bySlug[r.slug];
		const price = s.price_text ? el('span', { class: 'price' }, s.price_text, ' ', el('small', null, s.unit || '')) : el('span', { class: 'price-note' }, 'Báo giá khi khám');
		return el(
			'li',
			{ class: 'card quiz-rec' + (isTop ? ' quiz-rec--top' : '') },
			el(
				'div',
				{ class: 'card__body' },
				isTop ? el('span', { class: 'badge quiz-rec__badge' }, 'Phù hợp nhất với bạn') : null,
				el('h3', { class: 'card__title' }, el('a', { href: s.url }, s.title)),
				el('p', { class: 'quiz-rec__reason' }, reasonText(r, a)),
				s.excerpt ? el('p', { class: 'card__text' }, s.excerpt) : null,
				el(
					'dl',
					{ class: 'quiz-rec__facts' },
					el('div', null, el('dt', null, 'Giá từ'), el('dd', null, price)),
					s.sessions ? el('div', null, el('dt', null, 'Số buổi'), el('dd', null, s.sessions)) : null,
					s.downtime ? el('div', null, el('dt', null, 'Nghỉ dưỡng'), el('dd', null, s.downtime)) : null
				),
				el('a', { class: 'link-more', href: s.url }, 'Xem chi tiết', el('span', { class: 'sr-only' }, ` ${s.title}`))
			)
		);
	}

	function renderResult(a, recs) {
		const top = bySlug[recs[0].slug];
		const summary = [
			...a.concerns.map((c) => labelOf('concern', c)),
			...a.areas.map((x) => labelOf('area', x)),
			a.skin ? labelOf('skin', a.skin) : null,
			a.priority ? labelOf('priority', a.priority) : null,
		].filter(Boolean);

		const articlesSource = document.querySelector(`[data-articles="${top.category}"]`);

		resultEl.replaceChildren(
			el('h2', { class: 'quiz-result__title', id: 'quiz-result-title', tabindex: '-1' }, recs.length > 1 ? `${recs.length} dịch vụ bạn có thể quan tâm` : 'Dịch vụ bạn có thể quan tâm'),
			el('p', { class: 'quiz-result__summary' }, 'Dựa trên lựa chọn của bạn: ', el('strong', null, summary.join(' · '))),
			el('ol', { class: 'quiz-recs' }, recs.map((r, i) => card(r, a, i === 0))),
			el(
				'div',
				{ class: 'quiz-result__cta' },
				el('a', { class: 'btn btn--primary btn--lg', href: top.book }, 'Đặt lịch tư vấn miễn phí'),
				el('a', { class: 'btn btn--outline btn--lg', href: root.dataset.zalo, target: '_blank', rel: 'noopener' }, 'Hỏi bác sĩ qua Zalo')
			),
			el('p', { class: 'quiz-result__cta-note' }, `Đặt lịch sẽ tự chọn sẵn “${top.title}”. Bạn vẫn đổi được ở bước sau.`),
			el(
				'div',
				{ class: 'notice' },
				el('p', null, 'Đây là gợi ý ban đầu dựa trên câu trả lời của bạn. Bác sĩ sẽ thăm khám và xác nhận phác đồ phù hợp trực tiếp tại phòng khám.')
			),
			articlesSource
				? el('div', { class: 'quiz-result__articles' }, el('h3', { class: 'quiz-result__articles-title' }, 'Đọc thêm trước khi quyết định'), articlesSource.cloneNode(true))
				: null,
			el('button', { class: 'btn btn--ghost quiz-result__again', type: 'button', 'data-quiz-again': '' }, 'Làm lại từ đầu')
		);

		form.hidden = true;
		topEl.hidden = true;
		resultEl.hidden = false;
		resultEl.querySelector('#quiz-result-title').focus({ preventScroll: true });
		root.scrollIntoView({ block: 'start', behavior: 'smooth' });
	}

	function finish() {
		const a = answers();
		const recs = recommend(a);
		if (!recs.length) {
			errorEl.textContent = 'Chưa tìm thấy liệu trình khớp. Bạn thử chọn lại hoặc nhắn Zalo để bác sĩ tư vấn nhé.';
			errorEl.hidden = false;
			return;
		}
		const top = bySlug[recs[0].slug];
		safeStore(() =>
			localStorage.setItem(STORE_KEY, JSON.stringify({ v: 1, at: Date.now(), answers: a, top: { slug: top.slug, title: top.title }, picks: recs.map((r) => r.slug) }))
		);
		renderResult(a, recs);
	}

	function restart() {
		form.reset();
		show('concern', true);
	}

	/* ------------------------------------------------------------------
	 * Wire up
	 * ---------------------------------------------------------------- */
	form.addEventListener('submit', (e) => {
		e.preventDefault();
		next();
	});
	form.addEventListener('change', () => {
		errorEl.hidden = true;
	});
	backBtn.addEventListener('click', back);
	resultEl.addEventListener('click', (e) => {
		if (e.target.closest('[data-quiz-again]')) restart();
	});

	// Earlier result: offer to reopen it.
	const saved = safeStore(() => JSON.parse(localStorage.getItem(STORE_KEY) || 'null'));
	if (saved && saved.answers && saved.at && Date.now() - saved.at < STORE_MAX_AGE) {
		restoreLink.hidden = false;
		restoreLink.addEventListener('click', (e) => {
			e.preventDefault();
			const a = saved.answers;
			Object.entries({ concern: a.concerns, area: a.areas, skin: [a.skin], priority: [a.priority] }).forEach(([name, values]) => {
				form.querySelectorAll(`input[name="${name}"]`).forEach((i) => {
					i.checked = (values || []).includes(i.value);
				});
			});
			const recs = recommend(a);
			if (recs.length) renderResult(a, recs);
		});
	}

	show('concern', false);
})();
