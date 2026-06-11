<?php
// header.php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    <!-- Google Tag Manager — carga diferida (primera interacción o 4 s) para
         sacar ~590 KiB de JS del camino crítico. Los eventos previos quedan
         encolados en dataLayer y se procesan cuando gtm.js carga. -->
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('config', 'G-MGJ5GBGZTV', {'cookie_domain': 'auto', 'cookie_flags': 'SameSite=None;Secure'});
    (function(w,d){
        var loaded = false, timer;
        var events = ['pointerdown','keydown','touchstart','wheel'];
        function loadGTM(){
            if (loaded) return;
            loaded = true;
            clearTimeout(timer);
            events.forEach(function(e){ d.removeEventListener(e, loadGTM); });
            w.dataLayer.push({'gtm.start': new Date().getTime(), event: 'gtm.js'});
            var j = d.createElement('script');
            j.async = true;
            j.src = 'https://www.googletagmanager.com/gtm.js?id=GTM-M333M5N';
            d.head.appendChild(j);
        }
        events.forEach(function(e){ d.addEventListener(e, loadGTM, {passive: true}); });
        timer = setTimeout(loadGTM, 4000);
    })(window, document);
    </script>
    <!-- End Google Tag Manager -->

    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-M333M5N"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<?php get_template_part( 'template-parts/header/header-default'); ?>
