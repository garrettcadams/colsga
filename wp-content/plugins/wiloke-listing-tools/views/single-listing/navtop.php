<?php
global $post;
$aTabs = \WilokeListingTools\Framework\Helpers\GetSettings::getPostMeta($post->ID, wilokeListingToolsRepository()->get('listing-settings:keys', true)->sub('navigation'));
$aConfigurations = wilokeListingToolsRepository()->get('listing-settings:navigation', true)->sub('draggable');
$aTabs = !empty($aTabs) ? $aTabs : $aConfigurations;
?>
<div class="detail-navtop_module__zo_OS js-detail-navtop">
    <div class="container">
        <nav class="detail-navtop_nav__1j1Ti">

            <!-- list_module__1eis9 list-none -->
            <ul class="list_module__1eis9 list-none list_horizontal__7fIr5">

                <li class="list_item__3YghP active">
                    <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="#home">
                        <span class="list_icon__2YpTp"><i class="la la-home"></i></span>
                        <span class="list_text__35R07"><?php esc_html_e('Home', 'wiloke-listing-tools'); ?></span>
                    </a>
                </li>

				<?php
				foreach ($aTabs as $aTab) :
					if ( !$aTab['status'] ){
						continue;
					}
					?>

                    <li class="list_item__3YghP">
                        <a class="list_link__2rDA1 text-ellipsis color-primary--hover" @click="switchTab" href="#" data-tab="<?php echo esc_attr($aTab['key']); ?>">
                            <span class="list_icon__2YpTp"><i class="<?php echo esc_html($aConfigurations[$aTab['key']]['icon']); ?>"></i></span>
                            <span class="list_text__35R07"><?php echo esc_html($aConfigurations[$aTab['key']]['name']); ?></span>
                        </a>
                    </li>
				<?php endforeach; ?>

                <li class="list_item__3YghP">
                    <a class="list_link__2rDA1 text-ellipsis color-primary--hover" href="<?php echo esc_url(
						add_query_arg(
							array(
								'mode'=>'settings'
							),
							get_permalink($post->ID)
						)
					); ?>">
                        <span class="list_icon__2YpTp"><i class="la la-cog"></i></span>
                        <span class="list_text__35R07"><?php esc_html_e('Settings', 'wiloke-listing-tools'); ?></span>
                    </a>
                </li>

            </ul><!-- End /  list_module__1eis9 list-none -->

        </nav>
        <div class="detail-navtop_right__KPAlw">
            <a class="wil-btn wil-btn--primary wil-btn--round wil-btn--md wil-btn--block " href="#"><i class="la la-edit"></i> Book Now
            </a>
        </div>
    </div>
</div><!-- End / detail-navtop_module__zo_OS -->
