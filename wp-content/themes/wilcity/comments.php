<?php global $post; ?>
<?php if (post_password_required()) : ?>
    <p><?php esc_html_e( 'Post is password protected. Enter the password to view any comments.', 'wilcity' ); ?></p>
<?php else: ?>
	<?php if ( have_comments() ) :  ?>
        <ul id="comments-list" class="list-none">
			<?php
			wp_list_comments(
				array(
					'style'     => 'ul',
					'callback'  => array('WilokeComment', 'commentTemplates'),
					'max_depth' => get_option('thread_comments_depth')
				)
			);
			the_comments_pagination(array(
                'type' => 'list',
                'next_text' => '<i class="la la-angle-right"></i>',
                'prev_text' => '<i class="la la-angle-left"></i>'
            ));


			?>
        </ul>
	<?php endif; ?>
<?php endif; ?>
<?php get_template_part('single-post/comment-form'); ?>
