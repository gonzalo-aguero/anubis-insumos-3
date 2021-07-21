<?php
/**
 * This template corresponds to the product page.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once('./wp-content/themes/Anubis Insumos Theme - GMA/config.php');
get_header( 'shop' ); ?>

	<?php
	    //Remove product pagination. Priority = 11 to go after the Storefront's hook.
        add_filter( 'theme_mod_storefront_product_pagination', '__return_false', 11 );
        //Remove related products.
        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
        
		/**
		 * woocommerce_before_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action( 'woocommerce_before_main_content' );
	?>
		<?php while ( have_posts() ) : ?>
			<?php 
			the_post();
			$productId = get_the_ID();
			//Redirect to the home page and open the product detail there.
			header("Location: ".GMA_DOMAIN."?jsf=openProductDetail&params={productId:$productId}");
			die();
			?>
			
			<?php //wc_get_template_part( 'content', 'single-product' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'woocommerce_after_main_content' );
	?>

	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>

<?php
get_footer( 'shop' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
