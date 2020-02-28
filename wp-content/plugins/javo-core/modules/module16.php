<?php
/**
 *
 *
 * @since	1.0
 */
class module16 extends Jvbpd_Module {

	const METAKEY = 'lv_';

	public $fields = Array();

	public function __construct( $post, $param=Array() ) {
		$this->lghTitle			= 10;
		$this->lghContent	= 30;
		if( function_exists( 'get_fields' ) ) {
			$this->fields = get_fields( $post->ID );
		}
		parent::__construct( $post, $param );
	}

	public function getFavoriteButton() {

		if( !class_exists( 'lvDirectoryFavorite_button' ) ) {
			return;
		}
		$instance = new lvDirectoryFavorite_button( Array(
			'format' => '{text}',
			'post_id' => $this->post->ID,
			'save' => "<i class='fa fa-heart'></i>",
			'unsave' => "<i class='fa fa-heart'></i>"
		) );
		$instance->output();
	}

	public function getCustomValue( $key='', $default=false ) {
		if( array_key_exists( self::METAKEY . $key, $this->fields ) ) {
			$default = false !== $this->fields[ self::METAKEY . $key ] ? $this->fields[ self::METAKEY . $key ] : $default;
		}
		return $default;
	}

	public function output() {
		ob_start();
		$jvbpd_item_category = $this->c( 'listing_category' );
		$jvbpd_item_location = $this->c( 'listing_location' );
		$jvbpd_author_name = $this->author_name;
		$acfFields = get_fields( $this->post->ID );
		?>
		<div <?php $this->classes( 'card' ); ?>>
			<?php $this->before(); ?>
			<div class="thumb">
				<?php echo $this->thumbnail( 'jvbpd-box-v', false, false, 'card-img-top img-responsive' ); ?>
				<ul class="detail-icons">
					<li>
						<a href="#" class="move-marker" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Find it", 'jvfrmtd' ); ?>">
							<i class="jvbpd-icon2-mark"></i>
						</a>
					</li>
					<li>
						<a href="<?php echo $this->permalink;?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Detail", 'jvfrmtd' ); ?>">
							<i class="jvbpd-icon1-link"></i>
						</a>
					</li>
					<li>
						<a href="#" class="javo-infow-brief" data-post-id="<?php echo $this->post_id; ?>" data-toggle="tooltip" data-placement="top" title="<?php esc_html_e( "Preview", 'jvfrmtd' ); ?>">
							<i class="jvbpd-icon1-eyes"></i>
						</a>
					</li>
				</ul>
			</div>
			<div class="card-block">


				<div class="jvbpd-block-content-left">

					<ul class="jv-module-meta-top list-inline">
						<li class="jv-meta-status"><?php echo 'For Sale'; ?></li>
					</ul>

					<h3 class="card-title">
						<?php echo $this->title; ?>
						<span class="title-actions">
							<?php $this->getFavoriteButton(); ?>
						</span>
					</h3>

					<div class="jv-meta-location">
						<i class="fa fa-map-marker"></i>
						<?php echo $jvbpd_item_location!='' ? $jvbpd_item_location : __('No Location','jvfrmtd'); ?>
					</div><!-- jv-meta-location -->

					<!--<p class="card-text"><?php echo $this->excerpt; ?></p>-->
					
					<ul class="module-meta list-inline">
						<li class="jv-meta-bedrooms">
							<?php echo sprintf( _n('Bed: ', 'Beds: ', $this->getCustomValue( 'bedrooms' ), 'jvfrmtd') ).$this->getCustomValue( 'bedrooms' ); ?>
						</li>
						<li class="jv-meta-bathrooms">
							<?php echo sprintf( _n('Bath :', 'Baths: ', $this->getCustomValue( 'bathrooms' ), 'jvfrmtd') ).$this->getCustomValue( 'bathrooms' ); ?>
						</li>
						<li class="jv-meta-garages">
							<?php echo sprintf( _n('Garage: ', 'Garages: ', $this->getCustomValue( 'garages' ), 'jvfrmtd') ).$this->getCustomValue( 'garages' ); ?>
						</li>
						<li class="jv-meta-area">
							<?php echo $this->getCustomValue( 'area_size_prefix' ).': '.$this->getCustomValue( 'area' ); ?>
						</li>
					</ul>

					<div class="jv-meta-category">
						<i class="fa fa-bookmark"></i>
						<?php echo $jvbpd_item_category!='' ? $jvbpd_item_category : __('No Category','jvfrmtd'); ?>
					</div><!-- jv-meta-category -->

					<ul class="jv-module-meta-bottom list-inline">
						<li class="jv-meta-author"><i class="fa fa-user" aria-hidden="true"></i> <?php echo $jvbpd_author_name; ?></li>
						<li class="jv-meta-built-year"><i class="fa fa-calendar" aria-hidden="true"></i> <?php echo $this->getCustomValue( 'built_year' ); ?></li>
					</ul>
					
				</div>


				<div class="jvbpd-block-content-right">
					<div class="jv-meta-price"><?php echo $this->getCustomValue( 'price_prefix' ).number_format($this->getCustomValue( 'default_price' )); ?></div>
					<div class="jv-meta-second-price"><?php echo $this->getCustomValue( 'price_prefix' ).number_format($this->getCustomValue( 'default_price_second' )).$this->getCustomValue( 'after_price_label' ); ?></div>
					<button class="btn"><?php esc_html_e('Details','jvfrmtd'); ?></button>
				</div>


			</div><!-- /.media-body -->
			<?php $this->after(); ?>
		</div><!-- /.media -->
		<?php
		return ob_get_clean();
	}
}
