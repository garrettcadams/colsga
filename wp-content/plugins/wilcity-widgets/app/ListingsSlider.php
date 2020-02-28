<?php

namespace WilcityWidgets\App;


use WILCITY_SC\SCHelpers;
use WilokeListingTools\Framework\Helpers\General;

class ListingsSlider extends \WP_Widget {
	public $aDef = array('title'=>'', 'post_type' => 'listing', 'orderby'=>'post_date', 'order'=>'DESC', 'number_of_posts'=>4, 'post_ids'=>'', 'taxonomy'=>'', 'autoplay'=>'yes', 'img_size'=>'medium');

	public function __construct() {
        add_filter('wilcity/article-class', array($this, 'articleCssClass'), 10, 2);
		parent::__construct( 'wilcity_listings_slider', WILCITY_WIDGET . ' Listings Slider');
	}

	public function articleCssClass($style, $aAtts){
        if ( !isset($aAtts['isSlider']) ){
            return $style;
        }

        return 'listing_module__2EnGq wil-shadow js-listing-module';
    }

	public function form( $aInstance ) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);

		$aPostTypes = General::getPostTypeOptions(false, true);

		$aOrderbyOptions = array(
			'post_date'     => 'Post Date',
			'post_title'    => 'Post Title',
			'menu_order'    => 'Premium Listings Only',
			'menu_order post_date'    => 'Order By Menu Order, The latest listings fallback',
			'menu_order best_rated'   => 'Order By Menu Order, The best rated listings fallback',
			'menu_order best_viewed'  => 'Order By Menu Order, The best viewed listings fallback',
			'menu_order best_shared'  => 'Order By Menu Order, The best shared listings fallback',
			'best_viewed'   => 'Popular Viewed',
			'best_rated'    => 'Popular Rated',
			'best_shared'   => 'Popular Shared',
			'post__in'      => 'Specify Post Ids'
		);

		$aTaxonomies = array(
			'listing_location'  => 'Listing Location',
			'listing_category'  => 'Listing Category',
			'listing_tag'       => 'Listing Tag',
			'self_taxonomy'     => 'Self Taxonomy',
			'ignore'            => 'I do want to use Related By feature'
		);

		$aOrderOptions = array(
			'DESC' => 'DESC',
			'ASC'  => 'ASC'
		);
		?>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('post_type'); ?>">Post Type</label>
			<select class="widefat" name="<?php echo $this->get_field_name('post_type'); ?>" id="<?php echo $this->get_field_id('post_type'); ?>">
				<option value="all" <?php selected('all', $aInstance['post_type']); ?>>All Listing Types</option>
				<?php foreach ($aPostTypes as $option => $name): ?>
					<option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aInstance['post_type']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('orderby'); ?>">Order By</label>
			<select class="widefat" name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
				<?php foreach ($aOrderbyOptions as $orderby => $name): ?>
					<option value="<?php echo esc_attr($orderby); ?>" <?php selected($orderby, $aInstance['orderby']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('order'); ?>">Order</label>
			<select class="widefat" name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<?php foreach ($aOrderOptions as $order => $name): ?>
					<option value="<?php echo esc_attr($order); ?>" <?php selected($order, $aInstance['order']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>">Related By</label>
			<p><i>Self Taxonomy option: For instance, if a customer is in Listing Location page, it will get the listings that belongs to Listing Location category. But the he/she visits a Listing Category page, it will get the listings belongs to Listing Category. In other words, it depends on where the taxonomy page your customer in.</i></p>
			<select class="widefat" name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>">
				<?php foreach ($aTaxonomies as $option => $name): ?>
					<option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aInstance['taxonomy']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('post_ids'); ?>">Post IDs</label>
			<p><i>This option is available for Order by Specify posts only. For example: 1,2,3</i></p>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('post_ids'); ?>" id="<?php echo $this->get_field_id('post_ids'); ?>" value="<?php echo esc_attr($aInstance['post_ids']); ?>">
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('number_of_posts'); ?>">Number of posts</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('number_of_posts'); ?>" id="<?php echo $this->get_field_id('number_of_posts'); ?>" value="<?php echo esc_attr($aInstance['number_of_posts']); ?>">
		</div>
        <div class="widget-group">
			<label for="<?php echo $this->get_field_id('autoplay'); ?>">Auto Play?</label>
			<select class="widefat" name="<?php echo $this->get_field_name('autoplay'); ?>" id="<?php echo $this->get_field_id('autoplay'); ?>">
				<option value="yes" <?php selected('yes', $aInstance['autoplay']); ?>>Yes</option>
				<option value="no" <?php selected('no', $aInstance['autoplay']); ?>>No</option>
			</select>
		</div>
        <div class="widget-group">
            <label for="<?php echo $this->get_field_id('img_size'); ?>">Image Size</label>
            <input type="text" class="widefat" name="<?php echo $this->get_field_name('img_size'); ?>" id="<?php echo $this->get_field_id('img_size'); ?>" value="<?php echo esc_attr($aInstance['img_size']); ?>">
            <p>EG: Enter in image size key like large, medium, thumbnail</p>
        </div>
		<?php
	}

	public function widget( $aAtts, $aInstance ) {
		global $post;

		if ( $aInstance['post_type'] == 'all' ){
			$aPostTypes = General::getPostTypeKeys(false, true);
		}else{
			$aPostTypes = $aInstance['post_type'];
		}

		$aArgs = array(
			'post_type'         => $aPostTypes,
			'posts_per_page'    => $aInstance['number_of_posts'],
			'post_status'       => 'publish',
			'orderby'           => $aInstance['orderby'],
			'order'             => $aInstance['order']
		);

		$aAtts['img_size'] = SCHelpers::parseImgSize($aInstance['img_size']);
		if ( $aInstance['orderby'] == 'post__in' ){
			$aParsePosts = explode(',', $aInstance['post_ids']);
			$aArgs['post__in'] = array_map(function($postID){
				return trim($postID);
			}, $aParsePosts);
		}

		if ($aInstance['taxonomy'] == 'self_taxonomy'){
		    if ( is_tax() ){
			    $aArgs['tax_query'] = array(
				    array(
					    'taxonomy' => get_query_var( 'taxonomy' ),
					    'field'    => 'slug',
					    'terms'    => get_query_var( 'term' )
				    )
			    );
            }
		}else if ( $aInstance['taxonomy'] !== 'ignore' ){
		    if ( isset($post->ID) ){
			    $aTerms = wp_get_post_terms($post->ID, $aInstance['taxonomy']);
			    if ( !empty($aTerms) && !is_wp_error($aTerms) ){
				    $aTermsIn = array();
				    foreach ($aTerms as $oTerm){
					    $aTermsIn[] = $oTerm->term_id;
				    }
				    $aArgs['tax_query'] = array(
					    array(
						    'taxonomy' => $aInstance['taxonomy'],
						    'field'    => 'term_id',
						    'terms'    => $aTermsIn
					    )
				    );
			    }
            }

            return '';
		}
		$query = new \WP_Query($aArgs);
		if ( !$query->have_posts() ){
			wp_reset_postdata();
			return '';
		}
		echo $aAtts['before_widget'];
			if ( !empty($aInstance['title']) ): ?>
                <?php echo $aAtts['before_title']; ?><i class="la la-th-list"></i><span><?php echo esc_html($aInstance['title']); ?></span><?php echo $aAtts['after_title']; ?>
			<?php endif ?>
            <?php
            if ( !function_exists('wilcity_render_grid_item') ):
                \WilokeMessage::message(array(
                    'status' => 'danger',
                    'msg' => 'Please click on Appearance -> Instal Plugins -> Active Wilcity Shortcode plugin',
                    'icon' => 'la la-frown-o'
                ));
            else: ?>
			<div class="content-box_body__3tSRB">
				<div class="swiper__module swiper-container swiper--button-abs" data-options='{"slidesPerView":"auto","spaceBetween":10,"speed":1000,"autoplay":<?php if ( $aInstance['autoplay'] == 'yes' ) : ?>true<?php else: ?>false<?php endif; ?>,"loop":true}'>
					
					<div class="full-load">
						<!-- pill-loading_module__3LZ6v -->
						<div class="pill-loading_module__3LZ6v pos-a-center">
							<div class="pill-loading_loader__3LOnT"></div>
						</div><!-- End / pill-loading_module__3LZ6v -->
					</div>

					<div class="swiper-wrapper">
						<?php
                        while ( $query->have_posts() ){
                            $query->the_post();
                            wilcity_render_grid_item($query->post, array('img_size'=>$aAtts['img_size'], 'isSlider'=>true, 'style'=>'grid'));
                        } wp_reset_postdata();
                        ?>
					</div>
                    <div class="swiper-button-custom">
                        <div class="swiper-button-prev-custom"><i class='la la-angle-left'></i></div>
                        <div class="swiper-button-next-custom"><i class='la la-angle-right'></i></div>
                    </div>
				</div>
			</div>
            <?php endif; ?>
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