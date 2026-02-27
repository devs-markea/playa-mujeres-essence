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

$to_post_id = function ($related) {
    if (is_numeric($related)) return (int) $related;
    if (is_object($related) && !empty($related->ID)) return (int) $related->ID;
    return 0;
};

$acf_rel_to_ids = function($value) {
    $ids = array();
    if (empty($value)) return $ids;

    if (is_array($value)) {
        foreach ($value as $v) {
            if (is_numeric($v)) {
                $ids[] = (int) $v;
            } elseif (is_object($v) && !empty($v->ID)) {
                $ids[] = (int) $v->ID;
            }
        }
    } else {
        if (is_numeric($value)) {
            $ids[] = (int) $value;
        } elseif (is_object($value) && !empty($value->ID)) {
            $ids[] = (int) $value->ID;
        }
    }

    $ids = array_values(array_unique(array_filter($ids)));
    return $ids;
};

$post_slug = function($post_id) {
    return (string) get_post_field('post_name', (int)$post_id);
};

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
        $post_id = $to_post_id($related);
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
                $rel_ids = $acf_rel_to_ids($rel);

                $labels = array();
                foreach ($rel_ids as $rid) {
                    $slug = $post_slug($rid);
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

<section class="content-collection content-collection--dynamic-collection">
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

                    <div class="content-collection__filter-button-mobile" style="display:none;">
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

                <div class="content-collection__load-more" style="display:flex;justify-content:center;">
                    <button type="button" class="btn btn-primary btn-border-bottom-black" data-collection-load-more style="display:none;">
                        More
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="content-collection__bottom-sentinel" aria-hidden="true"></div>

    <div class="content-collection__filter-button-mobile-bottom" aria-hidden="true">
        <button type="button"
                class="content-collection__pill"
                data-collection-pill
                data-collection-filters-mobile-trigger
                data-filter-slug="">
            Filter By hotel
        </button>
    </div>

</section>

<style>
    .content-collection__filter-button-mobile-bottom{
        position: fixed;
        left: 50%;
        bottom: 16px;
        transform: translateX(-50%);
        z-index: 999;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 200ms ease, visibility 200ms ease;
        will-change: transform, opacity;
    }

    .content-collection__filter-button-mobile-bottom.is-visible{
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        animation: contentCollectionBounceIn 450ms cubic-bezier(.2,.9,.25,1.2);
    }

    @keyframes contentCollectionBounceIn{
        0%   { transform: translateX(-50%) translateY(18px) scale(0.98); }
        60%  { transform: translateX(-50%) translateY(-6px) scale(1.02); }
        100% { transform: translateX(-50%) translateY(0) scale(1); }
    }

    @media (prefers-reduced-motion: reduce){
        .content-collection__filter-button-mobile-bottom.is-visible{
            animation: none;
        }
    }

    /* opcional: no mostrar en desktop */
    @media (min-width: 991px){
        .content-collection__filter-button-mobile-bottom{ display:none !important; }
    }
    .content-collection { margin: 136px 0; }

    .content-collection__heading {
        font-family: var(--pm-font-secondary);
        font-size: 32px;
        font-style: italic;
        line-height: 1.2;
        font-weight: 600;
        text-align: start;
    }

    .content-collection__heading--text-only {
        font-family: inherit;
        font-style: normal;
        font-size: 20px;
        font-weight: 400;
        color: #323232;
    }

    .content-collection__filters-mobile {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .content-collection__filters {
        display: none;
    }

    .content-collection__description { margin-top: 14px; }
    .content-collection__grid { margin-top: 48px; }

    .content-collection__pill {
        border-radius: 50px;
        border: 1px solid #E5E5E5;
        background: #fff;
        padding: 8px 12px;
        color: #323232;
        text-align: center;
        font-size: 16px;
        font-style: normal;
        font-weight: 300;
        cursor: grab;
        flex: 0 0 auto;
        white-space: nowrap;
    }
    .content-collection__pill.is-active {
        background: #A89968;
        color: #fff;
        border-color: #A89968;
    }

    .content-collection__pills.is-dragging { cursor: grabbing; user-select: none; }
    .content-collection__pills.is-dragging .content-collection__pill { pointer-events: none; }

    .content-collection__media { position: relative; overflow: hidden; }
    .content-collection__image { width: 100%; height: auto; object-fit: cover; display: block; }

    .content-collection--dynamic-collection .content-collection__media {
        aspect-ratio: 4 / 3;
        max-height: 420px;
    }
    .content-collection--dynamic-collection .content-collection__image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .content-collection--dynamic-collection .content-collection__image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (min-width: 991px) {
        .content-collection__filters-mobile {
            display: none;
        }
        .content-collection__filters {
            display: block;
        }
        .content-collection--dynamic-collection .content-collection__media {
            height: 420px;
            max-height: none;
            aspect-ratio: auto;
        }
        .content-collection__inner-heading { margin-bottom: 1rem; }
    }

    .content-collection__media--overlay::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.25);
        pointer-events: none;
    }

    .content-collection__body { padding: 20px 10px 0; box-sizing: border-box; }

    .content-collection__card-heading {
        margin: 0 0 12px;
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 600;
        font-size: 28px;
        line-height: 1.2;
    }

    .content-collection__card-description {
        font-family: var(--pm-font-primary);
        font-weight: 300;
        font-size: 16px;
        line-height: 1.4;
    }

    .content-collection__supporting--absolute {
        position: absolute;
        right: 20px;
        bottom: 14px;
        z-index: 2;
        color: #FFF;
        text-align: right;
        font-family: var(--pm-font-secondary);
        font-size: 16px;
        font-style: italic;
        font-weight: 500;
        line-height: 20px;
    }

    .content-collection__pills-wrap { position: relative; }

    .content-collection__pills-wrap::before,
    .content-collection__pills-wrap::after {
        content: "";
        position: absolute;
        top: 0;
        bottom: 0;
        width: 36px;
        pointer-events: none;
        z-index: 2;
    }
    .content-collection__pills-wrap::before {
        left: 0;
        background: linear-gradient(to right, rgba(255,255,255,1), rgba(255,255,255,0));
    }
    .content-collection__pills-wrap::after {
        right: 0;
        background: linear-gradient(to left, rgba(255,255,255,1), rgba(255,255,255,0));
    }

    .content-collection__filter-row {
        display:flex;
        align-items:center;
    }

    .content-collection__search-wrap {
        position: relative;
        width: 100%;
        border-bottom: 1px solid #E5E5E5;
        border-radius: 2px;
        overflow: hidden;
    }

    .content-collection__search {
        width: 100%;
        display: flex;
        height: auto;
        padding: 12px 4px 12px 36px;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
        border-radius: 2px;
        border: none;
    }

    .content-collection__search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
    .content-collection__search:focus { border:none; outline: none; }

    .content-collection__pills {
        display: flex;
        gap: 10px;
        flex-wrap: nowrap;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
        padding: 0 36px;
    }
    .content-collection__pills::-webkit-scrollbar { display: none; }
    /* Layout interno del pill para label + X */
    .content-collection__pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .content-collection__filter-button-mobile {
        display: flex;
        flex-direction: column;
    }

    /* La X solo aparece cuando está activo (seleccionado) */
    .content-collection__pill-x {
        display: none;
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
    }
    .content-collection__pill.is-active .content-collection__pill-x {
        display: inline-flex;
    }

    /* Evita que “All” muestre la X */
    .content-collection__pill[data-filter-slug=""] .content-collection__pill-x {
        display: none !important;
    }
</style>


<script type="text/javascript">
    (function () {
        let ui = document.querySelector('[data-collection-filter-ui]');
        if (!ui) return;

        let input = ui.querySelector('[data-collection-filter-input]');
        let section = ui.closest('section') || document;

        let pillsScroller = ui.querySelector('.content-collection__pills');
        let cards = section.querySelectorAll('.content-collection__card[data-filter][data-search]');
        let loadMoreBtn = section.querySelector('[data-collection-load-more]');

        // Multi-select
        let selected = {}; // {slug: true}
        let STEP = 10;
        let visibleLimit = STEP;

        function normalize(s) {
            s = (s || '').toString().toLowerCase();

            if (s.normalize) {
                s = s.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
            }

            return s.replace(/\s+/g, ' ').trim();
        }

        function getQuery() {
            return input ? normalize(input.value) : '';
        }

        function getSelectedSlugs() {
            let out = [];
            for (let k in selected) {
                if (selected.hasOwnProperty(k) && selected[k]) out.push(k);
            }
            return out;
        }

        function cardHasAnySelectedFilter(card, selectedSlugs) {
            if (!selectedSlugs || selectedSlugs.length === 0) return true;

            let raw = normalize(card.getAttribute('data-filter') || '');
            if (!raw) return false;

            let parts = raw.split('|');
            let lookup = {};
            for (let i = 0; i < parts.length; i++) {
                let s = normalize(parts[i]);
                if (s) lookup[s] = true;
            }

            for (let j = 0; j < selectedSlugs.length; j++) {
                let sel = normalize(selectedSlugs[j]);
                if (sel && lookup[sel]) return true;
            }
            return false;
        }

        function matches(card, query, selectedSlugs) {
            let haystack = normalize(card.getAttribute('data-search') || '');
            let queryOk  = !query || haystack.indexOf(query) !== -1;
            let filterOk = cardHasAnySelectedFilter(card, selectedSlugs);
            return queryOk && filterOk;
        }

        function syncPillsUI() {
            if (!pillsScroller) return;

            let anySelected = getSelectedSlugs().length > 0;
            let pills = pillsScroller.querySelectorAll('[data-collection-pill]');

            for (let i = 0; i < pills.length; i++) {
                let slug = normalize(pills[i].getAttribute('data-filter-slug') || '');

                if (slug === '') {
                    // "All" activo cuando NO hay selección
                    if (!anySelected) pills[i].classList.add('is-active');
                    else pills[i].classList.remove('is-active');
                } else {
                    if (selected[slug]) pills[i].classList.add('is-active');
                    else pills[i].classList.remove('is-active');
                }
            }
        }

        function scrollPillIntoView(btn) {
            if (!btn) return;
            let scroller = btn.closest('.content-collection__pills');
            if (!scroller) return;

            // Centrar el pill dentro del contenedor
            let left = btn.offsetLeft - (scroller.clientWidth / 2) + (btn.offsetWidth / 2);

            scroller.scrollTo({
                left: left,
                behavior: 'smooth'
            });
        }

        function apply() {
            let query = getQuery();
            let selectedSlugs = getSelectedSlugs();

            let filtered = [];
            for (let i = 0; i < cards.length; i++) {
                if (matches(cards[i], query, selectedSlugs)) filtered.push(cards[i]);
            }

            for (let j = 0; j < cards.length; j++) {
                let colAll = cards[j].closest('.content-collection__col') || cards[j];
                colAll.style.display = 'none';
            }

            for (let k = 0; k < filtered.length; k++) {
                let col = filtered[k].closest('.content-collection__col') || filtered[k];
                if (k < visibleLimit) col.style.display = '';
            }

            if (loadMoreBtn) {
                loadMoreBtn.style.display = filtered.length > visibleLimit ? '' : 'none';
            }
        }

        function resetAndApply() {
            visibleLimit = STEP;
            apply();
        }

        // =========================
        // Pills click (event delegation)
        // =========================
        if (pillsScroller) {
            pillsScroller.addEventListener('click', function (e) {
                let btn = e.target.closest('[data-collection-pill]');
                if (!btn || !pillsScroller.contains(btn)) return;

                let slug = normalize(btn.getAttribute('data-filter-slug') || '');

                // "All" => limpia selección
                if (slug === '') {
                    selected = {};
                    syncPillsUI();
                    resetAndApply();
                    return;
                }

                // Toggle multi
                if (selected[slug]) delete selected[slug];
                else selected[slug] = true;

                syncPillsUI();
                scrollPillIntoView(btn);
                resetAndApply();
            });
        }

        // Search combinado con pills
        if (input) {
            input.addEventListener('input', function () {
                resetAndApply();
            });
        }

        // Load more
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', function () {
                visibleLimit += STEP;
                apply();
            });
        }

        // =========================
        // Drag-to-scroll (SOLO mouse) sin romper click
        // =========================
        if (pillsScroller) {
            let isDown = false;
            let startX = 0;
            let scrollLeftStart = 0;
            let didDrag = false;
            let DRAG_THRESHOLD = 8;

            pillsScroller.addEventListener('pointerdown', function (e) {
                if (e.pointerType !== 'mouse') return;
                if (e.button !== 0) return;

                isDown = true;
                didDrag = false;
                startX = e.clientX;
                scrollLeftStart = pillsScroller.scrollLeft;
            });

            pillsScroller.addEventListener('pointermove', function (e) {
                if (!isDown) return;

                let dx = e.clientX - startX;

                if (!didDrag && Math.abs(dx) >= DRAG_THRESHOLD) {
                    didDrag = true;
                    pillsScroller.classList.add('is-dragging');
                    try { pillsScroller.setPointerCapture(e.pointerId); } catch (err) {}
                }

                if (didDrag) {
                    pillsScroller.scrollLeft = scrollLeftStart - dx;
                }
            });

            function endDrag() {
                if (!isDown) return;
                isDown = false;

                if (didDrag) {
                    pillsScroller.classList.remove('is-dragging');

                    // Cancelar el click solo si realmente hubo drag
                    let cancelClickOnce = function (ev) {
                        ev.preventDefault();
                        ev.stopPropagation();
                        pillsScroller.removeEventListener('click', cancelClickOnce, true);
                    };
                    pillsScroller.addEventListener('click', cancelClickOnce, true);
                }
            }

            pillsScroller.addEventListener('pointerup', endDrag);
            pillsScroller.addEventListener('pointercancel', endDrag);
            pillsScroller.addEventListener('pointerleave', endDrag);
        }

        // init
        syncPillsUI();
        apply();

        // =========================
        // Bottom mobile button visibility:
        // SOLO visible cuando el usuario está viendo el contenedor
        // `.content-collection__wrapper-content-collection`
        // (no depende de `.content-collection__filters-mobile`)
        // =========================
        var bottomWrap = section.querySelector('.content-collection__filter-button-mobile-bottom');
        if (!bottomWrap) return;

        var wrapper = section.querySelector('.content-collection__wrapper-content-collection');
        if (!wrapper) return;

        var mm = window.matchMedia ? window.matchMedia('(max-width: 990px)') : null;

        function shouldRun() {
            return !mm || mm.matches;
        }

        function setBottomVisible(visible) {
            if (visible) bottomWrap.classList.add('is-visible');
            else bottomWrap.classList.remove('is-visible');
            bottomWrap.setAttribute('aria-hidden', visible ? 'false' : 'true');
        }

        // Estado inicial
        setBottomVisible(false);

        function wrapperIsInView() {
            var rect = wrapper.getBoundingClientRect();
            var vh = window.innerHeight || document.documentElement.clientHeight || 0;

            // Visible si cualquier parte del wrapper está en viewport
            return rect.bottom > 0 && rect.top < vh;
        }

        // Preferimos IntersectionObserver (sentinel-like, sin scroll handlers)
        if ('IntersectionObserver' in window) {
            var obs = new IntersectionObserver(function (entries) {
                if (!shouldRun()) { setBottomVisible(false); return; }
                var entry = entries && entries[0] ? entries[0] : null;
                setBottomVisible(!!(entry && entry.isIntersecting));
            }, {
                root: null,
                threshold: 0.01
            });

            obs.observe(wrapper);
        } else {
            // Fallback para navegadores antiguos
            function onScrollOrResize() {
                if (!shouldRun()) { setBottomVisible(false); return; }
                setBottomVisible(wrapperIsInView());
            }
            window.addEventListener('scroll', onScrollOrResize, { passive: true });
            window.addEventListener('resize', onScrollOrResize);
            onScrollOrResize();
        }

        function onMqChange() {
            if (!shouldRun()) setBottomVisible(false);
        }
        if (mm && mm.addEventListener) mm.addEventListener('change', onMqChange);
        else if (mm && mm.addListener) mm.addListener(onMqChange);
        // ... existing code ...
    })();
</script>
<style>
    .content-collection__filter-button-mobile-bottom{
        position: fixed;
        left: 50%;
        bottom: 1.5rem;
        width: 80%;
        display: flex;
        flex-direction: column;
        padding-left: 3rem;
        padding-right: 3rem;
        transform: translateX(-50%);
        z-index: 10;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 200ms ease, visibility 200ms ease;
        will-change: transform, opacity;
    }

    .content-collection__filter-button-mobile-bottom.is-visible{
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        animation: contentCollectionBounceIn 450ms cubic-bezier(.2,.9,.25,1.2);
    }

    @keyframes contentCollectionBounceIn{
        0%   { transform: translateX(-50%) translateY(18px) scale(0.98); }
        60%  { transform: translateX(-50%) translateY(-6px) scale(1.02); }
        100% { transform: translateX(-50%) translateY(0) scale(1); }
    }

    @media (prefers-reduced-motion: reduce){
        .content-collection__filter-button-mobile-bottom.is-visible{
            animation: none;
        }
    }

    /* opcional: no mostrar en desktop */
    @media (min-width: 991px){
        .content-collection__filter-button-mobile-bottom{ display:none !important; }
    }
</style>