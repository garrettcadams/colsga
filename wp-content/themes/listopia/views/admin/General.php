<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="general">
<!-- Themes setting > General -->
	<h2><?php esc_html_e("General", 'jvbpd' );?></h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e("Blank Image Settings",'jvbpd' ); ?>
		<span class='description'>
			<?php esc_html_e("Blank (or white) images are shown when no images are available. The preferred dimensions are 300x300.", 'jvbpd' );?>
		</span>
	</th><td>
		<h4><?php esc_html_e("Blank Image",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<p>
				<input type="text" name="jvbpd_ts[no_image]" value="<?php echo esc_attr( jvbpd_tso()->get('no_image', get_template_directory_uri().'/assets/dist/images/no-image.png'));?>" tar="g404">
				<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e('Select Image', 'jvbpd' );?>" tar="g404">
				<input class="fileuploadcancel button" tar="g404" value="<?php esc_attr_e('Delete', 'jvbpd' );?>" type="button">
			</p>
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get('no_image', get_template_directory_uri().'/assets/dist/images/no-image.png'));?>" tar="g404">
			</p>
		</fieldset>
		<h4><?php esc_html_e( "Google maps default marker",'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<p>
				<input type="text" name="jvbpd_ts[marker_image]" value="<?php echo esc_attr( jvbpd_tso()->get( 'marker_image', '' ));?>" tar="g11">
				<input type="button" class="button button-primary fileupload" value="<?php esc_attr_e( 'Select Image', 'jvbpd' );?>" tar="g11">
				<input class="fileuploadcancel button" tar="g404" value="<?php esc_attr_e('Delete', 'jvbpd' );?>" type="button">
			</p>
			<p>
				<?php esc_html_e("Preview",'jvbpd' ); ?><br>
				<img src="<?php echo esc_attr( jvbpd_tso()->get('marker_image', ''));?>" tar="g11">
			</p>
		</fieldset>
	</td></tr><tr><th>
		<?php esc_html_e("Login Settings",'jvbpd' ); ?>
		<span class='description'>
			<?php esc_html_e("The page to redirect users to after a successful login.", 'jvbpd' );?>
		</span>
	</th><td>
		<?php
		if( function_exists( 'jvbpdCore' ) ) :
			foreach( Array(
				'login_template' => Array(
					'label' => esc_html__("Login Template", 'jvbpd'),
					'options' => jvbpdCore()->admin->getElementorLoginID(),
				),
				'sign_template' => Array(
					'label' => esc_html__("Sign-up Template", 'jvbpd'),
					'options' => jvbpdCore()->admin->getElementorSignupID(),
				),
			) as $metaKey => $meta ){
				$templateOptions = Array();
				foreach( $meta['options'] as $template_id  ) {
					if( false === get_post_status( $template_id ) ) {
						continue;
					}
					$templateOptions[] = sprintf(
						'<option value="%1$s"%3$s>%2$s</option>', $template_id, get_the_title( $template_id ),
						selected( $template_id== jvbpd_tso()->get( $metaKey, '' ), true, false )
					);
				}
				printf(
					'<h4>%1$s</h4><fieldset><select name="%2$s">%3$s</select></fieldset>',
					$meta['label'], $metaKey, join(false, $templateOptions)
				);
			}
		endif; ?>

		<h4><?php esc_html_e("Redirect to",'jvbpd' ); ?> :</h4>
		<fieldset class="inner">
			<select name="jvbpd_ts[login_redirect]">
				<?php
				foreach(
					Array(
						'' => esc_html__('Profile Page (Default)', 'jvbpd' ),
						'home' => esc_html__('Main Page', 'jvbpd' ),
						'current' => esc_html__('Current Page', 'jvbpd' ),
						'admin' => esc_html__('WordPress Profile Page', 'jvbpd' )
					) as $key => $text){
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						$key,
						selected( jvbpd_tso()->get( 'login_redirect' ) == $key, true, false ),
						$text
					);
				} ?>
			</select>
		</fieldset>
		<h4><?php esc_html_e( "User Agreement",'jvbpd' ); ?> :</h4>
		<fieldset class="inner">
			<select name="jvbpd_ts[agree_register]">
				<option value=""><?php esc_html_e( "Disable", 'jvbpd' );?></option>
				<?php
				if( $pages = get_posts( "post_type=page&post_status=publish&posts_per_page=-1&suppress_filters=0" ) )
				{
					printf( "<optgroup label=\"%s\">", esc_html__( "Select a page for user agreement", 'jvbpd' ) );
					foreach( $pages as $post )
						printf(
							"<option value=\"{$post->ID}\" %s>{$post->post_title}</option>"
							, selected( $post->ID == jvbpd_tso()->get( 'agree_register', '' ), true, false )
						);
					echo "</optgroup>";
				} ?>
			</select>
		</fieldset>

	</td></tr>
	<tr><th>
		<?php esc_html_e("Color Settings",'jvbpd' ); ?>
		<span class="description">
			<?php esc_html_e("Choose colors to match your theme.", 'jvbpd' );?>
		</span>
	</th><td>

		<h4><?php esc_html_e( "Background Color", 'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[page_bg]" type="text" value="<?php echo esc_attr( jvbpd_tso()->get( 'page_bg', '#eeeff2' )  );?>" class="wp_color_picker" data-default-color="#FFFFFF">
		</fieldset>

		<h4><?php esc_html_e("Primary Color Selection", 'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[total_button_color]" type="text" value="<?php echo esc_attr( jvbpd_tso()->get( 'total_button_color' )  );?>" class="wp_color_picker" data-default-color="#0FAF97">
		</fieldset>

		<h4><?php esc_html_e( "Primary Font Color Selection", 'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[primary_font_color]" type="text" value="<?php echo esc_attr( jvbpd_tso()->get( 'primary_font_color' ) );?>" class="wp_color_picker" data-default-color="#fff">
		</fieldset>

		<h4><?php esc_html_e("Border Color Setup", 'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<label><input type="radio" name="jvbpd_ts[total_button_border_use]" value="use" <?php checked(jvbpd_tso()->get('total_button_border_use') == "use");?>><?php esc_html_e('Use', 'jvbpd' );?></label>
			<label><input type="radio" name="jvbpd_ts[total_button_border_use]" value="" <?php checked(jvbpd_tso()->get('total_button_border_use')== "");?>><?php esc_html_e('Not Use', 'jvbpd' );?></label>
		</fieldset>

		<h4><?php esc_html_e("Border Color Selection", 'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<input name="jvbpd_ts[total_button_border_color]" type="text" value="<?php echo esc_attr( jvbpd_tso()->get( 'total_button_border_color' ) );?>" class="wp_color_picker" data-default-color="#0FAF97">
		</fieldset>

	</td></tr><tr><th>

		<?php esc_html_e('Miscellaneous Settings','jvbpd' ); ?>
		<span class='description'>
			<?php esc_html_e('Other settings', 'jvbpd' );?>
		</span>
	</th><td>

		<h4><?php esc_html_e('Preloader', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<select name="jvbpd_ts[preloader]">
				<?php
				foreach(
					Array(
						'' => esc_html__( "Disabled", 'jvbpd' ),
						'enable' => esc_html__( "Enable", 'jvbpd' ),
					) as $strPreLoaderKey => $strPreLoaderLabel
				) {
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						$strPreLoaderKey, $strPreLoaderLabel,
						selected( $strPreLoaderKey == jvbpd_tso()->get( 'preloader', false ), true, false )
					);
				} ?>
			</select>

		</fieldset>

	</td></tr><tr><th>
		<?php esc_html_e("Floating Menu",'jvbpd' ); ?>
	</th><td>
		<h4><?php esc_html_e('Use floating menu ( Right bottom on all pages )', 'jvbpd' );?></h4>
		<fieldset class="inner">
			<select name="jvbpd_ts[floating_menu]" data-show-field='{"enable":[".field-floating-menu-content"]}'>
				<?php
				foreach(
					Array(
						'disable' => esc_html__('Disable', 'jvbpd' ),
						'enable' => esc_html__('Enable', 'jvbpd' ),
					) as $key => $text){
					printf(
						'<option value="%1$s" %2$s>%3$s</option>',
						$key,
						selected( jvbpd_tso()->get( 'floating_menu', 'disable' ) == $key, true, false ),
						$text
					);
				} ?>
			</select>
		</fieldset>
		<fieldset class="inner field-floating-menu-content">
			<label>
				<?php esc_html_e('Show to scroll', 'jvbpd' );?><br>
				<select name="jvbpd_ts[floating_menu_show_scroll]">
					<?php
					foreach(
						Array(
							'disable' => esc_html__('Disable', 'jvbpd' ),
							'enable' => esc_html__('Enable', 'jvbpd' ),
						) as $key => $text){
						printf(
							'<option value="%1$s" %2$s>%3$s</option>',
							$key,
							selected( jvbpd_tso()->get( 'floating_menu_show_scroll', 'disable' ) == $key, true, false ),
							$text
						);
					} ?>
				</select>
			</label>
		</fieldset>
		<fieldset class="inner field-floating-menu-content">
			<label>
				<?php esc_html_e('Add a shortcodde', 'jvbpd' );?><br>
				<input type="text" name="jvbpd_ts[floating_menu_content]" value="<?php echo esc_attr( jvbpd_tso()->get('floating_menu_content' ) );?>">
			</label>
			<p><?php esc_html_e('To create a shortcode, please go to javo builder and create one to get a shortcode.', 'jvbpd' );?></p>
		</fieldset>
	</td></tr><tr><th>
		<?php esc_html_e("Plugins Settings",'jvbpd' ); ?>
	</th><td>

		<h4><?php esc_html_e( "Module Excerpt", 'jvbpd' );?></h4>

		<fieldset class="inner">

			<label style="padding: 0 15px 0;">
				<input type="radio" name="jvbpd_ts[core_module_excerpt]" value='' <?php checked( '' == jvbpd_tso()->get('core_module_excerpt') );?>>
				<?php esc_html_e( "wp_trim_words ( Default )", 'jvbpd' );?>
			</label>
			<label style="padding: 0 15px 0;">
				<input type="radio" name="jvbpd_ts[core_module_excerpt]" value='mb_substr' <?php checked( 'mb_substr' == jvbpd_tso()->get('core_module_excerpt') );?>>
				<?php esc_html_e( "mb_substr", 'jvbpd' );?>
			</label>
			<div class="description"><?php esc_html_e( "(mb_subsr is for a few languages - excerpt length. ex. Chinese)", 'jvbpd' );?></div>

		</fieldset>
	</td></tr>
	</table>
</div>