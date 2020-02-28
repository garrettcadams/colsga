<table class="form-table">
	<tbody>

		<!-- ## Page Settings ########### //-->
		<tr valign="top">
			<th scope="row"><?php _e( "Page Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Add Listing", 'Lavacode' ); ?></th>
							<td>
								<select name="<?php echo $this->getOptionFieldName( 'page_add_' . $this->post_type ); ?>">
									<option value><?php _e( "Select a Page", 'Lavacode' ); ?></option>
									<?php echo lava_directory()->admin->getOptionsPagesLists( lava_directory_manager_get_option( "page_add_{$this->post_type}" ) ); ?>
								</select>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "My Page", 'Lavacode' ); ?></th>
							<td>
								<select name="<?php echo $this->getOptionFieldName( 'page_my_page' ); ?>">
									<option value><?php _e( "Select a Page", 'Lavacode' ); ?></option>
									<?php echo lava_directory()->admin->getOptionsPagesLists( lava_directory_manager_get_option( 'page_my_page' ) ); ?>
								</select>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Login Page", 'Lavacode' ); ?></th>
							<td>
								<fieldset>
									<select name="<?php echo $this->getOptionFieldName( 'login_page' ); ?>">
										<option value><?php _e( "Wordpress Login Page", 'Lavacode' ); ?></option>
										<optgroup label="<?php _e( "Custom Login Page", 'Lavacode' ); ?>">
											<?php echo lava_directory()->admin->getOptionsPagesLists( lava_directory_manager_get_option( 'login_page' ) ); ?>
										</optgroup>
									</select>
								</fieldset>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<!-- ## Add listing Settings ########### //-->
		<tr valign="top">
			<th scope="row"><?php _e( "Add listing Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>

						<!-- ## Map Settings > New listing Status //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "New listing Status", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input
										type="radio"
										name="<?php echo $this->getOptionFieldName( 'new_' . $this->post_type . '_status' ); ?>"
										value=""
										<?php checked( '' == lava_directory_manager_get_option( "new_{$this->post_type}_status" ) ); ?>
									>
									<?php _e( "Publish", 'Lavacode' ); ?>
								</label>
								<label>
									<input
										type="radio"
										name="<?php echo $this->getOptionFieldName( 'new_' . $this->post_type . '_status' ); ?>"
										value="pending"
										<?php checked( 'pending' == lava_directory_manager_get_option( "new_{$this->post_type}_status" ) ); ?>
									>
									<?php _e( "Pending", 'Lavacode' ); ?>
								</label>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<!-- ## Map Settings > New listing permit //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Post new listing permit", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'add_capability' ); ?>" value="" <?php checked( '' == lava_directory_manager_get_option( 'add_capability' ) ); ?>>
									<?php _e( "Anyone without login (it will generate an account automatically)", 'Lavacode' ); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'add_capability' ); ?>" value="member" <?php checked( 'member' == lava_directory_manager_get_option( "add_capability" ) ); ?>>
									<?php _e( "Only login members", 'Lavacode' ); ?>
								</label>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<!-- ## Map Settings > New listing Category Limit //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Max category users can choose on front-end form", 'Lavacode' ); ?></th>
							<td>
								<input type="number" name="<?php echo $this->getOptionFieldName( 'limit_category' ); ?>" value="<?php echo $this->get_settings( 'limit_category', 0 );?>">
								<span class="description"><?php _e( "0 is unlimited (recommended 1 ).", 'Lavacode' ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Max location users can choose on front-end form", 'Lavacode' ); ?></th>
							<td>
								<input type="number" name="<?php echo $this->getOptionFieldName( 'limit_location' ); ?>" value="<?php echo $this->get_settings( 'limit_location', 0 );?>">
								<span class="description"><?php _e( "0 is unlimited (recommended 1 ).", 'Lavacode' ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Limit Detail image upload", 'Lavacode' ); ?></th>
							<td>
								<input type="number" name="<?php echo $this->getOptionFieldName( 'limit_detail_images' ); ?>" value="<?php echo $this->get_settings( 'limit_detail_images', 0 );?>">
								<span class="description"><?php _e( "0 is unlimited (recommended 6 ).", 'Lavacode' ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Required Fields", 'Lavacode' ); ?></th>
							<td>
								<?php
								foreach( $this->getSubmitFields() as $field => $fieldMeta ) {
									printf(
										'<p><label><input type="checkbox" name="%1$s[]" value="%2$s"%3$s>%4$s</label></p>',
										$this->getOptionFieldName( 'required_fields' ),
										$field, checked( in_array( $field, $this->get_settings( 'required_fields', Array() ) ), true, false ),
										$fieldMeta[ 'label' ]
									);
								} ?>
							</td>
						</tr>

					</tbody>
				</table>
			</td>
		</tr>

		<!-- ## Map Settings ########### //-->
		<tr valign="top">
			<th scope="row"><?php _e( "Map Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Google Map API KEY", 'Lavacode' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->getOptionFieldName( 'google_map_api' ); ?>" value="<?php echo $this->get_settings( 'google_map_api' );?>" style="width:30%;">
								<?php
								printf(
									'<span class="description"><a href="%1$s" target="_blank">%2$s</a></span>',
									esc_url_raw( 'developers.google.com/maps/documentation/javascript/get-api-key' ),
									esc_html__( "More Detail", 'Lavacode' )
								); ?>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Category search result setting", 'Lavacode' ); ?></th>
							<td>
								<?php
								foreach(
									Array(
										esc_html__( "Parent category + (related) child categories", 'Lavacode' ) => 'enable',
										esc_html__( "Each category only", 'Lavacode' ) => 'disable',
									) as $strOptionLabel => $strOption
								) {
									printf(
										'<label><input type="radio" name="%1$s" value="%2$s"%4$s>%3$s</label><br>',
										$this->getOptionFieldName( 'json_create_term_type' ),
										$strOption, $strOptionLabel,
										checked( $strOption == $this->get_settings( 'json_create_term_type' ), true, false )
									);
								} ?>
								<br>
								<div><span><?php esc_html_e( 'Note : After changes, "Save" and press "Data refresh".', 'Lavacode' ); ?></span></div>
								<div><span><?php esc_html_e( 'You need to refresh map data ( Data refresh) after you change this option.', 'Lavacode' ); ?></span></div>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Data Refresh", 'Lavacode' ); ?></th>
							<td>
								<?php
								if(
									function_exists('icl_get_languages' ) &&
									false !== (bool)( $lava_wpml_langs = icl_get_languages('skip_missing=0') )
								){
									foreach( $lava_wpml_langs as $lang )
									{
										printf(
											"<button type='button' class='button button-primary lava-data-refresh-trigger' data-lang='%s'>\n\t
												<img src='%s'> %s %s\n\t
											</button>\n\t"
											, $lang['language_code']
											, $lang['country_flag_url']
											, $lang['native_name']
											, __("Refresh", 'Lavacode')
										);
									}
								}else{
									if(class_exists('\\LavaDirectoryManagerPro\\Base\\BaseController')) {
										printf('<h4>%s</h4>', esc_html__("Listing types", 'Lavacode'));
										$listingTypes = \LavaDirectoryManagerPro\Base\BaseController::ListingTypesArray();
										$output = '<div class="">';
										foreach($listingTypes as $typeID => $typeLabel) {
											$output .= sprintf(
												'<button type="button" class="button" data-listing-type-generator="%1$s">%2$s %3$s</button>',
												$typeID, $typeLabel,
												esc_html__("JSON Generator", 'Lavacode')
											);
										}
										$output .= '</div>';
										echo $output;
										printf('<h4>%s</h4>', esc_html__("All", 'Lavacode'));
									} ?>
									<button type="button" class="button button-primary lava-data-refresh-trigger" data-loading="<?php _e( "Processing", 'Lavacode' ); ?>...">
										<?php _e("JSON Generator", 'Lavacode');?>
									</button>
									<?php
								} ?>
								<div id="lava-setting-page-progressbar-wrap">
									<div class="progressbar"></div>
									<div class="text"></div>
								</div>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Cross Domain", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'crossdomain' ); ?>" value="" <?php checked( '' == lava_directory_manager_get_option( "crossdomain" ) ); ?>>
									<?php _e( "Disabled ( Default )", 'Lavacode' ); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'crossdomain' ); ?>" value="1" <?php checked( '1' == lava_directory_manager_get_option( 'crossdomain' ) ); ?>>
									<?php _e( "Enable", 'Lavacode' ); ?>
								</label>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Selectable regional restrictions", 'Lavacode' ); ?></th>
							<td>
								<label>
									<select name="<?php echo $this->getOptionFieldName( 'restrictions_country' ); ?>">
										<?php
										foreach(Array(
											'all' => esc_html__("All", 'Lavacode'),
											'au' => esc_html__("Australia", 'Lavacode'),
											'br' => esc_html__("Brazil", 'Lavacode'),
											'ca' => esc_html__("Canada", 'Lavacode'),
											'fr' => esc_html__("France", 'Lavacode'),
											'de' => esc_html__("Germany", 'Lavacode'),
											'mx' => esc_html__("Mexico", 'Lavacode'),
											'nz' => esc_html__("New Zealand", 'Lavacode'),
											'it' => esc_html__("Italy", 'Lavacode'),
											'za' => esc_html__("South Africa", 'Lavacode'),
											'es' => esc_html__("Spain", 'Lavacode'),
											'pt' => esc_html__("Portugal", 'Lavacode'),
											'us' => esc_html__("U.S.A.", 'Lavacode'),
											'uk' => esc_html__("United Kingdom", 'Lavacode'),
										) as $countryCode => $countryLabel) {
											printf(
												'<option value="%1$s"%2$s>%3$s</option>',
												$countryCode,
												selected($countryCode == $this->get_settings('restrictions_country', 'all'), true, false),
												$countryLabel
											);
										} ?>
									</select>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<!-- ## Single Page Settings ########### //-->

		<tr valign="top">
			<th scope="row"><?php _e( "Single Page Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Amenities display type", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'display_amenities' ); ?>" value="showall" <?php checked( 'showall' == $this->get_settings( 'display_amenities', 'showall' ) ); ?>>
									<?php _e( "List all (unselected & selected) (Default)", 'Lavacode' ); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'display_amenities' ); ?>" value="showexists" <?php checked( 'showexists' == $this->get_settings( 'display_amenities', 'showall' ) ); ?>>
									<?php _e( "List only selected", 'Lavacode' ); ?>
								</label>

							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Amenities : Icon type", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'display_amenities_icon' ); ?>" value="" <?php checked( '' == $this->get_settings( "display_amenities_icon" ) ); ?>>
									<?php _e( "Default icons ( check, uncheck icons)", 'Lavacode' ); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'display_amenities_icon' ); ?>" value="with-own-icon" <?php checked( 'with-own-icon' == $this->get_settings( 'display_amenities_icon' ) ); ?>>
									<?php _e( "Custom icons ( You can add them on Amenities taxonomy setting. )", 'Lavacode' ); ?>
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

		<!-- ## General Settings ########### //-->
		<tr valign="top">
			<th scope="row"><?php _e( "General Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>
						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Listing Slug", 'Lavacode' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->getOptionFieldName( 'main_slug_name' ); ?>" value="<?php echo $this->get_settings( 'main_slug_name', $this->getName() );?>">
								<span><?php printf( esc_html__( "Empty is default( %s )", 'Lavacode' ), $this->getName() ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Blank Image", 'Lavacode' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->getOptionFieldName( 'blank_image' ); ?>" value="<?php echo lava_directory_manager_get_option( 'blank_image' ); ?>" tar="lava-blank-image">
								<input type="button" class="button button-primary fileupload" value="<?php _e('Select an Image', 'Lavacode');?>" tar="lava-blank-image">
								<input class="fileuploadcancel button" tar="lava-blank-image" value="<?php _e('Delete', 'Lavacode');?>" type="button">
								<p>
									<?php
									_e("Preview","Lavacode");
									if( false === (boolean)( $strBlankImage = lava_directory_manager_get_option( 'blank_image' ) ) )
										$strBlankImage = lava_directory()->image_url . 'no-image.png';
									echo "<p><img src=\"{$strBlankImage}\" tar=\"lava-blank-image\" style=\"max-width:300px;\"></p>"; ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>

	</tbody>
</table>
<?php do_action( 'lava_directory_manager_settings_after' ); ?>