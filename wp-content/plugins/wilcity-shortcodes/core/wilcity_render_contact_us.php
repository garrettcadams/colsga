<?php
function wilcity_sc_render_contact_us($atts){
	$atts = \WILCITY_SC\SCHelpers::mergeIsAppRenderingAttr($atts);
	if ( $atts['isApp'] ){
		echo '%SC%' . json_encode($atts) . '%SC%';
		return '';
	}

	?>
	<div class="row <?php echo esc_attr(trim($atts['extra_class'])); ?>">
        <?php if(!empty($atts['contact_info'])) : ?>

        <div class="col-md-4 col-lg-4">
	        <div class="content-box_module__333d9">
				<header class="content-box_header__xPnGx clearfix">
					<div class="wil-float-left">
						<h4 class="content-box_title__1gBHS">
							<i class="la la-info-circle"></i>
							<span><?php echo esc_html($atts['contact_info_heading']); ?></span>
						</h4>
					</div>
				</header>
				<div class="content-box_body__3tSRB">
					<?php
					foreach ($atts['contact_info'] as $oContactInfo):
						$oContactInfo = is_array($oContactInfo) ? (object)$oContactInfo : $oContactInfo;
					?>
						<div class="icon-box-1_module__uyg5F one-text-ellipsis mt-20 mt-sm-15">
							<div class="icon-box-1_block1__bJ25J">
								<?php if ( $oContactInfo->type == 'email' ) : ?>
									<a href="mailto:<?php echo esc_attr($oContactInfo->link); ?>" target="<?php echo esc_attr($oContactInfo->target); ?>">
								<?php elseif ( $oContactInfo->type == 'phone' ) : ?>
									<a href="tel:<?php echo esc_attr($oContactInfo->link); ?>" target="<?php echo esc_attr($oContactInfo->target); ?>">
								<?php else: ?>
									<a href="<?php echo esc_attr($oContactInfo->link); ?>" target="<?php echo esc_attr($oContactInfo->target); ?>">
								<?php endif; ?>
										<div class="icon-box-1_icon__3V5c0 rounded-circle">
											<i class="<?php echo esc_attr($oContactInfo->icon); ?>"></i>
										</div>
										<div class="icon-box-1_text__3R39g"><?php echo esc_html($oContactInfo->info); ?></div>
									</a>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

        <?php else: ?>

        <div>
	        <?php endif; ?>
	        <div class="col-md-8 col-lg-8">
		        <div class="content-box_module__333d9">
		            <header class="content-box_header__xPnGx clearfix">
		                <div class="wil-float-left">
		                    <h4 class="content-box_title__1gBHS"><i class="la la-paper-plane"></i><span><?php echo esc_html($atts['contact_form_heading']); ?></span></h4>
		                </div>
		            </header>
		            <div class="content-box_body__3tSRB">
		                <?php
		                if ( !empty($atts['contact_form_7']) ){
		                    $aParse = explode(':', $atts['contact_form_7']);
			                echo do_shortcode("[contact-form-7 id='".$aParse[0]."']");
		                }else if (!empty($atts['contact_form_shortcode'])){
			                echo do_shortcode($atts['contact_form_shortcode']);
		                }else{
			                esc_html_e('Please select the contact form.', 'wilcity-shortcodes');
                        }
		                ?>
		            </div>
		        </div>
	        </div>
		</div>
	<?php
}