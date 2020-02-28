<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

global $wiloke;
$sidebarPosition = $wiloke->aThemeOptions['woocommerce_sidebar'];
$sidebarClass = '';
if ( $sidebarPosition == 'left' ){
    $mainClass ='col-md-8 col-md-push-4 sidebar-left';
	$sidebarClass = 'col-md-4 col-md-pull-8';
}else if ( $sidebarPosition == 'right' ){
	$mainClass = 'col-md-8 sidebar-right';
	$sidebarClass = 'col-md-4';
}else{
	$mainClass = 'col-md-12 no-sidebar';
}

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @hooked woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */

do_action( 'woocommerce_before_main_content' );


?>
    <div class="row">
        <div class="col-md-12">
        	<header class="woocommerce-products-header">
        		<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
        			<h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
        		<?php endif; ?>

        		<?php
        		/**
        		 * Hook: woocommerce_archive_description.
        		 *
        		 * @hooked woocommerce_taxonomy_archive_description - 10
        		 * @hooked woocommerce_product_archive_description - 10
        		 */
        		do_action( 'woocommerce_archive_description' );
        		?>
        	</header>
        </div>
    </div>
<?php
if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @hooked woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */

    echo '<div class="row mb-20">';
        echo '<div class="col-md-12">';
	       do_action( 'woocommerce_before_shop_loop' );
        echo '</div>';
    echo '</div>';

	echo '<div class="clearfix"><div class="row">';
	    echo '<div class="'.$mainClass.'">';
        woocommerce_product_loop_start();

        if ( wc_get_loop_prop( 'total' ) ) {
            while ( have_posts() ) {
                the_post();

                /**
                 * Hook: woocommerce_shop_loop.
                 *
                 * @hooked WC_Structured_Data::generate_product_data() - 10
                 */
                do_action( 'woocommerce_shop_loop' );

                wc_get_template_part( 'content', 'product' );
            }
        }

        woocommerce_product_loop_end();

        /**
         * Hook: woocommerce_after_shop_loop.
         *
         * @hooked woocommerce_pagination - 10
         */
        do_action( 'woocommerce_after_shop_loop' );
        echo '</div>';

        /**
         * Hook: woocommerce_sidebar.
         *
         * @hooked woocommerce_get_sidebar - 10
         */
        if ( !empty($sidebarClass) ){
            echo '<div class="'.$sidebarClass.'">';
            do_action( 'woocommerce_sidebar' );
            echo '</div>';
        }
	echo '</div></div>';

} else {
    echo '<div class="clearfix"><div class="row">';
        /**
         * Hook: woocommerce_no_products_found.
         *
         * @hooked wc_no_products_found - 10
         */
        do_action( 'woocommerce_no_products_found' );

        /**
         * Hook: woocommerce_sidebar.
         *
         * @hooked woocommerce_get_sidebar - 10
         */
        if ( !empty($sidebarClass) ){
            echo '<div class="'.$sidebarClass.'">';
            do_action( 'woocommerce_sidebar' );
            echo '</div>';
        }
    echo '</div></div>';
}

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
