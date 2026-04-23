<h1>Example</h1>

<?php


function getResortsGalleryData()
{

    $hotels = get_posts([
        'post_type' => 'hotel',
        'post_status' => 'publish',
        'order' => 'ASC',
        'numberposts' => 0
    ]);
    
    $lang = (function_exists('pll_current_language')) ? pll_current_language() : '';
    $lang = ( !in_array($lang, ['es','en','']) ? '_'.$lang : '' );

    $categoriesGallery = acf_get_field('category_image_gallery'.$lang );
    
    $hotel_gallery = [
        'hotels' => [],
        'categories' => $categoriesGallery['choices'] ?? [],
        'gallery' => []
    ];

    if (!empty($hotels)):
        foreach ($hotels as $hotel):

            $hotelFilter = 'filter-resort-' . $hotel->ID;
            $hotel_gallery['hotels'][$hotel->ID] = [
                "title" => $hotel->post_title,
                "filterId" => $hotelFilter,
                "isAvailable" => false,
                "gallery" => [],
                "classes" => [$hotelFilter]
            ];

            $available = get_field('available_hotel_filter_gallery', $hotel->ID);
            if (intval($available, 10)):

                $hotel_gallery['hotels'][$hotel->ID]['isAvailable'] = true;
                $galleryGroup = get_field('gallery_option_group', $hotel->ID);
                if ($galleryGroup) {
                    foreach ($galleryGroup as $key => $gg) {

                        if( !empty( $gg['hotel_image_gallery']['id'] ) ){

                            $gallery = [
                                'imageId' => $gg['hotel_image_gallery']['id'],
                                'image' => $gg['hotel_image_gallery']['url'],
                                'categories' =>  ( isset($gg['category_image_gallery'.$lang]) ? $gg['category_image_gallery'.$lang] : $gg['category_image_gallery']),
                                'tags' => $gg['custom_tag_filter'],
                                'resort' => [
                                    "title" => $hotel->post_title,
                                    "classes" => [$hotelFilter]
                                ]
                            ];

                            // $hotel_gallery['hotels'][$hotel->ID]['gallery'][] = $gallery;
                            $hotel_gallery['gallery'][] = $gallery;
                        }
                    }
                    
                    shuffle($hotel_gallery['gallery']);
                }

            endif;
        endforeach;
    endif;

    return $hotel_gallery;
}


function implodeTextForFilter($data, $separeteBy, $usePrefix = true)
{
    return implode($separeteBy, array_map(function ($data) use ($usePrefix) {
        return escFilterClases($data, $usePrefix);
    }, $data));
}


function escFilterClases($value, $usePrefix = true)
{
    return ($usePrefix ? 'filter-' : '') . strtolower(str_replace([' ', '&', "'", '"'], '-', $value));
}


function getTranslationJson( $name ){

    $wpLang = explode( '_', get_option('WPLANG'));
    $default = (isset(  $wpLang[0] ) && !empty($wpLang[0])  ? $wpLang[0] : 'en' );

    // file json route
    $ruta_json = PM_ESSENCE_TEMPLATE_URI . "/languages/{$name}.json";

    // exist file?
    if (file_exists($ruta_json)) {

        $lang = (function_exists('pll_current_language')) ? pll_current_language() : $default;

        // read and decode file JSON
        $json_contenido = file_get_contents($ruta_json);
        $data = json_decode($json_contenido, true);

        // set translation
        $translation =  $data[$lang];
        $translation['default'] =  $data[$default];

        if ($translation) {
            return $translation; 
        } 
    } 

    return []; 

}

function getTranslationText( $data , $key ){
    return (isset( $data[$key] ) ? $data[$key] : ( isset($data['default'][$key]) ? $data['default'][$key] : '' ));
}


function getTranslationLevel( $data , $level = [] ){
    $translation = [];
    if(!empty( $level )){
        $tmpData = [];
        foreach( $level as $lv){
            $searchIn = ( empty( $tmpData ) ? $data : $tmpData );

            $tmpData = ( isset( $searchIn[ $lv ] ) ? $searchIn[ $lv ] : []) ;
            $tmpData['default'] = ( isset( $searchIn['default'][ $lv ] ) ? $searchIn['default'][ $lv ] : []) ;
        }
        $translation = $tmpData;
    }
    return $translation;
}

$hotel_gallery = getResortsGalleryData();
$translation = getTranslationLevel($args['translation'], ['gallery']);


?>

<div class="row  mb-5">

    <div class="col-12 col-lg-3">

        <aside class="sidebar">

            <div class="pm-filter-form">

                <div class="form-wrap">

                    <button class="d-block d-lg-none close-button-filters trigger-filters-action"
                        type="button"></button>

                    <p class="gallery-title-filter">
                        <?= getTranslationText($translation , 'title')?>
                    </p>

                    <input type="text" class="w-100 input-filter-gallery" placeholder=" <?= getTranslationText($translation , 'search')?>">

                    <hr class="gallery-divisor-filter">

                    <div class="mb-5">

                        <div class="checkbox-group resorts-filter">
                            <input type="checkbox" checked="checked" class="chk-filter-gallery" data-hotel="all"
                                id="all-resorts">
                            <label class="checkbox-label button-label mb-0" for="all-resorts">
                                <?= getTranslationText($translation , 'all')?>
                            </label>
                        </div>

                        <?php
                            if (!empty($hotel_gallery['hotels'])):
                                foreach ($hotel_gallery['hotels'] as $hotel):

                                    if (intval($hotel['isAvailable'], 10)):
                                        ?>
                                        
                                        <div class="checkbox-group resorts-filter">
                                            <input type="checkbox" class="chk-filter-gallery" data-hotel="<?= $hotel['filterId'] ?>"
                                                id="hotel-<?= $hotel['title'] ?>">
                                            <label class="checkbox-label button-label mb-0" for="hotel-<?= $hotel['title'] ?>">
                                                <?= $hotel['title'] ?>
                                            </label>
                                        </div>

                                    <?php endif;
                                endforeach;
                            endif;
                        ?>

                    </div>
                </div>

                <div class="fixed-filter-actions d-flex d-lg-none">
                    <ul class="d-flex justify-content-center flex-grow-1 no-list mb-0">
                        <li>
                            <button class="btn btn-display-filters" type="button">
                                <?php _e('Display results', 'mqp'); ?> <span class="filtered-t fw-bold"></span>
                            </button>
                        </li>
                    </ul>
                </div>

            </div>
        </aside>

    </div>

    <div class="col-12 col-lg-9">

        <div class="d-block d-lg-none sticky-top filters-sticky text-center mb-5">
            <a class="btn btn-display-filters w-100" href="#">
                <?= getTranslationText($translation , 'title')?> <i class="fa fa-filter ms-2"></i>
            </a>
        </div>

        <div class="amenities-filter">

            <section class="panel">

                <div class="filter">
                    <button class="active btn-filter-gallery" data-category="all"> <?= getTranslationText($translation , 'all')?></button>
                    <?php
                    
                    if (!empty($hotel_gallery["categories"])):
                        foreach ($hotel_gallery["categories"] as $category):
                            ?>

                            <button class="btn-filter-gallery" data-category="<?= escFilterClases($category) ?>">
                                <?= $category ?>
                            </button>

                        <?php endforeach;
                    endif; ?>
                </div>

            </section>

        </div>

        <div class="gallery-grid-resorts mt-2">
        <?php

            if (!empty($hotel_gallery['gallery'])) {
                foreach ($hotel_gallery['gallery'] as $key => $gallery) {
                
                    ?>

                    <a href="<?= $gallery['image'] ?>"
                        class="custom-gallery-image <?= implodeTextForFilter($gallery["categories"], " ") ?>  <?= implode(" ", $gallery['resort']['classes']) ?>"
                        data-filter="<?= escFilterClases($gallery['resort']['title'], false) ?>,<?= implodeTextForFilter($gallery["categories"], ",", false) ?>,<?= implodeTextForFilter($gallery["tags"], ",", false) ?>"
                        data-lightbox="resorts-gallery" data-title="<?= $gallery['resort']['title'].( !empty($gallery["categories"]) ? ' - '.implode( ', ', $gallery["categories"] ) : '' ) ?>">

                        <?= wp_get_attachment_image($gallery['imageId'], 'medium', false, ['loading' => 'lazy'] ); ?>

                    </a>

                <?php }
            }
        ?>

        </div>
        
        <div class="fw-bold gallery-not-found d-none p-4">

            <p class="mb-2 title" style="">
                <?= getTranslationText($translation , 'notFoundTitle')?>
            </p>
            <p class="mb-2 fw-normal" >
                <?= getTranslationText($translation , 'notFoundTryAgain')?>
            </p>
            
            <p class="m-0 as-link fw-semibold reset-filters" >
                <?= getTranslationText($translation , 'notFoundReset')?>
            </p>
            
        </div>

    </div>

</div>
