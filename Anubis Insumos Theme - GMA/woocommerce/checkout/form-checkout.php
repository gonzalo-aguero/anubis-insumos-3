<?php
/**
 * Checkout and Cart page.
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );// Content hidden via CSS (display:none)

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

// INCLUDE THE CART CONTENT.
include("./wp-content/themes/Anubis Insumos Theme - GMA/woocommerce/cart/cart.php");
?>
    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
    	<?php //if ( $checkout->get_checkout_fields() ) : ?>
    		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
    		<div class="col2-set" id="customer_details">
    			<div class="col-1">
    				<?php 
        				//do_action( 'woocommerce_checkout_billing' );
        				/**
        				 * Billing form / Customer data.
        				 * This block corresponds to #2 step.
        				 **/
        				do_action("GMA_checkoutForm");
    				?>
    			</div>
    			<div class="col-2">
    				<?php //do_action( 'woocommerce_checkout_shipping' ); ?>
    			</div>
    		</div>
    		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
    	<?php //endif; ?>
    	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
    	<div id="order_review" class="woocommerce-checkout-review-order">
    	    <!-- Order review block, delivery method block and payment method block. -->
    		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
    	</div>
    	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
    </form>
    <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
    <!-- Back and Next Buttons -->
    <nav class="GMA checkoutNavigation">
        <button id="backBtn" style="cursor: default; opacity: 0;">Atrás</button>
        <button id="nextBtn">Continuar</button>
    </nav>
</section>
