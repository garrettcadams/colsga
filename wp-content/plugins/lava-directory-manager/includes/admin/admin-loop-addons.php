<div class="addon-wrap">
	<div class="thumb-wrap">
		<img src="<?php echo $addon->thumbnail;?>">
	</div>
	<h2 class="addon-title">
		<?php echo $addon->label; ?>
	</h2>
	<div class="addon-meta">
		<div class="addon-new">
			<span class="addon-new-version"><?php printf( esc_html__( "Current Version : %s", 'Lavacode' ), $addon->version ); ?></span> <br>
		</div>
		<div class="addon-new">
			<span class="addon-new-version"><?php printf( esc_html__( "Lastest Version : %s", 'Lavacode' ), $addon->lastest_version ); ?></span> <br>
		</div>
	</div>
	<div class="addon-action">
		<?php
		if( version_compare( $addon->lastest_version, $addon->version, '>' ) ) {
			printf( "<a href=\"%s\"class=\"button button-primary\">%s</a>"
				, esc_url( network_admin_url( 'update-core.php' ) )
				, __( "Update Plugin Page", 'Lavacode' )
			);
		}else{
			printf( "<div class='addon-lastest-button'>%s</div>"
				, __( "Lastest Version", 'Lavacode' )
			);
		}

		if( $addon->license_active )
			printf( "&nbsp;<button type=\"button\" class=\"lava-addon-deactive-license button\" data-slug=\"{$addon->name}\">%s</button>", __( "Deactivate License", 'Lavacode' ) );
		?>
	</div>
	<?php if( !$addon->license_active ) : ?>
		<div class="addon-activator">
			<?php
			printf( "
				<div class=\"lava-addons-license-field\">
					<p>
						<label>
							%s <br><input type=\"email\" name=\"lavaLicense[$addon->name]\" value=\"%s\" size=30>
						</label>
					</p>
					<p>
						<label>
							%s (<a href='%s' target='_blank'>%s</a>) <br><input type=\"text\" name=\"lavaLicense[$addon->name]\" size=30>
						</label>
					</p>
					<p>
						<button type=\"button\" class=\"lava-addon-input-license button button-primary\" data-slug=\"{$addon->name}\">
							%s
						</button>
					</p>
				</dv>"
				, __( "Email (registered in Lava code when purchased)", 'Lavacode' )
				, esc_attr( get_bloginfo( 'admin_email' ) )
				, __( "License Key", 'Lavacode' )
				, esc_url_raw( 'http://lava-code.com/directory/document/how-to-activate-addons-and-add-your-licence-key/' )
				, __( "Unregistered", 'Lavacode' )
				, __( "Register", 'Lavacode' )

			); ?>
		</div>
	<?php endif; ?>
</div>