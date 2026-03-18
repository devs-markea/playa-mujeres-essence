<?php
get_header();
$term = get_queried_object();

if ( ! $term instanceof WP_Term ) {
    get_footer();
    return;
}

?>

<main id="primary" class="site-main site-main--category" data-force-header-theme="menu">
    <?php
    $blog_page_id  = get_queried_object_id();
    $blog_settings = function_exists('pm_essence_get_blog_settings')
        ? pm_essence_get_blog_settings('category')
        : array();

    get_template_part( 'template-parts/blog/category-hero', null, array(
        'term' => $term,
    ) );

    get_template_part('template-parts/blog/content-listing', null, array(
        'blog_page_id'  => $blog_page_id,
        'blog_settings' => $blog_settings,
        'current_term'  => $term,
        'listing_context' => 'category',
    ));

    get_template_part('template-parts/blog/newsletter-subscribe-banner', null, array(
        'blog_page_id'  => $blog_page_id,
        'blog_settings' => $blog_settings,
    ));
    ?>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof window.Swiper !== 'undefined') {
            var featuredSwipers = document.querySelectorAll('[data-blog-listing-featured-swiper]');

            featuredSwipers.forEach(function (el) {
                if (el.dataset.swiperInitialized === '1') {
                    return;
                }

                el.dataset.swiperInitialized = '1';

                new window.Swiper(el, {
                    slidesPerView: '2.5',
                    spaceBetween: 40,
                    speed: 650,
                    grabCursor: true,
                    watchOverflow: true,
                    breakpoints: {
                        0: {
                            spaceBetween: 18
                        },
                        992: {
                            spaceBetween: 24
                        }
                    }
                });
            });
        }


        var listingRoots = document.querySelectorAll('[data-blog-listing-root]');
        if (!listingRoots.length) {
            return;
        }

        listingRoots.forEach(function (listingRoot) {
            var button = listingRoot.querySelector('[data-blog-listing-load-more][data-blog-listing-trigger="items"]');
            var itemsGrid = listingRoot.querySelector('[data-blog-listing-items]');

            if (!button || !itemsGrid) {
                return;
            }

            function getHiddenCards() {
                return itemsGrid.querySelectorAll('[data-blog-listing-grid-item][data-blog-listing-hidden="true"]');
            }

            function updateButtonVisibility() {
                button.style.display = getHiddenCards().length ? '' : 'none';
            }

            updateButtonVisibility();

            button.addEventListener('click', function () {
                var batchSize = parseInt(button.getAttribute('data-batch-size') || '8', 10);
                var hiddenCards = getHiddenCards();
                var revealed = 0;

                hiddenCards.forEach(function (card) {
                    if (revealed >= batchSize) {
                        return;
                    }

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
    });
</script>

<?php get_footer(); ?>
