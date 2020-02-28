<?php
function wilcityRenderRestaurantListMenu($aAtts, $postID = null, $isReturnJson=false){
	$aAtts = shortcode_atts(
		array(
            'wrapper_class'     => '',
			'group_title'       => '',
			'group_description' => '',
			'group_icon'        => '',
			'items'             => ''
		),
		$aAtts
	);

	if ( empty($postID) ){
	    global $post;
		$postID = $post->ID;
    }

	if ( empty($aAtts['items']) ){
	    return '';
    }

    if ( !$isReturnJson ) {
	?>
        <div class="<?php echo esc_attr(trim(apply_filters('wilcity/filter/class-prefix', $aAtts['wrapper_class']))); ?>">
            <?php if ( !empty($aAtts['group_title']) || !empty($aAtts['group_description']) ) : ?>
            <div class="mb-20 pt-10 wil-text-center">
                <?php if ( !empty($aAtts['group_title']) ) : ?>
                <h5 class="mt-0 mb-0"><?php Wiloke::ksesHTML($aAtts['group_title']); ?></h5>
                <?php endif; ?>

                <?php if ( !empty($aAtts['group_description']) ) : ?>
                <p class="fs-13"><?php Wiloke::ksesHTML($aAtts['group_description']); ?></p>
                <?php endif; ?>

                <?php if ( !empty($aAtts['group_icon']) ) : ?>
                <i class="<?php Wiloke::ksesHTML($aAtts['group_icon']); ?> fs-32 color-primary"></i>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <ul class="list-none mb-20">
                <?php
                foreach ($aAtts['items'] as $aItem){
                    wilcityRenderRestaurantMenuItem($aItem, $postID);
                }
                ?>
            </ul>
        </div>
	<?php
    }else{
	    $aItems = array();
	    foreach ($aAtts['items'] as $aItem){
		    $aItems[] = wilcityRenderRestaurantMenuItem($aItem, $postID, true);
	    }

	    unset($aAtts['items']);
	    return array(
	        'oAtts'     => $aAtts,
            'aItems'    => $aItems
        );
    }
}

add_shortcode('wilcity_render_restaurant_list_menu', 'wilcityRenderRestaurantListMenu');
