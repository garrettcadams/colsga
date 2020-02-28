<?php
/**
 *
 *	000 Block Grid Type with Tag
 * @since	1.0
 */

class moduleWC1 extends Jvbpd_Module
{
	public function __construct( $post, $param=Array() ) {
		$this->lghContent	= 9;
		parent::__construct( $post, $param );
	}

	public function output()
	{
		$objProduct	= get_product( $this->post->ID );
		$GLOBALS[ 'product' ] = $objProduct;
		ob_start();
		?>
		<li <?php echo $this->classes( 'product type-product status-publish sale shipping-taxable purchasable product-type-simple product-cat-cate1 instock' ); ?>>
			<a href="<?php echo $this->permalink; ?>">
				<?php if( isset( $this->on_sale ) ) : ?>
					<div class="onsale-wrap">
						<div class="onsale-inner">
							<span class="onsale"><?php _e( "SALE", 'jvfrmtd' ); ?></span>
						</div>
					</div>
				<?php endif; ?>
				<?php echo $objProduct->get_image( 'jvbpd-box' ); ?>
			</a>
			<div class="jv-hover-wrap">
				<h4><?php echo $this->title;?></h4>
				<?php echo $objProduct->get_price_html(); ?>
				<?php wc_get_template( 'loop/add-to-cart.php' ); ?>
			</div>
		</li>
		<?php
		return ob_get_clean();
	}
}