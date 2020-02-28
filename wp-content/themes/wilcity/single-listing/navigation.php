<?php
use WilokeListingTools\Frontend\SingleListing;
use WilokeListingTools\Framework\Helpers\GetSettings;
use WilokeListingTools\Frontend\User as WilokeUser;
use \WilokeListingTools\Framework\Helpers\General;

global $post;
$aTabs = SingleListing::getNavOrder();
$aTabs = General::unSlashDeep($aTabs);
$aTabs = apply_filters('wilcity/single-listing/tabs', $aTabs, $post);
$url = get_permalink($post->ID);
?>
<div class="detail-navtop_module__zo_OS js-detail-navtop">
	<div class="container">

        <?php if ( $buttonLink = GetSettings::getPostMeta($post->ID, 'button_link') ) : ?>
        <div class="detail-navtop_right__KPAlw">
            <a class="wil-btn wil-btn--primary2 wil-btn--round wil-btn--md wil-btn--block" rel="nofollow" target="_blank" href="<?php echo esc_url($buttonLink); ?>"><i class="<?php echo esc_attr(GetSettings::getPostMeta($post->ID, 'button_icon')); ?>"></i> <?php echo esc_html(GetSettings::getPostMeta($post->ID, 'button_name')); ?>
            </a>
        </div>
        <?php endif; ?>
        
		<nav class="detail-navtop_nav__1j1Ti">
			<ul class="list_module__1eis9 list-none list_horizontal__7fIr5" style="min-height: 70px;">
				<li :class="navLiClass('home')">
                    <switch-tab-btn tab-key="home" tab-title="<?php echo esc_attr(SingleListing::renderTabTitle(__('Home', 'wilcity'))); ?>" wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover" page-url="<?php echo esc_url($url); ?>">
                        <template slot="insideTab">
                            <span class="list_icon__2YpTp"><i class="la la-home"></i></span>
                            <span class="list_text__35R07"><?php esc_html_e('Home', 'wilcity'); ?></span>
                        </template>
                    </switch-tab-btn>
				</li>
				<?php
				foreach ($aTabs as $aTab) :
					if ( $aTab['key'] == 'coupon' || !isset($aTab['status']) || $aTab['status'] == 'no' ){
						continue;
					}
					?>
                    <li :class="navLiClass('<?php echo esc_attr($aTab['key']); ?>')">
                        <switch-tab-btn tab-key="<?php echo esc_attr($aTab['key']); ?>" tab-title="<?php echo esc_attr(SingleListing::renderTabTitle(__(ucfirst(str_replace('_', ' ', $aTab['key'])), 'wilcity'))); ?>" page-url="<?php echo esc_url($url); ?>">
                            <template slot="insideTab">
                                <span class="list_icon__2YpTp"><i class="<?php echo esc_html($aTab['icon']); ?>"></i></span>
                                <span class="list_text__35R07"><?php echo esc_html($aTab['name']); ?></span>
                            </template>
                        </switch-tab-btn>
					</li>
				<?php endforeach; ?>

                <?php if ( WilokeUser::isPostAuthor($post, true) ) : ?>
                <li class="list_item__3YghP listing-settings-tab" :class="navLiClass('listing-settings')">
                    <switch-tab-btn wrapper-class="list_link__2rDA1 text-ellipsis color-primary--hover" tab-key="listing-settings" tab-title="<?php echo esc_attr(SingleListing::renderTabTitle(__('Listing Settings', 'wilcity'))); ?>" page-url="<?php echo esc_url($url); ?>">
                        <template slot="insideTab">
                            <span class="list_icon__2YpTp"><i class="la la-cog"></i></span>
                            <span class="list_text__35R07"><?php esc_html_e('Settings', 'wilcity'); ?></span>
                        </template>
                    </switch-tab-btn>
				</li>
                <?php endif; ?>
			</ul>
		</nav>
	</div>
</div>
