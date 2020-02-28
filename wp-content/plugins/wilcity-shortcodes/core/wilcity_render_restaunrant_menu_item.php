<?php
use \WilokeListingTools\Framework\Helpers\General;
use \WilokeListingTools\Frontend\Gallery;

function wilcityRenderRestaurantMenuItem($aAtts, $postID, $isReturnJson = false)
{
    $aAtts = shortcode_atts(
        array(
            'gallery'            => '',
            'title'              => '',
            'description'        => '',
            'price'              => '',
            'link_to'            => '',
            'is_open_new_window' => '',
            'post_id'            => ''
        ),
        $aAtts
    );

    $target = '_self';
    if ( !empty($aAtts['is_open_new_window'])) {
        $target = '_blank';
    }

    if ( !empty($aAtts['gallery']) && !is_array($aAtts['gallery'])) {
        $aGallery = json_decode($aAtts['gallery'], true);
    } else {
        $aGallery = $aAtts['gallery'];
    }

    if ( !empty($aGallery)) {
        $belongsTo = \WilokeListingTools\Framework\Helpers\GetSettings::getListingBelongsToPlan($postID);
        if ( !empty($belongsTo)) {
            $aPlanSettings = \WilokeListingTools\Framework\Helpers\GetSettings::getPlanSettings($belongsTo);
            if (isset($aPlanSettings['maximum_restaurant_gallery_images']) && !empty($aPlanSettings['maximum_restaurant_gallery_images'])) {
                $aGallery = array_slice($aGallery, 0, $aPlanSettings['maximum_restaurant_gallery_images'], true);
            }
        }

        $aGalleryKeys = array_keys($aGallery);
        $aThumbnails  = array_map(function ($galleryID){
            return wp_get_attachment_image_url($galleryID, 'thumbnail');
        }, $aGalleryKeys);
    }

    if ( !$isReturnJson):
        ?>
        <li>
            <div class="utility-box-1_module__MYXpX utility-box-1_menus__17rbu">
                <?php if ( !empty($aGallery)) : ?>
                    <restaurant-gallery raw-thumbnails='<?php echo json_encode($aThumbnails); ?>'
                                        raw-images='<?php echo json_encode(array_values($aGallery)); ?>'></restaurant-gallery>
                <?php endif; ?>
                <?php if ( !empty($aAtts['link_to'])) : ?>
                    <a href="<?php echo esc_url($aAtts['link_to']); ?>" target="<?php echo esc_attr($target); ?>"
                   rel="<?php echo esc_attr(General::renderRel($aAtts['link_to'])); ?>">
                <?php endif; ?>
                        <div class="utility-box-1_body__8qd9j">
                            <div class="utility-box-1_group__2ZPA2">
                                <?php if ( !empty($aAtts['title'])) : ?>
                                    <h3 class="utility-box-1_title__1I925"><?php Wiloke::ksesHTML($aAtts['title']); ?></h3>
                                <?php endif; ?>
                                <?php if ( !empty($aAtts['description'])) : ?>
                                    <div class="utility-box-1_content__3jEL7"><?php Wiloke::ksesHTML($aAtts['description']); ?></div>
                                <?php endif; ?>
                            </div>
                            <?php if ( !empty($aAtts['price'])) : ?>
                                <div class="utility-box-1_description__2VDJ6"><?php Wiloke::ksesHTML($aAtts['price']); ?></div>
                            <?php endif; ?>
                        </div>
                <?php if ( !empty($aAtts['link_to'])) : ?>
                    </a>
                <?php endif; ?>
            </div>
        </li>
    <?php else:
        unset($aAtts['gallery']);

        return array(
            'aGallery'    => json_encode(array_values($aGallery)),
            'aThumbnails' => json_encode($aThumbnails),
            'oAtts'       => $aAtts
        );
    endif;
}

add_shortcode('wilcity_render_restaurant_menu_item', 'wilcityRenderRestaurantMenuItem');
