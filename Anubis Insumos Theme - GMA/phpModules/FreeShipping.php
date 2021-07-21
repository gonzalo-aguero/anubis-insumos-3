<?php 
if(function_exists("add_filter") === true){
    add_filter( 'woocommerce_package_rates', 'GMA_changeShippingInRosarioLabel' );
}
if(function_exists("add_action") === true){
    add_action('GMA_beforeHeader', 'GMA_showOffer');
    add_action('GMA_beforeFinalizePurchaseHeader', 'GMA_appliedFreeShippingNotification', 9);
    add_action( 'woocommerce_checkout_update_order_meta', 'GMA_applyFreeShipping', 9, 2);
}

// ====== BACK OFFICE ====== //
function GMA_getFreeShippings(){
    $connection = gma_connectToDB();
    if($connection){
        $freeShippings = [];
        
        // Selection of products with free shipping
        $sql = "SELECT data FROM gma_config WHERE dataKey = 'freeShipping1'";
        $result = mysqli_query($connection,$sql);
        $resultArr = mysqli_fetch_array($result);
        $freeShippings["products"] = json_decode($resultArr["data"]);
        
        // Minimum amount with free shipping.
        $sql = "SELECT data FROM gma_config WHERE dataKey = 'freeShipping2'";
        $result = mysqli_query($connection,$sql);
        $resultArr = mysqli_fetch_array($result);
        $freeShippings["minAmount"] = json_decode($resultArr["data"]);
        
        mysqli_close($connection);
        return $freeShippings;
    }else{
        ?>
        <div class='gma-actionMessage error'>
            <span>Error en la conexión a la base de datos.</span>
        </div>
        <?php
        return false;
    }
}
function GMA_printFreeShippings(){
    $freeShippings = GMA_getFreeShippings();
    
    $FS1_productIDs = implode(',',$freeShippings["products"]->ids);
    $FS1_actived = $freeShippings["products"]->actived;
    $FS1_actived === true ? $FS1_actived = "checked" : $FS1_actived = "";
    
    $FS2_actived = $freeShippings["minAmount"]->actived;
    $FS2_actived === true ? $FS2_actived = "checked" : $FS2_actived = "";
    
    ?>
    <h1>Envíos gratis</h1>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Data</th>
            <th>Activado</th>
            <th>Nota</th>
        </tr>
        <tr>
            <td>Productos seleccionados</td>
            <td><input type="text" value="<?php echo $FS1_productIDs; ?>" id="freeShippingProducts"></td>
            <td><input type="checkbox" id="freeShippingProductsStatus" <?php echo $FS1_actived; ?>></td>
            <td>Colocar en "data" la id de los productos separadas por coma.
                <br>Por ejemplo: 102,140,185,200
                <br>En caso de querer colocar cierta variación por separado de un producto se deberá colocar la id de dicha variación.
            </td>
        </tr>
        <tr>
            <td>A partir de cierto monto</td>
            <td>$<input type="number" value="<?php echo $freeShippings["minAmount"]->minAmount; ?>"  id="freeShippingMinAmount"></td>
            <td><input type="checkbox" id="freeShippingMinAmountStatus" <?php echo $FS2_actived; ?>></td>
            <td>Colocar en "data" el monto mínimo para que el envío sea gratis</td>
        </tr>
    </table>
    <nav class="actionBar">
        <button id="saveFreeShippings">Guardar cambios</button>
    </nav>
    <h2>Imagenes de ejemplos</h2>
    <div class="explanationImages">
        <div>
            <h3>Obtener id de un producto</h3>
            <img src="<?php echo GMA_IMAGES_PATH; ?>ProductId.jpg">
        </div>
        <div>
            <h3>Obtener id de variaciones de un producto</h3>
            <img src="<?php echo GMA_IMAGES_PATH; ?>ProductVariationID.jpg">
        </div>
    </div>
    <?php
}
function GMA_saveFreeShippings(){
    $FS1_data = $_POST['FS1_data'];//String with free shipping data.
    $FS2_data = $_POST['FS2_data'];//String with free shipping data.
    $connection = gma_connectToDB();
    if($connection){
        $count = 0;
        $sql = "UPDATE gma_config SET data = '$FS1_data' WHERE dataKey = 'freeShipping1'";
        if(mysqli_query($connection,$sql)){
            $count++;   
        }
        $sql = "UPDATE gma_config SET data = '$FS2_data' WHERE dataKey = 'freeShipping2'";
        if(mysqli_query($connection,$sql)){
            $count++;    
        }
        mysqli_close($connection);
        if($count === 2){
            echo 1;
        }else{
            echo 0;
        }
    }
}

// ====== USERS ====== //
function GMA_showOffer(){
    $freeShippings = GMA_getFreeShippings();
    $FS1_status = $freeShippings["products"]->actived;
    $FS2_status = $freeShippings["minAmount"]->actived;
    $FS2_formattedMinAmount = number_format($freeShippings["minAmount"]->minAmount, 2, ',', '.');
    
    $show = false;
    $FS1_title = "En productos seleccionados";
    if($FS1_status === true && $FS2_status === true):
        $text = "Envío gratis en productos seleccionados o a partir de $$FS2_formattedMinAmount";
        $FS1_title = "O llevando productos seleccionados";
        $show = true;
    elseif( $FS1_status === true ):
        $text = "Envío gratis en productos seleccionados";
        $show = true;
    elseif($FS2_status === true ):
        $text = "Envío gratis a partir de $$FS2_formattedMinAmount";
        $show = true;
    endif;
    
    if(CURRENT_PATH === '/' || strstr(CURRENT_PATH, '/?s=') === false){
        $href = "";
        $goToHomePage = false;
    }else{
        $href = "href='".GMA_DOMAIN."?jsf=openFreeShippings"."'";
        $goToHomePage = true;
    }
    
    if($show === true):
        ?>
        <div id="topBar">
            <span><?php echo $text; ?> <a <?php echo $href; ?> id="openFreeShippings">Ver más</a></span>
        </div>
        <?php 
        if($goToHomePage === false): 
            ?>
            <div class="closeDiv" id="closeFreeShippings"></div>
            <div id="freeShippings">
                <span class="closeSpan">x</span>
                <h2>Envios gratis</h2>
                <?php 
                if($FS2_status === true): 
                    ?>
                    <div class="minAmountWithFreeShipping">
                        <h3>Comprando por un monto igual o mayor a</h3>
                        <span class="price finalPrice">$ <?php echo $FS2_formattedMinAmount;?></span>
                    </div>
                    <?php
                endif;
                if($FS1_status === true): 
                    ?>
                    <div class="productsWithFreeShipping">
                        <h3><?php echo $FS1_title;?></h3>
                        <div class="GMA productSlider">
                        <?php
                        echo GMA_SCROLL_X_CONTROL_LEFT;// Scroll Control Left
                        echo GMA_SCROLL_X_CONTROL_RIGHT;// Scroll Control Right
                        $productIDs = $freeShippings["products"]->ids;
                        foreach($productIDs as $productId){
                            $cond1 = strpos(CURRENT_PATH, '/carrito/');
                            $cond2 = strpos(CURRENT_PATH, '/preguntas-frecuentes/');
                            $cond3 = strpos(CURRENT_PATH, '/gma-admin/');
                            if( $cond1 !== false || $cond2 !== false || $cond3 !== false ){
                                echo GMA_productTemplate($productId);
                            }else{
                                echo GMA_productTemplate($productId, false);
                            }
                        }
                        ?>
                        </div>
                    </div>
                    <?php 
                endif;
            ?>
            </div>
            <?php
        endif;
    else: 
        ?>
        <div id="topBar"></div>
        <?php
    endif;
}
function GMA_validateFreeShipping(){
    $freeShippings = GMA_getFreeShippings();
    $FS1_status = $freeShippings["products"]->actived;
    $FS2_status = $freeShippings["minAmount"]->actived;
    $productFreeShipping = false; //If the cart has a product with free shipping, it is true.
    $minAmount = false; //If the cart subtotal is higher than the minimum amount for free shipping, it is true.
    
    if($FS1_status === true){
        //Validate if the cart has a product with free shipping.
        $productIDs = $freeShippings["products"]->ids;
        $cartItems = WC()->cart->get_cart();
        foreach($cartItems as $cartItem){
            if(in_array($cartItem['product_id'], $productIDs)){
                $productFreeShipping = true;
                break;
            }
        }
    }
    
    if($FS2_status === true){
        //Validate if the cart subtotal is higher than the minimum amount for free shipping.
        $cartSubtotal = WC()->cart->subtotal;
        if($cartSubtotal >= $freeShippings["minAmount"]->minAmount){
            $minAmount = true;
        }
    }
    
    if($productFreeShipping === true || $minAmount === true){
        return true;
    }else{
        return false;
    }
}
function GMA_changeShippingInRosarioLabel($rates){
    if(GMA_validateFreeShipping() === true){
        foreach($rates as $key => $rate ) {
            $label = $rates[$key]->label;
            if($label === SHIPPING_IN_ROSARIO){
    		    $rates[$key]->label = "$label (Envío gratis si pagás con Contra Reembolso)";
            }
    	}
    	return $rates;
    }
	return $rates;
}
/**
 * Notify the customer that their purchase has free shipping.
 * */
function GMA_appliedFreeShippingNotification(){
    //Make the notification text.
    if(GMA_validateFreeShipping() === true){
        ?>
        <div class="GMA freeShippingNotification">
            <span>¡Tienes envío gratis!<br>(Solo para envíos dentro de Rosario y pagando en efectivo)</span>
        </div>
        <?php  
        // $productFreeShipping === true ? $msg .= "<h2>Tienes un producto en oferta :D</h2>" : $msg .= "<h2>No tienes un producto en oferta D:</h2>";
        // $minAmount === true ? $msg .= "<h2>Superas al mínimo de compra para envío gratis :D</h2>" : $msg .= "<h2>No superas al mínimo de compra para envío gratis D:</h2>";
    }else{
        $freeShippings = GMA_getFreeShippings();
        if($freeShippings["minAmount"]->actived === true){
            $minAmount = $freeShippings["minAmount"]->minAmount;
            $currentAmount = WC()->cart->subtotal;
            $missingAmount = $minAmount - $currentAmount;
            ?>
            <div class="GMA freeShippingNotification">
                <span>¡Te faltan $<?php echo number_format($missingAmount, 2, ',', '.'); ?> para tener envío gratis!<br>(Solo para envíos dentro de Rosario y pagando en efectivo)</span>
            </div>
            <?php  
        }
    }
}
function previewPaymentWithFreeShipping($finalPrice = null){
    if($finalPrice !== null){
        return "
            <tr id=\"amountToPayWithFreeShipping\">
                <th>Pagando con Contra Reembolso tenés envío gratis y pagás</th>
                <td class='GMA price finalPrice'>$$finalPrice</td>
            </tr>
        ";   
    }
}
function GMA_applyFreeShipping($orderId, $data){
    $order = wc_get_order($orderId);
    $paymentMethod = $data['payment_method'];
    $shippingMethod = $order->get_shipping_method();
    $shippingInRosarioLabel = SHIPPING_IN_ROSARIO." (Envío gratis si pagás con Contra Reembolso)";
    if(GMA_validateFreeShipping() === true && $paymentMethod === 'cod' && $shippingMethod === $shippingInRosarioLabel){
        $metaId = $orderId;
        $newTotal = $order->get_subtotal();//Cart subtotal without shipping cost.
        //Update the post meta (apply free shipping).
        update_post_meta($metaId, "_order_shipping", 0);
        update_post_meta($metaId, "_order_total", $newTotal);
    }
}
?>