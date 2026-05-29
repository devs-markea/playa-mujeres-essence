<?php
// template-parts/blog/sections/text-block.php
$text = get_sub_field('text');
$has_text = (!empty($text));
?>

<section class="blog-text-block">
    <div class="row g-0">
        <div class="col-12 col-md-10 offset-md-2">
            <div class="row align-items-center">
                <?php if ($has_text) : ?>
                    <div class="pm-text-block__text">
                        <?= wp_kses_post($text); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>