<?php
$args = wp_parse_args($args ?? array(), array(
    'post_id'     => 0,
    'heading_tag' => 'h2',
));

$post_id = (int) $args['post_id'];
$heading_tag = is_string($args['heading_tag']) ? strtolower($args['heading_tag']) : 'h2';

if (! $post_id) {
    return;
}

$allowed_heading_tags = array('h2', 'h3', 'h4', 'p', 'span', 'div');
if (! in_array($heading_tag, $allowed_heading_tags, true)) {
    $heading_tag = 'h2';
}

$title     = get_the_title($post_id);
$permalink = get_permalink($post_id);
$excerpt   = get_the_excerpt($post_id);

$image = get_the_post_thumbnail(
    $post_id,
    'large',
    array(
        'class' => 'blog-hero-card__image',
    )
);
?>

<article class="blog-hero-card">
    <div class="blog-hero-card__media">
        <?php echo $image; ?>
    </div>

    <div class="blog-hero-card__overlay"></div>

    <div class="blog-hero-card__content text-white">
        <span class="blog-hero-card__title-label" aria-hidden="true"><?php echo esc_html($title); ?></span>

        <div class="blog-hero-card__drawer">
            <<?php echo tag_escape($heading_tag); ?> class="blog-hero-card__title mb-2">
            <?php echo esc_html($title); ?>
        </<?php echo tag_escape($heading_tag); ?>>

        <?php if ($excerpt) : ?>
            <div class="blog-hero-card__excerpt mb-3">
                <?php echo esc_html($excerpt); ?>
            </div>
        <?php endif; ?>

        <div class="blog-hero-card__footer">
            <a
                href="<?php echo esc_url($permalink); ?>"
                class="blog-hero-card__cta-link"
                aria-label="<?php echo esc_attr($title); ?>"
            >
                <span class="blog-hero-card__cta-label">Read More</span>
                <span class="blog-hero-card__cta-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" focusable="false">
                            <path d="M5 12h14M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </span>
            </a>
        </div>
        </div>
    </div>
</article>
