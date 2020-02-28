<div class="form-inner">
	<label class="field-title"><?php _e("Login", "lvbp-bp-post"); ?></label>
	<?php _e( "If you have an account?", 'lvbp-bp-post'); ?>&nbsp;
	<a href="<?php echo $lava_loginURL; ?>"> <?php _e( "Please Login", 'lvbp-bp-post' ); ?> </a>
</div>

<div class="form-inner">
	<label class="field-title"><?php _e("User Email", "lvbp-bp-post"); ?></label>
	<input name="user_email" type="email" value="" placeholder="<?php _e( "Email Address",'lvbp-bp-post' ); ?>">
</div>

<div class="form-inner">
	<label class="field-title"><?php _e("User Password", "lvbp-bp-post"); ?></label>
	<input name="user_pass" type="password" value="" placeholder="<?php _e( "Password",'lvbp-bp-post' ); ?>">
</div>