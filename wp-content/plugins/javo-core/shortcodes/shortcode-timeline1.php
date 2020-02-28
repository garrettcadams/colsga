<?php
class jvbpd_timeline1 extends Jvbpd_Shortcode_Parse
{
	public function output( $attr, $content='' )
	{
		parent::__construct( $attr ); ob_start();
		$this->sHeader();
		?>
		<div id="<?php echo $this->sID; ?>" class="shortcode-container fadein">
			<div class="shortcode-header">
				<div class="shortcode-title">
					<?php echo $this->title; ?>
				</div>
				<div class="shortcode-nav">
					<?php $this->sFilter(); ?>
				</div>
			</div>
			<div class="row shortcode-output">
				<ul class="timeline-item-group">
					<?php $this->loop( $this->get_post() ); ?>
				</ul>
			</div>
		</div>
		<?php
		$this->sFooter(); return ob_get_clean();
	}

	public function loop( $queried_posts )
	{
		// Query Start
		if( ! empty( $queried_posts ) ) : foreach( $queried_posts as $index => $post ) {
			$objModule1	= new module1(
				$post,
				Array(
					'hide_content'		=> true,
					'hide_meta'			=> true,
					'hide_thumbnail'		=> true,
				)
			);
			echo join( "\n",
				Array(
					'<li class="timeline-item">',
					sprintf( "
						<a href=\"%s\" title=\"%s\">
							<span class=\"jv-data\">
								<span class=\"jv-date\">%s</span>
								<span class=\"jv-date-year\">%s</span>
								<i class=\"fa fa-clock-o\"></i> %s
							</span>
						</a>",
						get_permalink( $post->ID ),
						esc_attr( get_the_title( $post->ID ) ),
						get_the_date( 'M d', $post->ID ),
						get_the_date( 'Y', $post->ID ),
						$objModule1->output()
					),
					'</li>'
				)
			);
		} endif;
		$this->sParams();
		$this->pagination();
	}
}