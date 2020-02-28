<?php
add_shortcode('wilcity_sharing_post', 'wilcitySharingPosts');
function wilcitySharingPosts($aAtts){
	if ( !class_exists('\WilokeSocialNetworks') ){
		return '';
	}

	$aAtts = shortcode_atts(
		array(
			'post_id' => '',
            'style'   => 'list'
		),
		$aAtts
	);

	if ( empty($aAtts['post_id']) ){
		return '';
	}
	$title = get_the_title($aAtts['post_id']);
	$link = get_permalink($aAtts['post_id']);

	$aSocialLink = array(
	    'facebook' => add_query_arg(array(
            'u' => urlencode($link),
            't' => urlencode($title)
        ),'//facebook.com/sharer.php'),
        'twitter'  => add_query_arg(
            array(
                'text' => urlencode($title) . '-' . urlencode($link),
                'source'    => 'webclient'
            ),
            '//twitter.com/intent/tweet'
        ),
        'google-plus' => add_query_arg(
            array(
                'url' => urlencode($link),
                'title' => urlencode($title),
                'source' => 'webclient'
            ),
            '//plus.google.com/share'
        ),
        'linkedin' => add_query_arg(
                array(
                    'mini' => 'true',
                    'url'  => urlencode($link),
                    'title'=> urlencode($title),
                    'source' => 'webclient'
                ),
            '//www.linkedin.com/shareArticle'
        ),
        'reddit' => add_query_arg(
            array(
                'url' => urlencode($link),
                'title' => urlencode($title),
                'source' => 'webclient'
            ),
            '//reddit.com/submit'
        ),
	    'digg' => add_query_arg(
		    array(
			    'url' => urlencode($link),
			    'title' => urlencode($title),
			    'source' => 'webclient'
		    ),
		    '//www.digg.com/submit'
	    ),
	    'stumbleupon' => add_query_arg(
		    array(
			    'url' => urlencode($link),
			    'title' => urlencode($title),
			    'source' => 'webclient'
		    ),
		    '//www.stumbleupon.com/submit'
	    ),
	    'tumblr' => add_query_arg(
		    array(
			    'url' => urlencode($link),
			    'name' => urlencode($title),
			    'source' => 'webclient'
		    ),
		    '//www.tumblr.com/share/link'
	    ),
	    'pinterest' => add_query_arg(
		    array(
			    'url' => urlencode($link),
			    'description' => urlencode($title),
			    'source' => 'webclient'
		    ),
		    '//pinterest.com/pin/create/button/'
	    ),
	    'vk' => add_query_arg(
		    array(
			    'url' => urlencode($link),
			    'description' => urlencode($title),
			    'source' => 'webclient'
		    ),
		    '//vk.com/share.php'
		),
		'whatsapp' => add_query_arg(
		    array(
			    'text' => urlencode($link),
		    ),
		    '//wa.me'
	    )
    );

	$aUsedSocialNetworks = WilokeSocialNetworks::getUsedSocialNetworks();
	$wrapperClass = apply_filters('wilcity/filter/class-prefix', 'wilcity-social-sharing-wrapper list_module__1eis9 list-none list_social__31Q0V list_medium__1aT2c list_abs__OP7Og arrow--top-right');
	$itemClass = apply_filters('wilcity/filter/class-prefix', 'wilcity-social-sharing list_link__2rDA1 text-ellipsis color-primary--hover');

	ob_start();
	if ( $aAtts['style'] == 'list' ) :
	?>
	<ul data-postid="<?php echo esc_attr($aAtts['post_id']); ?>" class="<?php echo esc_attr($wrapperClass); ?>">
        <?php
        foreach ($aUsedSocialNetworks as $socialKey) :
            if ( !isset($aSocialLink[$socialKey]) ){
                continue;
            }
	        $socialName = $socialKey == 'google-plus' ? 'Google+' : ucfirst($socialKey);
        ?>
            <li class="list_item__3YghP">
                <a target="_blank" class="<?php echo esc_attr($itemClass); ?>" href="<?php echo esc_url($aSocialLink[$socialKey]) ?>"><span class="list_icon__2YpTp"><i class="fa fa-<?php echo esc_attr($socialKey); ?>"></i></span><span class="list_text__35R07"><?php echo $socialName; ?></span></a>
            </li>
        <?php endforeach; ?>
		<li class="list_item__3YghP">
			<a class="<?php echo esc_attr($itemClass); ?>" href="mailto:?Subject=<?php echo str_replace(' ', '%20', $title); ?>&Body=<?php echo esc_url($link); ?>"><span class="list_icon__2YpTp"><i class="fa fa-envelope"></i></span><span class="list_text__35R07">Email</span></a>
		</li>
		<li class="list_item__3YghP"><a class="<?php echo esc_attr(apply_filters('wilcity/filter/class-prefix', 'wilcity-copy-link wilcity-social-sharing list_link__2rDA1 text-ellipsis color-primary--hover')); ?>" href="#" data-shortlink="<?php echo esc_url($link); ?>" data-desc="<?php esc_html_e('Press Ctrl+C to copy this link', 'wilcity-shortcodes'); ?>"><span class="list_icon__2YpTp"><i class="fa fa-link"></i></span><span class="list_text__35R07"><?php esc_html_e('Copy link', 'wilcity-shortcodes'); ?></span></a></li>
	</ul>
	<?php
    else:
    ?>
    <div class="social-icon_module__HOrwr social-icon_style-2__17BFy">
	    <?php
	    foreach ($aUsedSocialNetworks as $socialKey) :
		    if ( !isset($aSocialLink[$socialKey]) ){
			    continue;
		    }
		    ?>
            <a class="social-icon_item__3SLnb" target="_blank" href="<?php echo esc_url($aSocialLink[$socialKey]) ?>"><i class="fa fa-<?php echo esc_attr($socialKey); ?>"></i></a>
        <?php endforeach; ?>
    </div>
    <?php
    endif;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}