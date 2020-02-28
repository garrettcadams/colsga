<?php
use WilokeListingTools\Framework\Helpers\GetWilokeSubmission;
use WilokeListingTools\Framework\Helpers\GetSettings;

function wilcityRenderRestaurantListing($post, $aAtts){
	$aPriceRange = GetSettings::getPriceRange($post->ID);
	$symbol = GetWilokeSubmission::getSymbol($aPriceRange['currency']);
	$symbol = apply_filters('wilcity/price-range/currencySymbol', $symbol);

	?>
	<div id="<?php echo esc_attr('listing-id-'.$post->ID); ?>" class="<?php echo esc_attr($aAtts['item_wrapper']); ?>">
		<a href="<?php echo esc_url(get_permalink($post->ID)) ?>">
			<div class="utility-box-1_module__MYXpX utility-box-1_menus__17rbu">
				<?php \WILCITY_SC\SCHelpers::renderLazyLoad(get_the_post_thumbnail_url($post->ID), array(
					'divClass' => 'utility-box-1_avatar__DB9c_ rounded-circle',
					'imgClass' => '',
					'alt' => $post->post_title
				)); ?>
				<div class="utility-box-1_body__8qd9j">
					<div class="utility-box-1_group__2ZPA2">
						<h3 class="utility-box-1_title__1I925"><?php echo esc_html($post->post_title); ?></h3>
						<div class="utility-box-1_content__3jEL7"><?php Wiloke::contentLimit($aAtts['excerpt_length'], $post, false, $post->post_content,  false); ?></div>
					</div>
					<?php if ( !empty($aPriceRange['minimumPrice']) && !empty($aPriceRange['maximumPrice']) ){ ?>
						<div class="utility-box-1_description__2VDJ6"><?php echo GetWilokeSubmission::renderPrice($aPriceRange['minimumPrice'], '', false, $symbol) . ' - ' . GetWilokeSubmission::renderPrice($aPriceRange['maximumPrice'], '', false, $symbol); ?></div>
					<?php }else{
						$price = GetSettings::getPostMeta( $post->ID, 'single_price' );
						if ( ! empty( $price ) ):
							?>
							<div class="utility-box-1_description__2VDJ6"><?php echo GetWilokeSubmission::renderPrice( $price, '', false, $symbol ); ?></div>
						<?php endif;
					}; ?>
				</div>
			</div>
		</a>
	</div>
	<?php
}
