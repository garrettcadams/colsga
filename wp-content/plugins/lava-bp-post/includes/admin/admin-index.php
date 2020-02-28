<table class="form-table">
	<tbody>

		<!-- ## Page Settings ########### //-->
		<tr valign="top">
			<th scope="row"><?php _e( "Page Settings", 'lvbp-bp-post' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Submit Page", 'lvbp-bp-post' ); ?></th>
							<td>
								<select name="<?php echo $this->getOptionFieldName( 'page_add_' . $this->post_type ); ?>">
									<option value><?php _e( "Select a Page", 'lvbp-bp-post' ); ?></option>
									<?php echo getOptionsPagesLists( lava_bpp_get_option( "page_add_{$this->post_type}" ) ); ?>
								</select>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "My Page", 'lvbp-bp-post' ); ?></th>
							<td>
								<select name="<?php echo $this->getOptionFieldName( 'page_my_page' ); ?>">
									<option value><?php _e( "Select a Page", 'lvbp-bp-post' ); ?></option>
									<?php echo getOptionsPagesLists( lava_bpp_get_option( 'page_my_page' ) ); ?>
								</select>
							</td>
						</tr>
						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Login Page", 'lvbp-bp-post' ); ?></th>
							<td>
								<fieldset>
									<select name="<?php echo $this->getOptionFieldName( 'login_page' ); ?>">
										<option value><?php _e( "Wordpress Login Page", 'lvbp-bp-post' ); ?></option>
										<optgroup label="<?php _e( "Custom Login Page", 'lvbp-bp-post' ); ?>">
											<?php echo getOptionsPagesLists( lava_bpp_get_option( 'login_page' ) ); ?>
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
			<th scope="row"><?php _e( "Add Post Settings", 'Lavacode' ); ?></th>
			<td>
				<table class="widefat">
					<tbody>

						<!-- ## Map Settings > New listing Status //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "New Post Status", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input
										type="radio"
										name="<?php echo $this->getOptionFieldName( 'new_' . $this->post_type . '_status' ); ?>"
										value=""
										<?php checked( '' == lava_bpp_get_option( "new_{$this->post_type}_status" ) ); ?>
									>
									<?php _e( "Publish", 'Lavacode' ); ?>
								</label>
								<label>
									<input
										type="radio"
										name="<?php echo $this->getOptionFieldName( 'new_' . $this->post_type . '_status' ); ?>"
										value="pending"
										<?php checked( 'pending' == lava_bpp_get_option( "new_{$this->post_type}_status" ) ); ?>
									>
									<?php _e( "Pending", 'Lavacode' ); ?>
								</label>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<!-- ## Map Settings > New listing permit //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Posting New Posts Permit", 'Lavacode' ); ?></th>
							<td>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'add_capability' ); ?>" value="" <?php checked( '' == lava_bpp_get_option( 'add_capability' ) ); ?>>
									<?php _e( "Anyone without login (it will generate an account automatically)", 'Lavacode' ); ?>
								</label>
								<br>
								<label>
									<input type="radio" name="<?php echo $this->getOptionFieldName( 'add_capability' ); ?>" value="member" <?php checked( 'member' == lava_bpp_get_option( "add_capability" ) ); ?>>
									<?php _e( "Only login members", 'Lavacode' ); ?>
								</label>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<!-- ## Map Settings > New listing Category Limit //-->
						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Max Categories Amount (users can choose on front-end form )", 'Lavacode' ); ?></th>
							<td>
								<input type="number" name="<?php echo $this->getOptionFieldName( 'limit_category' ); ?>" value="<?php echo $this->get_settings( 'limit_category', 0 );?>">
								<span class="description"><?php _e( "0 is unlimited (recommended 1 ).", 'Lavacode' ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Limit Detail Image Upload", 'Lavacode' ); ?></th>
							<td>
								<input type="number" name="<?php echo $this->getOptionFieldName( 'limit_detail_images' ); ?>" value="<?php echo $this->get_settings( 'limit_detail_images', 0 );?>">
								<span class="description"><?php _e( "0 is unlimited (recommended 6 ).", 'Lavacode' ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th><?php _e( "Admin Notice", 'Lavacode' ); ?></th>
							<td>
								<textarea class="large-text" rows="5" name="<?php echo $this->getOptionFieldName( 'add_new_admin_notice' ); ?>"><?php echo $this->get_settings( 'add_new_admin_notice', '' );?></textarea>
								<span class="description"><?php esc_html_e( "Admin message on submit form to authors ( ex. Admin will review your post within 24 hours and approve it to be published. )", 'Lavacode' ); ?></span>
							</td>
						</tr>

						<tr><td colspan="3" style="padding:0;"><hr style='margin:0;'></td></tr>

						<tr valign="top">
							<td width="1%"></td>
							<th>&nbsp;<?php _e( "Author message to admin", 'Lavacode' ); ?></th>
							<td>
								<div>
									<label>
										<input type="checkbox" name="<?php echo $this->getOptionFieldName( 'add_new_user_field' ); ?>" value="yes" <?php checked( 'yes' == lava_bpp_get_option( "add_new_user_field" ) ); ?>>
										<?php _e( "Enabled", 'Lavacode' ); ?>
									</label>
								</div>
								<div>
									<label>
										<?php _e( "Label", 'Lavacode' ); ?>
										<input type="text" class="regular-text" name="<?php echo $this->getOptionFieldName( 'add_new_user_field_label' ); ?>" value="<?php echo lava_bpp_get_option( 'add_new_user_field_label' ); ?>">
									</label>
								</div>
								<span class="description"><?php esc_html_e( " (ex Message to admin )
								if enable, authors can also leave a message when they submit posts.", 'Lavacode' ); ?></span>
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
							<th>&nbsp;<?php _e( "Blank Image", 'Lavacode' ); ?></th>
							<td>
								<input type="text" name="<?php echo $this->getOptionFieldName( 'blank_image' ); ?>" value="<?php echo lava_bpp_get_option( 'blank_image' ); ?>" tar="lava-blank-image">
								<input type="button" class="button button-primary fileupload" value="<?php _e('Select an Image', 'Lavacode');?>" tar="lava-blank-image">
								<input class="fileuploadcancel button" tar="lava-blank-image" value="<?php _e('Delete', 'Lavacode');?>" type="button">
								<p>
									<?php
									_e("Preview","Lavacode");
									if( false === (boolean)( $strBlankImage = lava_bpp_get_option( 'blank_image' ) ) )
										$strBlankImage = lava_bpp()->image_url . 'no-image.png';
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
<?php do_action( 'lava_bpp_settings_after' ); ?>