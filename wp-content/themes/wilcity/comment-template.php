<?php
$comments_args = array(
	'id_form'           => 'comment-respond',
	'class_form'        => 'comment-respond',
	'id_submit'         => 'submit',
	'class_submit'      => 'submit',
	'name_submit'       => 'submit',
	'title_reply_before'=> '<h3 class="comment-reply-title">',
	'title_reply_after' => '</h3>',
	'title_reply'       => is_singular('listing') ?  esc_html__( 'Leave your Review', 'wilcity') : esc_html__( 'Leave your comment', 'wilcity'),
	'title_reply_to'    => esc_html__( 'Leave a Reply to %s', 'wilcity'),
	'cancel_reply_link' => esc_html__( 'Cancel Review', 'wilcity' ),
	'label_submit'      => esc_html__( 'Post Comment', 'wilcity'),
	'format'            => 'xhtml',
	'comment_field' =>  '<p class="comment-form-comment"><textarea id="comment" placeholder="'.esc_attr__( 'Comment', 'wilcity' ).'" name="comment" cols="45" rows="8" aria-required="true">' .
	                    '</textarea></p>',

	'must_log_in' => '<p class="must-log-in">' .
	                 Wiloke::ksesHTML(sprintf(
		                 __( 'You must be <a href="%s">logged in</a> to post a comment.', 'wilcity' ),
		                 wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
	                 ), true) . '</p>',

	'logged_in_as' => '<p class="logged-in-as">' .
	                  Wiloke::ksesHTML(sprintf(
		                  __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'wilcity' ),
		                  admin_url( 'profile.php' ),
		                  $user_identity,
		                  wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
	                  ), true). '</p>',

	'comment_notes_before' => '<p class="comment-notes">' .
	                          esc_html__( 'Your email address will not be published.', 'wilcity' ) . ( $req ? '*' : '' ) .
	                          '</p>',

	'comment_notes_after' => '<p class="form-allowed-tags">' .
	                         Wiloke::ksesHTML(sprintf(
		                         __( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes: %s', 'wilcity' ),
		                         ' <code>' . allowed_tags() . '</code>'
	                         ), true) . '</p>',

	'fields' => apply_filters( 'comment_form_default_fields', $fields ),
);
comment_form($comments_args);