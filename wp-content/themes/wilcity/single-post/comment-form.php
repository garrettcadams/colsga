<?php
global $post;
if ( comments_open() && post_type_supports( get_post_type(), 'comments' ) ) {
	$commenter                         = wp_get_current_commenter();
	$commenter['comment_author']       = $commenter['comment_author'] == '' ? '' : $commenter['comment_author'];
	$commenter['comment_author_email'] = $commenter['comment_author_email'] == '' ? '' : $commenter['comment_author_email'];
	$commenter['comment_author_url']   = $commenter['comment_author_url'] == '' ? '' : $commenter['comment_author_url'];

	$req      = get_option( 'require_name_email' );
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$sao      = ( $req ? " <sup>*</sup>" : '' );

	$fields = array(
		'author' => '<div class="col-xs-6"><div class="field_module__1H6kT field_style2__2Znhe mb-15 js-field"><div class="field_wrap__Gv92k"><input type="text" id="author" class="field_field__3U_Rt" name="author" value="' . esc_attr( $commenter['comment_author'] ) . '" ' . $aria_req . ' /><span class="field_label__2eCP7 text-ellipsis required">'.esc_html__('Name', 'wilcity').'</span></div></div></div>',
		'email'  => '<div class="col-xs-6"><div class="field_module__1H6kT field_style2__2Znhe mb-15 js-field"><div class="field_wrap__Gv92k"><input type="email" id="email" class="field_field__3U_Rt" name="email" value="' . esc_attr( $commenter['comment_author_email'] ) . '" ' . $aria_req . ' /><span class="field_label__2eCP7 text-ellipsis required">'.esc_html__('Email', 'wilcity').'</span></div></div></div>',
	);

	$comment_field = '<div class="col-xs-12 mt-12"><div class="field_module__1H6kT field_style2__2Znhe mb-15 js-field"><div class="field_wrap__Gv92k"><textarea id="comment" name="comment" class="field_field__3U_Rt" required></textarea><span class="field_label__2eCP7 text-ellipsis">'.esc_html__('Comment', 'wilcity').'</span><span class="bg-color-primary"></span></div></div></div>';

	$userName = '';
	if( is_user_logged_in() ){
		$oUserInfo = get_userdata(get_current_user_id());
		$userName = $oUserInfo->display_name;
	}

	$comment_args = array(
		'fields'               => $fields,
		'title_reply_before'   => '<h6>',
		'title_reply'          => esc_html__( 'Leave your comment', 'wilcity' ),
		'title_reply_after'    => '</h6>',
		'comment_field'        => $comment_field,
		'comment_notes_after'  => '',
		'comment_notes_before' => '',
		'submit_field'         => '<div class="col-xs-12">%1$s %2$s</div>',
		'submit_button'        => '<input name="%1$s" type="submit" id="%2$s" class="wil-btn wil-btn--md wil-btn--gray wil-btn--block wil-btn--round %3$s" value="%4$s" />',
		'logged_in_as'         => '<div class="col-xs-12 form-login-logout">' . Wiloke::ksesHTML( sprintf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'wilcity' ), get_edit_user_link(), $userName, wp_logout_url( apply_filters( 'the_permalink', esc_url( get_permalink( $post->ID ) ) ) ) ), true ) . '</div>'
	);
	
	comment_form( $comment_args );

}