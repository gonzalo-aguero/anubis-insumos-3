<?php 
/**
 * Only the product templates and the payment form are found in this file.
 * The other templates are in their respective files.
 **/

function GMA_mostSelledProducts(){
    // ------ GET PRODUCTS ------ //
    $args = array(
        'post_type' => 'product',
        'meta_key' => 'total_sales',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 10,
    );
    $loop = new WP_Query( $args );
    ?>
    <h1 class="sectionTitle">MÁS VENDIDO</h1>
    <div class="GMA mostSelledProducts productSlider" id="mostSelledProducts">
    <?php
    echo GMA_SCROLL_X_CONTROL_LEFT;// Scroll Control Left
    echo GMA_SCROLL_X_CONTROL_RIGHT;// Scroll Control Right
    while($loop->have_posts()){ 
        $loop->the_post(); 
        $productId = $loop->post->ID;
        // ------ PRINT PRODUCT ------ //
        echo GMA_productTemplate($productId);
    }
    ?>
    </div>
    <?php
    wp_reset_query();
}
function GMA_printProductsByCategory(){
    // GET ALL THE PRODUCT CATEGORIES.
    $orderby = 'name';
    $order = 'asc';
    $hide_empty = false ;
    $cat_args = array(
        'orderby'    => $orderby,
        'order'      => $order,
        'hide_empty' => $hide_empty,
    );
    $productCategories = get_terms( 'product_cat', $cat_args );
    if(!empty($productCategories)){
        ?>
        <h1 class="sectionTitle">CATEGORÍAS</h1>
        <?php
        foreach ($productCategories as $key => $category) {
            // GET ALL THE PRODUCT ID IN THE CATEGORY.
            $productIds = get_posts( array(
                'post_type' => 'product',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => $category->name,
                        'operator' => 'IN',
                        )
                    ),
                )
            );
            if(count($productIds) < 1): continue; endif;
            ?>
            <h2 class="sliderTitle"><?php echo $category->name;?></h2>
            <div class="GMA productsByCategory productSlider" id="<?php echo $category->name;?>">
            <?php
            echo GMA_SCROLL_X_CONTROL_LEFT;// Scroll Control Left
            echo GMA_SCROLL_X_CONTROL_RIGHT;// Scroll Control Right
            foreach( $productIds as $productId ) {
                // ------ PRINT PRODUCT ------ //
                echo GMA_productTemplate($productId);
            }
            ?></div><?
        }
    }
}
function GMA_addToCartButton($productId,$variable = false){
    if($variable === false){
        return "
        <a href='?add-to-cart=$productId' 
            aria-label='Agregá “Producto de prueba” a tu carrito'
            data-quantity='1'
            data-product_id='$productId'
            data-product_sku=''
            rel='nofollow'
            class='add_to_cart_button ajax_add_to_cart GMA addToCartBtn'>Agregar al carrito</a>
        ";
        // class='wp-block-button__link add_to_cart_button ajax_add_to_cart'>Agregar al carrito</a>
    }else if($variable === true){
        return "<a class='add_to_cart_button GMA addToCartBtn showProductDetail' data-productId='$productId'>Ver opciones</a>";
    }
}
/**
 * @param $productId Id of the product to print.
 * @param $productDetail Si es true, se muestra. Por defecto es true.
 * @param $href Si es true, al tocar el boton de ver opciones o la imagen del producto, en vez de abrir el productDetail, se redirige a HomePage y allí se abre.
 **/
function GMA_productTemplate($productId, $productDetail = true){
    // ------ GET PRODUCT ------ //
    $product = wc_get_product($productId);
    if($product === false || $product === null){
        return;
    }
    $image = $product->get_image();
    $priceHtml = $product->get_price_html();
    $variable = $product->is_type('variable');
    if($product->is_on_sale()){
        $onSale = apply_filters( 'woocommerce_sale_flash', '<span class="onsale">'.esc_html__( 'Sale!', 'woocommerce' ).'</span>');//, $post , $product );
    }else{
        $onSale = "";
    }
    
    // ------ REMOVE DETAIL PRODUCT BLOCKS ------ //
    //Remove product pagination. Priority = 11 to go after the Storefront's hook.
    add_filter( 'theme_mod_storefront_product_pagination', '__return_false', 11 );
    //Remove related products.
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
    
    $productDetailHTML = "";
    if($productDetail === true):
        $productDetailHTML = "
            <div class='GMA productDetail' id='productDetail_$productId'>
                ".do_shortcode( '[product_page  id="'.$productId.'"]' )."
                <span class='closeSpan'></span>
            </div>
        ";
    endif;
    
    // ------ RETURN PRODUCT HTML ------ //
    return "
        <li class='GMA product item'>
            <a class='productImage showProductDetail' data-productId='$productId'>$image</a>
            <h2>".$product->get_name()."</h2>
            $onSale
            <h3>$priceHtml</h3>
            ".GMA_addToCartButton($productId, $variable)."
        </li>
        $productDetailHTML
    ";
}

if(function_exists("add_action") === true)
    add_action("GMA_checkoutForm","GMA_checkoutForm");
function GMA_checkoutForm(){
    $trialMode = false;//Set true to fill out the form without having to fill it out manually (For tests).
    if($trialMode):
        $fullName = "Javier Muñoz";
        $email = "gonzalo90fa@gmail.com";
        $phoneNumber = "3434258605";
        $billingCity = "Rosario";
        $billingAddress1 = "Belgrano 1200";
    else:
        $fullName = "";
        $email = "";
        $phoneNumber = "";
        $billingCity = "";
        $billingAddress1 = "";
    endif;
    ?>
    <div class="woocommerce-billing-fields">
        <h3>Detalles de facturación</h3>
        <div class="woocommerce-billing-fields__field-wrapper">
            <p class="form-row form-row-first thwcfd-field-wrapper thwcfd-field-text validate-required" id="billing_first_name_field" data-priority="10">
                <label for="billing_first_name" class="screen-reader-text">Nombre completo&nbsp;
                    <abbr class="required" title="obligatorio">*</abbr>
                </label>
                <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text " name="billing_first_name" id="billing_first_name" placeholder="Nombre completo" value="<?php echo $fullName ?>" autocomplete="given-name">
                </span>
            </p>
            <p class="form-row form-row-wide thwcfd-field-wrapper thwcfd-field-email validate-required validate-email" id="billing_email_field" data-priority="20">
                <label for="billing_email" class="screen-reader-text">Correo electrónico&nbsp;
                    <abbr class="required" title="obligatorio">*</abbr>
                </label>
                <span class="woocommerce-input-wrapper">
                    <input type="email" class="input-text" name="billing_email" id="billing_email" placeholder="Correo electrónico" value="<?php echo $email ?>" autocomplete="email username">
                </span>
            </p>
            <p class="form-row form-row-wide thwcfd-field-wrapper thwcfd-field-tel validate-required validate-phone" id="billing_phone_field" data-priority="30">
                <label for="billing_phone" class="screen-reader-text">Teléfono&nbsp;
                    <abbr class="required" title="obligatorio">*</abbr>
                </label>
                <span class="woocommerce-input-wrapper" style="
                    display: flex;
                    flex-flow: row nowrap;
                    align-items: center;
                    justify-content: space-between;
                    gap: .5em;
                ">
                    <span>+54 </span><input type="tel" class="input-text " name="billing_phone" id="billing_phone" placeholder="Teléfono" value="<?php echo $phoneNumber ?>" autocomplete="tel">
                </span>
            </p>
            <p class="form-row form-row-wide address-field thwcfd-field-wrapper thwcfd-field-text validate-required" id="billing_city_field" data-priority="40">
                <label for="billing_city" class="screen-reader-text">Localidad / Ciudad&nbsp;
                    <abbr class="required" title="obligatorio">*</abbr>
                </label>
                <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text " name="billing_city" id="billing_city" placeholder="Localidad / Ciudad" value="<?php echo $billingCity ?>" autocomplete="address-level2">
                </span>
            </p>
            <p class="form-row form-row-wide address-field thwcfd-field-wrapper thwcfd-field-text validate-required" id="billing_address_1_field" data-priority="50">
                <label for="billing_address_1" class="screen-reader-text">Dirección exacta (calle/altura/piso/depto)&nbsp;
                    <abbr class="required" title="obligatorio">*</abbr>
                </label>
                <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text " name="billing_address_1" id="billing_address_1" placeholder="Dirección exacta (calle/altura/piso/depto)" value="<?php echo $billingAddress1 ?>" autocomplete="address-line1">
                </span>
            </p>
            <p class="form-row form-row-wide address-field thwcfd-field-wrapper thwcfd-field-text" id="billing_address_2_field" data-priority="60">
                <label for="billing_address_2" class="screen-reader-text">Apartamento, habitación, unidad, referencia, etc. (opcional)&nbsp;
                    <span class="optional">(opcional) </span>
                </label>
                <span class="woocommerce-input-wrapper">
                    <input type="text" class="input-text " name="billing_address_2" id="billing_address_2" placeholder="Apartamento, habitación, unidad, referencia, etc. (opcional)" value="" autocomplete="address-line2">
                </span>
            </p>
            <p class="form-row notes thwcfd-field-wrapper thwcfd-field-textarea" id="order_comments_field" data-priority="">
                <label for="order_comments" class="">Nota del pedido&nbsp;
                    <span class="optional">(opcional)</span>
                </label>
                <span class="woocommerce-input-wrapper">
                    <textarea name="order_comments" class="input-text " id="order_comments" placeholder="No te olvides de aclarar aromas, colores y horarios de ser necesario!" rows="2" cols="5"></textarea>
                </span>
            </p>
        </div>
    </div>
    <?php
}
?>
