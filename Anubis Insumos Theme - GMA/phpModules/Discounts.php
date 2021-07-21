<?php
// ========== DISCOUNT FUNCTIONS ========== //

if(function_exists("add_action") === true){
    add_action("GMA_beforeFinalizePurchaseHeader","GMA_showDiscounts", 10);
    add_action("GMA_topPaymentMethod", "GMA_previewDiscount", 1);
    add_action( 'woocommerce_checkout_update_order_meta', 'GMA_applyDiscount', 10, 2);
}
/**
 * If ok it returns the discount object, else it returns false.
 **/
function GMA_getDiscounts(){
    $connection = gma_connectToDB();
    if($connection){
        $contacts = [];
        $sql = "SELECT data FROM gma_config WHERE dataKey = 'COD_discounts'";
        $result = mysqli_query($connection,$sql);
        $resultArr = mysqli_fetch_array($result);
        $discounts =  json_decode($resultArr["data"]);
        mysqli_close($connection);
        return $discounts;
    }else{
        echo "
        <div class='gma-actionMessage error'>
            <span>Error en la conexión a la base de datos.</span>
        </div>
        ";
        return false;
    }
}
/**
 * Print a table with all discounts to edit.
 **/
function GMA_printDiscounts(){
    $discounts = GMA_getDiscounts();
    ?>
    <h1>Descuentos en contra reembolso</h1>
    <table>
        <tr>
            <th>Monto mínimo</th>
            <th>Porcentaje</th>
            <th>Activado</th>
        </tr>
    <?php
    foreach($discounts as $discount){
        $discount->actived === true ? $actived = "checked" : $actived = "";
        ?>
        <tr>
            <td><input id='minAmount_<?php echo $discount->name; ?>' type='number' value='<?php echo $discount->minAmount; ?>' /></td>
            <td><input id='percent_<?php echo $discount->name; ?>' type='number' value='<?php echo $discount->percent; ?>' /></td>
            <td><input id='actived_<?php echo $discount->name; ?>' type='checkbox' <?php echo $actived; ?> /></td>
        </tr>
        <?php
    }
    ?>
    </table>
    <nav class="actionBar">
        <button id='saveDiscounts'>Guardar cambios</button>
    </nav>
    <div style="text-align:center;">
        <h2 style="font-weight: 500;">Nota:<h2>
        <h3 style="font-weight: 500;">Por favor, coloque los montos mínimos ordenados de menor a mayor.</h3>
    </div>
    <?php
}
/**
 * Save the discount changes. 
 **/
function GMA_saveDiscounts(){
    $data = $_POST['data'];//String with discounts data.
    $connection = gma_connectToDB();
    if($connection){
        $sql = "UPDATE gma_config SET data = '$data' WHERE dataKey = 'COD_discounts'";
        if(mysqli_query($connection,$sql)){
            echo 1;    
        }else{
            echo 0;
        }
        mysqli_close($connection);
    }
}
/**
 * Show discount offers to the customer.
 **/
function GMA_showDiscounts(){
    $discounts = GMA_getDiscounts();
    $formattedMinAmount0 = number_format($discounts[0]->minAmount,2,',','.');
    $formattedMinAmount1 = number_format($discounts[1]->minAmount,2,',','.');
    
    if($discounts[0]->actived === false && $discounts[1]->actived === false)
        return;
    if($discounts[0]->actived === true && $discounts[1]->actived === true):
        $msg = "Obtené un descuento del ".$discounts[0]->percent."% comprando por más de $$formattedMinAmount0 o ".$discounts[1]->percent."% de descuento superando los $$formattedMinAmount1.";
    else:
        if($discounts[0]->actived === true)
            $msg = "Obtené un descuento del ".$discounts[0]->percent."% comprando por más de $$formattedMinAmount0.";
        if($discounts[1]->actived === true)
            $msg = "Obtené un descuento del ".$discounts[1]->percent."% comprando por más de $$formattedMinAmount1.";
    endif;
    $msg .= " Solo pagando con efectivo (Contrareembolso).";
    ?>
    <div class="GMA discount">
        <span><?php echo $msg; ?></span>
    </div>
    <?php
}
/**
 * Calculate the new price with the discount and display it.
 **/
function GMA_previewDiscount(){
    $discounts = GMA_getDiscounts();
    $cartTotal = WC()->cart->get_subtotal();
    $shippingTotal = WC()->cart->get_shipping_total();
    $amountWithDiscount = 0;
    $showAmountWithDiscount = false;
    $freeShipping = GMA_validateFreeShipping();
    $discountStatus1 = $discounts[0]->actived;
    $discountStatus2 = $discounts[1]->actived;
    $minAmount1 = $discounts[0]->minAmount;
    $minAmount2 = $discounts[1]->minAmount;
    
    //Calculate what discount will be applied.
    if($discountStatus1 === true && $discountStatus2 === true){
        if($cartTotal >= $minAmount1 && $cartTotal < $minAmount2):
            //DISCOUNT 1;
            $amountWithDiscount = $cartTotal - ($cartTotal / 100 * $discounts[0]->percent);
            $showAmountWithDiscount = true;
        elseif($cartTotal >= $minAmount2):
            //DISCOUNT 2;
            $amountWithDiscount = $cartTotal - ($cartTotal / 100 * $discounts[1]->percent);
            $showAmountWithDiscount = true;
        else:
            //NO DISCOUNT;
            $showAmountWithDiscount = false;
        endif;
    }else{
        if($discountStatus1 === true && $cartTotal >= $minAmount1):
            //DISCOUNT 1;
            $amountWithDiscount = $cartTotal - ($cartTotal / 100 * $discounts[0]->percent);
            $showAmountWithDiscount = true;
        elseif($discountStatus2 === true && $cartTotal >= $minAmount2):
            //DISCOUNT 2;
            $amountWithDiscount = $cartTotal - ($cartTotal / 100 * $discounts[1]->percent);
            $showAmountWithDiscount = true;
        else:
            //NO DISCOUNT;
            $showAmountWithDiscount = false;
        endif;
    }
        
    $amountToPayNormally = $cartTotal + $shippingTotal;
    $amountToPayWithCOD = $amountWithDiscount + $shippingTotal;
    ?>
    <table class="GMA amountToPay">
        <tr>
            <th>Producto(s)</th><td class="GMA price">$<?php echo number_format($cartTotal,2,',','.');?></td>
        </tr>
        <tr>
            <th>Envío</th><td class="GMA price">$<?php echo number_format($shippingTotal,2,',','.');?></td>
        </tr>
        <tr>
            <th>Pagás</th>
            <td class="GMA price finalPrice">$<?php echo number_format($amountToPayNormally, 2, ',', '.'); ?></td>
        </tr>
        <?php 
        if($freeShipping === true){
            if($showAmountWithDiscount == true){
                echo previewPaymentWithFreeShipping(number_format($amountWithDiscount,2,',','.'));   
            }else{
                echo previewPaymentWithFreeShipping(number_format($cartTotal,2,',','.'));
            }
        }
        ?>
        <?php if($showAmountWithDiscount == true): ?>
        <tr id="amountToPayWithCOD">
            <th>Pagando con efectivo (contra reembolso) pagás</th>
            <td class="GMA price finalPrice">$<?php echo number_format($amountToPayWithCOD,2,',','.'); ?></td>
        </tr>
        <?php endif; ?>
    </table>
    <?php
}
/**
 * Apply the discount to the total of the cart.
 **/
function GMA_applyDiscount($orderId, $data) {
    $order = wc_get_order($orderId);
    $orderData = $order->get_data();
    $paymentMethod = $data['payment_method'];
    
    if($paymentMethod === 'cod'){
        // $cartDiscount = $orderData['discount_total'];//Coupon discounts.
        $metaId = $orderId;
        $shippingTotal = $orderData['shipping_total'];
        $cartTotal = $orderData['total'] - $shippingTotal;
        $discounts = GMA_getDiscounts();
        
        //Chooses the correct percent.
        if($discounts[1]->actived === true && $cartTotal >= $discounts[1]->minAmount){
            $percent = $discounts[1]->percent;
        }else if($discounts[0]->actived === true && $cartTotal >= $discounts[0]->minAmount){
            $percent = $discounts[0]->percent;
        }else{
            $percent = 0;
        }
        
        //Calculate the discount and the total with the discount.
        $discount = ($cartTotal / 100) * $percent;
        $newTotal = $cartTotal - $discount + $shippingTotal;
        
        //Update the post meta (apply discount).
        //Cart Discount
        update_post_meta($metaId, "_cart_discount", $discount);
        //Order Total
        update_post_meta($metaId, "_order_total", $newTotal);// metaId, metaKey, metaValue(New value), prevValue.
    }
}
?>