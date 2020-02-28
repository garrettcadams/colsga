<?php
use WilokeListingTools\Frontend\User as WilokeUser;

class WilokeComment{
	public static function commentTemplates($comment, $args, $depth){
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback':
				// Display trackbacks differently than normal comments.
				?>
                <li id="comment-<?php comment_ID(); ?>">
				    <div id="comment-<?php comment_ID(); ?>" <?php comment_class('comment-review_module__-Z5tr'); ?>>
				        <p><?php esc_html_e( 'Pingback:', 'wilcity' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'wilcity' ), '<span class="edit-link">', '</span>' ); ?></p>
                    </div>
				<?php
				break;
			default :
				$commentID   = get_comment_ID();
				$oAuthorInfo = get_comment($commentID);

				if ( !class_exists('WilokeListingTools\Frontend\User') ){
				    $authorIdentify = empty($oAuthorInfo->user_id) ? $oAuthorInfo->comment_author_email : $oAuthorInfo->user_id;
                    $avatar = get_avatar_url($authorIdentify, array('size'=>100));
                }else{
					$avatar = WilokeUser::getAvatar($oAuthorInfo->user_id);
                }
				?>
                <li id="comment-<?php comment_ID(); ?>">
			        <div <?php comment_class('comment-review_module__-Z5tr'); ?>>
                        <div class="comment-review_header__1si3M">
                            <!-- utility-box-1_module__MYXpX -->
                            <div class="utility-box-1_module__MYXpX utility-box-1_boxLeft__3iS6b clearfix utility-box-1_sm__mopok  review-author-avatar">
                                <div class="utility-box-1_avatar__DB9c_ rounded-circle" style="background-image: url(<?php echo esc_url($avatar); ?>);">
                                    <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($oAuthorInfo->comment_author); ?>"/>
                                </div>
                                <div class="utility-box-1_body__8qd9j">
                                    <div class="utility-box-1_group__2ZPA2">
                                        <h3 class="utility-box-1_title__1I925"><?php echo esc_attr($oAuthorInfo->comment_author); ?></h3>
                                    </div>
                                    <div class="utility-box-1_description__2VDJ6"><?php echo get_comment_date( get_option( 'date_format' ), $commentID ); ?></div>
                                </div>
                            </div><!-- End / utility-box-1_module__MYXpX -->
                        </div>
                        <div class="comment-review_body__qhUqq">
                            <div class="comment-review_content__1jFfZ">
                                <div><?php comment_text(); ?></div>
                            </div>
                        </div>
                        <footer class="comment-review_footer__3XR0_">
                            <div class="comment-review_btnGroup__1PqPh">
                                <div class="comment-review_btn__32CMP">
                                    <?php
                                    comment_reply_link( array_merge( $args, array( 'reply_text' => esc_html__( 'Reply', 'wilcity' ), 'before' => '<i class="la la-comments-o"></i> ', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
                                    ?>
                                </div>
                            </div>
                        </footer>
                    </div>

				<?php
				break;
		endswitch; // end comment_type check
	}
}