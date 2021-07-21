<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<?php do_action( 'wpo_wcpdf_before_document', $this->type, $this->order ); ?>
<?php
    // ##### QR TO SUBMIT TO SHIPPING EXCEL ##### //
    $functionURL1 = GMA_ADMIN_URL."?gma-action=".md5("submitToShippingExcel")."&order_id=".$order->id;
    $relativeImageURL1 = "../wp-content/themes/Anubis Insumos Theme - GMA/QR_CODES/submitToShippingExcel/$order->id.png";
    $absoluteImageURL1 = GMA_THEME_PATH."QR_CODES/submitToShippingExcel/$order->id.png";
    
    // ##### QR TO SEND AN ORDER DISPATCHED EMAIL TO CUSTOMER ##### //
    $functionURL2 = GMA_ADMIN_URL."?gma-action=".md5("orderDispatched")."&order_id=".$order->id;
    $relativeImageURL2 = "../wp-content/themes/Anubis Insumos Theme - GMA/QR_CODES/orderDispatched/$order->id.png";
    $absoluteImageURL2 = GMA_THEME_PATH."QR_CODES/orderDispatched/$order->id.png";
    
    // ##### QR CODES GENERATION ##### //
    GMA_createQrUrl($functionURL1, 350, $relativeImageURL1);
    GMA_createQrUrl($functionURL2, 350, $relativeImageURL2);
?>
<table class="head container">
    <tr id="cabeceraPrincipal">
        <td class="_1">
            <ul>
                <li>Rosario - Santa Fe</li>
                <li>+54 9 341 257-5916</li>
                <li>anubisinsumos@hotmail.com</li>
                <li>instagram.com/anubisinsumos</li>
                <li>www.anubisinsumos.com</li>
            </ul>
            <img class="gma-qrCode" src="<?php echo $absoluteImageURL1; ?>">
        </td>
        <td class="_2">
			<?php
				if( $this->has_header_logo() ) {
						$this->header_logo();
				}
			?>
            <h1>ANUBIS INSUMOS</h1>
        </td>
        <td class="_3">
            <ul>
                <li>
                    <?php _e( 'Fecha de factura:', 'woocommerce-pdf-invoices-packing-slips' ); ?>
			        <?php $this->invoice_date(); ?>
		        </li>
                <li>
                    <?php _e( 'Número de factura:', 'woocommerce-pdf-invoices-packing-slips' ); ?>
			        <?php $this->invoice_number(); ?>
                </li>
            </ul>
            <img class="gma-qrCode" src="<?php echo $absoluteImageURL2; ?>">
        </td>
    </tr>
</table>
<table id="datosCliente">
	<tr>
		<td>Cliente y dirección:<br><?php $this->billing_address(); ?></td>
		<td>Celular:   <?php $this->billing_phone(); ?></td>
		<td>Método de pago:<br><?php $this->payment_method(); ?></td>
		<td>Correo:    <?php $this->billing_email(); ?></td>
		<td>Vendedor:  Anubis Insumos</td>
	</tr>
</table>
<table class="order-details">
	<thead>
		<tr>
			<th class="product"><?php _e('Producto', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="quantity"><?php _e('Cantidad', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
			<th class="price"><?php _e('Precio', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php $items = $this->get_order_items(); if( sizeof( $items ) > 0 ) : foreach( $items as $item_id => $item ) : ?>
		<tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', $item_id, $this->type, $this->order, $item_id ); ?>">
			<td class="product">
				<?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
				<span class="item-name"><?php echo $item['name']; ?></span>
				<?php do_action( 'wpo_wcpdf_before_item_meta', $this->type, $item, $this->order  ); ?>
				<span class="item-meta"><?php echo $item['meta']; ?></span>
				<dl class="meta">
					<?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
					<?php if( !empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
					<?php if( !empty( $item['weight'] ) ) : ?><dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="weight"><?php echo $item['weight']; ?><?php echo get_option('woocommerce_weight_unit'); ?></dd><?php endif; ?>
				</dl>
				<?php do_action( 'wpo_wcpdf_after_item_meta', $this->type, $item, $this->order  ); ?>
			</td>
			<td class="quantity"><?php echo $item['quantity']; ?></td>
			<td class="price"><?php echo $item['order_price']; ?></td>
		</tr>
		<?php endforeach; endif; ?>
	</tbody>
	<tfoot>
		<tr class="no-borders">
			<td class="no-borders">
				<div class="document-notes">
					<?php do_action( 'wpo_wcpdf_before_document_notes', $this->type, $this->order ); ?>
					<?php if ( $this->get_document_notes() ) : ?>
						<h3><?php _e( 'Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
						<?php $this->document_notes(); ?>
					<?php endif; ?>
					<?php do_action( 'wpo_wcpdf_after_document_notes', $this->type, $this->order ); ?>
				</div>
			</td>
			<td class="no-borders" colspan="2">
				<table class="totals">
					<tfoot>
						<?php foreach( $this->get_woocommerce_totals() as $key => $total ) : ?>
						<tr class="<?php echo $key; ?>">
							<td class="no-borders"></td>
							<th class="description"><?php echo $total['label']; ?></th>
							<td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
						</tr>
						<?php endforeach; ?>
					</tfoot>
				</table>
			</td>
		</tr>
	</tfoot>
</table>
<table id="table-notaPedido">
    <tr><th>Nota del pedido</th></tr>
    <tr><td><?php $this->shipping_notes(); ?></td></tr>
</table>
<h3 id="invalidInvoice">Documento no válido como factura</h3>
<div class="bottom-spacer"></div>
<?php do_action( 'wpo_wcpdf_after_order_details', $this->type, $this->order ); ?>

<?php if ( $this->get_footer() ): ?>
<div id="footer">
	<?php $this->footer(); ?>
</div><!-- #letter-footer -->
<?php endif; ?>
<?php do_action( 'wpo_wcpdf_after_document', $this->type, $this->order ); ?>
