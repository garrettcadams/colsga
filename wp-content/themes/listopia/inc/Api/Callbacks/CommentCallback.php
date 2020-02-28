<?php
/**
 * Callbacks for Comment API
 *
 *
 */

namespace Awps\Api\Callbacks;

/**
 * Settings API Callbacks Class
 */
class CommentCallback
{
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own jvbpd_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Javo Themes 1.0
 *
 * @return void
 */

    static function jvbpd_comment( $comment, $args, $depth ) {
        $GLOBALS['comment'] = $comment;
        switch ( $comment->comment_type ) :
            case 'pingback' :
            case 'trackback' :
            // Display trackbacks differently than normal comments.
        ?>


        <li <?php comment_class( 'jv-single-post-comment-item' ); ?> id="comment-<?php comment_ID(); ?>">
        <p><?php esc_html_e( 'Pingback:', 'jvbpd' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'jvbpd' ), '<span class="edit-link">', '</span>' ); ?></p>
        <?php
        break;
        default :
        // Proceed with normal comments.
        global $post;
        ?>
        <article <?php comment_class( 'jv-single-post-comment-item' ); ?> id="comment-<?php comment_ID(); ?>">
            <div class="comment-content post-content" itemprop="text">
                <figure class="gravatar"><?php echo get_avatar( $comment, 75 ); ?></figure>
                <div class="comment-meta post-meta" role="complementary">
                    <div class="comment-author h5 text-bold">
                        <span class="comment-author-link"><b class="fn"><?php echo get_comment_author_link(); ?></b></span>
                    </div>
                    <time class="comment-meta-item" datetime="<?php echo get_comment_date(); ?>" itemprop="datePublished">
                        <span><?php echo get_comment_date(); ?>, <a href="#comment-<?php comment_ID();?>" itemprop="url"><?php echo get_comment_time(); ?></a></span>
                    </time>

                    <p><?php comment_text() ?></p>
                     <?php if ( '0' == $comment->comment_approved ) : ?>
                        <p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'jvbpd' ); ?></p>
                    <?php endif; ?>
                    <?php
                     echo join( "\n", Array(
                        sprintf( '<div class="comment-reply inline-block">%s</div>',
                            get_comment_reply_link(
                                Array_merge( $args,
                                    Array(
                                        'reply_text'	=> esc_html__( 'Reply', 'jvbpd' ),
                                        'depth'			=> $depth,
                                        'max_depth'	=> $args['max_depth']
                                    )
                                )
                            )
                        ),
                        sprintf( "<a href=\"%s\" class='edit-button inline-block'>%s</a>", get_edit_comment_link( get_comment_ID() ), esc_html__( "Edit", 'jvbpd' ) ),
                     ) );
                    ?>
                </div>
            </div>
        </article>

        <?php
        break;
        endswitch; // end comment_type check
    }
}