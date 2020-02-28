<h1><?php esc_html_e( "Lava ajax search settings", 'lvbp-ajax-search' ); ?></h1>
<table class="form-table">
	<form method="post" action="options.php">
	<?php
	settings_fields( lava_ajaxSearch()->admin->optionGroup ); ?>
	<tbody>
		<!-- ## Add listing Settings ########### //-->
		<tr valign="top">
			<th scope="row"><?php _e( "Search Setting", 'lvbp-ajax-search' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>

						<!-- ## Map Settings > New listing Status //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Search result count", 'lvbp-ajax-search' ); ?></th>
							<td>
								<input type="number" name="<?php echo lava_ajaxSearch()->admin->getOptionFieldName( 'search_limit' ); ?>" value="<?php echo intVal( lava_ajaxSearch()->admin->get_settings( 'search_limit', 0 ) ); ?>">
								<p><small><?php esc_html_e( "Unlimited = 0", 'lvbp-ajax-search' ); ?></small></p>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Search Filter", 'lvbp-ajax-search' ); ?></th>
							<td>
								<?php
								foreach(
									Array(
										Array(
											'type' => 'separator',
											'label' => esc_html__( "Post Types", 'lvbp-ajax-search' ),
										),
										'posts' => Array(
											'label' => esc_html__( "Posts", 'lvbp-ajax-search' ),
										),
										'pages' => Array(
											'label' => esc_html__( "Pages", 'lvbp-ajax-search' ),
										),
										'listings' => Array(
											'label' => esc_html__( "Listings", 'lvbp-ajax-search' ),
										),
										Array(
											'type' => 'separator',
											'label' => esc_html__( "Buddy Press", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'BuddyPress' ),
										),
										'members' => Array(
											'label' => esc_html__( "Members", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'BuddyPress' ),
										),
										'groups' => Array(
											'label' => esc_html__( "Groups", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'BuddyPress' ),
										),
										Array(
											'type' => 'separator',
											'label' => esc_html__( "BBPress", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'bbPress' ),
										),
										'forum' => Array(
											'label' => esc_html__( "Forum", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'bbPress' ),
										),
										'topic' => Array(
											'label' => esc_html__( "Topics", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'bbPress' ),
										),
										'reply' => Array(
											'label' => esc_html__( "Replies", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'bbPress' ),
										),
										Array(
											'type' => 'separator',
											'label' => esc_html__( "Woocommerce", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'WC' ),
										),
										'products' => Array(
											'label' => esc_html__( "Products", 'lvbp-ajax-search' ),
											'allow' => function_exists( 'WC' ),
										),
									) as $strFilter => $arrFilterMeta
								) {

									if( isset( $arrFilterMeta[ 'allow' ] ) && true !== $arrFilterMeta[ 'allow' ] ) {
										continue;
									}

									if( isset( $arrFilterMeta[ 'type' ] ) && 'separator' == $arrFilterMeta[ 'type' ] ) {
										printf( '<h4>%s</h4>', $arrFilterMeta[ 'label' ] );
										continue;
									}

									printf(
										'<div style="margin-left:10px;"><label><input type="checkbox" name="%1$s[]" value="%2$s"%4$s>%3$s</label></div>',
										lava_ajaxSearch()->admin->getOptionFieldName( 'search_filter' ),
										$strFilter, $arrFilterMeta[ 'label' ],
										checked( in_array( $strFilter, (array) lava_ajaxSearch()->admin->get_settings( 'search_filter' ) ), true, false )
									);
								} ?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"></th>
			<td><?php submit_button(); ?></td>
		</tr>
	</tbody>
	</form>
</table>