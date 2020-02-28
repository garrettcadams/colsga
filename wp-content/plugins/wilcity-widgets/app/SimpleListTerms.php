<?php
namespace WilcityWidgets\App;

class SimpleListTerms extends \WP_Widget {
	public $aDef = array('title'=>'', 'taxonomy' => 'listing', 'orderby'=>'count', 'order'=>'DESC', 'number_of_terms'=> 7);

	public function __construct() {
		parent::__construct( 'wilcity_simple_list_terms', WILCITY_WIDGET . ' Simple List Terms');
	}

	public function form( $aInstance ) {
		$aInstance = wp_parse_args($aInstance, $this->aDef);
		$aOrderBy = array('count'=>'Count', 'id'=>'Id', 'slug'=>'Slug', 'name'=>'Name', 'none'=>'None')
		?>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('title'); ?>">Title</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($aInstance['title']); ?>">
		</div>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('taxonomy'); ?>">Taxonomy</label>
			<select class="widefat" name="<?php echo $this->get_field_name('taxonomy'); ?>" id="<?php echo $this->get_field_id('taxonomy'); ?>">
				<?php foreach (array('listing_cat'=>'Listing Category', 'listing_location'=>'Listing Location') as $option => $name): ?>
					<option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aInstance['taxonomy']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('number_of_terms'); ?>">Number of terms</label>
			<input type="text" class="widefat" name="<?php echo $this->get_field_name('number_of_posts'); ?>" id="<?php echo $this->get_field_id('number_of_terms'); ?>" value="<?php echo esc_attr($aInstance['number_of_terms']); ?>">
		</div>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('orderby'); ?>">Order By</label>
			<select class="widefat" name="<?php echo $this->get_field_name('orderby'); ?>" id="<?php echo $this->get_field_id('orderby'); ?>">
				<?php foreach ($aOrderBy as $option => $name): ?>
					<option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aInstance['orderby']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="widget-group">
			<label for="<?php echo $this->get_field_id('order'); ?>">Order</label>
			<select class="widefat" name="<?php echo $this->get_field_name('order'); ?>" id="<?php echo $this->get_field_id('order'); ?>">
				<?php foreach (array('DESC'=>'DESC', 'ASC'=>'ASC') as $option => $name): ?>
					<option value="<?php echo esc_attr($option); ?>" <?php selected($option, $aInstance['orderby']); ?>><?php echo esc_html($name); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php
	}

	public function widget( $aAtts, $aInstance ) {
		$aTerms = get_terms(
			array(
				'taxonomy' => $aInstance['taxonomy'],
				'number'   => $aInstance['number_of_terms'],
				'hide_empty' => 0,
				'orderby'   => $aInstance['orderby'],
				'order'     => $aInstance['order']
			)
		);

		if ( empty($aTerms) || is_wp_error($aTerms) ){
			return '';
		}

		echo $aAtts['before_widget'];
            if ( !empty($aInstance['title']) ){
                echo $aAtts['before_title']; ?><i class="la la-th-list"></i><span><?php echo esc_html($aInstance['title']); ?><?php echo $aAtts['after_title'];
            }
            echo '<ul>';
	            foreach ($aTerms as $oTerm){
					echo '<li><a href="'.esc_url(get_term_link($oTerm->term_id)).'">'.$oTerm->name.'</a></li>';
	            }
			echo '</ul>';
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