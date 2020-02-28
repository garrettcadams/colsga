<?php
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Framework\Helpers\Message;
use WilokeListingTools\Frontend\SingleListing;
global $post;

$aRenderMachine = wilokeListingToolsRepository()->get('listing-settings:sidebar_settings', true)->sub('renderMachine');

$aSidebarSettings = SingleListing::getSidebarOrder();

if ( empty($aSidebarSettings) ){
	return '';
}

do_action('wilcity/single-listing/sidebar-top', $post);

foreach ($aSidebarSettings as $aSidebarSetting){
	if ( !isset($aSidebarSetting['key']) || ( isset($aSidebarSetting['status']) && $aSidebarSetting['status'] == 'no') ){
		continue;
	}

	if ( $aSidebarSetting['key'] == 'google_adsense' ){
        $content = do_shortcode("[wilcity_google_adsense]");
        if ( !empty($content) ):
	        ?>
            <div class="content-box_module__333d9">
                <div class="content-box_body__3tSRB">
			        <?php echo $content; ?>
                </div>
            </div>
        <?php
        endif;
    }else{

		if ( !isset($aRenderMachine[$aSidebarSetting['key']]) ){
		    if ( $aSidebarSetting['key'] == 'promotion' ){
			    do_action('wilcity/single-listing/sidebar-promotion', $post, $aSidebarSetting);
            }else{
			    $scKey = str_replace('wilcity_single_sidebar_', '', $aSidebarSetting['key']);
			    if ( is_array($aSidebarSetting) ){
				    $aSidebarSetting = \WilokeListingTools\Framework\Helpers\General::unSlashDeep($aSidebarSetting);
			    }

			    $buildSC = SingleListing::parseCustomFieldSC($aSidebarSetting['content']);
			    $content = do_shortcode(stripslashes($buildSC));
			    if ( !empty($content) ) :
				    ?>
                    <div class="content-box_module__333d9 <?php echo esc_attr('wilcity-sidebar-item-'.$aSidebarSetting['key']); ?>">
					    <?php wilcityRenderSidebarHeader($aSidebarSetting['name'], $aSidebarSetting['icon']); ?>
                        <div class="content-box_body__3tSRB">
						    <?php echo $content; ?>
                        </div>
                    </div>
				    <?php
			    endif;
            }
		}else{
			echo do_shortcode("[".$aRenderMachine[$aSidebarSetting['key']]. " atts='".stripslashes(utf8_encode(json_encode($aSidebarSetting, JSON_UNESCAPED_UNICODE)))."'/]");
		}
    }
}

do_action('wilcity/single-listing/sidebar-bottom', $post);