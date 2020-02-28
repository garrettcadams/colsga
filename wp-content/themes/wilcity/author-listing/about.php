<?php
use WilokeListingTools\Frontend\User;
$authorID = get_query_var('author');
$about = Wiloke::ksesHTML(nl2br(User::getField('description', $authorID)), true); ?>
<div class="row equal-height" data-col-xs-gap="10">
	<div class="col-md-8 col-lg-8 col-xs-12 col-sm-12">
		<div class="content-box_module__333d9">
			<header class="content-box_header__xPnGx clearfix">
				<div class="wil-float-left">
					<h4 class="content-box_title__1gBHS"><i class="la la-user"></i><span><?php esc_html_e('Profile', 'wilcity'); ?></span></h4>
				</div>
			</header>
			<div class="content-box_body__3tSRB">
                <div>
                <?php if ( !empty($about) ) : ?>
				    <?php Wiloke::ksesHTML(nl2br(User::getField('description', $authorID))); ?>
                <?php else: ?>
                    <?php echo sprintf(__('Thank for joining %s. Please go to Dashboard -> Profile -> Description and write something about yourself.', 'wilcity'), get_option('bloginfo')); ?>
                <?php endif; ?>
                </div>
			</div>
		</div>
	</div>

	<div class="col-md-4 col-lg-4 col-xs-12 col-sm-12">
		<div class="content-box_module__333d9">
			<header class="content-box_header__xPnGx clearfix">
				<div class="wil-float-left">
					<h4 class="content-box_title__1gBHS"><i class="la la-map-signs"></i><span><?php esc_html_e('Intro', 'wilcity'); ?></span></h4>
				</div>
			</header>
			<div class="content-box_body__3tSRB">

				<div class="author-listing_module__3K7-I">
					<?php do_action('wilcity/author-listing/top/about', $authorID); ?>

					<?php
					$aSocialNetworks = User::getSocialNetworks($authorID);
					if ( !empty($aSocialNetworks) ) :
					?>
					<div class="wilcity-remove-if-no-child social-icon_module__HOrwr social-icon_style-2__17BFy">
						<?php
                        foreach ($aSocialNetworks as $icon => $url) :
                            if ( empty($url) ){
                                continue;
                            }
                        ?>
						    <a class="social-icon_item__3SLnb" href="<?php echo esc_url($url); ?>" target="_blank"><i class="fa fa-<?php echo esc_attr($icon); ?>"></i></a>
						<?php endforeach; ?>
					</div><!-- End /  social-icon_module__HOrwr -->
					<?php endif; ?>

					<div class="wil-divider mt-20 mt-sm-15 mb-15"></div>

					<?php
                    $address = User::getAddress($authorID);
                    if ( !empty($address) ) :
                    ?>
					<div class="icon-box-1_module__uyg5F one-text-ellipsis mt-20 mt-sm-15 text-pre">
						<div class="icon-box-1_block1__bJ25J">
							<a href="<?php echo esc_url('https://www.google.com/maps/search/'.$address); ?>">
								<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-map-marker"></i></div>
								<div class="icon-box-1_text__3R39g"><?php Wiloke::ksesHTML($address); ?></div>
							</a>
						</div>
					</div>
                    <?php endif; ?>

					<?php
					$phone = User::getPhone($authorID);
					if ( !empty($phone) ) :
					?>
					<div class="icon-box-1_module__uyg5F one-text-ellipsis mt-20 mt-sm-15 text-pre">
						<div class="icon-box-1_block1__bJ25J">
							<a href="tel:<?php echo esc_attr($phone); ?>">
								<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-phone"></i></div>
								<div class="icon-box-1_text__3R39g"><?php Wiloke::ksesHTML($phone); ?></div>
							</a>
						</div>
					</div>
                    <?php endif; ?>

					<?php
					$website = User::getWebsite($authorID);
					if ( !empty($website) ) :
					?>
					<div class="icon-box-1_module__uyg5F one-text-ellipsis mt-20 mt-sm-15 text-pre">
						<div class="icon-box-1_block1__bJ25J">
							<a target="_blank" href="<?php echo esc_url($website); ?>" rel="nofollow">
								<div class="icon-box-1_icon__3V5c0 rounded-circle"><i class="la la-globe"></i></div>
								<div class="icon-box-1_text__3R39g"><?php echo esc_url($website); ?></div>
							</a>
						</div>
					</div>
                    <?php endif; ?>

					<?php do_action('wilcity/author-listing/bottom/about', $authorID); ?>

				</div><!-- End / author-listing_module__3K7-I -->

			</div>
		</div><!-- End / content-box_module__333d9 -->

	</div>
</div>