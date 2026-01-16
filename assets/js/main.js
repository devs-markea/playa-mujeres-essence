let ytPlayer = null;
let ytReady  = false;

// Callback de la API de YouTube
function onYouTubeIframeAPIReady() {
    if (window.App && typeof window.App.initYouTubePlayer === 'function') {
        window.App.initYouTubePlayer();
    }
}

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
        logoPicture,
        logoImg,
        logoSource,
        videoHero,
        playButton,
        heroVideo,
        info,
        divider,
        overlayVideo;

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
        menuWhereTrigger      = document.querySelector('.pm-navbar-desktop .trigger-filters');
        menuExpTrigger        = document.querySelector('.pm-navbar-desktop .trigger-experiences');

        langSwitcher          = document.querySelector('.pm-lang-switcher__current');
        navDesktop            = document.querySelectorAll('.pm-header__menu .pm-navbar .menu-item > a');
        logoPicture           = document.querySelector('.site-logo.logo-desktop');
        logoImg               = logoPicture ? logoPicture.querySelector('img') : null;
        logoSource            = logoPicture ? logoPicture.querySelector('source') : null;

        videoHero             = document.querySelector('.video-hero');
        playButton            = document.getElementById('play-button-hero');
        heroVideo             = document.querySelector('.video-hero__video');
        info                  = document.querySelector('.video-hero__information');
        divider               = document.querySelector('.divider');
        overlayVideo          = document.querySelector('.video-hero__overlay');
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

        const lightUrl = logoPicture.dataset.logoLight || logoImg.dataset.logoLight;
        const darkUrl  = logoPicture.dataset.logoDark  || logoImg.dataset.logoDark;

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

            if (langSwitcher) {
                langSwitcher.style.color = '#323232';
            }

            if (hamburgerBtn) {
                hamburgerBtn.style.color = '#323232';
            }

            header.classList.add('menu-open');
        } else {
            navDesktop.forEach(item => item.classList.remove('nav-item-dark'));

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


    // Menú mobile
    function initMobileMenu() {
        if (!header) return;

        const openMenu = () => {
            if (menuMobile) menuMobile.classList.add("-is-active");
            if (body) body.style.overflow = "hidden";
        };

        const closeMenu = () => {
            //header.classList.remove("menu-open");
            if (menuMobile) menuMobile.classList.remove("-is-active");
            if (body) body.style.overflow = "";
        };

        hamburgerBtn && hamburgerBtn.addEventListener("click", openMenu);
        closeBtn     && closeBtn.addEventListener("click", closeMenu);
    }

    // Menús mobile de filtros/experiencias
    function initMobilePanels() {
        const openersWhere = document.querySelectorAll('.pm-navbar-mobile .trigger-filters');
        const closerWhere  = document.querySelector('.back-arrow-where-to-stay');

        if (panelWhereMobile) {
            openersWhere.forEach(opener => {
                opener.addEventListener('click', e => {
                    e.preventDefault();
                    panelWhereMobile.classList.add('is-open');
                });
            });

            closerWhere && closerWhere.addEventListener('click', e => {
                e.preventDefault();
                panelWhereMobile.classList.remove('is-open');
            });
        }

        const openersExp = document.querySelectorAll('.pm-navbar-mobile .trigger-experiences');
        const closerExp  = document.querySelector('.back-arrow-menu-experiences');

        if (panelExpMobile) {
            openersExp.forEach(opener => {
                opener.addEventListener('click', e => {
                    e.preventDefault();
                    panelExpMobile.classList.add('is-open');
                });
            });

            closerExp && closerExp.addEventListener('click', e => {
                e.preventDefault();
                panelExpMobile.classList.remove('is-open');
            });
        }
    }

    // Megapanel desktop
    function initMegaPanels() {
        if (menuWhereTrigger && megaPanelWhere) {
            menuWhereTrigger.addEventListener('click', (event) => {
                togglePanel('where', event);
            });
        }

        if (menuExpTrigger && megaPanelExp) {
            menuExpTrigger.addEventListener('click', (event) => {
                togglePanel('experiences', event);
            });
        }

        // Cerrar al hacer click fuera del header
        document.addEventListener('click', (event) => {
            const clickInsideHeader = header && header.contains(event.target);
            const anyPanelOpen = state.isWhereOpen || state.isExperiencesOpen;

            if (!clickInsideHeader && anyPanelOpen) {
                closeAllPanels();
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

            if (isPlaying) {
                heroVideo.muted = false;
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });

                setTimeout(() => {
                    if (body) body.classList.add('body-overflow-hidden');
                }, 600);
            } else {
                heroVideo.muted = true;
                if (body) body.classList.remove('body-overflow-hidden');
            }

            if (info) {
                info.classList.toggle('is-hidden', isPlaying);
            }

            if (divider) {
                divider.classList.toggle('is-hidden', isPlaying);
            }

            if (overlayVideo) {
                overlayVideo.classList.toggle('is-hidden', isPlaying);
            }
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


    function initExperiencesTabsSwiper() {
        if (typeof window.Swiper === 'undefined') return;

        const block = document.querySelector('[data-experiences-block]') || document;
        const contentRoot = document.querySelector('[data-experiences-swiper]');
        if (!contentRoot) return;

        // Swiper contenido (imagen/copy)
        const contentSwiper = new Swiper(contentRoot, {
            slidesPerView: 1,
            speed: 600,
            allowTouchMove: true,
            autoHeight: false,
            observer: true,
            observeParents: true,
            resizeObserver: true,
            navigation: {
                nextEl: document.querySelector('.experiences-content__next'),
                prevEl: document.querySelector('.experiences-content__prev'),
            },
            pagination: {
                el: document.querySelector('.experiences-content__pagination'),
                clickable: true
            }
        });

        const sectionEl = document.querySelector('.experiences-tabs');
        //Function ScrollLock Experiences
        //initExperiencesDesktopScrollLock(sectionEl, contentSwiper);


        const tabsRoot = block.querySelector('[data-experiences-tabs-swiper]');
        const underline = block.querySelector('.experiences-tabs__underline');
        const desktopRail = block.querySelector('.experiences-tabs__rail--desktop');
        const desktopIndicator = block.querySelector('.experiences-tabs__rail--desktop .experiences-tabs__indicator');
        const desktopTabs = desktopRail ? Array.from(desktopRail.querySelectorAll('.experiences-tab')) : [];


        // Tabs: swiper horizontal (scrollable)
        let tabsSwiper = null;



        if (tabsRoot) {
            tabsSwiper = new Swiper(tabsRoot, {
                slidesPerView: 3,
                slidesPerGroup: 3, // grupos de 3
                spaceBetween: 0,
                allowTouchMove: true,
                watchSlidesProgress: true,
                resistanceRatio: 0.85,
            });
        }


        const tabs = Array.from(block.querySelectorAll('.experiences-tabs__rail--mobile .experiences-tab'))
            .concat(Array.from(block.querySelectorAll('.experiences-tabs__rail--desktop .experiences-tab')));

        function getTabsBaseOffset() {
            // Si tu tabsRoot tiene padding-left visual (ej. la línea base inicia en 18px)
            // ajusta aquí para que el underline coincida perfecto.
            // Si NO quieres offset, regresa 0.
            return 0;
        }

        function moveUnderlineTo(index) {
            if (!tabsSwiper || !underline) return;

            const slideEl = tabsSwiper.slides[index];
            if (!slideEl) return;

            const btn = slideEl.querySelector('.experiences-tab') || slideEl;

            const container = tabsSwiper.el; // el div .swiper (tabs)
            const pl = parseFloat(getComputedStyle(container).paddingLeft) || 0;

            // translate real del wrapper (negativo cuando avanzas)
            const tx = (typeof tabsSwiper.translate === 'number')
                ? tabsSwiper.translate
                : tabsSwiper.getTranslate();

            // ✅ posición dentro del contenedor (no uses getBoundingClientRect aquí)
            const x = pl + slideEl.offsetLeft + tx;

            underline.style.transform = `translate3d(${x}px,0,0)`;
            underline.style.width = `${btn.offsetWidth}px`;
        }

        function moveDesktopIndicatorTo(index) {
            if (!desktopRail || !desktopIndicator || !desktopTabs[index]) return;

            const btn = desktopTabs[index];

            // offset dentro del rail (no uses document)
            const y = btn.offsetTop + 10; // el +10 es para alinear como tu diseño (ajustable)
            desktopIndicator.style.transform = `translateY(${y}px)`;
        }


        function setActive(index) {
            // Activo para TODOS los tabs (mobile + desktop)
            block.querySelectorAll('.experiences-tab').forEach((btn) => {
                const active = Number(btn.dataset.slide) === index;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            // ✅ Desktop: mover indicador vertical
            if (window.innerWidth >= 768) {
                requestAnimationFrame(() => moveDesktopIndicatorTo(index));
                return; // desktop no necesita mover tabsSwiper
            }

            // ✅ Mobile: mover tabs al grupo + underline
            if (tabsSwiper && window.innerWidth < 768) {
                const groupStart = Math.floor(index / 3) * 3;
                tabsSwiper.slideTo(groupStart, 250);

                tabsSwiper.once('transitionEnd', () => moveUnderlineTo(index));
                requestAnimationFrame(() => moveUnderlineTo(index));
            } else {
                requestAnimationFrame(() => moveUnderlineTo(index));
            }
        }


        /* Mantener underline pegado al tab activo mientras el wrapper se mueve */
        if (tabsSwiper) {
            tabsSwiper.on('setTranslate', () => {
                const activeIdx = contentSwiper.activeIndex || 0;
                moveUnderlineTo(activeIdx);
            });

            tabsSwiper.on('transitionEnd', () => {
                const activeIdx = contentSwiper.activeIndex || 0;
                moveUnderlineTo(activeIdx);
            });
        }

// Click tab -> ir al slide
        block.querySelectorAll('.experiences-tab').forEach((tab) => {
            tab.addEventListener('click', () => {
                const idx = Number(tab.dataset.slide || 0);
                contentSwiper.slideTo(idx);
                setActive(idx);
            });
        });

        requestAnimationFrame(() => setActive(0));

        contentSwiper.on('activeIndexChange', () => {
            setActive(contentSwiper.activeIndex);
        });


        // Reajuste en resize
        window.addEventListener('resize', () => {
            if (tabsSwiper) tabsSwiper.update();
            contentSwiper.update();
            setActive(contentSwiper.activeIndex);
        }, { passive: true });
    }








    // API YouTube
    App.initYouTubePlayer = function () {
        const heroIframe = document.querySelector('.video-hero__video');

        if (heroIframe && heroIframe.tagName.toLowerCase() === 'iframe' && window.YT && YT.Player) {
            ytPlayer = new YT.Player(heroIframe, {
                events: {
                    onReady: function () {
                        ytReady = true;
                        ytPlayer.mute();
                        ytPlayer.playVideo();
                    }
                }
            });
        }
    };

    // Init global
    App.init = function () {
        cacheElements();
        initPrimaryShowcaseHeroDropdown();
        if (!header) return;
        initToTopButton();
        initLangSwitcher();
        initMobileMenu();
        initMobilePanels();
        initMegaPanels();
        initVideoHeroControls();
        initScrollHandler();
        initHotelsParallax();
        initExperiencesTabsSwiper();
    };

})(window.App);

// DOM listo
document.addEventListener("DOMContentLoaded", function () {
    if (window.App && typeof window.App.init === 'function') {
        window.App.init();
    }
});
