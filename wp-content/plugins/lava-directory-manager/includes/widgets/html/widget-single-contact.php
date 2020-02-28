<div id='lava-single-contact-widget'>

	<div class='author-avatar'>
		<img src="<?php echo $post->avatar; ?>">
	</div>
	<div class='author-name'><?php echo $post->display_name; ?></div>
	<?php if( $contact_phone = $post->_phone1 ): ?>
		<div class='author-phone'>
			<?php printf( "<a href=\"%s\">%s</a>", esc_url( "tel:{$contact_phone}" ), $contact_phone ); ?>
		</div>
	<?php endif; ?>
	<?php if( $contact_email = $post->email ): ?>
		<div class='author-email'>
			<?php printf( "<a href=\"%s\">%s</a>", esc_url( "mailto:{$contact_email}" ), $contact_email ); ?>
		</div>
	<?php endif; ?>
	<div class="author-properties">
		<?php printf( __( "%d properties", 'Lavacode' ), $post->item_count ); ?>
	</div>

</div>