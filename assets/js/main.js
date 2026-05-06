// ─── Detección de plataforma (corre antes del DOMContentLoaded) ──────────────
// Añade clases al <html> para que CSS pueda ajustar font-weight por OS.
// .is-mac   → macOS (cualquier navegador)
// .is-safari → Safari (macOS o iOS)
(function () {
    var ua  = navigator.userAgent;
    var isMac    = /Macintosh|MacIntel|MacPPC/.test(ua);
    var isSafari = /^((?!chrome|android).)*safari/i.test(ua);

    var isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);

    if (isMac)    document.documentElement.classList.add('is-mac');
    if (isSafari) document.documentElement.classList.add('is-safari');
    if (isTouch)  document.documentElement.classList.add('is-touch');
})();
// ─────────────────────────────────────────────────────────────────────────────

window.App = window.App || {};

(function (App) {
    // Estado
    const state = {
        lastScrollTop: 0,
        videoHeroBottom: 0,
        isWhereOpen: false,
        isExperiencesOpen: false
    };

    // Nodos compartidos
    let body,
        header,
        menuMobile,
        closeBtn,
        hamburgerBtn,
        panelWhereMobile,
        panelExpMobile,
        megaPanelWhere,
        megaPanelExp,
        menuWhereTrigger,
        menuExpTrigger,
        langSwitcher,
        navDesktop,
        navGroups,
        logoPicture,
        logoImg,
        logoSource,
        videoHero,
        playButton,
        heroVideo,
        info,
        divider,
        overlayVideo,
        forceHeaderThemeEl;

    // Cache de elementos
    function cacheElements() {
        body            = document.body;
        header          = document.querySelector('[data-header]');
        menuMobile      = document.querySelector('.pm-menu-mobile');
        closeBtn        = document.querySelector('[data-menu-close]');
        hamburgerBtn    = document.querySelector('[data-menu-hamburger]');

        panelWhereMobile      = document.querySelector('.pm-where-to-stay');
        panelExpMobile        = document.querySelector('.pm-menu-experiences');

        megaPanelWhere        = document.querySelector('.mega-panel__where-to-stay');
        megaPanelExp          = document.querySelector('.mega-panel__experiences');
        menuWhereTrigger      = null;
        menuExpTrigger        = null;

        langSwitcher          = document.querySelector('.pm-lang-switcher__current');
        navDesktop            = document.querySelectorAll('.pm-header__menu .pm-navbar .menu-item > a');
        navGroups             = document.querySelector('.pm-navbar__more-btn');

        logoPicture           = document.querySelector('.site-logo.logo-desktop');
        logoImg               = null;
        logoSource            = null;

        if (logoPicture) {
            const tag = (logoPicture.tagName || '').toLowerCase();

            if (tag === 'img') {
                logoImg = logoPicture;
            } else {
                logoImg = logoPicture.querySelector('img');
                logoSource = logoPicture.querySelector('source');
            }
        }

        videoHero             = document.querySelector('.video-hero');
        playButton            = document.getElementById('play-button-hero');
        heroVideo             = document.querySelector('.video-hero__video');
        info                  = document.querySelector('.video-hero__information');
        divider               = document.querySelector('.divider');
        overlayVideo          = document.querySelector('.video-hero__overlay');

        forceHeaderThemeEl    = document.querySelector('[data-force-header-theme="menu"]');
    }


    // Actualiza límite inferior del video-hero
    function updateVideoHeroBottom() {
        if (!videoHero) return;
        const rect = videoHero.getBoundingClientRect();
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        state.videoHeroBottom = rect.top + scrollTop + rect.height;
    }

    // Tema del header
    function setHeaderTheme(isMenuTheme) {
        if (!logoPicture || !logoImg || !header) return;

        // dataset puede estar en el wrapper o en el <img> (con la nueva función queda en el <img>)
        const lightUrl = (logoPicture.dataset && logoPicture.dataset.logoLight) || (logoImg.dataset && logoImg.dataset.logoLight);
        const darkUrl  = (logoPicture.dataset && logoPicture.dataset.logoDark)  || (logoImg.dataset && logoImg.dataset.logoDark);

        const moreBtn = navGroups || document.querySelector('.pm-navbar__more-btn');

        if (isMenuTheme) {
            if (darkUrl) {
                logoImg.src = darkUrl;
                logoImg.removeAttribute('srcset');
                logoImg.removeAttribute('sizes');

                if (logoSource) {
                    logoSource.srcset = darkUrl;
                }
            }

            navDesktop.forEach(item => item.classList.add('nav-item-dark'));
            if (moreBtn) {
                moreBtn.classList.add('nav-item-dark');
            }

            if (langSwitcher) {
                langSwitcher.style.color = '#323232';
            }

            if (hamburgerBtn) {
                hamburgerBtn.style.color = '#323232';
            }

            header.classList.add('menu-open');
        } else {
            navDesktop.forEach(item => item.classList.remove('nav-item-dark'));
            if (moreBtn) {
                moreBtn.classList.remove('nav-item-dark');
            }

            if (lightUrl) {
                logoImg.src = lightUrl;
                logoImg.removeAttribute('srcset');
                logoImg.removeAttribute('sizes');

                if (logoSource) {
                    logoSource.srcset = lightUrl;
                }
            }

            if (langSwitcher) {
                langSwitcher.style.color = '#FFF';
            }
            if (hamburgerBtn) {
                hamburgerBtn.style.color = '#FFF';
            }

            header.classList.remove('menu-open');
        }
    }

    // Decide tema según scroll y estado
    function updateHeaderTheme() {
        const SCROLL_THRESHOLD = 150;

        if (forceHeaderThemeEl) {
            setHeaderTheme(true);
            return;
        }



        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const pastHero  = scrollTop >= SCROLL_THRESHOLD;

        const anyPanelOpen = state.isWhereOpen || state.isExperiencesOpen;

        const useMenuTheme = pastHero || anyPanelOpen;
        setHeaderTheme(useMenuTheme);
    }

    // Cerrar todos los megapaneles
    function closeAllPanels() {
        if (!body) return;

        body.classList.remove('is-where-to-stay-open', 'is-experiences-open');

        if (megaPanelWhere) megaPanelWhere.style.maxHeight = 0;
        if (megaPanelExp)   megaPanelExp.style.maxHeight   = 0;

        state.isWhereOpen       = false;
        state.isExperiencesOpen = false;

        updateHeaderTheme();
    }

    // Abrir/cerrar panel específico
    function togglePanel(type, event) {
        if (event) event.preventDefault();
        if (!body) return;

        const isWhere   = type === 'where';
        const openClass = isWhere ? 'is-where-to-stay-open' : 'is-experiences-open';
        const panelEl   = isWhere ? megaPanelWhere : megaPanelExp;

        if (!panelEl) return;

        const isAlreadyOpen = body.classList.contains(openClass);

        if (isAlreadyOpen) {
            closeAllPanels();
            return;
        }

        closeAllPanels();

        body.classList.add(openClass);

        state.isWhereOpen       = isWhere;
        state.isExperiencesOpen = !isWhere;

        const inner = panelEl.querySelector('.mega-panel__inner');

        if (inner && header) {
            const headerHeight  = header.offsetHeight;
            const contentHeight = inner.scrollHeight;
            panelEl.style.maxHeight = (contentHeight + headerHeight) + 'px';
        }

        updateHeaderTheme();
    }

    // Scroll global: cierra megapaneles al bajar y actualiza tema
    function onScroll() {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const anyPanelOpen = state.isWhereOpen || state.isExperiencesOpen;

        // Cerrar al hacer scroll hacia abajo
        if (anyPanelOpen && scrollTop > state.lastScrollTop + 30) {
            closeAllPanels();
        }

        state.lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        updateHeaderTheme();
    }
    // ToTop Button
    function initToTopButton() {
        const button = document.querySelector('.js-top-button');
        if (!button) return;

        const toggleVisibility = () => {
            if (window.scrollY > 300) {
                button.classList.remove('is-hidden');
            } else {
                button.classList.add('is-hidden');
            }
        };

        // Estado inicial
        toggleVisibility();

        // Escuchar scroll
        window.addEventListener('scroll', toggleVisibility, { passive: true });

        // Click → scroll top
        button.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth',
            });
        });
    }


    // Lang switcher
    function initLangSwitcher() {
        const switchers = document.querySelectorAll(".pm-lang-switcher");

        switchers.forEach((switcher) => {
            const trigger  = switcher.querySelector(".pm-lang-switcher__current");
            const dropdown = switcher.querySelector(".pm-lang-switcher__list");

            if (!trigger || !dropdown) return;

            trigger.addEventListener("click", (e) => {
                e.stopPropagation();
                switcher.classList.toggle("is-open");
            });

            document.addEventListener("click", (e) => {
                if (!switcher.contains(e.target)) {
                    switcher.classList.remove("is-open");
                }
            });
        });
    }

    // Primary Showcase Hero - Contact Dropdown
    function initPrimaryShowcaseHeroDropdown() {
        const dropdowns = document.querySelectorAll('[data-psh-dropdown]');
        if (!dropdowns.length) return;

        function closeAll() {
            dropdowns.forEach((dd) => {
                dd.classList.remove('is-open');
                const btn = dd.querySelector('.primary-showcase-hero__dropdown-toggle');
                if (btn) btn.setAttribute('aria-expanded', 'false');
            });
        }

        dropdowns.forEach((root) => {
            const btn  = root.querySelector('.primary-showcase-hero__dropdown-toggle');
            const menu = root.querySelector('.primary-showcase-hero__dropdown-menu');
            if (!btn || !menu) return;

            btn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                const isOpen = root.classList.contains('is-open');
                closeAll();

                if (!isOpen) {
                    root.classList.add('is-open');
                    btn.setAttribute('aria-expanded', 'true');
                }
            });

            root.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        });

        document.addEventListener('click', function () {
            closeAll();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeAll();
        });
    }


    const openMenu = () => {
        if (menuMobile) menuMobile.classList.add("-is-active");
        if (body) body.style.overflow = "hidden";
    };

    const closeMenu = () => {
        if (menuMobile) menuMobile.classList.remove("-is-active");
        if (body) body.style.overflow = "";
    };

    // Menú mobile
    function initMobileMenu() {
        if (!header) return;
        hamburgerBtn && hamburgerBtn.addEventListener("click", openMenu);
        closeBtn     && closeBtn.addEventListener("click", closeMenu);
    }

    var isMobile = () => window.innerWidth < 992;

    // Menús mobile de filtros/experiencias
    function initMobilePanels() {
        const openersWhere = document.querySelectorAll('.trigger-filters');
        const closerWhere  = document.querySelector('.back-arrow-where-to-stay');

        if (panelWhereMobile) {
            openersWhere.forEach(opener => {
                opener.addEventListener('click', e => {
                    if (!isMobile()) return;
                    e.preventDefault();
                    openMenu();
                    panelWhereMobile.classList.add('is-open');
                    opener.classList.add('is-open');
                });
            });

            closerWhere && closerWhere.addEventListener('click', e => {
                e.preventDefault();
                panelWhereMobile.classList.remove('is-open');
                openersWhere.forEach(opener => opener.classList.remove('is-open'));
            });
        }

        const openersExp = document.querySelectorAll('.trigger-experiences');
        const closerExp  = document.querySelector('.back-arrow-menu-experiences');

        if (panelExpMobile) {
            openersExp.forEach(opener => {
                opener.addEventListener('click', e => {
                    if (!isMobile()) return;
                    e.preventDefault();
                    openMenu();
                    panelExpMobile.classList.add('is-open');
                    opener.classList.add('is-open');
                });
            });

            closerExp && closerExp.addEventListener('click', e => {
                e.preventDefault();
                panelExpMobile.classList.remove('is-open');
                openersExp.forEach(opener => opener.classList.remove('is-open'));
            });
        }
    }

    // Megapanel desktop
    function initMegaPanels() {
        document.addEventListener('click', function (event) {
            var whereTrigger = event.target.closest('.trigger-filters');
            var expTrigger   = event.target.closest('.trigger-experiences');

            if (whereTrigger && megaPanelWhere && !isMobile()) {
                togglePanel('where', event);
                return;
            }

            if (expTrigger && megaPanelExp && !isMobile()) {
                togglePanel('experiences', event);
                return;
            }

            // Cerrar al hacer click fuera del header
            var clickInsideHeader = header && header.contains(event.target);
            var anyPanelOpen = state.isWhereOpen || state.isExperiencesOpen;

            if (!clickInsideHeader && anyPanelOpen) {
                closeAllPanels();
            }
        });
    }

    // Submenús nativos del nav de escritorio — toggle click + cerrar fuera.
    // WP renderiza los hijos como .sub-menu dentro de .menu-item-has-children.
    function initDesktopSubmenus() {
        var nav = document.querySelector('.pm-navbar-desktop');
        if (!nav) return;

        var parentItems = Array.from(nav.querySelectorAll(':scope > .menu-item-has-children'));
        if (!parentItems.length) return;

        parentItems.forEach(function (li) {
            var link = li.querySelector(':scope > a');
            if (!link) return;

            // Toggle al hacer click en el enlace padre
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var isOpen = li.classList.contains('is-open');

                // Cerrar todos primero
                parentItems.forEach(function (el) { el.classList.remove('is-open'); });

                if (!isOpen) li.classList.add('is-open');
            });
        });

        // Cerrar al hacer click fuera del nav
        document.addEventListener('click', function (e) {
            if (!nav.contains(e.target)) {
                parentItems.forEach(function (el) { el.classList.remove('is-open'); });
            }
        });
    }

    // Video hero: botón y scroll top
    function initVideoHeroControls() {
        if (!playButton || !heroVideo) return;

        let isPlaying = false;

        playButton.addEventListener('click', function (e) {
            e.preventDefault();
            isPlaying = !isPlaying;

            playButton.classList.toggle('is-playing', isPlaying);

            if (header) {
                header.classList.toggle('pm-header--hidden', isPlaying);
            }

            const vbgInst = window.VIDEO_BACKGROUNDS && window.VIDEO_BACKGROUNDS.get(heroVideo);

            if (isPlaying) {
                window.scrollTo({ top: 0, behavior: 'smooth' });

                setTimeout(() => {
                    if (body) body.classList.add('body-overflow-hidden');
                }, 600);

                if (vbgInst) {
                    vbgInst.unmute();
                    vbgInst.play();
                } else if (typeof heroVideo.play === 'function') {
                    heroVideo.muted = false;
                    heroVideo.play();
                }
            } else {
                if (vbgInst) {
                    vbgInst.mute();
                } else {
                    heroVideo.muted = true;
                }

                if (body) body.classList.remove('body-overflow-hidden');
            }

            if (info) info.classList.toggle('is-hidden', isPlaying);
            if (divider) divider.classList.toggle('is-hidden', isPlaying);
            if (overlayVideo) overlayVideo.classList.toggle('is-hidden', isPlaying);
        });
    }

    // Scroll y resize
    function initScrollHandler() {
        updateVideoHeroBottom();
        window.addEventListener('resize', updateVideoHeroBottom);
        window.addEventListener('scroll', onScroll);
        updateHeaderTheme();
    }

    function initHotelsParallax() {
        const section = document.querySelector('#hotels-parallax');
        if (!section) return;

        const rowTop = section.querySelector('.parallax-row--right');
        const rowBot = section.querySelector('.parallax-row--left');
        if (!rowTop || !rowBot) return;

        const getCols = (row) => Array.from(row.querySelectorAll(':scope > div[class*="col-"]'));

        let all = [...getCols(rowTop), ...getCols(rowBot)];

        for (let i = all.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [all[i], all[j]] = [all[j], all[i]];
        }

        rowTop.innerHTML = '';
        rowBot.innerHTML = '';

        all.forEach((col, idx) => {
            (idx % 2 === 0 ? rowTop : rowBot).appendChild(col);
        });

        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        let baseTop = 0;
        let baseBot = 0;
        let ticking = false;

        const clamp = (n, min, max) => Math.max(min, Math.min(max, n));
        const lerp  = (a, b, t) => a + (b - a) * t;

        function computeBaseOffset(targetEl) {
            if (!targetEl) return 0;

            const sectionRect = section.getBoundingClientRect();
            const targetRect  = targetEl.getBoundingClientRect();

            // OJO: tu /6 es raro; si querías centro real usa /2. Lo dejo igual por compat.
            const sectionCenterX = sectionRect.left + sectionRect.width / 6;
            const targetCenterX  = targetRect.left + targetRect.width / 6;

            return sectionCenterX - targetCenterX;
        }

        function recalcBases() {
            if (prefersReduced || window.innerWidth < 768) {
                baseTop = 0;
                baseBot = 0;
                rowTop.style.transform = '';
                rowBot.style.transform = '';
                return;
            }

            // medir SIN transform
            rowTop.style.transform = '';
            rowBot.style.transform = '';

            const topItems = rowTop.querySelectorAll('div[class*="col-"]');
            const botItems = rowBot.querySelectorAll('div[class*="col-"]');

            const topLast  = topItems.length ? topItems[topItems.length - 1] : null;
            const botFirst = botItems.length ? botItems[0] : null;

            baseTop = computeBaseOffset(topLast);
            baseBot = computeBaseOffset(botFirst);
        }

        function update() {
            ticking = false;

            if (prefersReduced || window.innerWidth < 768) {
                rowTop.style.transform = '';
                rowBot.style.transform = '';
                return;
            }

            const rect = section.getBoundingClientRect();
            const vh   = window.innerHeight || document.documentElement.clientHeight;

            const TOP_OFFSET = 50; // tu umbral
            const p = clamp((vh - (rect.top - TOP_OFFSET)) / vh, 0, 1);

            // ✅ Tope: cuando p llega a 1, tx = 0 y YA NO PASA DE 0
            const txTop = lerp(baseTop, 0, p);
            const txBot = lerp(baseBot, 0, p);

            rowTop.style.transform = `translate3d(${txTop}px, 0, 0)`;
            rowBot.style.transform = `translate3d(${txBot}px, 0, 0)`;
        }

        function onScroll() {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', () => {
            recalcBases();
            onScroll();
        });

        recalcBases();
        update();
    }


    function initExperiencesDesktopScrollLock(sectionEl, contentSwiper) {
        if (!sectionEl || !contentSwiper) return;

        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReduced) return;

        const DESKTOP_MIN = 1024;
        const exitBtn = sectionEl.querySelector('[data-experiences-exit]');

        let enabled = true;
        let active = false;

        // wheel throttle
        let wheelAccum = 0;
        let wheelLock = false;
        const WHEEL_THRESHOLD = 90;
        const COOLDOWN_MS = 450;

        // Scroll lock sin jump (padding-right por scrollbar)
        function lockScroll() {
            if (active) return;
            active = true;

            const sbw = window.innerWidth - document.documentElement.clientWidth;
            document.body.style.overflow = 'hidden';
            document.body.style.paddingRight = sbw ? `${sbw}px` : '';
            sectionEl.classList.add('is-scrolllock-active');
        }

        function unlockScroll() {
            if (!active) return;
            active = false;

            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            sectionEl.classList.remove('is-scrolllock-active');
        }

        function disable() {
            enabled = false;
            unlockScroll();
        }

        function isDesktop() {
            return window.innerWidth >= DESKTOP_MIN;
        }

        function onWheel(e) {
            if (!enabled || !active) return;

            // IMPORTANTE: sin esto, el scroll no se bloquea
            e.preventDefault();

            if (wheelLock) return;

            wheelAccum += e.deltaY;

            if (Math.abs(wheelAccum) < WHEEL_THRESHOLD) return;

            const dir = wheelAccum > 0 ? 1 : -1;
            wheelAccum = 0;

            const last = contentSwiper.slides.length - 1;
            const idx = contentSwiper.activeIndex;

            // si llega al final y sigue bajando, suelta el lock para seguir scrolleando la página
            if (dir > 0 && idx >= last) {
                unlockScroll();
                return;
            }

            // si llega al inicio y sigue subiendo, suelta el lock
            if (dir < 0 && idx <= 0) {
                unlockScroll();
                return;
            }

            wheelLock = true;
            if (dir > 0) contentSwiper.slideNext();
            else contentSwiper.slidePrev();

            setTimeout(() => { wheelLock = false; }, COOLDOWN_MS);
        }

        function onKeyDown(e) {
            if (!enabled) return;
            if (e.key === 'Escape') disable();
        }

        // Activación confiable: IntersectionObserver
        const io = new IntersectionObserver((entries) => {
            if (!enabled) return;

            const entry = entries[0];
            if (!entry) return;

            if (!isDesktop()) { unlockScroll(); return; }

            // si está visible >= 60% => lock
            if (entry.isIntersecting && entry.intersectionRatio >= 1) lockScroll();
            else unlockScroll();
        }, { threshold: [0, 1, 1] });

        io.observe(sectionEl);

        // listeners (wheel NO pasivo)
        window.addEventListener('wheel', onWheel, { passive: false });
        window.addEventListener('keydown', onKeyDown);

        // Exit button
        if (exitBtn) {
            exitBtn.addEventListener('click', (e) => {
                e.preventDefault();
                disable();
            });
        }

        // resize: si baja de desktop, desbloquea
        window.addEventListener('resize', () => {
            wheelAccum = 0;
            if (!isDesktop()) unlockScroll();
        }, { passive: true });
    }

    function initBlogHeroSwiper() {
        if (typeof window.Swiper === 'undefined') return;

        var roots = document.querySelectorAll('[data-blog-hero-swiper]');
        if (!roots || !roots.length) return;

        Array.prototype.forEach.call(roots, function (el) {
            if (el.dataset.swiperInitialized === '1') return;
            el.dataset.swiperInitialized = '1';

            var section = el.closest('.blog-hero');
            var featuredIndex = 0;
            var groupStartIndex = 0;
            var desktopGroupSize = parseInt(el.getAttribute('data-group-size-desktop'), 10);
            if (isNaN(desktopGroupSize) || desktopGroupSize < 2) {
                desktopGroupSize = 4;
            }

            function isDesktopViewport() {
                return window.innerWidth >= 992;
            }

            function getGroupSize(total) {
                if (!isDesktopViewport()) return 1;
                return Math.min(desktopGroupSize, Math.max(total, 1));
            }

            function getLastGroupStart(total, size) {
                if (total <= size) return 0;
                return Math.floor((total - 1) / size) * size;
            }

            function setDesktopWidths(swiper, visibleInGroup) {
                var slides = swiper.slides || [];
                var total = slides.length;

                if (window.innerWidth < 992) {
                    el.style.setProperty('--blog-hero-featured-width', '82%');
                    el.style.setProperty('--blog-hero-compact-width', '82%');
                    return;
                }

                if (total <= 1) {
                    el.style.setProperty('--blog-hero-featured-width', '100%');
                    el.style.setProperty('--blog-hero-compact-width', '100%');
                    return;
                }

                if (visibleInGroup <= 1) {
                    el.style.setProperty('--blog-hero-featured-width', '100%');
                    el.style.setProperty('--blog-hero-compact-width', '100%');
                    return;
                }

                var gapPx = 8;
                var totalGaps = visibleInGroup - 1;
                var containerWidth = el.offsetWidth;
                var featuredPct = 60;

                var featuredPx = Math.floor(containerWidth * (featuredPct / 100));
                var remainingPx = containerWidth - featuredPx - (gapPx * totalGaps);
                var compactPx = Math.floor(remainingPx / (visibleInGroup - 1));
                if (compactPx < 120) compactPx = 120;

                el.style.setProperty('--blog-hero-featured-width', featuredPx + 'px');
                el.style.setProperty('--blog-hero-compact-width', compactPx + 'px');
            }

            function updateNavButtons(total, size) {
                if (!section) return;
                var prevBtn = section.querySelector('.blog-hero__nav--prev');
                var nextBtn = section.querySelector('.blog-hero__nav--next');
                var lastStart = getLastGroupStart(total, size);
                if (prevBtn) prevBtn.classList.toggle('is-disabled', groupStartIndex <= 0);
                if (nextBtn) nextBtn.classList.toggle('is-disabled', groupStartIndex >= lastStart);
            }

            function applyFeaturedSlide(swiper, index, options) {
                options = options || {};
                var slides = swiper.slides || [];
                var total = slides.length;
                var size = getGroupSize(total);

                if (!total) return;
                if (index < 0) index = 0;
                if (index > total - 1) index = total - 1;

                var nextGroupStart = typeof options.forceGroupStart === 'number'
                    ? options.forceGroupStart
                    : Math.floor(index / size) * size;
                var lastStart = getLastGroupStart(total, size);
                if (nextGroupStart < 0) nextGroupStart = 0;
                if (nextGroupStart > lastStart) nextGroupStart = lastStart;

                groupStartIndex = nextGroupStart;
                var groupEndIndex = Math.min(groupStartIndex + size - 1, total - 1);
                if (index < groupStartIndex || index > groupEndIndex) {
                    index = groupStartIndex;
                }

                featuredIndex = index;

                var visibleInGroup = groupEndIndex - groupStartIndex + 1;
                setDesktopWidths(swiper, visibleInGroup);

                Array.prototype.forEach.call(slides, function (slide, slideIndex) {
                    slide.classList.toggle('is-featured', slideIndex === featuredIndex);
                });

                updateNavButtons(total, size);
                swiper.update();

                if (isDesktopViewport()) {
                    if (options.slideToGroup !== false) {
                        swiper.slideTo(groupStartIndex);
                    }
                    return;
                }

                if (typeof swiper.activeIndex === 'number' && swiper.activeIndex !== featuredIndex) {
                    swiper.slideTo(featuredIndex);
                }
            }

            function moveGroup(swiper, direction) {
                var slides = swiper.slides || [];
                var total = slides.length;
                if (!total) return;

                var size = getGroupSize(total);
                var lastStart = getLastGroupStart(total, size);
                var nextStart = groupStartIndex + (direction * size);

                if (nextStart < 0) nextStart = 0;
                if (nextStart > lastStart) nextStart = lastStart;
                if (nextStart === groupStartIndex) return;

                if (!isDesktopViewport()) {
                    applyFeaturedSlide(swiper, nextStart, {
                        forceGroupStart: nextStart,
                        slideToGroup: true
                    });
                    return;
                }

                groupStartIndex = nextStart;
                updateNavButtons(total, size);

                applyFeaturedSlide(swiper, groupStartIndex, {
                    forceGroupStart: groupStartIndex,
                    slideToGroup: true
                });
            }

            var swiper = new Swiper(el, {
                slidesPerView: 1.2,
                spaceBetween: 8,
                speed: 650,
                allowTouchMove: true,
                watchOverflow: true,
                grabCursor: true,
                centeredSlides: false,
                slideToClickedSlide: false,
                navigation: false,
                on: {
                    init: function () {
                        applyFeaturedSlide(this, 0);
                    },
                    slideChangeTransitionEnd: function () {
                        if (!isDesktopViewport() && typeof this.activeIndex === 'number') {
                            applyFeaturedSlide(this, this.activeIndex, { slideToGroup: false });
                        }
                    },
                    resize: function () {
                        applyFeaturedSlide(this, featuredIndex);
                    }
                },
                breakpoints: {
                    768: {
                        slidesPerView: 'auto',
                        spaceBetween: 10,
                        allowTouchMove: true
                    },
                    992: {
                        slidesPerView: 'auto',
                        spaceBetween: 8,
                        allowTouchMove: false
                    }
                }
            });

            // Flechas: navegan por grupos
            if (section) {
                var prevBtn = section.querySelector('.blog-hero__nav--prev');
                var nextBtn = section.querySelector('.blog-hero__nav--next');

                if (prevBtn) {
                    prevBtn.addEventListener('click', function () {
                        moveGroup(swiper, -1);
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function () {
                        moveGroup(swiper, 1);
                    });
                }
            }

            // Clicks en slides
            Array.prototype.forEach.call(swiper.slides, function (slide, index) {
                var ctaLink = slide.querySelector('.blog-hero-card__cta-link');

                if (ctaLink) {
                    ctaLink.addEventListener('click', function (event) {
                        event.stopPropagation();
                    });
                }

                slide.addEventListener('click', function (event) {
                    if (event.target.closest('.blog-hero-card__cta-link')) return;
                    applyFeaturedSlide(swiper, index);
                });
            });
        });
    }

    // Helper compartido: lógica de tabs + swiper para content-showcase
    function _initContentShowcaseTabs(section, contentSwiper) {
        const block        = section.querySelector('[data-showcase-block]');
        const tabsRoot     = section.querySelector('[data-showcase-tabs-swiper]');
        const desktopRail  = section.querySelector('.content-showcase__rail--desktop');
        const indicator    = section.querySelector('.content-showcase__indicator');
        const underline    = section.querySelector('.content-showcase__underline');
        const desktopTabs  = desktopRail ? Array.from(desktopRail.querySelectorAll('.content-showcase__tab')) : [];

        let tabsSwiper = null;
        if (tabsRoot) {
            tabsSwiper = new Swiper(tabsRoot, {
                slidesPerView: 2,
                slidesPerGroup: 1,
                spaceBetween: 0,
                allowTouchMove: true,
                watchSlidesProgress: true,
                resistanceRatio: 0.85,
            });
        }

        function moveUnderlineTo(index) {
            if (!tabsSwiper || !underline) return;
            const slideEl = tabsSwiper.slides[index];
            if (!slideEl) return;
            const btn = slideEl.querySelector('.content-showcase__tab') || slideEl;
            // Batch reads first — evita reflow forzado por write-then-read
            const pl       = parseFloat(getComputedStyle(tabsSwiper.el).paddingLeft) || 0;
            const tx       = (typeof tabsSwiper.translate === 'number') ? tabsSwiper.translate : tabsSwiper.getTranslate();
            const offsetL  = slideEl.offsetLeft;
            const btnWidth = btn.offsetWidth;
            // Solo propiedades compuestas — scaleX reemplaza la animación de width
            underline.style.transform = `translate3d(${pl + offsetL + tx}px,0,0) scaleX(${btnWidth})`;
        }

        // Cache de offsetTop por índice — evita leer del DOM en cada animación
        let indicatorOffsets = desktopTabs.map(tab => tab.offsetTop);

        function moveIndicatorTo(index) {
            if (!indicator || indicatorOffsets[index] == null) return;
            indicator.style.transform = `translateY(${indicatorOffsets[index] + 10}px)`;
        }

        function setActive(index, opts = {}) {
            const moveUi = (opts.moveUi !== false);

            section.querySelectorAll('.content-showcase__tab').forEach((btn) => {
                const active = Number(btn.dataset.slide) === index;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            if (window.innerWidth >= 768) {
                if (moveUi) requestAnimationFrame(() => moveIndicatorTo(index));
                return;
            }

            if (!moveUi) return;

            if (tabsSwiper) {
                const total      = tabsSwiper.slides ? tabsSwiper.slides.length : 0;
                const lastStart  = Math.max(0, total - 2);
                let desiredStart = index <= 0 ? 0 : index >= total - 1 ? lastStart : index - 1;
                desiredStart     = Math.max(0, Math.min(desiredStart, lastStart));

                if (tabsSwiper.activeIndex !== desiredStart) {
                    tabsSwiper.slideTo(desiredStart, 250);
                    tabsSwiper.once('transitionEnd', () => moveUnderlineTo(index));
                }
            }
            requestAnimationFrame(() => moveUnderlineTo(index));
        }

        // Click en tab → slide contenido
        section.querySelectorAll('.content-showcase__tab').forEach((tab) => {
            tab.addEventListener('click', () => {
                const idx = Number(tab.dataset.slide || 0);
                contentSwiper.slideTo(idx);
                setActive(idx, { moveUi: true });
            });
        });

        // Sync tabs cuando cambia el swiper contenido
        contentSwiper.on('slideChangeTransitionStart', () => {
            setActive(contentSwiper.activeIndex, { moveUi: window.innerWidth >= 768 });
        });
        contentSwiper.on('slideChangeTransitionEnd', () => {
            if (window.innerWidth < 768) setActive(contentSwiper.activeIndex, { moveUi: true });
        });

        // Mantener underline mientras el tabs swiper se mueve
        // setTranslate dispara en cada frame de drag — throttle con rAF para evitar reflows continuos
        if (tabsSwiper) {
            let _underlineRaf = null;
            tabsSwiper.on('setTranslate', () => {
                if (_underlineRaf) return;
                _underlineRaf = requestAnimationFrame(() => {
                    moveUnderlineTo(contentSwiper.activeIndex || 0);
                    _underlineRaf = null;
                });
            });
            tabsSwiper.on('transitionEnd', () => moveUnderlineTo(contentSwiper.activeIndex || 0));
        }

        // Resize
        window.addEventListener('resize', () => {
            indicatorOffsets = desktopTabs.map(tab => tab.offsetTop);
            if (tabsSwiper) tabsSwiper.update();
            contentSwiper.update();
            setActive(contentSwiper.activeIndex, { moveUi: true });
        }, { passive: true });

        // Estado inicial
        requestAnimationFrame(() => setActive(0, { moveUi: true }));
    }

    // Content Showcase — Classic (tabs + swiper imagen/card)
    function initContentShowcaseClassic() {
        if (typeof window.Swiper === 'undefined') return;

        const section     = document.querySelector('.content-showcase--classic');
        const contentRoot = section ? section.querySelector('[data-showcase-swiper]') : null;
        if (!section || !contentRoot) return;

        const contentSwiper = new Swiper(contentRoot, {
            slidesPerView: 1,
            speed: 600,
            allowTouchMove: true,
            autoHeight: false,
            observer: true,
            observeParents: true,
            resizeObserver: true,
            navigation: {
                nextEl: section.querySelector('.content-showcase__next'),
                prevEl: section.querySelector('.content-showcase__prev'),
            },
            pagination: {
                el:        section.querySelector('.content-showcase__pagination'),
                clickable: true,
            },
        });

        _initContentShowcaseTabs(section, contentSwiper);
    }

    function initImagesCarouselClassicSwiper() {
        if (typeof window.Swiper === 'undefined') return;

        let els = document.querySelectorAll('.images-carousel__media[data-images-carousel-variant="classic"]');

        if (!els || !els.length) return;

        els.forEach(function (el) {
            if (el.dataset.swiperInitialized === '1') return;
            el.dataset.swiperInitialized = '1';

            new Swiper(el, {
                slidesPerView: 1.25,
                spaceBetween: 16,
                speed: 600,
                pagination: {
                    el: el.querySelector('.images-carousel__pagination'),
                    clickable: true
                },
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 16
                    },
                    992: {
                        slidesPerView: 3,
                        spaceBetween: 16
                    }
                }
            });
        });
    }

    function initImagesCarouselGallerySwipers() {
        if (typeof window.Swiper === 'undefined') return;

        let els = document.querySelectorAll('.images-carousel__media[data-images-carousel-variant="gallery-slider"]');

        if (!els || !els.length) return;

        els.forEach(function (el) {
            if (el.dataset.swiperInitialized === '1') return;
            el.dataset.swiperInitialized = '1';
            let slideCount = el.querySelectorAll('.swiper-slide').length;
            let lastIndex = slideCount > 0 ? (slideCount - 1) : 0;


            new Swiper(el, {

                centeredSlides: true,
                slidesPerView: 1.25,
                spaceBetween: 16,
                speed: 600,
                pagination: {
                    el: el.querySelector('.images-carousel__pagination'),
                    clickable: true
                }
            });
        });
    }

    function initContentCarouselClassicSwiper() {
        if (typeof window.Swiper === 'undefined') return;

        let roots = document.querySelectorAll('[data-content-carousel-swiper]');
        if (!roots || !roots.length) return;

        Array.prototype.forEach.call(roots, function (el) {
            if (el.dataset.swiperInitialized === '1') return;
            el.dataset.swiperInitialized = '1';

            let variant = el.getAttribute('data-content-carousel-variant') || '';


            // Defaults (classic)
            let slidesMobile = 1.15;
            let spaceMobile = 16;
            let slidesTablet = 3;
            let spaceTablet = 24;

            if (variant === 'essence') {
                slidesMobile = 1;
                spaceMobile = 16;
                slidesTablet = 3;
                spaceTablet = 24;
            }

            new Swiper(el, {
                slidesPerView: slidesMobile,
                spaceBetween: spaceMobile,
                speed: 600,
                centerInsufficientSlides: true,
                watchOverflow: false,
                pagination: {
                    el: el.querySelector('.content-carousel__pagination'),
                    clickable: true
                },
                breakpoints: {
                    768: {
                        slidesPerView: slidesTablet,
                        spaceBetween: spaceTablet
                    },
                    992: {
                        slidesPerView: slidesTablet,
                        spaceBetween: spaceTablet
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initContentCarouselClassicSwiper();
        initBlogHeroSwiper();
    });


    //Funciones de Adriel
    function initRecaptchaV3() {
        const forms = document.querySelectorAll('.newsletter-subscribe-banner__form, .blog-listing__newsletter-form');
        if (!forms || !forms.length) return;

        const siteKey = (window.pmNewsletter && window.pmNewsletter.recaptchaSiteKey) || '';
        if (!siteKey) return;

        function attachSubmit(form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                grecaptcha.ready(function () {
                    grecaptcha.execute(siteKey, { action: 'newsletter_submit' })
                        .then(function (token) {
                            sendFormAjax(form, token);
                        })
                        .catch(function (error) {
                            console.error('reCAPTCHA error:', error);
                            showFormMessage(form, 'Security verification failed. Please reload the page.', false);
                        });
                });
            });
        }

        // reCAPTCHA puede no estar listo aún en DOMContentLoaded — esperar con grecaptcha.ready
        if (typeof grecaptcha !== 'undefined') {
            forms.forEach(attachSubmit);
        } else {
            // Fallback: esperar a que el script de reCAPTCHA termine de cargar
            window.onRecaptchaLoad = function () {
                forms.forEach(attachSubmit);
            };
        }
    }

    // Muestra mensaje de éxito/error inline debajo del form
    function showFormMessage(form, message, success) {
        var existing = form.parentNode.querySelector('.newsletter-form__message');
        if (existing) existing.remove();

        var msg = document.createElement('p');
        msg.className = 'newsletter-form__message newsletter-form__message--' + (success ? 'success' : 'error');
        msg.textContent = message;
        form.parentNode.insertBefore(msg, form.nextSibling);
    }

    function sendFormAjax(form, token) {
        const emailInput = form.querySelector('input[type="email"]');
        if (!emailInput) return;

        const email = emailInput.value.trim();
        if (!email || !email.includes('@')) {
            showFormMessage(form, 'Please enter a valid email address.', false);
            return;
        }

        const submitBtn   = form.querySelector('button[type="submit"]');
        const originalBtn = submitBtn ? submitBtn.innerHTML : '';
        if (submitBtn) {
            submitBtn.innerHTML = 'Sending...';
            submitBtn.disabled  = true;
        }

        const ajaxurl = (window.pmNewsletter && window.pmNewsletter.ajaxurl) || '/wp-admin/admin-ajax.php';

        const data = new URLSearchParams();
        data.append('action',          'newsletter_submit');
        data.append('email',           email);
        data.append('recaptcha_token', token);

        fetch(ajaxurl, {
            method:  'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body:    data.toString(),
        })
        .then(res => {
            if (!res.ok) throw new Error('Network error');
            return res.json();
        })
        .then(res => {
            if (res.success) {
                showFormMessage(form, res.data.response || 'You have been successfully subscribed.', true);
                form.reset();
            } else {
                showFormMessage(form, res.data?.response || 'There was an error processing your subscription.', false);
            }
        })
        .catch(err => {
            console.error('AJAX error:', err);
            showFormMessage(form, 'Connection error. Please try again.', false);
        })
        .finally(() => {
            if (submitBtn) {
                submitBtn.innerHTML = originalBtn;
                submitBtn.disabled  = false;
            }
        });
    }

    // Hero reveal — fade-in del body + zoom de imagen con GSAP
    // Aplica a: page-cover--variant-classic, page-cover--variant-essence (solo primary) y primary-showcase-hero
    function initPageCoverReveal() {
        if (typeof gsap === 'undefined') return;

        var essencePrimary = document.querySelector('.page-cover--variant-essence .page-cover__image--primary');
        var signaturePrimary = document.querySelector('.page-cover--variant-signature .page-cover-signature__img');
        var img = essencePrimary
               || document.querySelector('.page-cover--variant-classic .page-cover__image')
               || document.querySelector('.primary-showcase-hero .hotel-hero__bg');

        window.__revealPage = function () {
            gsap.to(document.body, {
                opacity:  1,
                duration: 0.5,
                ease:     'power2.out',
            });
            // La primary image del essence no lleva zoom, solo el fade del body
            if (img && !essencePrimary && !signaturePrimary) {
                gsap.fromTo(img,
                    { scale: 1 },
                    { scale: 1.01, duration: 0.5, ease: 'none' }
                );
            }
        };

        // Si la imagen ya había cargado antes de que GSAP estuviera disponible
        if (window.__pageCoverReady) {
            window.__revealPage();
        }
    }

    // Fade-in animations — [data-animate] > .fade-in-{n}
    // Uso: <div data-animate> <h1 class="fade-in-1">...</h1> <p class="fade-in-2">...</p> </div>
    // Cada número define el orden de aparición (delay escalonado de 0.15s).
    function initFadeAnimations() {
        if (typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;

        gsap.registerPlugin(ScrollTrigger);

        // Parsea el valor del atributo: "slide-up delay-3" → { type: 'slide-up', delay: 0.3 }
        function parseAnim(value) {
            var parts  = (value || '').trim().split(/\s+/);
            var type   = 'fade';
            var delay  = 0;
            parts.forEach(function (part) {
                if (part.indexOf('delay-') === 0) {
                    delay = parseInt(part.replace('delay-', ''), 10) * 0.1;
                } else if (part) {
                    type = part;
                }
            });
            return { type: type, delay: delay };
        }

        // Propiedades iniciales según el tipo de animación
        function getFromProps(type) {
            var base = { opacity: 0, duration: 0.7, ease: 'power2.out' };
            switch (type) {
                case 'slide-up':    return Object.assign({}, base, { y: 32 });
                case 'slide-down':  return Object.assign({}, base, { y: -32 });
                case 'slide-left':  return Object.assign({}, base, { x: 32 });
                case 'slide-right': return Object.assign({}, base, { x: -32 });
                case 'fade':        return base;
                default:            return Object.assign({}, base, { y: 32 });
            }
        }

        function inViewport(el) {
            var rect = el.getBoundingClientRect();
            return rect.top < window.innerHeight && rect.bottom > 0;
        }

        // ── Elemento individual: data-anim="slide-up delay-2" ──
        document.querySelectorAll('[data-anim]').forEach(function (el) {
            var parsed    = parseAnim(el.getAttribute('data-anim'));
            var fromProps = Object.assign({}, getFromProps(parsed.type), {
                delay:    parsed.delay,
                onComplete: function () { gsap.set(el, { clearProps: 'transform' }); },
            });

            if (inViewport(el)) {
                gsap.from(el, fromProps);
            } else {
                gsap.from(el, Object.assign({}, fromProps, {
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 70%',
                        toggleActions: 'play none none none',
                    }
                }));
            }
        });

        // ── Grupo: data-anim-wrap > data-anim-child="slide-up delay-1" ──
        document.querySelectorAll('[data-anim-wrap]').forEach(function (wrap) {
            var children = wrap.querySelectorAll('[data-anim-child]');
            if (!children.length) return;

            if (inViewport(wrap)) {
                var tl = gsap.timeline();
                children.forEach(function (child) {
                    var parsed = parseAnim(child.getAttribute('data-anim-child'));
                    tl.from(child, getFromProps(parsed.type), parsed.delay);
                });
            } else {
                var tl = gsap.timeline({
                    scrollTrigger: {
                        trigger: wrap,
                        start: 'top 70%',
                        toggleActions: 'play none none none',
                    }
                });
                children.forEach(function (child) {
                    var parsed = parseAnim(child.getAttribute('data-anim-child'));
                    tl.from(child, getFromProps(parsed.type), parsed.delay);
                });
            }
        });

        // Recalcular posiciones tras carga completa (imágenes, layout final)
        window.addEventListener('load', function () {
            ScrollTrigger.refresh();
        });
    }


    // Lazy loading con IntersectionObserver
    function lazyLoadImages(selector, options = { threshold: 0.5 }) {
        const images = document.querySelectorAll(`${selector}[data-src]`);

        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    observer.unobserve(img);
                }
            });
        }, options);

        images.forEach(image => {
            observer.observe(image);
        });
    }

    // Dynamic Collection — filter, search, load-more, drag-to-scroll
    function initDynamicCollectionFilter() {
        var ui = document.querySelector('[data-collection-filter-ui]');
        if (!ui) return;

        var input = ui.querySelector('[data-collection-filter-input]');
        var section = ui.closest('section') || document;
        var pillsScroller = ui.querySelector('.content-collection__pills');
        var cards = section.querySelectorAll('.content-collection__card[data-filter][data-search]');
        var loadMoreBtn = section.querySelector('[data-collection-load-more]');

        var selected = {};
        var STEP = 10;
        var visibleLimit = STEP;

        function normalize(s) {
            s = (s || '').toString().toLowerCase();
            if (s.normalize) s = s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            return s.replace(/\s+/g, ' ').trim();
        }

        function getQuery() { return input ? normalize(input.value) : ''; }

        function getSelectedSlugs() {
            var out = [];
            for (var k in selected) {
                if (selected.hasOwnProperty(k) && selected[k]) out.push(k);
            }
            return out;
        }

        function cardHasAnySelectedFilter(card, selectedSlugs) {
            if (!selectedSlugs || selectedSlugs.length === 0) return true;
            var raw = normalize(card.getAttribute('data-filter') || '');
            if (!raw) return false;
            var parts = raw.split('|');
            var lookup = {};
            for (var i = 0; i < parts.length; i++) {
                var s = normalize(parts[i]);
                if (s) lookup[s] = true;
            }
            for (var j = 0; j < selectedSlugs.length; j++) {
                var sel = normalize(selectedSlugs[j]);
                if (sel && lookup[sel]) return true;
            }
            return false;
        }

        function matches(card, query, selectedSlugs) {
            var haystack = normalize(card.getAttribute('data-search') || '');
            return (!query || haystack.indexOf(query) !== -1) && cardHasAnySelectedFilter(card, selectedSlugs);
        }

        function syncPillsUI() {
            if (!pillsScroller) return;
            var anySelected = getSelectedSlugs().length > 0;
            var pills = pillsScroller.querySelectorAll('[data-collection-pill]');
            for (var i = 0; i < pills.length; i++) {
                var slug = normalize(pills[i].getAttribute('data-filter-slug') || '');
                if (slug === '') {
                    pills[i].classList[anySelected ? 'remove' : 'add']('is-active');
                } else {
                    pills[i].classList[selected[slug] ? 'add' : 'remove']('is-active');
                }
            }
        }

        function syncExternalPillsUI() {
            var externalWrap = document.querySelector('[data-collection-filters-external-pills]');
            if (!externalWrap) return;
            var anySelected = getSelectedSlugs().length > 0;
            var buttons = externalWrap.querySelectorAll('[data-collection-external-pill]');
            for (var i = 0; i < buttons.length; i++) {
                var slug = normalize(buttons[i].getAttribute('data-filter-slug') || '');
                if (slug === '') {
                    buttons[i].classList[anySelected ? 'remove' : 'add']('is-active');
                } else {
                    buttons[i].classList[selected[slug] ? 'add' : 'remove']('is-active');
                }
            }
        }

        function mountExternalPillsFromScroller() {
            var externalWrap = document.querySelector('[data-collection-filters-external-pills]');
            if (!externalWrap || !pillsScroller) return;
            if (externalWrap.getAttribute('data-mounted') === '1') return;

            var wrap = document.createElement('div');
            wrap.className = 'content-collection__pills-wrap';
            var row = document.createElement('div');
            row.className = 'content-collection__pills';

            var sourceButtons = pillsScroller.querySelectorAll('[data-collection-pill]');
            for (var i = 0; i < sourceButtons.length; i++) {
                var src = sourceButtons[i];
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'content-collection__pill';
                btn.setAttribute('data-collection-external-pill', '');
                btn.setAttribute('data-filter-slug', src.getAttribute('data-filter-slug') || '');
                btn.textContent = src.textContent || '';
                row.appendChild(btn);
            }

            wrap.appendChild(row);
            externalWrap.innerHTML = '';
            externalWrap.appendChild(wrap);
            externalWrap.setAttribute('data-mounted', '1');
            syncExternalPillsUI();
        }

        function apply() {
            var query = getQuery();
            var selectedSlugs = getSelectedSlugs();
            var filtered = [];

            for (var i = 0; i < cards.length; i++) {
                if (matches(cards[i], query, selectedSlugs)) filtered.push(cards[i]);
            }
            for (var j = 0; j < cards.length; j++) {
                var colAll = cards[j].closest('.content-collection__col') || cards[j];
                colAll.style.display = 'none';
                colAll.classList.remove('content-collection__col--odd', 'content-collection__col--even');
            }
            for (var k = 0; k < filtered.length; k++) {
                var col = filtered[k].closest('.content-collection__col') || filtered[k];
                if (k < visibleLimit) {
                    col.style.display = '';
                    // Reasignar odd/even según posición visible para mantener el escalonado
                    col.classList.add(k % 2 === 0 ? 'content-collection__col--odd' : 'content-collection__col--even');
                }
            }
            if (loadMoreBtn) {
                loadMoreBtn.style.display = filtered.length > visibleLimit ? '' : 'none';
            }
            var countEl = document.querySelector('[data-pm-results-count]');
            if (countEl) countEl.textContent = filtered.length;
        }

        function resetAndApply() { visibleLimit = STEP; apply(); }

        function toggleSlug(slugRaw) {
            var slug = normalize(slugRaw || '');
            if (slug === '') { selected = {}; }
            else if (selected[slug]) { delete selected[slug]; }
            else { selected[slug] = true; }
            syncPillsUI();
            syncExternalPillsUI();
            resetAndApply();
        }

        if (pillsScroller) {
            pillsScroller.addEventListener('click', function (e) {
                var btn = e.target.closest('[data-collection-pill]');
                if (!btn || !pillsScroller.contains(btn)) return;
                toggleSlug(btn.getAttribute('data-filter-slug') || '');
            });
        }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('[data-collection-external-pill]');
            if (!btn) return;
            toggleSlug(btn.getAttribute('data-filter-slug') || '');
        });

        if (input) input.addEventListener('input', resetAndApply);
        if (loadMoreBtn) loadMoreBtn.addEventListener('click', function () { visibleLimit += STEP; apply(); });

        // Drag-to-scroll (mouse only)
        if (pillsScroller) {
            var isDown = false, startX = 0, scrollLeftStart = 0, didDrag = false;
            var DRAG_THRESHOLD = 8;

            pillsScroller.addEventListener('pointerdown', function (e) {
                if (e.pointerType !== 'mouse' || e.button !== 0) return;
                isDown = true; didDrag = false;
                startX = e.clientX; scrollLeftStart = pillsScroller.scrollLeft;
            });

            pillsScroller.addEventListener('pointermove', function (e) {
                if (!isDown) return;
                var dx = e.clientX - startX;
                if (!didDrag && Math.abs(dx) >= DRAG_THRESHOLD) {
                    didDrag = true;
                    pillsScroller.classList.add('is-dragging');
                    try { pillsScroller.setPointerCapture(e.pointerId); } catch (err) {}
                }
                if (didDrag) pillsScroller.scrollLeft = scrollLeftStart - dx;
            });

            function endDrag() {
                if (!isDown) return;
                isDown = false;
                if (didDrag) {
                    pillsScroller.classList.remove('is-dragging');
                    var cancelClickOnce = function (ev) {
                        ev.preventDefault(); ev.stopPropagation();
                        pillsScroller.removeEventListener('click', cancelClickOnce, true);
                    };
                    pillsScroller.addEventListener('click', cancelClickOnce, true);
                }
            }

            pillsScroller.addEventListener('pointerup', endDrag);
            pillsScroller.addEventListener('pointercancel', endDrag);
            pillsScroller.addEventListener('pointerleave', endDrag);
        }

        // Bottom mobile button visibility
        var bottomWrap = section.querySelector('.content-collection__filter-button-mobile-bottom');
        var wrapper = section.querySelector('.content-collection__wrapper-content-collection');
        if (bottomWrap && wrapper) {
            var mm = window.matchMedia ? window.matchMedia('(max-width: 990px)') : null;
            function shouldRun() { return !mm || mm.matches; }

            if (window.gsap) window.gsap.set(bottomWrap, { autoAlpha: 0 });

            function animateBottomVisible(visible) {
                bottomWrap.classList[visible ? 'add' : 'remove']('is-visible');
                bottomWrap.setAttribute('aria-hidden', visible ? 'false' : 'true');
                if (!window.gsap) {
                    bottomWrap.style.opacity = visible ? '1' : '0';
                    bottomWrap.style.visibility = visible ? 'visible' : 'hidden';
                    return;
                }
                window.gsap.killTweensOf(bottomWrap);
                window.gsap.to(bottomWrap, { autoAlpha: visible ? 1 : 0, duration: 0.20, ease: 'power1.out' });
            }

            animateBottomVisible(false);

            if ('IntersectionObserver' in window) {
                new IntersectionObserver(function (entries) {
                    if (!shouldRun()) { animateBottomVisible(false); return; }
                    var entry = entries && entries[0] ? entries[0] : null;
                    animateBottomVisible(!!(entry && entry.isIntersecting));
                }, { root: null, threshold: 0.01 }).observe(wrapper);
            } else {
                function onScrollOrResize() {
                    if (!shouldRun()) { animateBottomVisible(false); return; }
                    var rect = wrapper.getBoundingClientRect();
                    var vh = window.innerHeight || document.documentElement.clientHeight || 0;
                    animateBottomVisible(rect.bottom > 0 && rect.top < vh);
                }
                window.addEventListener('scroll', onScrollOrResize, { passive: true });
                window.addEventListener('resize', onScrollOrResize);
                onScrollOrResize();
            }

            function onMqChange() { if (!shouldRun()) animateBottomVisible(false); }
            if (mm && mm.addEventListener) mm.addEventListener('change', onMqChange);
            else if (mm && mm.addListener) mm.addListener(onMqChange);
        }

        syncPillsUI();
        apply();
        mountExternalPillsFromScroller();

        // Wire trigger button → existing .pm-collection-filters-menu panel
        var triggerBtn = section.querySelector('[data-collection-filters-mobile-trigger]');
        if (triggerBtn) {
            triggerBtn.addEventListener('click', function (e) {
                e.preventDefault();
                var panel = document.querySelector('.pm-collection-filters-menu');
                if (!panel) return;
                var opening = !panel.classList.contains('-is-active');
                panel.classList.toggle('-is-active', opening);
                panel.setAttribute('aria-hidden', opening ? 'false' : 'true');
                document.body.style.overflow = opening ? 'hidden' : '';
                if (opening) apply();
            });
        }

    }

    // Blog Listing — Featured swiper
    function initBlogListingFeaturedSwiper() {
        if (typeof window.Swiper === 'undefined') return;

        var featuredSwipers = document.querySelectorAll('[data-blog-listing-featured-swiper]');
        if (!featuredSwipers.length) return;

        featuredSwipers.forEach(function (el) {
            if (el.dataset.swiperInitialized === '1') return;
            el.dataset.swiperInitialized = '1';

            new window.Swiper(el, {
                slidesPerView: '1.2',
                spaceBetween: 24,
                speed: 650,
                grabCursor: true,
                watchOverflow: true,
                breakpoints: {
                    992: {
                        slidesPerView: '2.5',
                        spaceBetween: 24
                    }
                }
            });
        });
    }

    // Blog Listing — Load more
    function initBlogListingLoadMore() {
        var listingRoots = document.querySelectorAll('[data-blog-listing-root]');
        if (!listingRoots.length) return;

        listingRoots.forEach(function (listingRoot) {
            var button   = listingRoot.querySelector('[data-blog-listing-load-more][data-blog-listing-trigger="items"]');
            var itemsGrid = listingRoot.querySelector('[data-blog-listing-items]');
            if (!button || !itemsGrid) return;

            function getHiddenCards() {
                return itemsGrid.querySelectorAll('[data-blog-listing-grid-item][data-blog-listing-hidden="true"]');
            }

            function updateButtonVisibility() {
                button.style.display = getHiddenCards().length ? '' : 'none';
            }

            updateButtonVisibility();

            button.addEventListener('click', function () {
                var batchSize   = parseInt(button.getAttribute('data-batch-size') || '8', 10);
                var hiddenCards = getHiddenCards();
                var revealed    = 0;

                hiddenCards.forEach(function (card) {
                    if (revealed >= batchSize) return;

                    var article = card.querySelector('.blog-listing__card');
                    card.classList.remove('is-hidden');
                    card.removeAttribute('data-blog-listing-hidden');

                    if (article) {
                        article.classList.remove('is-hidden');
                        article.removeAttribute('data-blog-listing-hidden');
                    }

                    revealed += 1;
                });

                updateButtonVisibility();
            });
        });
    }

    // Content Showcase — Essence (rail + swiper imágenes, sin card overlay)
    function initContentShowcaseEssence() {
        if (typeof window.Swiper === 'undefined') return;

        document.querySelectorAll('.content-showcase--essence').forEach((section) => {
            const contentRoot = section.querySelector('[data-showcase-swiper]');
            if (!contentRoot) return;

            const contentSwiper = new Swiper(contentRoot, {
                slidesPerView: 1,
                speed: 600,
                allowTouchMove: true,
                autoHeight: false,
                observer: true,
                observeParents: true,
                resizeObserver: true,
                navigation: {
                    nextEl: section.querySelector('.content-showcase__next'),
                    prevEl: section.querySelector('.content-showcase__prev'),
                },
                pagination: {
                    el:        section.querySelector('.content-showcase__pagination'),
                    clickable: true,
                },
            });

            _initContentShowcaseTabs(section, contentSwiper);
        });
    }

    // ─── Weather display (°F / °C) ─────────────────────────────────────────────
    function initWeatherToggle() {
        const widget = document.querySelector('[data-weather-widget]');
        if (!widget) return;

        const ajaxUrl = widget.dataset.weatherAjax;
        if (!ajaxUrl) return;

        const temp = widget.querySelector('.weather-now__temp');
        if (!temp) return;

        fetch(ajaxUrl + '?action=pm_weather')
            .then(r => r.json())
            .then(function (data) {
                if (!data.success || !data.data) return;
                temp.textContent = data.data.fahrenheit + '\u00b0F / ' + data.data.celsius + '\u00b0C';
                temp.hidden = false;
            })
            .catch(function () { /* API no disponible, widget permanece oculto */ });
    }

    // Init global
    App.init = function () {
        cacheElements();
        initPrimaryShowcaseHeroDropdown();
        initRecaptchaV3();

        // Inicializar youtube-background diferido: espera a que el browser esté idle
        // para no competir con el LCP durante la carga inicial (~778 KiB de scripts YT).
        const vbgEl = document.querySelector('[data-vbg]');
        if (vbgEl) {
            const heroPoster = document.querySelector('.video-hero__poster');

            const initVbg = () => {
                if (window.VideoBackgrounds && !window.VIDEO_BACKGROUNDS) {
                    window.VIDEO_BACKGROUNDS = new VideoBackgrounds('[data-vbg]');
                    if (heroPoster) {
                        vbgEl.addEventListener('video-background-play', function () {
                            heroPoster.classList.add('is-fading');
                            heroPoster.classList.add('is-hidden');
                        }, { once: true });
                    }
                }
            };

            if ('requestIdleCallback' in window) {
                requestIdleCallback(initVbg, { timeout: 800 });
            } else {
                setTimeout(initVbg, 800);
            }
        }

        if (!header) return;
        initToTopButton();
        initLangSwitcher();
        initDesktopSubmenus();
        initMobileMenu();
        initMobilePanels();
        initMegaPanels();
        initVideoHeroControls();
        initScrollHandler();
        initHotelsParallax();
        initContentShowcaseClassic();
        initContentShowcaseEssence();
        initImagesCarouselClassicSwiper();
        initImagesCarouselGallerySwipers();
        initContentCarouselClassicSwiper();
        initBlogHeroSwiper();
        initBlogListingFeaturedSwiper();
        initBlogListingLoadMore();
        initWeatherToggle();
        initPageCoverReveal();
        initFadeAnimations();
        initDynamicCollectionFilter();
        lazyLoadImages('.img-fluid');
    };

})(window.App);

// DOM listo — si DOMContentLoaded ya disparó (script defer cargó tarde), ejecutar de inmediato.
if (document.readyState === 'loading') {
    document.addEventListener("DOMContentLoaded", function () {
        window.App.init();
    });
} else {
    window.App.init();
}
