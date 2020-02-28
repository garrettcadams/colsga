<?php
$arrPages = jvbpd_tso()->getPages();
$jvbpd_options = Array(
	'header_type' => apply_filters( 'jvbpd_options_header_types', Array() )
	, 'header_skin' => Array(
		esc_html__("Dark", 'jvbpd' )							=> ""
		, esc_html__("Light", 'jvbpd' )						=> "light"
	)
	, 'able_disable' => Array(
		esc_html__("Disable", 'jvbpd' )					=> "disabled"
		,esc_html__("Able", 'jvbpd' )							=> "enable"

	)
	, 'header_fullwidth' => Array(
		esc_html__("Center Left", 'jvbpd' )						=> "fixed"
		, esc_html__("Center Right", 'jvbpd' )			=> "fixed-right"
		, esc_html__("Wide", 'jvbpd' )						=> "full"
	)
	, 'header_relation' => Array(
		esc_html__("Transparency menu", 'jvbpd' )	=> "absolute"
		,esc_html__("Default menu", 'jvbpd' )				=> "relative"
	)
	, 'portfolio_detail_page_head_style' => Array(
		esc_html__("Featured image with Title", 'jvbpd' )	=> "featured_image"
		,esc_html__("Title on the top", 'jvbpd' )	=> "title_on_top"
		,esc_html__("Title upper content ", 'jvbpd' )				=> "title_upper_content"
	)

	, 'portfolio_detail_page_layout' => Array(
		esc_html__("Full Width - Content After", 'jvbpd' )					=> "fullwidth-content-after"
		,esc_html__("Full Width - Content Before", 'jvbpd' )					=> "fullwidth-content-before"
		,esc_html__("Right - Side Bar", 'jvbpd' )					=> "quick-view"

	)
); ?>

<div class="jvbpd_ts_tab javo-opts-group-tab hidden" tar="portfolio">
	<h2><?php esc_html_e("Portfolio Page Settings", 'jvbpd' ); ?> </h2>
	<table class="form-table">
	<tr><th>
		<?php esc_html_e( "Page Setting", 'jvbpd' );?>
	</th><td>

		<h4><?php esc_html_e( "Listing page", 'jvbpd' );?>: </h4>
		<fieldset  class="inner">
			<select name="jvbpd_ts[blog_list_page_id]">
				<option value=""><?php esc_html_e( "Select a page", 'jvbpd' ); ?></option>
				<?php
				foreach( $arrPages as $objPage ) {
					printf(
						'<option value="%1$s"%3$s>%2$s</option>',
						$objPage->ID, $objPage->post_title,
						selected( jvbpd_tso()->get( 'blog_list_page_id' ) == $objPage->ID, true, false )
					);
				} ?>
			</select>
			<div class="description"><?php esc_html_e( "A portfolio list page to be moved when list button (or icon) is pressed on single portfolio pages.", 'jvbpd' );?></div>
		</fieldset>

	</td></tr><tr><th>
		<?php esc_html_e( "Header / Menu", 'jvbpd' );?>
		<span class="description"></span>
	</th><td>

		<h4><?php esc_html_e( "Header Style", 'jvbpd' ); ?></h4>
		<fieldset class="inner">
			<dl>
				<dt><?php esc_html_e("Header type", 'jvbpd' ); ?></dt>
				<dd>
					<select name="jvbpd_ts[hd][portfolio_detail_page_head_style]">
						<?php
						foreach( $jvbpd_options['portfolio_detail_page_head_style'] as $label => $value )
						{
							printf( "<option value='{$value}' %s>{$label}</option>", selected( $value == jvbpd_tso()->header->get("portfolio_detail_page_head_style"), true, false ) );
						} ?>
					</select>
					<div class="description"><?php esc_html_e("Caution: If you choose transparent menu type, page's main text contents ascends as much as menu's height to make menu transparent.", 'jvbpd' );?></div>
				</dd>
			</dl>
		</fieldset>
	</td></tr>


	<tr>
		<th>
			<?php esc_html_e( "Default Style", 'jvbpd' );?>
			<span class="description"></span>
		</th>
		<td>
			<h4><?php esc_html_e( "Default Page Layout", 'jvbpd' ); ?></h4>
			<fieldset class="inner">
				<select name="jvbpd_ts[hd][portfolio_detail_page_layout]">
							<?php
							foreach( $jvbpd_options['portfolio_detail_page_layout'] as $label => $value )
							{
								printf( "<option value='{$value}' %s>{$label}</option>", selected( $value == jvbpd_tso()->header->get("portfolio_detail_page_layout"), true, false ) );
							} ?>
						</select>
						<div class="description"><?php esc_html_e("Caution: If you choose transparent menu type, page's main text contents ascends as much as menu's height to make menu transparent.", 'jvbpd' );?></div>
			</fieldset>
		</td>
	</tr>
	</table>
</div>