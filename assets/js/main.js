(() => {
    const grid = document.querySelector('#program-grid');
    if (!grid) return;
    const tabs = [...document.querySelectorAll('.school-tabs button')];
    const groupSelect = document.querySelector('#group-filter');
    const originalItems = [...grid.querySelectorAll('.program-item')];
    let items = [...originalItems];
    const empty = document.querySelector('#no-results');
    const showAll = document.querySelector('#show-all-programs');
    let school = 'all';
    let expanded = false;

    const shuffle = (values) => {
        const result = [...values];
        for (let index = result.length - 1; index > 0; index -= 1) {
            const randomIndex = Math.floor(Math.random() * (index + 1));
            [result[index], result[randomIndex]] = [result[randomIndex], result[index]];
        }
        return result;
    };

    function arrangeItems(randomize = false) {
        if (!randomize) {
            items = [...originalItems];
        } else {
            const groups = new Map();
            originalItems.forEach((item) => {
                const key = item.querySelector('.program-card').dataset.school;
                if (!groups.has(key)) groups.set(key, []);
                groups.get(key).push(item);
            });
            const schoolKeys = shuffle([...groups.keys()]);
            const firstRound = schoolKeys.map((key) => shuffle(groups.get(key)).shift());
            const selected = new Set(firstRound);
            const remaining = shuffle(originalItems.filter((item) => !selected.has(item)));
            items = [...firstRound, ...remaining];
        }
        items.forEach((item) => grid.append(item));
    }

    function applyFilters() {
        let visible = 0;
        const group = groupSelect.value;
        items.forEach((item) => {
            const card = item.querySelector('.program-card');
            const matches =
                (school === 'all' || card.dataset.school === school) &&
                (group === 'all' || card.dataset.group === group);
            const withinLimit = expanded || visible < 6;
            item.hidden = !matches || !withinLimit;
            item.classList.remove('is-collapsed');
            if (matches) visible += 1;
        });
        const matchingCount = items.filter((item) => {
            const card = item.querySelector('.program-card');
            return (
                (school === 'all' || card.dataset.school === school) &&
                (group === 'all' || card.dataset.group === group)
            );
        }).length;
        empty.hidden = matchingCount !== 0;
        const summary = document.querySelector('#filter-summary');
        if (summary)
            summary.textContent = `Hiển thị ${Math.min(expanded ? matchingCount : 6, matchingCount)} / ${matchingCount} chương trình`;
        showAll.hidden = expanded || matchingCount <= 6;
        if (!showAll.hidden) showAll.textContent = `Xem tất cả ${matchingCount} chương trình`;
    }

    tabs.forEach((tab) =>
        tab.addEventListener('click', () => {
            school = tab.dataset.school;
            expanded = false;
            arrangeItems(school === 'all');
            tabs.forEach((button) => {
                button.classList.toggle('is-active', button === tab);
                button.setAttribute('aria-selected', String(button === tab));
            });
            applyFilters();
        })
    );
    groupSelect.addEventListener('change', () => {
        expanded = false;
        applyFilters();
    });
    showAll.addEventListener('click', () => {
        expanded = true;
        applyFilters();
    });
    document.querySelectorAll('.js-school-filter').forEach((button) =>
        button.addEventListener('click', () => {
            const target = tabs.find((tab) => tab.dataset.school === button.dataset.school);
            target?.click();
            document.querySelector('#chuong-trinh')?.scrollIntoView({ behavior: 'smooth' });
        })
    );
    arrangeItems(true);
    applyFilters();
})();

(() => {
    const drawer = document.querySelector('#program-drawer');
    if (!drawer) return;
    const panel = drawer.querySelector('.drawer-panel');
    const content = drawer.querySelector('#drawer-content');
    const programs = JSON.parse(document.querySelector('#program-data').textContent);
    let lastFocus = null;

    const esc = (value = '') =>
        String(value).replace(
            /[&<>'"]/g,
            (char) =>
                ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#39;', '"': '&quot;' })[char]
        );
    const list = (values) => `<ul class="drawer-info-list">${values.map((value) => `<li>${esc(value)}</li>`).join('')}</ul>`;
    const documentChecklist = (values) => `<ul class="drawer-document-checklist">${values.map((value) => `<li><svg aria-hidden="true" viewBox="0 0 24 24"><path d="m5 12 4 4L19 6"/></svg><span>${esc(value)}</span></li>`).join('')}</ul>`;
    const documentLinks = (p) => {
        if (!Array.isArray(p.document_links) || !p.document_links.length) return '';
        return `<div class="drawer-document-links">${p.document_links
            .map(
                (item) =>
                    `<a class="document-template-link" href="${esc(item.href)}" download><span aria-hidden="true">↓</span>${esc(item.label)}</a>`
            )
            .join('')}</div>`;
    };

    const contactMarkup = (p) => {
        if (!Array.isArray(p.contact_details) || !p.contact_details.length) {
            return `<p>${esc(p.contact)}</p>`;
        }
        return `<div class="contact-text">${p.contact_details
            .map((item) => {
                const value = item.href
                    ? `<a href="${esc(item.href)}">${esc(item.value)}</a>`
                    : esc(item.value);
                return `<p class="contact-line"><span class="contact-label-inline">${esc(item.label)}:</span> ${value}</p>`;
            })
            .join('')}</div>`;
    };
    function markup(p) {
        const pathways = Array.isArray(p.pathways) && p.pathways.length
            ? `<h3>Lộ trình học theo văn bằng đầu vào</h3>${list(p.pathways)}`
            : '';
        const training = p.study_format
            ? `<h3>Hình thức đào tạo</h3><p>${esc(p.study_format)}</p>${list(p.training_details || [])}`
            : '';
        const curriculum = Array.isArray(p.curriculum_details) && p.curriculum_details.length
            ? `<h3>${p.introduction ? 'Chương trình đào tạo' : 'Bạn sẽ học những gì?'}</h3>${p.introduction ? '<p>Chương trình tập trung vào các nhóm kiến thức chính:</p>' : ''}${list(p.curriculum_details)}`
            : '';
        const introduction = p.introduction ? `<h3>Giới thiệu ngành học</h3><p>${esc(p.introduction)}</p>` : '';
        const careers = Array.isArray(p.career_details) && p.career_details.length
            ? `<h3>Cơ hội việc làm</h3>${list(p.career_details)}` : '';
        const assessment = Array.isArray(p.assessment_details) && p.assessment_details.length
            ? `<h3>Học tập và đánh giá</h3>${list(p.assessment_details)}`
            : '';
        const contact = contactMarkup(p);
        const intake = p.introduction ? '' : `<h3>Khóa/đợt nhận hồ sơ</h3><p>${esc(p.intake)}</p>`;
        return `<p class="drawer-school">${esc(p.school_name)}</p><h2 id="drawer-title">${esc(p.name)}</h2><p>${esc(p.degree)}</p>
      <div class="drawer-meta"><div><span>Số tín chỉ</span><strong>${esc(p.credits)}</strong></div><div><span>Thời gian</span><strong>${esc(p.duration)}</strong></div><div class="drawer-meta-wide"><span>Tuyển sinh</span><strong>${esc(p.method)}</strong></div></div>
      ${introduction}<h3>Ai có thể đăng ký?</h3>${list(p.audience)}${pathways}${curriculum}${careers}${training}${assessment}${intake}<h3>${p.documents_intro ? 'Hồ sơ đăng ký xét tuyển' : 'Hồ sơ cần chuẩn bị'}</h3>${p.documents_intro ? `<p>${esc(p.documents_intro)}</p>` : ''}${documentChecklist(p.documents)}${documentLinks(p)}<h3>Nơi nhận hồ sơ/liên hệ</h3>${contact}
      <p class="drawer-note">Thông tin theo thông báo tuyển sinh 2026 của nhà trường, có thể thay đổi. Vui lòng liên hệ để xác nhận.</p><a href="${esc(p.source)}" target="_blank" rel="noopener">Xem nguồn chính thức</a>
      <a class="btn btn-gold js-drawer-consult" href="#dang-ky" data-program="${esc(p.slug)}">Đăng ký tư vấn ngành này</a>`;
    }
    function focusables() {
        return [...panel.querySelectorAll('a[href],button:not([disabled])')];
    }
    function open(slug, updateHash = true) {
        const p = programs[slug];
        if (!p) return;
        lastFocus = document.activeElement;
        content.innerHTML = markup(p);
        panel.scrollTop = 0;
        drawer.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');
        document.body.classList.add('no-scroll');
        if (updateHash) history.replaceState(null, '', `#nganh-${slug}`);
        panel.focus({ preventScroll: true });
        requestAnimationFrame(() => {
            panel.scrollTop = 0;
        });
        content.querySelector('.js-drawer-consult').addEventListener('click', (event) => {
            selectProgram(event.currentTarget.dataset.program);
            close(false);
        });
    }
    function close(clearHash = true) {
        drawer.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('no-scroll');
        if (clearHash && location.hash.startsWith('#nganh-'))
            history.replaceState(null, '', location.pathname + location.search);
        lastFocus?.focus();
    }
    function selectProgram(slug) {
        const select = document.querySelector('[name="program"]');
        const program = programs[slug];
        if (!select || !program) return;
        select.value = program.name;
    }
    document
        .querySelectorAll('.js-open-program')
        .forEach((button) =>
            button.addEventListener('click', () =>
                open(button.closest('.program-card').dataset.slug)
            )
        );
    document
        .querySelectorAll('.js-consult')
        .forEach((link) =>
            link.addEventListener('click', () => selectProgram(link.dataset.program))
        );
    drawer
        .querySelectorAll('[data-close-drawer]')
        .forEach((el) => el.addEventListener('click', () => close()));
    document.addEventListener('keydown', (event) => {
        if (!drawer.classList.contains('is-open')) return;
        if (event.key === 'Escape') close();
        if (event.key === 'Tab') {
            const f = focusables();
            if (!f.length) return;
            const first = f[0],
                last = f[f.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });
    const hashSlug = location.hash.startsWith('#nganh-') ? location.hash.slice(7) : '';
    if (programs[hashSlug]) open(hashSlug, false);
})();

(() => {
    const form = document.querySelector('#lead-form');
    if (!form) return;
    const status = form.querySelector('.form-status');
    const phone = form.elements.phone;
    const programSelect = form.elements.program;
    const button = form.querySelector('[type="submit"]');
    const programs = JSON.parse(document.querySelector('#program-data').textContent);
    let sending = false;
    let registrationId = '';
    let signature = '';

    function setStatus(message, type = '') {
        status.textContent = message;
        status.className = `form-status ${type ? `is-${type}` : ''}`;
        if (message) status.focus();
    }

    function createRegistrationId() {
        const characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let code = '';

        do {
            const bytes = new Uint8Array(6);
            crypto.getRandomValues(bytes);
            code = Array.from(bytes, (value) => characters[value % characters.length]).join('');
        } while (!/[A-Z]/.test(code) || !/\d/.test(code));

        return code;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (sending) return;
        setStatus('');
        if (!form.reportValidity()) return;

        const endpoint = String(window.VTS_REGISTRATION?.endpoint || '').trim();
        if (!/^https:\/\/script\.google\.com\/macros\/s\/[A-Za-z0-9_-]+\/exec$/.test(endpoint)) {
            setStatus('Form đang được kết nối. Vui lòng gọi hotline 0964 380 333.', 'error');
            return;
        }

        const values = new FormData(form);
        const name = String(values.get('name') || '').trim();
        const phoneNumber = String(values.get('phone') || '').trim();
        const programName = String(values.get('program') || '').trim();
        const currentSignature = JSON.stringify([name, phoneNumber, programName]);
        if (signature !== currentSignature) {
            signature = currentSignature;
            registrationId = createRegistrationId();
        }
        const programLabel = programName || 'Chưa chọn';
        const data = new URLSearchParams({
            full_name: name,
            phone: phoneNumber,
            program: programLabel,
            privacy_consent: values.get('consent') === '1' ? '1' : '0',
            website: String(values.get('website') || ''),
            registration_id: registrationId,
        });

        const controller = new AbortController();
        const timeout = window.setTimeout(() => controller.abort(), 35000);
        sending = true;
        button.disabled = true;
        button.textContent = 'Đang gửi…';
        form.setAttribute('aria-busy', 'true');
        setStatus('Đang gửi thông tin đăng ký của bạn…');
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: data,
                credentials: 'omit',
                redirect: 'follow',
                signal: controller.signal,
            });
            const result = await response.json();
            if (!response.ok || result.ok !== true) throw new Error('Registration not confirmed');
            form.reset();
            setStatus('Đăng ký thành công. VTS sẽ liên hệ với bạn trong thời gian sớm nhất.', 'success');
            registrationId = '';
            signature = '';
        } catch (error) {
            setStatus(
                'Chưa xác nhận được đăng ký. Thông tin đã nhập được giữ lại; vui lòng thử lại hoặc gọi 0964 380 333.',
                'error'
            );
        } finally {
            window.clearTimeout(timeout);
            sending = false;
            form.removeAttribute('aria-busy');
            button.disabled = false;
            button.textContent = 'Đăng ký ngay';
        }
    });
    phone.addEventListener('input', () => phone.setCustomValidity(''));
})();

(() => {
    const header = document.querySelector('#site-header');
    const toggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('#main-nav');
    const topButton = document.querySelector('#back-to-top');
    const reduced = matchMedia('(prefers-reduced-motion: reduce)').matches;
    const navLinks = [...(nav?.querySelectorAll('a[href^="#"]') || [])];
    const navSections = navLinks
        .map((link) => ({ link, section: document.querySelector(link.getAttribute('href')) }))
        .filter((item) => item.section);

    const setActiveLink = (activeLink = null) => {
        navLinks.forEach((link) => {
            const active = link === activeLink;
            link.classList.toggle('is-active', active);
            if (active) link.setAttribute('aria-current', 'location');
            else link.removeAttribute('aria-current');
        });
    };

    const updateScrollSpy = () => {
        const threshold = scrollY + header.offsetHeight + 32;
        let activeLink = null;
        navSections.forEach(({ link, section }) => {
            if (section.offsetTop <= threshold) activeLink = link;
        });
        if (innerHeight + scrollY >= document.documentElement.scrollHeight - 4) {
            activeLink = navSections.at(-1)?.link || activeLink;
        }
        setActiveLink(activeLink);
    };

    const onScroll = () => {
        header.classList.toggle('is-scrolled', scrollY > 12);
        topButton.classList.toggle('is-visible', scrollY > 600);
        updateScrollSpy();
    };
    addEventListener('scroll', onScroll, { passive: true });
    onScroll();
    toggle?.addEventListener('click', () => {
        const open = nav.classList.toggle('is-open');
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Đóng menu' : 'Mở menu');
    });
    navLinks.forEach((link) =>
        link.addEventListener('click', (event) => {
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                event.preventDefault();
                const top = target.getBoundingClientRect().top + scrollY - header.offsetHeight - 16;
                scrollTo({ top: Math.max(0, top), behavior: reduced ? 'auto' : 'smooth' });
                history.replaceState(null, '', link.getAttribute('href'));
                setActiveLink(link);
            }
            nav.classList.remove('is-open');
            toggle?.setAttribute('aria-expanded', 'false');
            toggle?.setAttribute('aria-label', 'Mở menu');
        })
    );
    topButton?.addEventListener('click', () =>
        scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' })
    );
    const reveal = [...document.querySelectorAll('.reveal')];
    if (reduced || !('IntersectionObserver' in window))
        reveal.forEach((el) => el.classList.add('is-visible'));
    else {
        const io = new IntersectionObserver(
            (entries) =>
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        io.unobserve(entry.target);
                    }
                }),
            { threshold: 0.12 }
        );
        reveal.forEach((el) => io.observe(el));
    }
    document.querySelectorAll('.hero [data-count]').forEach((el) => {
        el.textContent = el.dataset.count;
    });
    const audience = {
        thpt: [
            'Đã tốt nghiệp THPT hoặc tương đương',
            'Có thể đăng ký các chương trình phù hợp điều kiện đầu vào.',
            'Thời gian theo từng trường và chương trình.',
            'Xét tuyển hồ sơ theo thông báo của trường.',
        ],
        trungcap: [
            'Đã tốt nghiệp trung cấp',
            'Một số chương trình phù hợp tiếp nhận nhóm này.',
            'Thời gian phụ thuộc tín chỉ được công nhận.',
            'Kết quả do nhà trường thẩm định theo từng hồ sơ.',
        ],
        caodang: [
            'Đã tốt nghiệp cao đẳng',
            'Một số chương trình HOU tiếp nhận nhóm này.',
            'Có thể được xem xét công nhận/chuyển đổi tín chỉ.',
            'Kết quả do trường thẩm định hồ sơ.',
        ],
        daihoc: [
            'Đã tốt nghiệp đại học',
            'Có thể đăng ký chương trình phù hợp hoặc văn bằng thứ hai.',
            'Có thể được xem xét công nhận và chuyển đổi tín chỉ.',
            'Lộ trình cụ thể phụ thuộc chương trình và kết quả thẩm định hồ sơ của từng trường.',
        ],
    };
    document.querySelectorAll('.audience-tabs button').forEach((button) =>
        button.addEventListener('click', () => {
            const data = audience[button.dataset.audience];
            document.querySelectorAll('.audience-tabs button').forEach((b) => {
                b.classList.toggle('is-active', b === button);
                b.setAttribute('aria-selected', String(b === button));
            });
            document.querySelector('#audience-title').textContent = data[0];
            document.querySelector('#audience-list').innerHTML = data
                .slice(1)
                .map((x) => `<li>${x}</li>`)
                .join('');
        })
    );
    document.querySelectorAll('.faq-item button').forEach((button) =>
        button.addEventListener('click', () => {
            const item = button.closest('.faq-item');
            const panel = item.querySelector('.faq-panel');
            const open = !item.classList.contains('is-open');
            item.classList.toggle('is-open', open);
            button.setAttribute('aria-expanded', String(open));
            panel.hidden = !open;
        })
    );
})();

(() => {
    const copyPhone = document.querySelector('[data-copy-phone]');
    copyPhone?.addEventListener('click', async () => {
        const status = document.querySelector('.copy-status');
        try {
            await navigator.clipboard.writeText(copyPhone.dataset.copyPhone);
            status.textContent = 'Đã sao chép số hotline.';
        } catch {
            status.textContent = 'Hotline: 0964 380 333';
        }
    });
    // The hero remains static; progressive enhancement only below it.
    if (
        !matchMedia('(prefers-reduced-motion: reduce)').matches &&
        'IntersectionObserver' in window
    ) {
        const candidates = [...document.querySelectorAll('.reveal')].filter(
            (el) => !el.closest('.hero')
        );
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.05 }
        );
        candidates.forEach((el) => {
            if (el.getBoundingClientRect().top < innerHeight) el.classList.add('is-visible');
            observer.observe(el);
        });
        document.documentElement.classList.add('motion-ready');
    }
    // Roving keyboard navigation for both filter and entry-level tab groups.
    document.querySelectorAll('[role="tablist"]').forEach((group) => {
        const tabs = [...group.querySelectorAll('[role="tab"]')];
        function sync() {
            tabs.forEach(
                (tab) => (tab.tabIndex = tab.getAttribute('aria-selected') === 'true' ? 0 : -1)
            );
        }
        sync();
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', sync);
            tab.addEventListener('keydown', (event) => {
                let next;
                if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
                if (event.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
                if (event.key === 'Home') next = 0;
                if (event.key === 'End') next = tabs.length - 1;
                if (next === undefined) return;
                event.preventDefault();
                tabs[next].click();
                tabs[next].focus();
                sync();
            });
        });
    });
})();
