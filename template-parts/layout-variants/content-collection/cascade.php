<?php

?>
<section data-anim="slide-up delay-2" class="content-collection">
    <div class="container">

        <div class="row g-0">
            <div class="col-12 col-lg-10 mx-auto">
                <!-- Heading -->
                <div class="content-collection__inner-heading">
                    <div class="row">
                        <div class="col-12 col-lg-6 mx-auto">
                            <?php if (! empty($heading_tag) && ! empty($heading)) : ?>
                            <<?php echo tag_escape($heading_tag); ?> class="content-collection__heading">
                            <?php echo esc_html($heading); ?>
                        </<?php echo tag_escape($heading_tag); ?>>
                        <?php elseif (! empty($heading)) : ?>
                            <div class="content-collection__heading content-collection__heading--text-only">
                                <?php echo esc_html($heading); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (! empty($description)) : ?>
                            <div class="content-collection__description">
                                <?php echo wp_kses_post($description); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Grid Cascade -->
            <?php if (! empty($items) && is_array($items)) : ?>
            <div class="row g-2 justify-content-between content-collection__grid" role="list">
                <?php foreach ($items as $item) : ?>
                <?php
                $item_heading               = isset($item['heading']) ? $item['heading'] : '';
                $item_heading_level         = isset($item['heading_level']) ? $item['heading_level'] : '';
                $item_description           = isset($item['description']) ? $item['description'] : '';
                $item_show_button           = ! empty($item['show_button']);
                $item_button_link           = isset($item['button_link']) ? $item['button_link'] : null;

                $item_image                 = isset($item['image']) ? $item['image'] : null;
                $item_enable_overlay        = ! empty($item['enable_overlay']);

                $item_supporting            = isset($item['supporting']) ? $item['supporting'] : '';
                $item_supporting_text_level = isset($item['text_level']) ? $item['text_level'] : ''; // none|h2..h6

                // Si es none => null. Si es h2..h6 => tag válido.
                $supporting_tag = pm_essence_heading_tag_or_null($item_supporting_text_level, '');

                $item_enable_supporting_img = ! empty($item['enable_supporting_image']);
                $item_supporting_image      = isset($item['supporting_image']) ? $item['supporting_image'] : null;

                $item_tag       = pm_essence_heading_tag_or_null($item_heading_level, 'h3');
                $supporting_tag = pm_essence_heading_tag_or_null($item_supporting_text_level, ''); // si none => null

                // Imagen principal (ACF puede devolver: ID (int), array, o URL (string))
                $img_id  = 0;
                $img_url = '';
                $img_alt = '';

                if (is_numeric($item_image)) {
                    $img_id = (int) $item_image;
                } elseif (is_array($item_image)) {
                    $img_id  = ! empty($item_image['ID']) ? (int) $item_image['ID'] : 0;
                    $img_url = ! empty($item_image['url']) ? (string) $item_image['url'] : '';
                    $img_alt = ! empty($item_image['alt']) ? (string) $item_image['alt'] : '';
                } elseif (is_string($item_image) && $item_image !== '') {
                    $img_url = $item_image;
                }

                // Si solo tenemos URL, intentamos resolver ID (para poder usar wp_get_attachment_image)
                if ($img_id <= 0 && $img_url !== '') {
                    $maybe_id = (int) attachment_url_to_postid($img_url);
                    if ($maybe_id > 0) {
                        $img_id = $maybe_id;
                    }
                }

                // Supporting image (ACF puede devolver: ID (int), array, o URL (string))
                $support_id  = 0;
                $support_url = '';
                $support_alt = '';

                if (is_numeric($item_supporting_image)) {
                    $support_id = (int) $item_supporting_image;
                } elseif (is_array($item_supporting_image)) {
                    $support_id  = ! empty($item_supporting_image['ID']) ? (int) $item_supporting_image['ID'] : 0;
                    $support_url = ! empty($item_supporting_image['url']) ? (string) $item_supporting_image['url'] : '';
                    $support_alt = ! empty($item_supporting_image['alt']) ? (string) $item_supporting_image['alt'] : '';
                } elseif (is_string($item_supporting_image) && $item_supporting_image !== '') {
                    $support_url = $item_supporting_image;
                }

                if ($support_id <= 0 && $support_url !== '') {
                    $maybe_id = (int) attachment_url_to_postid($support_url);
                    if ($maybe_id > 0) {
                        $support_id = $maybe_id;
                    }
                }

                $btn_url    = '';
                $btn_title  = '';
                $btn_target = '';
                if (is_array($item_button_link)) {
                    $btn_url    = ! empty($item_button_link['url']) ? $item_button_link['url'] : '';
                    $btn_title  = ! empty($item_button_link['title']) ? $item_button_link['title'] : '';
                    $btn_target = ! empty($item_button_link['target']) ? $item_button_link['target'] : '';
                }
                ?>
                <div class="col-12 col-md-6 content-collection__col">
                    <article class="content-collection__card" role="listitem">
                        <div class="content-collection__media<?php echo $item_enable_overlay ? ' content-collection__media--overlay' : ''; ?>">
                            <?php if ($img_id > 0) : ?>
                                <?php
                                echo wp_get_attachment_image(
                                    $img_id,
                                    'medium_large',
                                    false,
                                    array(
                                        'class'    => 'content-collection__image',
                                        'alt'      => $img_alt,
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                    )
                                );
                                ?>
                            <?php elseif (! empty($img_url)) : ?>
                                <img class="content-collection__image" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy" decoding="async">
                            <?php endif; ?>

                            <?php if (! empty($item_supporting)) : ?>
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
                    <?php if (! empty($item_tag) && ! empty($item_heading)) : ?>
                    <<?php echo tag_escape($item_tag); ?> class="content-collection__card-heading">
                    <?php echo esc_html($item_heading); ?>
                </<?php echo tag_escape($item_tag); ?>>
            <?php elseif (! empty($item_heading)) : ?>
                <div class="content-collection__card-heading">
                    <?php echo esc_html($item_heading); ?>
                </div>
            <?php endif; ?>

                <?php if ($item_enable_supporting_img) : ?>
                    <?php if ($support_id > 0) : ?>
                        <?php
                        echo wp_get_attachment_image(
                            $support_id,
                            'medium',
                            false,
                            array(
                                'class'    => 'content-collection__supporting-image',
                                'alt'      => $support_alt,
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            )
                        );
                        ?>
                    <?php elseif (! empty($support_url)) : ?>
                        <img class="content-collection__supporting-image c" src="<?php echo esc_url($support_url); ?>" alt="<?php echo esc_attr($support_alt); ?>" loading="lazy" decoding="async" >
                    <?php endif; ?>
                <?php endif; ?>

                <?php if (! empty($item_description)) : ?>
                    <div class="content-collection__card-description">
                        <?php echo wp_kses_post($item_description); ?>
                    </div>
                <?php endif; ?>

                <?php if ($item_show_button && ! empty($btn_url) && ! empty($btn_title)) : ?>
                    <a class="btn btn-primary btn-border-bottom-black"
                       href="<?php echo esc_url($btn_url); ?>"
                        <?php echo ! empty($btn_target) ? 'target="' . esc_attr($btn_target) . '"' : ''; ?>
                        <?php echo ! empty($btn_target) ? 'rel="noopener noreferrer"' : ''; ?>
                    >
                        <?php echo esc_html($btn_title); ?>
                    </a>
                <?php endif; ?>
            </div>
            </article>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    </div>
    </div>


</section>