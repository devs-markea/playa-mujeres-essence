<?php
?>
<section data-anim="slide-up delay-2" class="content-collection content-collection--list<?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">
    <div class="container">

        <div class="row g-0">
            <div class="col-12 col-lg-10 mx-auto">

                <div class="content-collection__inner-heading">
                    <div class="row">
                        <div class="col-12 col-md-6 mx-auto">
                            <?php if ( ! empty( $heading_tag ) && ! empty( $heading ) ) : ?>
                            <<?php echo tag_escape( $heading_tag ); ?> class="content-collection__heading--list">
                                <?php echo esc_html( $heading ); ?>
                            </<?php echo tag_escape( $heading_tag ); ?>>
                            <?php elseif ( ! empty( $heading ) ) : ?>
                            <div class="content-collection__heading--list content-collection__heading--text-only">
                                <?php echo esc_html( $heading ); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php if ( ! empty( $description ) ) : ?>
                    <div class="content-collection__description content-collection__description--list">
                        <?php echo wp_kses_post( $description ); ?>
                    </div>
                <?php endif; ?>
                </div>

                <?php if ( ! empty( $items ) && is_array( $items ) ) : ?>
                <div class="row g-4 content-collection__grid content-collection__grid--list" role="list">
                    <?php foreach ( $items as $item ) :
                        $item_heading               = $item['heading'] ?? '';
                        $item_heading_level         = $item['heading_level'] ?? '';
                        $item_description           = $item['description'] ?? '';
                        $item_show_button           = ! empty( $item['show_button'] );
                        $item_button_link           = $item['button_link'] ?? null;
                        $item_image                 = $item['image'] ?? null;
                        $item_enable_overlay        = ! empty( $item['enable_overlay'] );
                        $item_supporting            = $item['supporting'] ?? '';
                        $item_supporting_text_level = $item['text_level'] ?? '';
                        $item_enable_supporting_img = ! empty( $item['enable_supporting_image'] );
                        $item_supporting_image      = $item['supporting_image'] ?? null;

                        $item_tag       = pm_essence_heading_tag_or_null( $item_heading_level, 'h3' );
                        $supporting_tag = pm_essence_heading_tag_or_null( $item_supporting_text_level, '' );

                        list( $img_id, $img_url, $img_alt )             = pm_normalize_acf_image( $item_image );
                        list( $support_id, $support_url, $support_alt ) = pm_normalize_acf_image( $item_supporting_image );

                        $btn_url    = '';
                        $btn_title  = '';
                        $btn_target = '';
                        if ( is_array( $item_button_link ) ) {
                            $btn_url    = $item_button_link['url'] ?? '';
                            $btn_title  = $item_button_link['title'] ?? '';
                            $btn_target = $item_button_link['target'] ?? '';
                        }
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 content-collection__col--list">
                        <article class="content-collection__card content-collection__card--list" role="listitem">

                            <div class="content-collection__media content-collection__media--list<?php echo $item_enable_overlay ? ' content-collection__media--overlay' : ''; ?>">
                                <?php if ( $img_id > 0 ) : ?>
                                    <?php echo wp_get_attachment_image( $img_id, 'medium_large', false, [
                                        'class'    => 'content-collection__image content-collection__image--list',
                                        'alt'      => $img_alt,
                                        'loading'  => 'lazy',
                                        'decoding' => 'async',
                                    ] ); ?>
                                <?php elseif ( ! empty( $img_url ) ) : ?>
                                    <img class="content-collection__image content-collection__image--list" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" loading="lazy" decoding="async">
                                <?php endif; ?>

                                <?php if ( ! empty( $item_supporting ) ) : ?>
                                    <?php if ( ! empty( $supporting_tag ) ) : ?>
                                    <<?php echo tag_escape( $supporting_tag ); ?> class="content-collection__supporting content-collection__supporting--list content-collection__supporting--absolute">
                                        <?php echo esc_html( $item_supporting ); ?>
                                    </<?php echo tag_escape( $supporting_tag ); ?>>
                                    <?php else : ?>
                                    <div class="content-collection__supporting content-collection__supporting--list content-collection__supporting--absolute content-collection__supporting--text-only">
                                        <?php echo esc_html( $item_supporting ); ?>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>

                            <div class="content-collection__body--list">
                                <?php if ( ! empty( $item_tag ) && ! empty( $item_heading ) ) : ?>
                                <<?php echo tag_escape( $item_tag ); ?> class="content-collection__card-heading--list">
                                    <?php echo esc_html( $item_heading ); ?>
                                </<?php echo tag_escape( $item_tag ); ?>>
                                <?php elseif ( ! empty( $item_heading ) ) : ?>
                                <div class="content-collection__card-heading--list">
                                    <?php echo esc_html( $item_heading ); ?>
                                </div>
                                <?php endif; ?>

                                <?php if ( $item_enable_supporting_img ) : ?>
                                    <?php if ( $support_id > 0 ) : ?>
                                        <?php echo wp_get_attachment_image( $support_id, 'medium', false, [
                                            'class'    => 'content-collection__supporting-image content-collection__supporting-image--list',
                                            'alt'      => $support_alt,
                                            'loading'  => 'lazy',
                                            'decoding' => 'async',
                                        ] ); ?>
                                    <?php elseif ( ! empty( $support_url ) ) : ?>
                                        <img class="content-collection__supporting-image content-collection__supporting-image--list" src="<?php echo esc_url( $support_url ); ?>" alt="<?php echo esc_attr( $support_alt ); ?>" loading="lazy" decoding="async">
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if ( ! empty( $item_description ) ) : ?>
                                <div class="content-collection__card-description--list">
                                    <?php echo wp_kses_post( $item_description ); ?>
                                </div>
                                <?php endif; ?>

                                <?php if ( $item_show_button && ! empty( $btn_url ) && ! empty( $btn_title ) ) : ?>
                                <a class="btn btn-primary btn-border-bottom-black"
                                   href="<?php echo esc_url( $btn_url ); ?>"
                                   <?php echo ! empty( $btn_target ) ? 'target="' . esc_attr( $btn_target ) . '"' : ''; ?>
                                   <?php echo ! empty( $btn_target ) ? 'rel="noopener noreferrer"' : ''; ?>>
                                    <?php echo esc_html( $btn_title ); ?>
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
    </div>
</section>
