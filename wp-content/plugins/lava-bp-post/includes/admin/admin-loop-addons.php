<div class="addon-wrap">
	<div class="thumb-wrap">
		<img src="<?php echo $addon->thumbnail;?>">
	</div>
	<h2 class="addon-title">
		<?php echo $addon->label; ?>
	</h2>
	<div class="addon-meta">
		<div class="addon-new">
			<span class="addon-new-version"><?php printf( esc_html__( "Current Version : %s", 'lvbp-bp-post' ), $addon->version ); ?></span> <br>
		</div>
		<div class="addon-new">
			<span class="addon-new-version"><?php printf( esc_html__( "Lastest Version : %s", 'lvbp-bp-post' ), $addon->lastest_version ); ?></span> <br>
		</div>
	</div>
	<div class="addon-action">
		<?php
		if( version_compare( $addon->lastest_version, $addon->version, '>' ) ) {
			printf( "<a href=\"%s\"class=\"button button-primary\">%s</a>"
				, esc_url( network_admin_url( 'update-core.php' ) )
				, __( "Update Plugin Page", 'lvbp-bp-post' )
			);
		}else{
			printf( "<div class='addon-lastest-button'>%s</div>"
				, __( "Lastest Version", 'lvbp-bp-post' )
			);
		}

		if( $addon->license_active )
			printf( "&nbsp;<button type=\"button\" class=\"lava-addon-deactive-license button\" data-slug=\"{$slug}\">%s</button>", __( "Deactivate License", 'lvbp-bp-post' ) );
		?>
	</div>
	<?php if( !$addon->license_active ) : ?>
		<div class="addon-activator">
			<?php
			printf( "
				<div class=\"lava-addons-license-field\">
					<p>
						<label>
							%s <br><input type=\"email\" name=\"lavaLicense[$slug]\" value=\"%s\" size=30>
						</label>
					</p>
					<p>
						<label>
							%s (<a href='%s' target='_blank'>%s</a>) <br><input type=\"text\" name=\"lavaLicense[$slug]\" size=30	>
						</label>
					</p>
					<p>
						<button type=\"button\" class=\"lava-addon-input-license button button-primary\" data-slug=\"{$slug}\">
							%s
						</button>
					</p>
				</dv>"
				, __( "Email (registered in Lava code when purchased)", 'lvbp-bp-post' )
				, esc_attr( get_bloginfo( 'admin_email' ) )
				, __( "License Key", 'lvbp-bp-post' )
				, esc_url_raw( 'http://lava-code.com/directory/document/how-to-activate-addons-and-add-your-licence-key/' )
				, __( "Unregistered", 'lvbp-bp-post' )
				, __( "Register", 'lvbp-bp-post' )

			); ?>
		</div>
	<?php endif; ?>
</div>