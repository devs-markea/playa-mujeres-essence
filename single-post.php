<?php

get_header();


if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $blog_sections = get_field( 'blog_sections' );
        $blog_settings = get_field( 'blog_page_settings', 'option' );
        $blog_settings = is_array( $blog_settings ) ? $blog_settings : array();

        $blog_page_id = (int) get_option( 'page_for_posts' );

        $current_site_id = get_current_blog_id();

        if ( ! $blog_page_id ) {
            $blog_pages = get_pages(
                array(
                    'meta_key'    => '_wp_page_template',
                    'meta_value'  => 'page-templates/template-page-blog.php',
                    'number'      => 1,
                    'post_status' => 'publish',
                )
            );

            if ( ! empty( $blog_pages ) ) {
                $blog_page_id = (int) $blog_pages[0]->ID;
            }
        }

        $display_categories_filter = ! empty( $blog_settings['single_page_display_categories_filter'] );
        $display_featured_posts    = ! empty( $blog_settings['single_page_display_featured_posts'] );

        $selected_category_slug = isset( $_GET['blog_category'] ) ? sanitize_title( wp_unslash( $_GET['blog_category'] ) ) : '';
        $selected_category      = $selected_category_slug ? get_term_by( 'slug', $selected_category_slug, 'category' ) : false;

        if ( $selected_category && is_wp_error( $selected_category ) ) {
            $selected_category = false;
        }

        if ( ! ( $selected_category instanceof WP_Term ) ) {
            $post_categories = get_the_terms( get_the_ID(), 'category' );
            if ( ! is_wp_error( $post_categories ) && ! empty( $post_categories ) ) {
                $selected_category = $post_categories[0];
            }
        }

        $categories = get_categories(
            array(
                'taxonomy'   => 'category',
                'hide_empty' => true,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );

        $sidebar_featured_posts = array();
        $current_post_id        = get_the_ID();
        $sticky_posts           = array_values( array_diff( array_map( 'absint', (array) get_option( 'sticky_posts' ) ), array( $current_post_id ) ) );
        $featured_post_ids      = array();
        $featured_limit         = 4;

        $append_featured_posts = static function ( $query_args ) use ( &$sidebar_featured_posts, &$featured_post_ids, $featured_limit ) {
            if ( count( $featured_post_ids ) >= $featured_limit ) {
                return;
            }

            $query_args['posts_per_page'] = $featured_limit - count( $featured_post_ids );
            $query                        = new WP_Query( $query_args );

            if ( $query->have_posts() ) {
                foreach ( $query->posts as $featured_post ) {
                    $featured_post_id = (int) $featured_post->ID;

                    if ( in_array( $featured_post_id, $featured_post_ids, true ) ) {
                        continue;
                    }

                    $featured_post_ids[]      = $featured_post_id;
                    $sidebar_featured_posts[] = $featured_post;

                    if ( count( $featured_post_ids ) >= $featured_limit ) {
                        break;
                    }
                }
            }

            wp_reset_postdata();
        };

        if ( ! empty( $sticky_posts ) ) {
            $sticky_query_args = array(
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'post__in'            => $sticky_posts,
                'post__not_in'        => array( $current_post_id ),
                'orderby'             => 'post__in',
                'ignore_sticky_posts' => false,
            );

            if ( $selected_category instanceof WP_Term ) {
                $sticky_query_args['tax_query'] = array(
                    array(
                        'taxonomy' => 'category',
                        'field'    => 'term_id',
                        'terms'    => $selected_category->term_id,
                    ),
                );
            }

            $append_featured_posts( $sticky_query_args );
        }

        if ( count( $featured_post_ids ) < $featured_limit && $selected_category instanceof WP_Term ) {
            $append_featured_posts(
                array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'post__not_in'        => array_merge( array( $current_post_id ), $featured_post_ids ),
                    'ignore_sticky_posts' => true,
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                    'tax_query'           => array(
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'term_id',
                            'terms'    => $selected_category->term_id,
                        ),
                    ),
                )
            );
        }

        if ( count( $featured_post_ids ) < $featured_limit ) {
            $append_featured_posts(
                array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'post__not_in'        => array_merge( array( $current_post_id ), $featured_post_ids ),
                    'ignore_sticky_posts' => true,
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                )
            );
        }

        $related_posts      = array();
        $related_post_ids   = array();
        $related_posts_limit = 4;

        $append_related_posts = static function ( $query_args ) use ( &$related_posts, &$related_post_ids, $related_posts_limit ) {
            if ( count( $related_post_ids ) >= $related_posts_limit ) {
                return;
            }

            $query_args['posts_per_page'] = $related_posts_limit - count( $related_post_ids );
            $query                        = new WP_Query( $query_args );

            if ( $query->have_posts() ) {
                foreach ( $query->posts as $related_post ) {
                    $related_post_id = (int) $related_post->ID;

                    if ( in_array( $related_post_id, $related_post_ids, true ) ) {
                        continue;
                    }

                    $related_post_ids[] = $related_post_id;
                    $related_posts[]    = $related_post;
                }
            }

            wp_reset_postdata();
        };

        if ( $selected_category instanceof WP_Term ) {
            $append_related_posts(
                array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'post__not_in'        => array( $current_post_id ),
                    'ignore_sticky_posts' => true,
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                    'tax_query'           => array(
                        array(
                            'taxonomy' => 'category',
                            'field'    => 'term_id',
                            'terms'    => $selected_category->term_id,
                        ),
                    ),
                )
            );
        }

        if ( count( $related_post_ids ) < $related_posts_limit ) {
            $append_related_posts(
                array(
                    'post_type'           => 'post',
                    'post_status'         => 'publish',
                    'post__not_in'        => array_merge( array( $current_post_id ), $related_post_ids ),
                    'ignore_sticky_posts' => true,
                    'orderby'             => 'date',
                    'order'               => 'DESC',
                )
            );
        }

        $has_categories_sidebar = $display_categories_filter && ! empty( $categories );
        $has_top_posts_sidebar  = $display_featured_posts && ! empty( $sidebar_featured_posts );

        $sticky_sidebar_target = '';

        if ( $has_categories_sidebar ) {
            $sticky_sidebar_target = 'categories';
        } elseif ( $has_top_posts_sidebar ) {
            $sticky_sidebar_target = 'top-posts';
        }

        $post_title    = get_the_title();
        $post_date     = get_the_date( 'F j, Y' );
        $hero_image    = get_the_post_thumbnail(
            get_the_ID(),
            'full',
            array(
                'class' => 'single-blog-hero__image',
                'alt'   => $post_title,
                'loading' => 'eager',
            )
        );

        $collect_section_text = static function ( $value ) use ( &$collect_section_text ) {
            $text = '';

            if ( is_array( $value ) ) {
                foreach ( $value as $nested_value ) {
                    $text .= ' ' . $collect_section_text( $nested_value );
                }

                return $text;
            }

            if ( is_string( $value ) ) {
                return wp_strip_all_tags( $value, true );
            }

            return '';
        };

        $blog_sections_text = $collect_section_text( is_array( $blog_sections ) ? $blog_sections : array() );
        $word_count         = str_word_count( wp_strip_all_tags( $blog_sections_text, true ) );
        $read_time_minutes  = max( 1, (int) ceil( $word_count / 200 ) );
        $read_time_label    = sprintf(
            _n( '%s Minute', '%s Minutes', $read_time_minutes, 'playa-mujeres-essence' ),
            number_format_i18n( $read_time_minutes )
        );
        $related_posts_label = pll__('Related Posts');
        $read_more_label     = pll__('Read more');
        ?>
        <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=63ea7f8a4825b500129efd91&product=inline-share-buttons&source=platform" async="async"></script>

        <main id="primary" class="site-main site-main--single-blog" data-force-header-theme="menu">

            <div class="container">
                <section class="single-blog-hero">
                    <div class="row g-0">
                        <div class="col-12 col-lg-8 offset-lg-2">
                            <div class="single-blog-hero__subheading">
                                <span class="single-blog-hero__subheading-line" aria-hidden="true"></span>
                                <span class="single-blog-hero__subheading-label"><?php pll_e('Blog'); ?></span>
                            </div>

                            <h1 class="single-blog-hero__title"><?php echo esc_html( $post_title ); ?></h1>

                            <div class="single-blog-hero__meta">
                                <?php if ( $selected_category instanceof WP_Term ) : ?>
                                    <span><?php echo esc_html( $selected_category->name ); ?></span>
                                    <span class="single-blog-hero__meta-line" aria-hidden="true"></span>
                                <?php endif; ?>
                                <span><?php echo esc_html( $post_date ); ?></span>
                                <?php if ( function_exists('get_favorites_button') ) : ?>
                                    <span class="single-blog-hero__favorite">
                                        <?php echo get_favorites_button( get_the_ID() ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ( $hero_image ) : ?>
                        <div class="row g-0">
                            <div class="col-12">
                                <div class="single-blog-hero__media">
                                    <?php echo $hero_image; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

                <div class="row g-5">
                    <div class="col-12 col-md-8">
                        <div class="single-blog-content">
                            <?php
                            if ( have_rows( 'blog_sections' ) ) :

                                while ( have_rows( 'blog_sections' ) ) :
                                    the_row();

                                    $layout = get_row_layout();
                                    $template = str_replace( '_', '-', $layout );

                                    get_template_part( 'template-parts/blog/sections/' . $template );

                                endwhile;

                            endif;
                            ?>
                        </div>
                        <div class="single-post-share-this-container">
                            <div class="row g-0">
                                <div class="col-12 col-md-10 offset-md-2">
                                    <div class="single-post-share-this">
                                        <label><?php pll_e('Share this post'); ?></label>
                                        <div class="sharethis-inline-share-buttons"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-3">
                        <aside class="blog-listing__sidebar<?php echo $sticky_sidebar_target ? ' blog-listing__sidebar--sticky blog-listing__sidebar--sticky-' . esc_attr($sticky_sidebar_target) : ''; ?>">
                            <?php if ($has_categories_sidebar) : ?>
                                <div class="blog-listing__sidebar-block blog-listing__sidebar-block--categories">
                                    <h3 class="blog-listing__sidebar-title"><?php pll_e('Categories'); ?></h3>
                                    <ul class="blog-listing__categories">
                                        <?php foreach ($categories as $category) : ?>
                                            <?php
                                            $is_active = $selected_category instanceof WP_Term && (int) $selected_category->term_id === (int) $category->term_id;
                                            $url       = remove_query_arg('blog_category', get_permalink($blog_page_id ?: get_the_ID()));
                                            $url       = add_query_arg('blog_category', $category->slug, $url);
                                            ?>
                                            <li class="blog-listing__categories-item<?php echo $is_active ? ' is-active' : ''; ?>">
                                                <a href="<?php echo esc_url($url); ?>" class="blog-listing__categories-link">
                                                    <span><?php echo esc_html($category->name); ?></span>
                                                    <span class="blog-listing__categories-arrow" aria-hidden="true">&rarr;</span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if ( function_exists('wpp_get_mostpopular') ) : ?>
                                <div class="blog-listing__sidebar-block blog-listing__sidebar-block--panel blog-listing__sidebar-block--top-posts">
                                    <h3 class="blog-listing__sidebar-title"><?php pll_e('Top Posts'); ?></h3>
                                    <?php
                                    wpp_get_mostpopular( array(
                                        'limit'              => 4,
                                        'range'              => 'all',
                                        'thumbnail_width'    => 0,
                                        'thumbnail_height'   => 0,
                                        'display_post_title' => 1,
                                        'wpp_start'          => '<ul class="blog-listing__top-posts">',
                                        'wpp_end'            => '</ul>',
                                        'post_html'          => '<li class="blog-listing__top-posts-item"><a href="{url}" class="blog-listing__top-posts-link">{text_title}</a></li>',
                                    ) );
                                    ?>
                                </div>
                            <?php elseif ($has_top_posts_sidebar) : ?>
                                <div class="blog-listing__sidebar-block blog-listing__sidebar-block--panel blog-listing__sidebar-block--top-posts">
                                    <h3 class="blog-listing__sidebar-title"><?php pll_e('Top Posts'); ?></h3>
                                    <ul class="blog-listing__top-posts">
                                        <?php foreach ($sidebar_featured_posts as $featured_post) : ?>
                                            <li class="blog-listing__top-posts-item">
                                                <a href="<?php echo esc_url(get_permalink($featured_post)); ?>" class="blog-listing__top-posts-link">
                                                    <?php echo esc_html(get_the_title($featured_post)); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                    </aside>
                    </div>
                </div>
                <?php if ( ! empty( $related_posts ) ) : ?>
                    <section class="single-blog-related">
                        <div class="row g-0">
                            <div class="col-12 col-md-10 mx-auto">
                                <h2 class="single-blog-related__title"><?php echo esc_html( $related_posts_label ); ?></h2>
                                <div class="row g-0 single-blog-related__grid">
                                    <?php foreach ( $related_posts as $related_post ) : ?>
                                        <?php
                                        $related_post_id        = (int) $related_post->ID;
                                        $related_post_title     = get_the_title( $related_post_id );
                                        $related_post_permalink = get_permalink( $related_post_id );
                                        $related_post_terms     = get_the_terms( $related_post_id, 'category' );
                                        $related_post_term_name = ( ! is_wp_error( $related_post_terms ) && ! empty( $related_post_terms ) ) ? $related_post_terms[0]->name : '';
                                        ?>
                                        <div class="col-12 col-lg-6 single-blog-related__item">
                                            <article class="blog-listing__card single-blog-related__card card flex-row">
                                                <a href="<?php echo esc_url( $related_post_permalink ); ?>" class="blog-listing__card-media-link single-blog-related__media-link" aria-label="<?php echo esc_attr( $related_post_title ); ?>">
                                                    <?php if ( has_post_thumbnail( $related_post_id ) ) : ?>
                                                        <?php echo get_the_post_thumbnail( $related_post_id, 'large', array( 'class' => 'blog-listing__card-image single-blog-related__image card-img-left example-card-img-responsive' ) ); ?>
                                                    <?php else : ?>
                                                        <span class="blog-listing__card-image blog-listing__card-image--placeholder single-blog-related__image card-img-left example-card-img-responsive"></span>
                                                    <?php endif; ?>
                                                </a>

                                                <div class="blog-listing__card-content single-blog-related__content card-body d-flex flex-column justify-content-center">
                                                    <?php if ( $related_post_term_name ) : ?>
                                                        <p class="blog-listing__card-taxonomy single-blog-related__taxonomy card-text"><?php echo esc_html( $related_post_term_name ); ?></p>
                                                    <?php endif; ?>

                                                    <h3 class="blog-listing__card-title single-blog-related__card-title card-title h5 h4-sm">
                                                        <a href="<?php echo esc_url( $related_post_permalink ); ?>">
                                                            <?php echo esc_html( $related_post_title ); ?>
                                                        </a>
                                                    </h3>

                                                    <a href="<?php echo esc_url( $related_post_permalink ); ?>" class="blog-listing__card-link single-blog-related__card-link card-text">
                                                        <?php echo esc_html( $read_more_label ); ?>
                                                    </a>
                                                </div>
                                            </article>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
                <div class="instagram-feed">
                    <div class="row g-0">
                        <div class="col-md-10 mx-auto">
                            <?php
                            $feed_id = ($current_site_id == 2) ? 777 : 1043;
                            echo do_shortcode( '[instagram feed="'.$feed_id.'"]' );
                            ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php get_template_part('template-parts/blog/newsletter-subscribe-banner', null, array(
                    'blog_page_id'  => $blog_page_id,
                    'blog_settings' => $blog_settings,)); ?>

        </main>

    <?php
    endwhile;
endif;

get_footer();
?>

<style>
    /* ===============================
   Newsletter Subscribe Banner
   First Mobile
   =============================== */

    /* SECTION spacing */


    /* background */
    .newsletter-subscribe-banner__background {
        position: relative;
        min-height: 420px;
        display: flex;
        align-items: center;
        background-size: cover;
        background-position: center;
    }

    /* overlay */
    .newsletter-subscribe-banner__overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.35);
        z-index: 1;
    }

    /* content layer */
    .newsletter-subscribe-banner__content {
        position: relative;
        z-index: 2;
        width: 100%;
        border-top: 1px solid rgba(255, 255, 255, 0.25);
        border-bottom: 1px solid rgba(255, 255, 255, 0.25);
        padding: 0;
    }

    .newsletter-subscribe-banner__inner {
        padding: 40px 0;
        max-width: 100%;
    }

    /* typography */
    .newsletter-subscribe-banner__title {
        color: #fff;
        font-family: var(--pm-font-secondary);
        font-size: 24px;
        font-weight: 500;
        font-style: italic;
        letter-spacing: 2px;
        margin-bottom: 0.75rem;
    }

    .newsletter-subscribe-banner__description {
        color: #fff;
        font-size: 16px;
        font-weight: 300;
        margin-bottom: 1.5rem;
    }

    /* form */
    .newsletter-subscribe-banner__form {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        max-width: 100%;
    }

    .newsletter-subscribe-banner__input {
        width: 100%;
        height: 48px;
        padding: 0 1rem;
        background: white;
        font-size: 16px;
        font-weight: 300;
        color: var(--pm-secondary-800);
        border: none;
    }

    .newsletter-subscribe-banner__submit {
        height: 48px;
        background: transparent;
        color: #fff;
        border: 0;
    }

    @media (min-width: 768px) {



        .newsletter-subscribe-banner__background {
            min-height: 520px;
        }

        .newsletter-subscribe-banner__inner {
            padding: 3.5rem 0;
            max-width: 620px;
        }

        .newsletter-subscribe-banner__content {
            padding: 16px 0;
        }

        .newsletter-subscribe-banner__title {
            font-size: 40px;
            letter-spacing: 2px;
        }

        .newsletter-subscribe-banner__form {
            flex-direction: row;
            flex-wrap: wrap;
            max-width: 75%;
        }

        .newsletter-subscribe-banner__input {
            flex: 1 1 260px;
        }
    }

</style>
