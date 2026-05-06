<?php
$padding_top           = get_sub_field( 'padding_top' );
$padding_bottom        = get_sub_field( 'padding_bottom' );
$padding_top_mobile    = get_sub_field( 'padding_top_mobile' );
$padding_bottom_mobile = get_sub_field( 'padding_bottom_mobile' );
$has_spacing           = ( $padding_top !== '' || $padding_bottom !== '' || $padding_top_mobile !== '' || $padding_bottom_mobile !== '' );
$section_uid           = $has_spacing ? 'pm-' . get_the_ID() . '-' . get_row_index() : '';
pm_essence_render_section_spacing( $section_uid, $padding_top, $padding_bottom, $padding_top_mobile, $padding_bottom_mobile );

// Campos ACF
$video_url = get_sub_field('video_url');
$poster = get_sub_field('poster_image');
$title = get_sub_field('title');
$description = get_sub_field('description');
$button_text = get_sub_field('button_label');
$button_link = get_sub_field('button_link');
$enable_overlap = get_sub_field('enable_overlap');
$target = get_sub_field('button_target');
$decorative_image = get_sub_field('image_decorative');

// Clase opcional para overlap
$overlap_class = $enable_overlap ? 'video-hero--overlap' : '';

$video = pm_parse_video($video_url);



?>

<section class="video-hero <?php echo esc_attr($overlap_class); ?><?php echo $section_uid ? ' ' . esc_attr( $section_uid ) : ''; ?>">

    <div class="video-hero__media">
        <div class="video-hero__overlay"></div>
        <div class="video-hero__iframe-blocker" aria-hidden="true"></div>
        <?php if ($video['type'] === 'youtube'):
            wp_enqueue_script('pm-youtube-background');

            $poster_attrs = [
                'class'         => 'video-hero__poster',
                'alt'           => '',
                'aria-hidden'   => 'true',
                'fetchpriority' => 'high',
                'loading'       => 'eager',
                'decoding'      => 'sync',
                'sizes'         => '100vw'
            ];

            if ( $poster && ! empty( $poster['ID'] ) ) {
                // ACF image array → usa tamaño 2048 como base (no el original full)
                $poster_img = wp_get_attachment_image( $poster['ID'], '2048x2048', false, $poster_attrs );
            } else {
                // Fallback: thumbnail de YouTube
                $vid_id     = $video['id'];
                $poster_url = esc_url( pm_get_cached_yt_thumbnail( $vid_id, 'max' ) );
                $srcset     = esc_attr(
                    pm_get_cached_yt_thumbnail( $vid_id, 'hq' )  . ' 480w, ' .
                    pm_get_cached_yt_thumbnail( $vid_id, 'sd' )  . ' 640w, ' .
                    pm_get_cached_yt_thumbnail( $vid_id, 'max' ) . ' 1280w'
                );
                $poster_img = $poster_url
                    ? '<img class="video-hero__poster skip-lazy" src="' . $poster_url . '" srcset="' . $srcset . '" sizes="100vw" alt="" aria-hidden="true" fetchpriority="high" loading="eager" data-no-lazy="1" decoding="sync">'
                    : '';
            }
        ?>
            <div
                class="video-hero__video"
                data-vbg="<?php echo esc_url($video['original_url']); ?>"
                data-vbg-autoplay="true"
                data-vbg-muted="true"
                data-vbg-controls="false"
                data-vbg-loop="true"
                data-vbg-no-cookie="true"
                aria-hidden="true"
            ></div>
            <?php echo $poster_img; ?>
        <?php endif; ?>
        <?php if ($video['type'] === 'mp4'): ?>
            <video
                    class="video-hero__video"
                    autoplay
                    muted
                    loop
                    playsinline
                    <?php if ($poster): ?>poster="<?php echo esc_url($poster['url']); ?>"<?php endif; ?>>

                <source src="<?php echo esc_url($video['embed_url']); ?>" type="video/mp4">
            </video>
        <?php endif; ?>

    </div>

    <div class="video-hero__content container">
        <div class="video-hero__inner">
            <div class="divider"></div>
            <div class="video-hero__information">
                <?php if ($title): ?>
                    <h1 class="video-hero__title"><?php echo esc_html($title); ?></h1>
                <?php endif; ?>

                <?php if ($description): ?>
                    <p class="video-hero__subtitle"><?php echo esc_html($description); ?></p>
                <?php endif; ?>

                <?php if ($button_text && $button_link): ?>

                    <div class="arrow-circle">
                        <a href="<?php echo esc_url($button_link); ?>" class="video-hero__button arrow-circle__link" <?php if ($target): ?> target="<?php echo esc_attr($target); ?>" <?php endif; ?>>
                        <span class="arrow-circle__label">
                            <?php echo esc_html($button_text); ?>
                        </span>
                            <span class="arrow-circle__icon">
                            <span class="arrow">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M17.25 15.75L21 12M21 12L17.25 8.25M21 12H3"
                                          stroke="white" stroke-width="0.75"
                                          stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                            <span class="circle">
                                <svg width="28" height="28" viewBox="0 0 28 28" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <rect x="0.375" y="0.375"
                                          width="27.25" height="27.25"
                                          rx="13.625"
                                          stroke="white" stroke-width="0.75"/>
                                </svg>
                            </span>
                        </span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="play-button">
            <button class="play-button__inner" id="play-button-hero">
                <span>Discover Playa Mujeres</span>
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="0.5" y="0.5" width="27" height="27" rx="13.5" stroke="white"/>
                    <path d="M11 19.1963V8.80371L20.001 14L11 19.1963Z" stroke="white"/>
                </svg>
            </button>
        </div>
    </div>



</section>

<?php if ( $decorative_image && ! empty( $decorative_image['ID'] ) ) : ?>
    <div class="video-hero__decorative-image-wrapper" aria-hidden="true">
        <?php
        $img_id   = $decorative_image['ID'];
        $img_src  = wp_get_attachment_image_src( $img_id, 'full' );
        $metadata = wp_get_attachment_metadata( $img_id );
        $webp_src = '';

        // WP 5.8+ stores generated WebP sources in attachment metadata
        if ( ! empty( $metadata['sources']['image/webp']['file'] ) && ! empty( $metadata['file'] ) ) {
            $upload_dir = wp_upload_dir();
            $webp_src   = trailingslashit( $upload_dir['baseurl'] )
                          . trailingslashit( dirname( $metadata['file'] ) )
                          . $metadata['sources']['image/webp']['file'];
        }

        if ( $webp_src && $img_src ) :
        ?>
            <picture>
                <source type="image/webp" srcset="<?php echo esc_url( $webp_src ); ?>">
                <img src="<?php echo esc_url( $img_src[0] ); ?>"
                     width="<?php echo esc_attr( $img_src[1] ); ?>"
                     height="<?php echo esc_attr( $img_src[2] ); ?>"
                     class="video-hero__decorative-image"
                     alt="" role="presentation" aria-hidden="true" loading="lazy" decoding="async">
            </picture>
        <?php else : ?>
            <?php echo wp_get_attachment_image( $img_id, 'full', false, [
                'class'       => 'video-hero__decorative-image',
                'alt'         => '',
                'role'        => 'presentation',
                'aria-hidden' => 'true',
                'loading'     => 'lazy',
                'decoding'    => 'async',
            ] ); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

