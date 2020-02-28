<div class="form-inner">
	<label class="field-title"><?php _e("Login", "Lavacode"); ?></label>
	<?php _e( "If you have an account?", 'Lavacode'); ?>&nbsp;
	<a href="<?php echo $lava_loginURL; ?>"> <?php _e( "Please Login", 'Lavacode' ); ?> </a>
</div>

<div class="form-inner">
	<label class="field-title"><?php _e("User Email", "Lavacode"); ?></label>
	<input name="user_email" type="email" value="" placeholder="<?php _e( "Email Address",'Lavacode' ); ?>">
</div>

<div class="form-inner">
	<label class="field-title"><?php _e("User Password", "Lavacode"); ?></label>
	<input name="user_pass" type="password" value="" placeholder="<?php _e( "Password",'Lavacode' ); ?>">
</div>