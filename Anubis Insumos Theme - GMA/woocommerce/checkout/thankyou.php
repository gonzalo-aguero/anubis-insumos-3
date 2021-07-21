<?php
/**
 * Thankyou page.
 **/

defined( 'ABSPATH' ) || exit;

do_action("GMA_beforeThankYouPageContent");
global $GMA_ThankYouPage;
$editableZones = array(
        $GMA_ThankYouPage->editableZones[0],
        $GMA_ThankYouPage->editableZones[1],
        $GMA_ThankYouPage->editableZones[2],
        $GMA_ThankYouPage->editableZones[3],
        $GMA_ThankYouPage->editableZones[4],
        $GMA_ThankYouPage->editableZones[5]
    );
$GMA_ThankYouPage->page();
?>
<div class="woocommerce-order">
	<?php
	if ( $order ) :
		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>
		<?php if ( $order->has_status( 'failed' ) ) : ?>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>
		<?php else : ?>
		    <?php if(noEmptyNoNull($editableZones[0])): ?>
		    <div class="editableZone _0">
		        <?php echo $editableZones[0]; ?>
		    </div>
		    <?php endif; ?>
		    <div class="GMA ThankYou">
		        <?php if(noEmptyNoNull($editableZones[1])): ?>
		        <h1 class="firstTitle editableZone _1"><?php echo $editableZones[1]; ?></h1>
		        <?php endif; ?>
		        <h1 class="icon-checkmark animate__animated animate__fadeInUp"></h1>
		        <?php if(noEmptyNoNull($editableZones[2])): ?>
		        <h1 class="thanks animate__animated animate__fadeIn editableZone _2"><?php echo $editableZones[2]; ?></h1>
		        <?php endif; ?>
		        <?php if(noEmptyNoNull($editableZones[3])): ?>
    		    <div class="editableZone _3">
    		        <?php echo $editableZones[3]; ?>
    		    </div>
    		    <?php endif; ?>
		    </div>
		    <?php if(noEmptyNoNull($editableZones[4])): ?>
		    <div class="editableZone _4">
		        <?php echo $editableZones[4]; ?>
		    </div>
		    <?php endif; ?>
		    <nav class="GMA" id="afterThankYouNavigation">
		        <button><a href="<?php echo GMA_DOMAIN; ?>">Realizar otra compra</a></button>
		        <button id="seeOrderDetail">Ver detalle del pedido</button>
		    </nav>
		    <?php if(noEmptyNoNull($editableZones[5])): ?>
		    <div class="editableZone _5">
		        <?php echo $editableZones[5]; ?>
		    </div>
		    <?php endif; ?>
		    <section class="GMA orderDetail">
			<!--<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php //echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>-->
			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>
				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>
				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>
				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>
				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>
			</ul>
		<?php endif; ?>
		<?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>
		</section>
	<?php else : ?>
		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
	<?php endif; ?>
</div>
