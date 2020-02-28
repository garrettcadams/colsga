<?php
$jvbpd_options = Array(
	'header_type' => apply_filters( 'jvbpd_options_header_types', Array() ),
	'header_skin' => Array(
		esc_html__("Light", 'jvbpd' ) => "",
		esc_html__("Dark", 'jvbpd' ) => "dark"
	),
	'able_disable' => Array(
		esc_html__("Disable", 'jvbpd' ) => "disabled",
		esc_html__("Able", 'jvbpd' ) => "enable"
	),
	'sticky_able_disable' => Array(
		esc_html__("Able", 'jvbpd' ) => "enable",
		esc_html__("Disable", 'jvbpd' ) => "disabled"
	),
	'header_fullwidth' => Array(
		esc_html__("Center Left", 'jvbpd' ) => "fixed",
		esc_html__("Center Right", 'jvbpd' ) => "fixed-right",
		esc_html__("Wide", 'jvbpd' ) => "full"
	),
	'header_relation' => Array(
		esc_html__("Transparency menu", 'jvbpd' )	=> "absolute",
		esc_html__("Default menu", 'jvbpd' ) => "relative"
	),
	'default_header_relation' => Array(
		esc_html__("Default menu", 'jvbpd' )	=> "relative",
		esc_html__("Transparency menu", 'jvbpd' )	=> "absolute"
	),
); ?>

<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="header">
	<h2><?php esc_html_e("Heading Settings", 'jvbpd' ); ?> </h2>
	<table class="form-table">

	<tr><th>
		<?php esc_html_e( "Select a header", 'jvbpd' );?>
		<span class="description"></span>
	</th><td>

	<?php
	if( function_exists( 'jvbpdCore' ) ) :
		foreach( Array(
			'elementor_header' => Array(
				'label' => esc_html__( "Default(Global) Header", 'jvbpd' ),
				'description' => sprintf(
					__(
					'To create more headers, Core >> page builder ( <a href="%1$s" target="_blank">Go page builder</a> )<br>
					 If you do not select, the recent header ( you created ) will be displayed.', 'jvbpd' ),
					esc_url( add_query_arg( Array( 'post_type' => 'jvbpd-listing-elmt', ), admin_url( 'edit.php' ) ) )
				),
			),
			'elementor_header_post' => Array(
				'label' => esc_html__( "Single post detail pages", 'jvbpd' ),
				'description' => esc_html__( 'If you do not select "Header", "Default(Global) Header" will be displayed.', 'jvbpd' ),
			),
             'elementor_header_post_archive' => Array(
                 'label' => esc_html__("Post archive pages", 'jvbpd'),
                 'description' => esc_html__('If you do not select "Header", "Default(Global) Header" will be displayed.','jvbpd'),
             ),
			'elementor_header_lv_listing' => Array(
                'label' => esc_html__( "Single listing detail pages", 'jvbpd' ),
                'description' => esc_html__( 'If you do not select "Header", "Default(Global) Header" will be displayed.', 'jvbpd' ),
            ),
            'elementor_header_lv_listing_archive' => Array(
                'label' => esc_html__( "Listing archive pages", 'jvbpd' ),
                'description' => esc_html__( 'If you do not select "Header", "Default(Global) Header" will be displayed.', 'jvbpd' ),
            ),
            'elementor_header_profile' => Array(
                'label' => esc_html__( "My Dashboard Page", 'jvbpd' ),
				'description' => esc_html__( 'If you do not select "Header", "Default(Global) Header" will be displayed.', 'jvbpd' ),
				'condition' => function_exists( 'bp_is_active' ),
			),
			'elementor_header_member' => Array(
                'label' => esc_html__( "Member Page", 'jvbpd' ),
				'description' => esc_html__( 'If you do not select "Header", "Default(Global) Header" will be displayed.', 'jvbpd' ),
				'condition' => function_exists( 'bp_is_active' ),
            ),
			'elementor_header_group' => Array(
                'label' => esc_html__( "Group Page", 'jvbpd' ),
				'description' => esc_html__( 'If you do not select "Header", "Default(Global) Header" will be displayed.', 'jvbpd' ),
				'condition' => function_exists( 'bp_is_active' ) && bp_is_active( 'groups'),
            ),


		) as $elementor_header_key => $elementor_header_meta ) {
			if( isset( $elementor_header_meta['condition'] ) ){
				if( false === $elementor_header_meta['condition'] ) {
					continue;
				}
			} ?>
			<h4><?php echo esc_html( $elementor_header_meta[ 'label' ] ); ?></h4>
			<fieldset class="inner">
				<select name="jvbpd_ts[<?php echo esc_attr($elementor_header_key); ?>]">
					<option value=''><?php esc_html_e( "Select a header", 'jvbpd' ); ?></option>
					<?php
					foreach( jvbpdCore()->admin->getElementorHeaderID() as $template_id  ) {
						if( false === get_post_status( $template_id ) ) {
							continue;
						}
						printf(
							'<option value="%1$s"%3$s>%2$s</option>', $template_id, get_the_title( $template_id ),
							selected( $template_id == jvbpd_tso()->get( $elementor_header_key, '' ), true, false )
						);
					} ?>
				</select>
				<?php
				if( isset( $elementor_header_meta[ 'description' ] ) ) { ?>
					<div>
						<span class="description"><?php echo esc_html($elementor_header_meta[ 'description' ]); ?></span>
					</div>
					<?php
				} ?>
			</fieldset>
			<?php
		}
	endif; ?>

	</td></tr><tr><th>
		<?php esc_html_e( "Select a sidebars", 'jvbpd' );?>
		<span class="description"></span>
	</th><td>
	<?php
	foreach(
		Array(
			'sidebar_left' => Array(
				'label' => esc_html__( "Left Sidebar", 'jvbpd' ),
				'note' => esc_html__( "It shows when there is at least one menu. otherwise, it doesn't show.", 'jvbpd' ),
			),
		) as $strOptionName => $strOptionMeta
	) {
		?>
		<h4 class="pull-left"><?php echo esc_html( $strOptionMeta[ 'label' ] ); ?></h4>
		<fieldset class="inner margin-20-0 <?php if($strOptionMeta[ 'label' ]=='Member Sidebar') echo 'margin-custom-28-0'; ?>">
			<select name="jvbpd_ts[<?php echo esc_attr( $strOptionName ); ?>]">
				<?php
				foreach(
					Array(
						'disabled' => esc_html__( "Disable (Default)", 'jvbpd' ),
						'enabled' => esc_html__( "Enable", 'jvbpd' ),
					) as $strOption => $strOptionLabel
				) {
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						$strOption, $strOptionLabel,
						selected( $strOption == jvbpd_tso()->get( $strOptionName, apply_filters( 'jvbpd_theme_option_' . $strOption . '_default', 'disabled' ) ), true, false )
					);
				} ?>
			</select>
			<?php printf( '<div class="description">%1$s : %2$s</div>', esc_html__( "Note", 'jvbpd' ), $strOptionMeta[ 'note' ] ); ?>
		</fieldset>
		<?php
	} ?>
	</td></tr>
	</table>
</div>