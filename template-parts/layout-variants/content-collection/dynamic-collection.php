<?php
/**
 * Dynamic Collection (Generalized)
 * - Items come from ACF Relationship: `collection_items`
 * - Pills + filtering can be driven by:
 *   A) ACF Relationship field inside each item (default for culinary-experience: `hotel`)
 *   B) Taxonomy terms (exception for Blog: `category`)
 *
 * CONFIG (requested):
 * 1) culinary-experience
 * 2) category (for blog)
 */

// =========================
// CONFIG
// =========================
//$current_collection_post_type = 'culinary-experience'; // your items are culinary-experience
$blog_post_type              = 'post';                // WordPress blog
$blog_filter_taxonomy        = 'category';            // blog uses category
$default_relationship_field  = 'hotel';               // culinary-experience → ACF Relationship field name
$default_search_placeholder  = __('Search', 'textdomain');

// If you ever reuse this template for blog items, set this to true (or infer from collection_items types).
// For now, since your collection is culinary-experience, we’ll use relationship mode by default.
$pill_mode = 'acf_relationship'; // 'acf_relationship' | 'taxonomy'
$pill_acf_field = $default_relationship_field; // e.g. 'hotel'
$pill_taxonomy  = ''; // not used for culinary

// =========================
// INPUTS
// =========================
$collection_items = isset($collection_items) ? $collection_items : get_sub_field('collection_items');

// NEW: ensure we don't override outer block variables
$section_heading     = isset($heading) ? $heading : '';
$section_heading_tag = isset($heading_tag) ? $heading_tag : '';
$section_description = isset($description) ? $description : '';

// Helpers
$has_acf = function_exists('get_field');


// =========================
// BUILD ITEMS + PILLS MAP
// =========================
$collection_items_normalized = array();

/**
 * Pills map:
 * slug => label
 */
$pills_map = array();

// Auto-infer mode when mixing types (optional):
// If you later include blog posts in the same collection, this will switch to taxonomy mode for those items.
// But pills are one set per section; for now, requested setup is culinary-only.
if ($pill_mode === 'taxonomy') {
    $pill_taxonomy = $blog_filter_taxonomy; // category
}

if (!empty($collection_items) && is_array($collection_items)) {
    foreach ($collection_items as $related) {
        $post_id = pm_collection_to_post_id($related);
        if ($post_id <= 0) continue;

        $post_type = get_post_type($post_id);

        // Featured image
        $thumb_id = (int) get_post_thumbnail_id($post_id);
        $img_alt  = $thumb_id > 0 ? (string) get_post_meta($thumb_id, '_wp_attachment_image_alt', true) : '';

        // Description (post_content with WP filters)
        $item_post_content = (string) get_post_field('post_content', $post_id);
        $item_description_html = $item_post_content !== '' ? apply_filters('the_content', $item_post_content) : '';

        // Title
        $item_heading = get_the_title($post_id);

        // ---------------------------
        // FILTER TAGS (GENERAL)
        // ---------------------------
        $filter_slugs   = array(); // for JS filtering
        $supporting_txt = '';      // display (optional)

        // Exception: Blog posts use taxonomy category
        $is_blog_post = ($post_type === $blog_post_type);

        if ($is_blog_post) {
            // Blog: taxonomy mode (category)
            $terms = get_the_terms($post_id, $blog_filter_taxonomy);
            if (!empty($terms) && !is_wp_error($terms)) {
                $labels = array();
                foreach ($terms as $t) {
                    $slug = (string) $t->slug;
                    $label = (string) $t->name;

                    $filter_slugs[] = $slug;
                    $labels[] = $label;

                    if (!isset($pills_map[$slug])) {
                        $pills_map[$slug] = $label;
                    }
                }
                if (!empty($labels)) $supporting_txt = implode(', ', $labels);
            }
        } else {
            // Culinary (and future CPTs): ACF relationship mode (default: hotel)
            if ($has_acf && $pill_acf_field) {
                $rel = get_field($pill_acf_field, $post_id); // Relationship
                $rel_ids = pm_acf_rel_to_ids($rel);

                $labels = array();
                foreach ($rel_ids as $rid) {
                    $slug = (string) get_post_field('post_name', $rid);
                    if ($slug === '') continue;

                    $label = get_the_title($rid);

                    $filter_slugs[] = $slug;
                    $labels[] = $label;

                    if (!isset($pills_map[$slug])) {
                        $pills_map[$slug] = $label;
                    }
                }

                if (!empty($labels)) {
                    $supporting_txt = implode(', ', $labels);
                }
            }
        }

        $filter_slugs = array_values(array_unique(array_filter($filter_slugs)));

        $collection_items_normalized[] = array(
                'post_id'       => $post_id,
                'post_type'     => $post_type,

                'heading'       => $item_heading,
                'heading_level' => 'h3',
                'description'   => $item_description_html,

                'image_id'      => $thumb_id,
                'image_alt'     => $img_alt,

                'enable_overlay' => false,

            // Display (optional)
                'supporting'    => $supporting_txt,
                'text_level'    => 'none',

            // Filter slugs (important)
                'filter_slugs'  => $filter_slugs,
        );
    }
}

if (!empty($pills_map)) {
    asort($pills_map, SORT_NATURAL | SORT_FLAG_CASE);
}

// Placeholder logic
$search_placeholder = __('Search by Hotel', 'textdomain');
if ($pill_mode === 'taxonomy') {
    $search_placeholder = __('Search by Category', 'textdomain');
}
?>

<section data-anim="slide-up delay-2" class="content-collection content-collection--dynamic-collection<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-lg-10 mx-auto">
                <div class="content-collection__inner-heading">
                    <?php if (! empty($section_heading_tag) && ! empty($section_heading)) : ?>
                    <<?php echo tag_escape($section_heading_tag); ?> class="content-collection__heading">
                    <?php echo esc_html($section_heading); ?>
                </<?php echo tag_escape($section_heading_tag); ?>>
                <?php elseif (! empty($section_heading)) : ?>
                    <div class="content-collection__heading content-collection__heading--text-only">
                        <?php echo esc_html($section_heading); ?>
                    </div>
                <?php endif; ?>

                    <?php if (! empty($section_description)) : ?>
                    <div class="content-collection__description">
                        <?php echo wp_kses_post($section_description); ?>
                    </div>
                <?php endif; ?>
                </div>
                <!-- SEARCH + PILLS -->
                <div class="content-collection__filters" data-collection-filter-ui>
                    <div class="row align-items-center gx-4">
                        <div class="col-12 col-md-2">
                            <div class="content-collection__filter-row">
                                <div class="content-collection__search-wrap">
                                    <svg class="content-collection__search-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7 2.5C4.51472 2.5 2.5 4.51472 2.5 7C2.5 9.48528 4.51472 11.5 7 11.5C8.24278 11.5 9.36709 10.9969 10.182 10.182C10.9969 9.36709 11.5 8.24278 11.5 7C11.5 4.51472 9.48528 2.5 7 2.5ZM1.5 7C1.5 3.96243 3.96243 1.5 7 1.5C10.0376 1.5 12.5 3.96243 12.5 7C12.5 8.33855 12.0213 9.56604 11.2266 10.5195L14.3536 13.6464C14.5488 13.8417 14.5488 14.1583 14.3536 14.3536C14.1583 14.5488 13.8417 14.5488 13.6464 14.3536L10.5195 11.2266C9.56604 12.0213 8.33855 12.5 7 12.5C3.96243 12.5 1.5 10.0376 1.5 7Z" fill="black" fill-opacity="0.5"/>
                                    </svg>
                                    <input
                                            id="content-collection-search"
                                            class="content-collection__search"
                                            type="search"
                                            placeholder="<?php echo esc_attr($search_placeholder); ?>"
                                            autocomplete="off"
                                            data-collection-filter-input
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-10">
                            <?php if (!empty($pills_map) && is_array($pills_map)) : ?>
                                <div class="content-collection__pills-wrap">
                                    <div class="content-collection__pills">
                                        <button type="button"
                                                class="content-collection__pill is-active"
                                                data-collection-pill
                                                data-filter-slug="">
                                            <?php echo esc_html__('All', 'textdomain'); ?>
                                        </button>

                                        <?php foreach ($pills_map as $slug => $label) : ?>
                                            <button type="button"
                                                    class="content-collection__pill"
                                                    data-collection-pill
                                                    data-filter-slug="<?php echo esc_attr($slug); ?>">
                                                <span class="content-collection__pill-label"><?php echo esc_html($label); ?></span>
                                                <span class="content-collection__pill-x" aria-hidden="true">
                                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M5.28033 4.21967C4.98744 3.92678 4.51256 3.92678 4.21967 4.21967C3.92678 4.51256 3.92678 4.98744 4.21967 5.28033L6.93934 8L4.21967 10.7197C3.92678 11.0126 3.92678 11.4874 4.21967 11.7803C4.51256 12.0732 4.98744 12.0732 5.28033 11.7803L8 9.06066L10.7197 11.7803C11.0126 12.0732 11.4874 12.0732 11.7803 11.7803C12.0732 11.4874 12.0732 11.0126 11.7803 10.7197L9.06066 8L11.7803 5.28033C12.0732 4.98744 12.0732 4.51256 11.7803 4.21967C11.4874 3.92678 11.0126 3.92678 10.7197 4.21967L8 6.93934L5.28033 4.21967Z" fill="white"/>
                                                    </svg>
                                                </span>
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="content-collection__filters-mobile">
                    <div class="content-collection__search-wrap">
                        <svg class="content-collection__search-icon" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7 2.5C4.51472 2.5 2.5 4.51472 2.5 7C2.5 9.48528 4.51472 11.5 7 11.5C8.24278 11.5 9.36709 10.9969 10.182 10.182C10.9969 9.36709 11.5 8.24278 11.5 7C11.5 4.51472 9.48528 2.5 7 2.5ZM1.5 7C1.5 3.96243 3.96243 1.5 7 1.5C10.0376 1.5 12.5 3.96243 12.5 7C12.5 8.33855 12.0213 9.56604 11.2266 10.5195L14.3536 13.6464C14.5488 13.8417 14.5488 14.1583 14.3536 14.3536C14.1583 14.5488 13.8417 14.5488 13.6464 14.3536L10.5195 11.2266C9.56604 12.0213 8.33855 12.5 7 12.5C3.96243 12.5 1.5 10.0376 1.5 7Z" fill="black" fill-opacity="0.5"/>
                        </svg>
                        <input
                                id="content-collection-search"
                                class="content-collection__search"
                                type="search"
                                placeholder="<?php echo esc_attr($search_placeholder); ?>"
                                autocomplete="off"
                                data-collection-filter-input
                        />
                    </div>

                    <div class="content-collection__filter-button-mobile">
                        <button type="button"
                                class="content-collection__pill"
                                data-collection-pill
                                data-filter-slug="">
                            <?php echo esc_html__('Filter By hotel', 'textdomain'); ?>
                        </button>
                    </div>
                </div>


                <div class="content-collection__wrapper-content-collection">
                    <?php if (! empty($collection_items_normalized) && is_array($collection_items_normalized)) : ?>
                    <div class="row g-4 justify-content-between content-collection__grid" role="list">
                        <?php foreach ($collection_items_normalized as $item) : ?>
                        <?php
                        $item_heading       = isset($item['heading']) ? (string) $item['heading'] : '';
                        $item_heading_level = isset($item['heading_level']) ? (string) $item['heading_level'] : '';
                        $item_description   = isset($item['description']) ? (string) $item['description'] : '';

                        $item_image_id      = isset($item['image_id']) ? (int) $item['image_id'] : 0;
                        $item_image_alt     = isset($item['image_alt']) ? (string) $item['image_alt'] : '';
                        $item_enable_overlay = ! empty($item['enable_overlay']);

                        $item_supporting            = isset($item['supporting']) ? (string) $item['supporting'] : '';
                        $item_supporting_text_level = isset($item['text_level']) ? (string) $item['text_level'] : '';

                        // Filter attribute supports multiple slugs: "slug1|slug2|slug3"
                        $filter_slugs = !empty($item['filter_slugs']) ? (array) $item['filter_slugs'] : array();
                        $filter_attr  = !empty($filter_slugs) ? implode('|', $filter_slugs) : '';

                        $search_blob = strtolower(
                                $item_heading . ' ' .
                                $item_supporting . ' ' .
                                wp_strip_all_tags($item_description)
                        );

                        $item_tag       = function_exists('pm_essence_heading_tag_or_null') ? pm_essence_heading_tag_or_null($item_heading_level, 'h3') : 'h3';
                        $supporting_tag = function_exists('pm_essence_heading_tag_or_null') ? pm_essence_heading_tag_or_null($item_supporting_text_level, '') : '';
                        ?>
                        <div class="col-12 col-md-6 content-collection__col">
                            <article class="content-collection__card"
                                     role="listitem"
                                     data-filter="<?php echo esc_attr($filter_attr); ?>"
                                     data-search="<?php echo esc_attr($search_blob); ?>">

                                <div class="content-collection__media<?php echo $item_enable_overlay ? ' content-collection__media--overlay' : ''; ?>">
                                    <?php if ($item_image_id > 0) : ?>
                                        <?php
                                        echo wp_get_attachment_image(
                                                $item_image_id,
                                                'large',
                                                false,
                                                array(
                                                        'class'    => 'content-collection__image',
                                                        'alt'      => $item_image_alt,
                                                        'loading'  => 'lazy',
                                                        'decoding' => 'async',
                                                )
                                        );
                                        ?>
                                    <?php endif; ?>

                                    <?php if ($item_supporting !== '') : ?>
                                    <?php if (! empty($supporting_tag)) : ?>
                                    <<?php echo tag_escape($supporting_tag); ?> class="content-collection__supporting content-collection__supporting--absolute">
                                    <?php echo esc_html($item_supporting); ?>
                                </<?php echo tag_escape($supporting_tag); ?>>
                                <?php else : ?>
                                    <div class="content-collection__supporting content-collection__supporting--absolute content-collection__supporting--text-only">
                                        <?php echo esc_html($item_supporting); ?>
                                    </div>
                                <?php endif; ?>
                                <?php endif; ?>
                        </div>

                        <div class="content-collection__body">
                            <?php if (! empty($item_tag) && $item_heading !== '') : ?>
                            <<?php echo tag_escape($item_tag); ?> class="content-collection__card-heading">
                            <?php echo esc_html($item_heading); ?>
                        </<?php echo tag_escape($item_tag); ?>>
                    <?php elseif ($item_heading !== '') : ?>
                        <div class="content-collection__card-heading">
                            <?php echo esc_html($item_heading); ?>
                        </div>
                    <?php endif; ?>

                        <?php if ($item_description !== '') : ?>
                            <div class="content-collection__card-description">
                                <?php echo wp_kses_post($item_description); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
                </div>

                <div class="content-collection__load-more">
                    <button type="button" class="content-collection__load-more-btn" data-collection-load-more style="display:none;">
                        <?php pll_e('Load more'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="content-collection__bottom-sentinel" aria-hidden="true"></div>

    <!-- Botón fijo mobile que abre el drawer -->
    <button type="button"
            class="content-collection__filter-button content-collection__filter-button-mobile-bottom"
            aria-hidden="true"
            data-collection-filters-mobile-trigger>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4.5 15.75L12 8.25L19.5 15.75" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <?php echo esc_html__('Filter By Hotel', 'textdomain'); ?>
    </button>


</section>