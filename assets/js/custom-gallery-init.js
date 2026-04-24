document.addEventListener("DOMContentLoaded", function () {

    /* =========================
       LIGHTBOX
    ========================== */
    const lightboxOptions = {
        alwaysShowNavOnTouchDevices: false,
        resizeDuration: 200,
        wrapAround: true,
        disableScrolling: true,
        fitImagesInViewport: true
    };

    if (typeof lightbox !== "undefined") {
        lightbox.option(lightboxOptions);
    }

    /* =========================
       CONSTANTS
    ========================== */
    const filterClass = Object.freeze({
        category: 'btn-filter-gallery',
        resort: 'chk-filter-gallery',
        inputFilter: 'input-filter-gallery'
    });

    /* =========================
       HELPERS
    ========================== */

    const qs = (selector) => document.querySelector(selector);
    const qsa = (selector) => [...document.querySelectorAll(selector)];

    const setTotalFiltered = (total) => {
        const el = qs('.filtered-t');
        if (el) el.textContent = ` : ${total}`;
    };

    const getActiveCategories = () => {
        // New UI: .hg-cat-btn
        const newPills = qsa('.hg-cat-btn.active');
        if (newPills.length) {
            const classes = newPills.map(el => el.dataset.category);
            return classes.includes("all") ? [] : classes;
        }
        // Legacy UI: .btn-filter-gallery
        const actives = qsa(`.${filterClass.category}.active`);
        const classes = actives.map(el => el.dataset.category);
        return classes.includes("all") ? [] : classes;
    };

    const getCheckedResorts = () => {
        // New UI: hotel select dropdown (.hg-select-resort)
        const select = qs('.hg-select-resort');
        if (select) {
            const val = select.value;
            return val === 'all' ? [] : [val];
        }
        // Legacy UI: checkbox-based (.chk-filter-gallery)
        const checked = qsa(`.${filterClass.resort}:checked`);
        const classes = checked.map(el => el.dataset.hotel);
        return classes.includes("all") ? [] : classes;
    };

    const inputDoFilter = () => {
        const input = qs(`.${filterClass.inputFilter}`);
        if (!input) return [];
        return input.value
            .split(" ")
            .filter(word => /^[0-9a-zA-Z]+$/.test(word));
    };

    /* =========================
       FILTER LOGIC
    ========================== */

    const filterImages = (categories, resorts, inputWords) => {

        const items = qsa('.hg-grid-item');
        const lightboxLink = 'resorts-gallery';
        let total = 0;

        items.forEach(item => {
            const link = item.querySelector('.custom-gallery-image');

            if (!categories.length && !resorts.length && !inputWords.length) {
                item.classList.remove('d-none');
                if (link) link.setAttribute('data-lightbox', lightboxLink);
                total++;
                return;
            }

            let flagCategory = true;
            let flagResort = true;
            let flagInput = true;

            const itemClasses = [...item.classList];

            /* CATEGORY */
            if (categories.length) {
                flagCategory = categories.some(cat => itemClasses.includes(cat));
            }

            /* RESORT */
            if (resorts.length) {
                flagResort = resorts.some(res => itemClasses.includes(res));
            }

            /* INPUT */
            if (inputWords.length) {
                const tags = (item.dataset.filter || "").toLowerCase();
                flagInput = inputWords.some(word =>
                    tags.includes(word.toLowerCase())
                );
            }

            if (flagCategory && flagResort && flagInput) {
                item.classList.remove('d-none');
                if (link) link.setAttribute('data-lightbox', lightboxLink);
                total++;
            } else {
                item.classList.add('d-none');
                if (link) link.removeAttribute('data-lightbox');
            }
        });

        return total;
    };

    /* =========================
       PAGINATION
    ========================== */

    const ITEMS_PER_PAGE = 15;
    let currentPage = 1;

    const paginateGallery = (reset = false) => {
        if (reset) currentPage = 1;

        const filtered = qsa('.hg-grid-item:not(.d-none)');
        const limit    = currentPage * ITEMS_PER_PAGE;

        filtered.forEach((item, i) => {
            const link = item.querySelector('.custom-gallery-image');
            if (i < limit) {
                item.classList.remove('hg-page-hidden');
                if (link) link.setAttribute('data-lightbox', 'resorts-gallery');
            } else {
                item.classList.add('hg-page-hidden');
                if (link) link.removeAttribute('data-lightbox');
            }
        });

        const btn = qs('.hg-load-more');
        if (btn) {
            btn.style.display = limit >= filtered.length ? 'none' : 'flex';
        }
    };

    const loadMoreBtn = qs('.hg-load-more');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', () => {
            currentPage++;
            paginateGallery(false);
            applySkeletons();
            if (window.lenis) window.lenis.resize();
        });
    }

    /* =========================
       PER-IMAGE SKELETON LOADER
    ========================== */

    const applySkeletons = () => {
        const items = qsa('.hg-grid-item:not(.d-none):not(.hg-page-hidden)');

        items.forEach(item => {
            const inner = item.querySelector('.hg-grid-item__inner');
            const img   = item.querySelector('img');
            if (!inner || !img) return;

            if (img.complete && img.naturalWidth > 0) return;

            inner.classList.add('hg-skeleton');

            const onDone = () => inner.classList.remove('hg-skeleton');
            img.addEventListener('load',  onDone, { once: true });
            img.addEventListener('error', onDone, { once: true });
        });
    };

    /* =========================
       MAIN FILTER
    ========================== */

    const startFilter = () => {

        const notFound = qs('.gallery-not-found');
        if (notFound) notFound.classList.add('d-none');

        const categories = getActiveCategories();
        const resorts = getCheckedResorts();
        const inputWords = inputDoFilter();

        const total = filterImages(categories, resorts, inputWords);

        if (!categories.length) {
            const allCat = qs(`.${filterClass.category}[data-category="all"]`);
            if (allCat) allCat.classList.add('active');
        }

        if (!resorts.length) {
            const allRes = qs(`.${filterClass.resort}[data-hotel="all"]`);
            if (allRes) allRes.checked = true;
        }

        if (total <= 0 && notFound) {
            notFound.classList.remove('d-none');
        } else {
            paginateGallery(true);
            applySkeletons();
        }

        setTotalFiltered(total);
    };

    /* =========================
       EVENTS
    ========================== */

    qsa(`.${filterClass.category}`).forEach(button => {
        button.addEventListener('click', () => {

            const filter = button.dataset.category;
            const allBtn = qs(`.${filterClass.category}[data-category="all"]`);

            if (filter !== 'all') {
                if (allBtn) allBtn.classList.remove('active');
                button.classList.toggle('active');
            } else {
                qsa(`.${filterClass.category}`).forEach(b => b.classList.remove('active'));
                button.classList.add('active');
            }

            startFilter();
        });
    });

    qsa(`.${filterClass.resort}`).forEach(checkbox => {
        checkbox.addEventListener('change', () => {

            const filter = checkbox.dataset.hotel;
            const allCheckbox = qs(`.${filterClass.resort}[data-hotel="all"]`);

            if (filter !== 'all') {
                if (allCheckbox) allCheckbox.checked = false;
            } else {
                qsa(`.${filterClass.resort}:checked`)
                    .forEach(cb => cb.checked = false);
                if (allCheckbox) allCheckbox.checked = true;
            }

            startFilter();
        });
    });

    const resetBtn = qs('.reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {

            qsa(`.${filterClass.resort}:checked`)
                .forEach(cb => cb.checked = false);

            const allRes = qs(`.${filterClass.resort}[data-hotel="all"]`);
            if (allRes) allRes.checked = true;

            qsa(`.${filterClass.category}`)
                .forEach(b => b.classList.remove('active'));

            const allCat = qs(`.${filterClass.category}[data-category="all"]`);
            if (allCat) allCat.classList.add('active');

            const input = qs(`.${filterClass.inputFilter}`);
            if (input) input.value = "";

            startFilter();
        });
    }

    /* Hotel select (new UI) */
    const hotelSelect = qs('.hg-select-resort');
    if (hotelSelect) {
        hotelSelect.addEventListener('change', startFilter);
    }

    /* Category pills (new UI — .hg-cat-btn) */
    qsa('.hg-cat-btn').forEach(button => {
        button.addEventListener('click', () => {
            const filter = button.dataset.category;
            const allBtn = qs('.hg-cat-btn[data-category="all"]');
            if (filter !== 'all') {
                if (allBtn) allBtn.classList.remove('active');
                button.classList.toggle('active');
                if (!qs('.hg-cat-btn.active')) {
                    if (allBtn) allBtn.classList.add('active');
                }
            } else {
                qsa('.hg-cat-btn').forEach(b => b.classList.remove('active'));
                button.classList.add('active');
            }
            startFilter();
        });
    });

    /* INPUT debounce */
    let typingTimer;
    const doneTypingInterval = 300;

    const input = qs(`.${filterClass.inputFilter}`);
    if (input) {
        input.addEventListener('keyup', () => {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(startFilter, doneTypingInterval);
        });
    }

    // Paginación inicial
    if (qs('.hg-grid')) {
        paginateGallery(true);
        applySkeletons();
    }

});
