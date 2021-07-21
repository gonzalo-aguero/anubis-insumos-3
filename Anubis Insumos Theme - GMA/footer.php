<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>
		</div><!-- .col-full -->
	</div><!-- #content -->
	<?php do_action( 'storefront_before_footer' ); ?>
    <footer>
        <?php if( CURRENT_PATH !== '/carrito/' ): ?>
        <div class="_0">
            <ul class="linkList">
                <li>
                    <a href="<?php echo GMA_ANUBIS_INSTAGRAM; ?>" target="_blank">
                        <img src="<?php echo GMA_IMAGES_PATH; ?>Instagram.svg" alt="Instagram">
                        <span class="about">Instagram</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo GMA_ANUBIS_FACEBOOK; ?>" target="_blank">
                        <img src="<?php echo GMA_IMAGES_PATH; ?>Facebook.svg" alt="Facebook">
                        <span class="about">Facebook</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo GMA_ANUBIS_WHATSAPP; ?>" target="_blank">
                        <img src="<?php echo GMA_IMAGES_PATH; ?>Whatsapp.svg" alt="Whatsapp">
                        <span class="about">Hablar por WhatsApp</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo GMA_ANUBIS_MERCADO_LIBRE; ?>" target="_blank">
                        <img src="<?php echo GMA_IMAGES_PATH; ?>MercadoLibre.png" alt="Mercado Libre">
                        <span class="about">Mercado Libre</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo GMA_ANUBIS_PRICE_LIST; ?>" target="_blank">
                        <img src="<?php echo GMA_IMAGES_PATH; ?>Sheets.svg" alt="Lista de precios">
                        <span class="about">Lista de precios</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        <div class="_1">
            <ul class="cardList">
                <?php
                
                
                //API URL
                $url = 'https://api.mercadopago.com/sites/MLA/payment_methods?marketplace=NONE&operation_type=recurring_payment';
                //create a new cURL resource
                $ch = curl_init($url);
                //headers
                $headers = array(
                        "Content-Type: application/json",
                    );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                //max time out
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                //execute the request
                $result = curl_exec($ch);
                //close curl resource
                curl_close($ch);
                $result = json_decode($result);
                forEach($result as $paymentMethod ){
                    ?>
                    <li><img src="<?php echo $paymentMethod->thumbnail; ?>" /></li>
                <?php } ?>
            </ul>
        </div>
        <div class="_2" style="background: url('<?php echo GMA_IMAGES_PATH;?>AnubisBackground.jpg')">
            <h1><?php echo GMA_ANUBIS_NAME; ?></h1>
    	    <span><?php echo GMA_ANUBIS_COPYRIGHT; ?></span>
        </div>
    </footer>
	<?php do_action( 'storefront_after_footer' ); ?>
</div><!-- #page -->
<?php wp_footer(); ?>
<script src="<?php echo GMA_DOMAIN; ?>wp-content/themes/Anubis Insumos Theme - GMA/js/config.js?v=2.0.3"></script>
<script src="<?php echo GMA_DOMAIN; ?>wp-content/themes/Anubis Insumos Theme - GMA/js/ajax-functions.js?v=2.0.3"></script>
<?php if($pagename === "carrito"): ?>
<script src="<?php echo GMA_DOMAIN; ?>wp-content/themes/Anubis Insumos Theme - GMA/js/Checkout.js?v=2.0.3"></script>
<?php endif; ?>
<script src="<?php echo GMA_DOMAIN; ?>wp-content/themes/Anubis Insumos Theme - GMA/js/FreeShipping.js?v=2.0.3"></script>
<script src="<?php echo GMA_DOMAIN; ?>wp-content/themes/Anubis Insumos Theme - GMA/js/main.js?v=2.0.3"></script>
<script src="<?php echo GMA_DOMAIN; ?>wp-content/themes/Anubis Insumos Theme - GMA/js/ajax-add-to-cart.js?v=2.0.3"></script>

<?php getAndExecuteJSFunction(); ?>
</body>
</html>
