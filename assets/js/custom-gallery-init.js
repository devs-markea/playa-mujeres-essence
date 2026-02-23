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
        const actives = qsa(`.${filterClass.category}.active`);
        const classes = actives.map(el => el.dataset.category);
        return classes.includes("all") ? [] : classes;
    };

    const getCheckedResorts = () => {
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

        const images = qsa('.custom-gallery-image');
        const lightboxLink = 'resorts-gallery';
        let total = 0;

        images.forEach(image => {

            if (!categories.length && !resorts.length && !inputWords.length) {
                image.classList.remove('d-none');
                image.setAttribute('data-lightbox', lightboxLink);
                total++;
                return;
            }

            let flagCategory = true;
            let flagResort = true;
            let flagInput = true;

            const imageClasses = [...image.classList];

            /* CATEGORY */
            if (categories.length) {
                flagCategory = categories.some(cat => imageClasses.includes(cat));
            }

            /* RESORT */
            if (resorts.length) {
                flagResort = resorts.some(res => imageClasses.includes(res));
            }

            /* INPUT */
            if (inputWords.length) {
                const tags = (image.dataset.filter || "").toLowerCase();
                flagInput = inputWords.some(word =>
                    tags.includes(word.toLowerCase())
                );
            }

            if (flagCategory && flagResort && flagInput) {
                image.classList.remove('d-none');
                image.setAttribute('data-lightbox', lightboxLink);
                total++;
            } else {
                image.classList.add('d-none');
                image.removeAttribute('data-lightbox');
            }
        });

        return total;
    };

    /* =========================
       BLOCK UI (simple version)
    ========================== */

    const blockContainer = (selector) => {
        const container = qs(selector);
        if (!container) return;

        container.style.position = "relative";

        const overlay = document.createElement("div");
        overlay.className = "vanilla-blocker";
        overlay.style.position = "absolute";
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = "100%";
        overlay.style.height = "100%";
        overlay.style.background = "rgba(255,255,255,0.9)";
        overlay.style.display = "flex";
        overlay.style.alignItems = "center";
        overlay.style.justifyContent = "center";
        overlay.style.zIndex = 9;
        overlay.innerHTML = '<span class="loader"></span>';

        container.appendChild(overlay);

        setTimeout(() => overlay.remove(), 300);
    };

    /* =========================
       MAIN FILTER
    ========================== */

    const startFilter = () => {

        blockContainer('.gallery-grid-resorts');

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

});
