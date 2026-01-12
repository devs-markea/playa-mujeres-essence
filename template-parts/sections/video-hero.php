<?php
// Campos ACF
$video_url = get_sub_field('video_url');
$poster = get_sub_field('poster');
$title = get_sub_field('title');
$description = get_sub_field('description');
$button_text = get_sub_field('button_label');
$button_link = get_sub_field('button_link');
$enable_overlap = get_sub_field('enable_overlap');
$target = get_sub_field('button_target');

// Clase opcional para overlap
$overlap_class = $enable_overlap ? 'video-hero--overlap' : '';

$video = pm_parse_video($video_url);



?>

<section class="video-hero <?php echo esc_attr($overlap_class); ?>">

    <div class="video-hero__media">
        <div class="video-hero__overlay"></div>
        <?php if ($video['type'] === 'youtube'): ?>
            <iframe
                    class="video-hero__video"
                    src="<?php echo esc_url($video['embed_url']); ?>"
                    allow="autoplay; encrypted-media"
                    frameborder="0"
                    playsinline>
            </iframe>
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
