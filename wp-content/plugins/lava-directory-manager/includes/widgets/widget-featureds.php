<?php
class Lava_Directory_Featured extends WP_Widget
{
	static $load_script;
	function __construct()
	{
		parent::__construct(
			'Lava_Directory_Featured'
			, __( "[Lava] Featured Listings Widget", 'Lavacode' )
			, array( 'description' => __( "This widget shows a list of featured listings as box style.", 'Lavacode' ) )
		);

		add_action( 'wp_footer'								, Array(__CLASS__, 'scripts' ) );
	}

	public function widget( $args, $instance )
	{
		global $lava_directory_manager;
		self::$load_script	= true;

		$instance	= wp_parse_args($instance , Array(
			'widget_title'=>__('Featured','Lavacode')
			,'count'	=> 3			
			, 'post_type' => 'lv_listing'
		) );

		$lava_posts_args = Array(
			'post_type' => $instance['post_type']
			, 'posts_per_page' => $instance['count']
			,  'meta_query' => array(
					array(
						'key' => '_featured_item',
						'value' => '1'
					)
				)
			, 'post_status' => 'publish'
			
		);
		$widget_title = $instance['widget_title'];
		$lava_posts = new WP_Query($lava_posts_args);

		ob_start();
		echo $args[ 'before_widget' ];
		?>
		<div class="lava-featured-widget">
			<div class="lava-featured-widget-title">
				<h3><?php echo $widget_title; ?></h3>
			</div><!-- lava-featured-widget-title -->
		<?php
				if( $lava_posts->have_posts() )
				{
					while( $lava_posts->have_posts() )
					{
						$lava_posts->the_post();
						?>

						<div class="latest-posts posts">
							<div class="lava-featured-widget-content thumb-content">
								<span class='thumb'>
									<a href="<?php echo get_permalink(); ?>">
										<?php
										if( has_post_thumbnail() )
										{
											the_post_thumbnail( array(300,300) );
										}
										else
										{
											printf('<img src="%s" class="wp-post-image" style="width:250px; height:250px;">',apply_filters( 'lava_directory_listing_featured_no_image', $lava_directory_manager->image_url . 'no-image.png' ));
										} ?>
									</a>
								</span>
							</div><!-- lava-recent-widget-content thumb-content -->
							<div class="lava-featured-widget-content text-content">
								<?php
									printf('<h3><a href="%s">%s</a></h3><span class="lava-featured-description">%s</span> <a href="%s">%s</a>'
										, get_permalink()
										, get_the_title()
										, get_the_excerpt()!='' ? substr(get_the_excerpt(), 0, 70 ).(strlen(get_the_excerpt()) > 70 ? '...' : '') : 'Not found description'
										, get_permalink()
										, get_the_excerpt()!='' ? '&nbsp;&nbsp;'.__('Read more','Lavacode') : ''
										
									); ?>
							</div><!-- lava-recent-widget-content text-content -->
						</div><!-- latest-posts posts -->
						<?php
					}
				}
				else
				{
					_e('Not Found Posts.', 'Lavacode');
				}
				?>
			</div><!-- lava-featured-widget -->
		<?php
		wp_reset_query();
		echo $args[ 'after_widget' ];
		ob_end_flush();
	}

	public static function scripts()
	{
		if( ! self::$load_script )
			return;

		// Todo : insert your code here.
	}

	public function form( $instance )
	{
		$lava_wg_fields					= Array(
				'widget_title' => Array(
					'label'				=> __( "Title", 'Lavacode' )
					, 'type'			=> 'text'
				)
				,'post_type' => Array(
					'label'				=> __( "Post type", 'Lavacode' )
					, 'type'			=> 'radio'
					, 'value'			=>
						Array(
							'lv_listing'	=> __( "Item", 'Lavacode' )
							, 'post'	=> __( "Post", 'Lavacode' )

						)
				)
				,'count' => Array(
					'label'				=> __( "count", 'Lavacode' )
					, 'type'			=> 'number'
				)
		);

		$output_html			= Array();

		if( !empty( $lava_wg_fields ) )
		{
			foreach( $lava_wg_fields as $id => $options )
			{
				if( $options === 'separate' ) {
					$output_html[]	= "<hr>"; continue;
				}
				$values				= isset( $instance[ $id ] ) ? esc_attr( $instance[ $id ] ) : null;
				$output_html[]		= "<p>";
				$output_html[]		= "<label for=\"" . $this->get_field_id( $id ) . "\">{$options['label']}</label>";

				switch( $options['type'] )
				{
					case 'radio':
						if( !empty( $options['value'] ) )
							foreach( $options['value'] as $value => $label )
								$output_html[]	= "<input id=\"" . $this->get_field_id( $id ) . "\" name=\"" . $this->get_field_name( $id ) . "\" type=\"{$options['type']}\" value=\"{$value}\"" . checked( $values == $value, true, false ) . "> {$label}";
					break;

					case 'number':
					case 'text':
					default:
						$output_html[]	= "<input id=\"" . $this->get_field_id( $id ) . "\" name=\"" . $this->get_field_name( $id ) . "\" type=\"{$options['type']}\" value=\"{$values}\">";
				}

				$output_html[]	= "</p>";
			}

			echo @implode( "\n", $output_html );
		}
	}

	public function update( $new_instance, $old_instance ) { return $new_instance; }

} // class Foo_Widget