<?php
$args = wp_parse_args($args ?? array(), array(
    'term' => null,
));

$term = $args['term'];

if ( ! $term instanceof WP_Term ) {
    return;
}
?>

<section class="category-hero">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="category-hero__content">
                    <div class="category-hero__subheading">
                        <span class="category-hero__subheading-line" aria-hidden="true"></span>
                        <span class="category-hero__subheading-label"><?php pll_e('Blog'); ?></span>
                        <span class="category-hero__subheading-line" aria-hidden="true"></span>
                    </div>
                    <h1 class="category-hero__title"><?php echo esc_html( $term->name ); ?></h1>
                </div>
            </div>
        </div>
    </div>
</section>
