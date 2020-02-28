<?php

namespace WilcityWidgets\App;


class SimpleListListings extends \WP_Widget {
	public $aDef = array('title'=>'', 'orderby'=>'post_date', 'order'=>'DESC', 'number_of_posts'=>4, 'post_ids'=>'', 'related_by'=>'');

	public function __construct() {
		// Instantiate the parent object
		parent::__construct( 'wilcity_simple_list_listings', WILCITY_WIDGET . ' Simple List Listings');
	}

	public function form( $aInstance ) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);

		$aOrderbyOptions = array(
			'post_date' => 'Post Date',
			'post_title'=> 'Post Title',
			'menu_order'=> 'Menu Order',
			'comment_count'=> 'Number of comments',
			'post__in' => 'Specify Post Ids'
		);

		$aRelateOptions = array(
			'category' => 'Category',
			'tag'=> 'Tag',
			'ignore'=> 'I do want to use Related By feature'
		);

		$aOrderOptions = array(
			'DESC' => 'DESC',
			'ASC'  => 'ASC'
		);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>">Order By</label>
			<select class="widefat" name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
				<?php foreach ($aOrderbyOptions as $orderby => $name): ?>
					<option value="<?php echo esc_attr($orderby); ?>" <?php selected($orderby, $aInstance['orderby']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>">Order</label>
			<select class="widefat" name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<?php foreach ($aOrderOptions as $order => $name): ?>
					<option value="<?php echo esc_attr($order); ?>" <?php selected($order, $aInstance['order']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('related_by'); ?>">Related By</label>
			<i>This option is not available for Order by Specify posts option.</i>
			<select class="widefat" name="<?php echo $this->get_field_name('related_by'); ?>" id="<?php echo $this->get_field_id('related_by'); ?>">
				<?php foreach ($aRelateOptions as $relate => $name): ?>
					<option value="<?php echo esc_attr($relate); ?>" <?php selected($relate, $aInstance['related_by']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_ids'); ?>">Post IDs</label>
			<i>This option is available for Order by Specify posts only. For example: 1,2,3</i>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('post_ids'); ?>" id="<?php echo $this->get_field_id('post_ids'); ?>" value="<?php echo esc_attr($aInstance['post_ids']); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number_of_posts'); ?>">Number of posts</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('number_of_posts'); ?>" id="<?php echo $this->get_field_id('number_of_posts'); ?>" value="<?php echo esc_attr($aInstance['number_of_posts']); ?>">
		</p>
		<?php
	}

	public function widget( $aAtts, $aInstance ) {
		global $post;

		$aArgs = array(
			'post_type'         => 'post',
			'posts_per_page'    => $aInstance['number_of_posts'],
			'post_status'       => 'publish',
			'orderby'           => $aInstance['orderby'],
			'order'             => $aInstance['order']
		);

		if ( $aInstance['orderby'] == 'post__in' ){
			$aParsePosts = explode(',', $aInstance['post_ids']);
			$aArgs['post__in'] = array_map(function($postID){
				return trim($postID);
			}, $aParsePosts);
		}

		if ( $aInstance['related_by'] !== 'ignore' && isset($post->ID) ){
			$aTerms = wp_get_post_terms($post->ID, $aInstance['related_by']);
			if ( !empty($aTerms) && !is_wp_error($aTerms) ){
				$aTermsIn = array();
				foreach ($aTerms as $oTerm){
					$aTermsIn[] = $oTerm->term_id;
				}
				$aArgs['tax_query'] = array(
					array(
						'taxonomy' => $aInstance['related_by'],
						'field'    => 'term_id',
						'terms'    => $aTermsIn
					)
				);
				$aArgs['post__not_in'] = array($post->ID);
			}
		}
		$query = new \WP_Query($aArgs);
		if ( !$query->have_posts() ){
			wp_reset_postdata();
			return '';
		}

		if ( !function_exists('wilcityWidgetListStyle') ){
			\WilokeMessage::message(array(
				'msg' => 'The Wilcity Shortcodes is required. Please go to Appearance -> Install Plugins to activate it'
			));
			echo $aAtts['after_widget'];
			return '';
		}

		echo $aAtts['before_widget'];
		if ( !empty($aInstance['title']) ){
			echo $aAtts['before_title'] . $aInstance['title'] . $aAtts['after_title'];
		}
		?>
		<ul>
			<?php
			while ( $query->have_posts() ){
				$query->the_post();
				echo '<li><a href="'.get_permalink($query->post->ID).'">'.get_the_title($query->post->ID).'</a></li>';
			}
			wp_reset_postdata();
			?>
		</ul>
		<?php
		echo $aAtts['after_widget'];
	}

	public function update( $aNewInstance, $aOldInstance ) {
		$aInstance = $aOldInstance;
		foreach ($aNewInstance as $key => $val){
			$aInstance[$key] = strip_tags($val);
		}
		return $aInstance;
	}
}