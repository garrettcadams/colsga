<?php

namespace WilcityWidgets\App;


class Instagram extends \WP_Widget {
	public $aDef = array( 'title' =>'Instagram', 'username'=>'', 'number_of_photos' => 3, 'cache_interval'=>'');
	public function __construct()
	{
		$args = array('classname'=>'widget_instagram widget_wiloke_instagram widget_photo', 'description'=>'');
		parent::__construct("wiloke_instagram", WILCITY_WIDGET . 'Instagram Feed ', $args);
	}

	public function form($aInstance)
	{
		$aInstance            = wp_parse_args( $aInstance, $this->aDef );
		$aInstagramSettings   = get_option('wiloke_instagram_settings');
		if ( isset($aInstagramSettings['access_token']) && !empty($aInstagramSettings['access_token']) )
		{
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
				<input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>">Username</label>
				<input type="text" class="widefat" name="<?php echo $this->get_field_name('username'); ?>" id="<?php echo $this->get_field_id('username'); ?>" value="<?php echo esc_attr($aInstance['username']); ?>">
				<i>Leave empty to use the default username</i>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number_of_photos'); ?>">Number Of Photos</label>
				<input type="text" class="widefat" name="<?php echo $this->get_field_name('number_of_photos'); ?>" id="<?php echo $this->get_field_id('number_of_photos'); ?>" value="<?php echo esc_attr($aInstance['number_of_photos']); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('cache_interval'); ?>">Cache Interval</label>
				<input type="text" class="widefat" name="<?php echo $this->get_field_name('cache_interval'); ?>" id="<?php echo $this->get_field_id('cache_interval'); ?>" value="<?php echo esc_attr($aInstance['cache_interval']); ?>">
				<i>Leave empty to clear cache</i>
			</p>
			<?php
		}else{
			?>
			<p>
				<code class="wiloke-help">Instagram Access Token is required. <a target="_blank" href="<?php echo esc_url(admin_url('options-general.php?page=wiloke-instagram')) ?>">Click me to provide it</a></code>
			</p>
			<?php
		}
	}

	public function update($aNewinstance, $aOldinstance)
	{
		$aInstance = $aOldinstance;
		foreach ( $aNewinstance as $key => $val )
		{
			if ( $key == 'number_of_photos' )
			{
				$aInstance[$key] = (int)$val;
			}else{
				$aInstance[$key] = strip_tags($val);
			}
		}

		return $aInstance;
	}

	public function widget( $atts, $aInstance )
	{
		$aInstance                  = wp_parse_args($aInstance, $this->aDef);
		$aInstagramSettings         = get_option('wiloke_instagram_settings');
		$aInstance['access_token']  = isset($aInstagramSettings['access_token']) ? $aInstagramSettings['access_token'] : '';
		$cacheInstagram = null;

		echo $atts['before_widget'];

		if ( !empty($aInstance['title']) )
		{
			echo $atts['before_title'] . esc_html($aInstance['title']) . $atts['after_title'];
		}
		?>
		<div class="widget-gallery">
			<?php
			if ( empty($aInstance['access_token']) )
			{
				if ( current_user_can('edit_theme_options') )
				{
					echo 'Please config your Instagram';
				}
			}else{
				if ( !empty($aInstance['username']) )
				{
					$type = 'username';
					$info = $aInstance['username'];
				}else{
					$type = 'self';
					$info = $aInstagramSettings['userid'];
				}

				if ( !empty($aInstance['cache_interval']) )
				{
					$cacheInstagram = get_transient('wiloke_cache_instagram_'.$info);
				}else{
					delete_transient('wiloke_cache_instagram_'.$info);
                }
				if ( !empty($cacheInstagram) )
				{
					echo $cacheInstagram;
				}else{
					$content = $this->parseInstagramFeed($info, $aInstance['access_token'], $aInstance['number_of_photos'], $type);
					echo $content;

					if ( !empty($aInstance['cache_interval']) )
					{
						set_transient('wiloke_cache_instagram_'.$info, $content, absint($aInstance['cache_interval']));
					}
				}
			}
			?>
		</div>
		<?php
		echo $atts['after_widget'];
	}

	public function getUserID($info, $accessToken, $args)
	{
		$url = 'https://api.instagram.com/v1/users/search?q='.$info.'&access_token='.$accessToken;
		$oSearchProfile = wp_remote_get( esc_url_raw( $url ), $args);
		if ( !empty($oSearchProfile) && !is_wp_error($oSearchProfile) )
		{
			$oSearchProfile = wp_remote_retrieve_body($oSearchProfile);
			$oSearchProfile = json_decode($oSearchProfile);

			if ( $oSearchProfile->meta->code === 200 )
			{
				foreach ( $oSearchProfile->data as $oInfo )
				{
					if ( $oInfo->username === $info )
					{
						return $oInfo->id;
					}
				}
			}
		}

		return '';
	}

	public function parseInstagramFeed($info, $accessToken, $count=6, $type='self')
	{
		$args = array( 'decompress' => false, 'timeout' => 30, 'sslverify'   => true );
		if ( $type == 'self' )
		{
			return $this->getPhotos($info, $accessToken, $count, $args);
		}else{
			$userID = $this->getUserID($info, $accessToken, $args);
			if ( !empty($userID) )
			{
				return $this->getPhotos($userID, $accessToken, $count, $args);
			}
		}
	}

	public function getPhotos($info, $accessToken, $count, $args)
	{
		$url   = 'https://api.instagram.com/v1/users/'.$info.'/media/recent?access_token='.$accessToken.'&count='.$count;

		$getInstagram = wp_remote_get( esc_url_raw( $url ), $args);
		if ( !is_wp_error($getInstagram) )
		{
			$getInstagram = wp_remote_retrieve_body($getInstagram);
			$getInstagram = json_decode($getInstagram);

			if ( $getInstagram->meta->code === 200 )
			{
				$out = '';
				for ( $i=0; $i<$count; $i++ )
				{
					$caption = isset($getInstagram->data[$i]->caption->text) ? $getInstagram->data[$i]->caption->text : 'Instagram';

					$out .= '<div class="widget-gallery__item"><a href="'.esc_url($getInstagram->data[$i]->link).'" target="_blank" style="background-image: url('. esc_url($getInstagram->data[$i]->images->thumbnail->url) .')"><img src="'.esc_url($getInstagram->data[$i]->images->thumbnail->url).'" alt="'.esc_attr($caption).'" /></a></div>';
				}
				return $out;
			}
		}

		return '';
	}
}