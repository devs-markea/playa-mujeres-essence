<?php
$args = wp_parse_args($args ?? array(), array(
    'post_id'     => 0,
    'heading_tag' => 'h2',
));

$post_id     = (int) $args['post_id'];
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
$yoast_desc = get_post_meta( $post_id, '_yoast_wpseo_metadesc', true );
$excerpt    = $yoast_desc ?: get_the_excerpt( $post_id );

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
            <<?php echo tag_escape($heading_tag); ?> class="blog-hero-card__title">
                <?php echo esc_html($title); ?>
            </<?php echo tag_escape($heading_tag); ?>>

            <div class="blog-hero-card__footer">
                <?php if ($excerpt) : ?>
                    <p class="blog-hero-card__excerpt">
                        <?php echo esc_html($excerpt); ?>
                    </p>
                <?php endif; ?>

                <div class="arrow-circle">
                    <a href="<?php echo esc_url($permalink); ?>" class="arrow-circle__link" aria-label="<?php echo esc_attr($title); ?>">
                        <span class="arrow-circle__label">Read More</span>
                        <span class="arrow-circle__icon">
                            <span class="arrow">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.25 15.75L21 12M21 12L17.25 8.25M21 12H3" stroke="white" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="circle">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.375" y="0.375" width="27.25" height="27.25" rx="13.625" stroke="white" stroke-width="0.75"/>
                                </svg>
                            </span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</article>