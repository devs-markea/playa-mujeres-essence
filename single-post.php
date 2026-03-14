<?php

get_header();


if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $blog_sections = get_field( 'blog_sections' );
        $blog_settings = get_field( 'blog_page_settings', 'option' );
        $blog_settings = is_array( $blog_settings ) ? $blog_settings : array();

        $blog_page_id = (int) get_option( 'page_for_posts' );

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
        $display_newsletter_form   = ! empty( $blog_settings['single_page_display_newsletter_form'] );

        $newsletter_heading       = isset( $blog_settings['sidebar_heading'] ) ? $blog_settings['sidebar_heading'] : '';
        $newsletter_heading_level = isset( $blog_settings['sidebar_heading_level'] ) ? $blog_settings['sidebar_heading_level'] : 'h3';
        $newsletter_description   = isset( $blog_settings['sidebar_description'] ) ? $blog_settings['sidebar_description'] : '';
        $newsletter_placeholder   = isset( $blog_settings['placeholder'] ) ? $blog_settings['placeholder'] : '';
        $newsletter_submit_text   = isset( $blog_settings['submit_text'] ) ? $blog_settings['submit_text'] : '';
        $newsletter_heading_tag   = function_exists( 'pm_essence_heading_tag_or_null' ) ? pm_essence_heading_tag_or_null( $newsletter_heading_level, 'h3' ) : 'h3';

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

        $has_categories_sidebar = $display_categories_filter && ! empty( $categories );
        $has_top_posts_sidebar  = $display_featured_posts && ! empty( $sidebar_featured_posts );
        $has_newsletter_sidebar = $display_newsletter_form && ( $newsletter_heading || $newsletter_description || $newsletter_submit_text );

        $sticky_sidebar_target = '';

        if ( $has_categories_sidebar ) {
            $sticky_sidebar_target = 'categories';
        } elseif ( $has_top_posts_sidebar ) {
            $sticky_sidebar_target = 'top-posts';
        } elseif ( $has_newsletter_sidebar ) {
            $sticky_sidebar_target = 'newsletter';
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
        ?>

        <main id="primary" class="site-main site-main--single-blog" data-force-header-theme="menu">

            <div class="container">
                <section class="single-blog-hero">
                    <div class="row g-0">
                        <div class="col-12 col-lg-8 offset-lg-2">
                            <div class="single-blog-hero__subheading">
                                <span class="single-blog-hero__subheading-line" aria-hidden="true"></span>
                                <span class="single-blog-hero__subheading-label"><?php esc_html_e( 'Blog', 'playa-mujeres-essence' ); ?></span>
                            </div>

                            <h1 class="single-blog-hero__title"><?php echo esc_html( $post_title ); ?></h1>

                            <div class="single-blog-hero__meta">
                                <span><?php echo esc_html( $post_date ); ?></span>
                                <span><?php echo esc_html( $read_time_label ); ?></span>
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
                    <div class="col-12 col-md-4">
                        <aside class="blog-listing__sidebar<?php echo $sticky_sidebar_target ? ' blog-listing__sidebar--sticky blog-listing__sidebar--sticky-' . esc_attr($sticky_sidebar_target) : ''; ?>">
                            <?php if ($has_categories_sidebar) : ?>
                                <div class="blog-listing__sidebar-block blog-listing__sidebar-block--categories">
                                    <h3 class="blog-listing__sidebar-title">Categories</h3>
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
                                                    <?php if ($is_active) : ?>
                                                        <span class="blog-listing__categories-arrow" aria-hidden="true">&rarr;</span>
                                                    <?php endif; ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if ($has_top_posts_sidebar) : ?>
                                <div class="blog-listing__sidebar-block blog-listing__sidebar-block--panel blog-listing__sidebar-block--top-posts">
                                    <h3 class="blog-listing__sidebar-title">Top Posts</h3>
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

                            <?php if ($has_newsletter_sidebar) : ?>
                            <div class="blog-listing__sidebar-block blog-listing__sidebar-block--newsletter">
                                <?php if ($newsletter_heading && $newsletter_heading_tag) : ?>
                                <<?php echo tag_escape($newsletter_heading_tag); ?> class="blog-listing__sidebar-title">
                                <?php echo esc_html($newsletter_heading); ?>
                            </<?php echo tag_escape($newsletter_heading_tag); ?>>
                        <?php endif; ?>

                            <?php if ($newsletter_description) : ?>
                                <div class="blog-listing__newsletter-copy">
                                    <?php echo wp_kses_post($newsletter_description); ?>
                                </div>
                            <?php endif; ?>

                            <form class="blog-listing__newsletter newsletter-subscribe-banner__form">
                                <input
                                        type="email"
                                        class="blog-listing__newsletter-input newsletter-subscribe-banner__input"
                                        placeholder="<?php echo esc_attr($newsletter_placeholder ?: 'Type your email address'); ?>"
                                        required
                                >
                                <button
                                        type="submit"
                                        class="blog-listing__newsletter-submit arrow-circle__link"
                                >
                                    <span class="arrow-circle__label"><?php echo esc_html($newsletter_submit_text ?: 'Subscribe'); ?></span>
                                    <span class="arrow-circle__icon">
                                    <span class="arrow">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.25 15.75L21 12M21 12L17.25 8.25M21 12H3" stroke="currentColor" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                    <span class="circle">
                                        <svg width="28" height="30" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect x="0.375" y="0.375" width="27.25" height="27.25" rx="13.625" stroke="currentColor" stroke-width="0.75"/>
                                        </svg>
                                    </span>
                                </span>
                                </button>
                            </form>
                    </div>
                    <?php endif; ?>
                    </aside>
                    </div>
                </div>
            </div>

        </main>

    <?php
    endwhile;
endif;

get_footer();
?>
<style>
    .site-main--single-blog {
        padding-top: 128px;
        padding-bottom: 96px;
    }

    .single-blog-hero {
        margin-bottom: 72px;
    }

    .single-blog-hero__subheading {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
    }

    .single-blog-hero__subheading-line {
        width: 32px;
        height: 1px;
        background: #323232;
        flex: 0 0 auto;
    }

    .single-blog-hero__subheading-label {
        color: #323232;
        font-size: 16px;
        font-weight: 400;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .single-blog-hero__title {
        max-width: 820px;
        margin: 0 0 24px;
        color: #323232;
        font-family: var(--pm-font-secondary);
        font-size: 32px;
        font-style: italic;
        font-weight: 500;
        line-height: normal;
        letter-spacing: 2px;
    }

    .single-blog-hero__meta {
        display: flex;
        align-items: center;
        gap: 32px;
        margin-bottom: 48px;
        color: #323232;
        font-size: 16px;
        font-weight: 400;
    }

    .single-blog-hero__media {
        overflow: hidden;
    }

    .single-blog-hero__image {
        display: block;
        width: 100%;
        height: auto;
        aspect-ratio: 16 / 7;
        object-fit: cover;
    }

    .site-main--single-blog h2,
    .site-main--single-blog h3,
    .site-main--single-blog h4,
    .site-main--single-blog h5,
    .site-main--single-blog h6{
        color: #323232;
        font-size: 20px;
        font-weight: 500;
        margin-bottom: 1.5rem;
    }
    .site-main--single-blog p {
        color: #323232;
        font-size: 16px;
        font-weight: 400;
    }

    @media (max-width: 991.98px) {
        .site-main--single-blog {
            padding-top: 112px;
            padding-bottom: 72px;
        }

        .single-blog-hero {
            margin-bottom: 56px;
        }

        .single-blog-hero__meta {
            gap: 20px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .single-blog-hero__image {
            aspect-ratio: 16 / 10;
        }
    }
</style>
