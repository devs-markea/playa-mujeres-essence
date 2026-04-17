<?php
// template-parts/sections/inline-notice.php

$icon        = get_sub_field('icon');
$description = get_sub_field('description');

$icon_id  = (is_array($icon) && ! empty($icon['ID'])) ? (int) $icon['ID'] : 0;
$icon_alt = (is_array($icon) && ! empty($icon['alt'])) ? $icon['alt'] : '';

if (! $description) return;
?>

<div data-anim="slide-up delay-2" class="inline-notice">
    <div class="container">
        <div class="row g-0">
            <div class="col-12 col-md-10 mx-auto">
                <div class="inline-notice__inner">

                    <?php if ($icon_id) : ?>
                        <div class="inline-notice__icon" aria-hidden="true">
                            <?php echo wp_get_attachment_image($icon_id, 'thumbnail', false, [
                                'class'    => 'inline-notice__icon-img',
                                'alt'      => $icon_alt,
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            ]); ?>
                        </div>
                    <?php endif; ?>

                    <div class="inline-notice__body">
                        <?php echo wp_kses_post($description); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
