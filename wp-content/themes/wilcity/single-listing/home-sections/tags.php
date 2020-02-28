<?php
global $post, $wilcityArgs;
$aTags = \WilokeListingTools\Framework\Helpers\GetSettings::getPostTerms($post->ID, 'listing_tag');

if ( $aTags ) :
	?>
	<div class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'content-box_module__333d9 wilcity-single-listing-tag-box')); ?>">
		<header class="content-box_header__xPnGx clearfix">
			<div class="wil-float-left">
				<h4 class="content-box_title__1gBHS"><i class="<?php echo esc_attr($wilcityArgs['icon']) ?>"></i><span><?php echo esc_html($wilcityArgs['name']); ?></span></h4>
			</div>
		</header>
		<div class="content-box_body__3tSRB">
			<div class="row">
				<?php
				foreach ($aTags as $oTag) :
					if ( !empty($oTag) && !is_wp_error($oTag) ) :
					?>
					<div class="col-sm-4">
						<div class="icon-box-1_module__uyg5F three-text-ellipsis mt-20 mt-sm-15">
							<div class="icon-box-1_block1__bJ25J">
								<?php echo WilokeHelpers::getTermIcon($oTag, 'icon-box-1_icon__3V5c0 rounded-circle', true, array('type'=>$post->post_type)); ?>
							</div>
						</div>
					</div>
				<?php
                    endif;
                endforeach;
                ?>
			</div>
		</div>
	</div>
<?php endif; ?>