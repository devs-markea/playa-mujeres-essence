<?php
// template-parts/sections/content-accordion.php

$heading_title       = get_sub_field('heading_title');
$heading_level_title = get_sub_field('heading_level_title');
$accordion_items     = get_sub_field('accordion_items');

// heading_level_title default es "none" → fallback h2
$heading_tag = pm_essence_heading_tag_or_null($heading_level_title, 'h2');
if (empty($heading_tag)) {
    $heading_tag = 'h2';
}

// ID único para el accordion (evita colisiones si hay varios en la misma página)
$accordion_id = 'content-accordion-' . uniqid();
?>

<section class="content-accordion">
    <div class="container">

        <?php if ($heading_title) : ?>
            <div class="content-accordion__header">
                <<?php echo esc_html($heading_tag); ?> class="content-accordion__title">
                    <?php echo esc_html($heading_title); ?>
                </<?php echo esc_html($heading_tag); ?>>
            </div>
        <?php endif; ?>

        <?php if ($accordion_items) : ?>
            <div class="content-accordion__list" id="<?php echo esc_attr($accordion_id); ?>">
                <?php foreach ($accordion_items as $index => $item) :
                    $item_heading       = $item['heading']       ?? '';
                    $item_heading_level = $item['heading_level'] ?? 'none';
                    $item_description   = $item['description']   ?? '';

                    // heading_level item default es "none" → fallback h3
                    $item_tag = pm_essence_heading_tag_or_null($item_heading_level, 'h3');
                    if (empty($item_tag)) {
                        $item_tag = 'h3';
                    }

                    $collapse_id = $accordion_id . '-collapse-' . $index;
                    $is_first    = $index === 0;
                ?>
                    <div class="content-accordion__item">

                        <button
                            class="content-accordion__trigger <?php echo $is_first ? '' : 'collapsed'; ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#<?php echo esc_attr($collapse_id); ?>"
                            aria-expanded="<?php echo $is_first ? 'true' : 'false'; ?>"
                            aria-controls="<?php echo esc_attr($collapse_id); ?>">

                            <<?php echo esc_html($item_tag); ?> class="content-accordion__item-heading">
                                <?php echo esc_html($item_heading); ?>
                            </<?php echo esc_html($item_tag); ?>>

                            <span class="content-accordion__chevron" aria-hidden="true">
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6L8 11L13 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </button>

                        <div
                            class="collapse <?php echo $is_first ? 'show' : ''; ?>"
                            id="<?php echo esc_attr($collapse_id); ?>"
                            data-bs-parent="#<?php echo esc_attr($accordion_id); ?>">
                            <div class="content-accordion__body">
                                <?php echo wp_kses_post($item_description); ?>
                            </div>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>
<style>
    /* ============================================================
       CONTENT ACCORDION  |  mobile-first
    ============================================================ */

    .content-accordion {
        padding: 56px 0 72px;
        background-color: var(--pm-primary-100);
    }

    /* ── Header ── */
    .content-accordion__header {
        text-align: center;
        margin-bottom: 48px;
    }

    .content-accordion__title {
        font-family: var(--pm-font-secondary);
        font-style: italic;
        font-weight: 500;
        font-size: 26px;
        letter-spacing: 2px;
        color: var(--pm-secondary-900);
        margin: 0;
    }

    /* ── List ── */
    .content-accordion__list {
        max-width: 680px;
        margin: 0 auto;
    }

    /* ── Item ── */
    .content-accordion__item {
        border-top: 1px solid var(--pm-secondary-300, #d4cfc9);
    }

    .content-accordion__item:last-child {
        border-bottom: 1px solid var(--pm-secondary-300, #d4cfc9);
    }

    /* ── Trigger ── */
    .content-accordion__trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        gap: 16px;
        padding: 20px 0;
        background: none;
        border: none;
        cursor: pointer;
        text-align: left;
        color: inherit;
    }

    .content-accordion__trigger:focus-visible {
        outline: 2px solid var(--pm-secondary-900);
        outline-offset: 2px;
    }

    /* ── Item heading ── */
    .content-accordion__item-heading {
        font-size: 16px;
        font-weight: 500;
        color: var(--pm-secondary-900);
        margin: 0;
        flex: 1;
    }

    /* ── Chevron ── */
    .content-accordion__chevron {
        flex-shrink: 0;
        color: var(--pm-secondary-600, #888);
        transition: transform 0.25s ease;
        display: flex;
        align-items: center;
    }

    .content-accordion__trigger:not(.collapsed) .content-accordion__chevron {
        transform: rotate(180deg);
    }

    /* ── Body ── */
    .content-accordion__body {
        padding: 0 0 20px;
        color: var(--pm-secondary-900);
        font-size: 16px;
        font-weight: 300;
    }

    .content-accordion__body p {
        margin: 0;
    }

    .content-accordion__list .content-accordion__item:first-child {
        border-top: none;
    }

    /* ============================================================
       DESKTOP >= 992px
    ============================================================ */
    @media (min-width: 992px) {

        .content-accordion {
            padding: 72px 0 96px;
        }

        .content-accordion__title {
            font-size: 32px;
        }

    }
</style>
