<?php
function wilcity_render_team_intro_slider_item($oInfo, $aArgs){
	if ( $aArgs['get_by'] == 'custom' ){
		if ( !empty($oInfo->social_networks) ){
			$aParseSocial = explode(',', $oInfo->social_networks);
			$aSocialNetworks = array();

			foreach ($aParseSocial as $rawSocial){
				$rawSocial = trim($rawSocial);
				$aParseSocialItem = explode(':', $rawSocial);
				$aSocialNetworks[$aParseSocialItem[0]] = $aParseSocialItem[1];
			}
		}
	}

	if ( isset($aArgs['pageBuilder']) && $aArgs['pageBuilder'] == 'elementor' && $aArgs['get_by'] == 'custom' ){
	    $picture  = $oInfo->picture['url'];
	    $avatar   = $oInfo->avatar['url'];
    }else{
	    $picture = $oInfo->picture;
		$avatar = $oInfo->avatar;
    }

	$avatar = is_numeric($avatar) ? wp_get_attachment_image_url($avatar, array(100, 100)) : $avatar;
	$picture = is_numeric($picture) ? wp_get_attachment_image_url($picture, 'large') : $picture;

	?>
	<!-- team_module__AckMk -->
	<div class="team_module__AckMk">
		<header class="team_header__jAQl_ bg-cover" style="background-image: url('<?php echo esc_url($picture); ?>')">
            <img src="<?php echo esc_url($picture); ?>" alt="<?php echo esc_attr($oInfo->display_name); ?>"/>
        </header>
		<div class="team_body__236m6">
			<div class="team_thumb__ST807 bg-cover" style="background-image: url('<?php echo esc_url($avatar); ?>')"></div>
			<h2 class="team_name__2LMUU"><?php echo esc_html($oInfo->display_name); ?></h2><span class="team_work__2Fxrh"><?php echo esc_html($oInfo->position); ?></span>
			<div class="team_text__3qce9"><?php echo esc_html($oInfo->intro); ?></div>

			<!-- social-icon_module__HOrwr -->
			<?php if ( isset($aSocialNetworks) && !empty($aSocialNetworks) ) : ?>
			<div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
				<?php foreach ($aSocialNetworks as $name => $link): ?>
				<a class="social-icon_item__3SLnb" href="<?php echo esc_url($link); ?>"><i class="fa fa-<?php echo esc_attr($name); ?>"></i></a>
				<?php endforeach; ?>
			</div><!-- End /  social-icon_module__HOrwr -->
			<?php endif; ?>
		</div>
	</div><!-- End / team_module__AckMk -->
	<?php
}