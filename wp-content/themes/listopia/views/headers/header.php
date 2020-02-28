<?php
$boolUseTopNavBarInHeader = apply_filters( 'jvbpd_display_navi_in_header', true ); ?>
<div id="wrapper">
	<!-- Navigation -->

	<nav class="navbar navbar-expand-lg theme-nav">
		<div class="container d-flex justify-content-between">
			<a class="navbar-brand" href="/"><img src="<?php echo get_template_directory_uri(); ?>/assets/dist/images/jv-logo2.png"></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

			<div class="float-right collapse navbar-collapse flex-grow-0" id="navbarSupportedContent">
			<?php
						wp_nav_menu( array(
							'menu'				=> 'primary',
							'theme_location'	=> 'primary',						
							'menu_id'			=> '',
							'menu_class'		=> 'nav jvbpd-nav jvbpd-nav-menu navbar-nav mr-auto',/*'nav jvbpd-nav jvbpd-nav-menu'*/
							'echo'				=> true,
							'depth'				=> 3,
							'fallback_cb'		=> '\jvnavwalker::fallback',
							'walker'			=> new \jvnavwalker()
						) ); ?>
				
			</div>
						</div>
	</nav>		