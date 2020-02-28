<?php
use WilokeListingTools\Frontend\User;

function wilcity_render_team_intro_slider($aAtts){
	?>
	<div class="swiper__module swiper-container swiper--button-pill swiper--button-abs-outer swiper--button-abs-center swiper--button-mobile-disable" data-options='{"spaceBetween":30,"breakpoints":{"992":{"spaceBetween":10}}}'>
		
		<div class="full-load">
			<!-- pill-loading_module__3LZ6v -->
			<div class="pill-loading_module__3LZ6v pos-a-center">
				<div class="pill-loading_loader__3LOnT"></div>
			</div><!-- End / pill-loading_module__3LZ6v -->
		</div>

		<div class="swiper-wrapper">
            <?php
                if ( $aAtts['get_by'] == 'custom' ){
                    foreach ($aAtts['members'] as $oStaff){
	                    $oStaff = is_array($oStaff) ? (object)$oStaff : $oStaff;
	                    wilcity_render_team_intro_slider_item($oStaff, $aAtts);
                    }
                }else{
	                $args = array(
		                'role'  => $aAtts['get_by']
	                );
	                $aRawMembers = get_users($args);
	                if ( empty($aRawMembers) ){
	                    echo sprintf(esc_html__('Sorry, We found no members who have %s role.', 'wilcity-shortcodes'), $aAtts['get_by']);
                    }else{
	                    foreach ($aRawMembers as $oRawStaff){
		                    $oStaff = array(
			                    'avatar'    => User::getAvatar($oRawStaff->ID),
                                'position'  => User::getPosition($oRawStaff->ID),
                                'picture'   => User::getPicture($oRawStaff->ID),
                                'display_name' => User::getField('display_name', $oRawStaff->ID),
                                'intro'     => User::getField('description', $oRawStaff->ID),
			                    'social_networks'=>User::getSocialNetworks($oRawStaff->ID)
		                    );
		                    $oStaff = (object)$oStaff;
		                    wilcity_render_team_intro_slider_item($oStaff, $aAtts);
                        }

                    }
                }
            ?>
		</div>

		<div class="swiper-button-custom">
			<div class="swiper-button-prev-custom">
				<i class="la la-angle-left"></i>
			</div>
			<div class="swiper-button-next-custom">
				<i class="la la-angle-right"></i>
			</div>
		</div>
		
	</div>
	<?php
}