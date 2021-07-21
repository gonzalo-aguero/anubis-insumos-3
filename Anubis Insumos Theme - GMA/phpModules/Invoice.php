<?php
// ============ INVOICE FUNCTIONS ============ //

// ========== QR CODE ========== //
/**
 * Generate QR code image
 *
 * @param string $codeData
 * @param int $size
 * @param string $filename
 * @return bool
 */
function QR_CODE($codeData = "",$size = 200, $filename = null) {
    // Google Chart API URL
    $googleChartAPI = 'http://chart.apis.google.com/chart';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $googleChartAPI);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "chs={$size}x{$size}&cht=qr&chl=" . urlencode($codeData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    $img = curl_exec($ch);
    curl_close($ch);

    if($img) {
        if($filename != null) {
            if(!preg_match("#.png$#i", $filename)) {
                $filename .= ".png";
            }
            
            return file_put_contents($filename, $img);
        } else {
            header("Content-type: image/png");
            print $img;
            return true;
        }
    }
    return false;
}
/**
 * It uses QR_CODE() to generate the QR code. It is the best option to generate a QR code with a URL.
 * @param string $url CODE DATA.
 * @param int $size SIZE OF THE IMAGE.
 * @param string $filename NAME OF THE FILE GENERATED (ONLY IF $filename != null).
 * @return bool
 **/
function GMA_createQrUrl($url = "",$size = 200, $filename = null){
    // Code data
    $codeData = preg_match("#^https?://#", $url) ? $url : "http://{$url}";
    QR_CODE($codeData, $size, $filename);
}
// ========== END QR CODE ========== //

/**
 * This function generates a URL to see invoice in PDF.
 * @param Object $order The order object.
 * @return String The URL.
 **/
function GMA_getUrlToSeeInvoiceInPdf($order) {
    // $invoiceUrl = admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf&template_type=invoice&order_ids=' . $order->get_id() . '&order_key=' . $order->get_order_key() );
    $invoiceUrl = admin_url( 'admin-ajax.php?action=generate_wpo_wcpdf%26template_type=invoice%26order_ids=' . $order->get_id() . '%26order_key=' . $order->get_order_key() );
    return $invoiceUrl;
}
/**
 * This function generates a URL to send the order data to the customer via WhatsApp.
 * @param int $orderId
 * @return String The URL.
 **/
function GMA_invoiceViaWhatsAppURL($orderId){
    if(isset($orderId)){
        $order = wc_get_order($orderId);//Get order data.
        $orderData = $order->get_data();
        
        //### CUSTOMER PHONE NUMBER FORMAT ###//
        $customerPhone = $order->get_billing_phone();
        if(preg_match("/^\+54/", $customerPhone) === 0){
            $customerPhone = "+54".$customerPhone;
        }

        // $customerName = $order->get_billing_first_name();
        $customerName = $orderData['billing']['first_name'];
        // $orderTotal = str_replace('.','%2c',$orderData['total']);
        $orderTotal = $orderData['total'];
        $invoiceUrl = GMA_getUrlToSeeInvoiceInPdf($order);
        
        //### URL ###//
        switch($order->get_shipping_method()){
            case REST_OF_THE_COUNTRY:
                $msg = "*_¡Hola $customerName!_* Recibimos tu pedido correctamente."
                    ."\n"
                    ."\nPara que podamos enviar tu pedido por _Mercado Envíos - (Correo Argentino)_ debes comprar la siguiente publicación *con envío*: bit.ly/38nbNCo. 📦"
                    ."\n"
                    ."\n_(Si necesitas que lo despachemos por otra logística, avísanos)_"
                    ."\n"
                    ."\n_Gracias por tu compra_ 😄"
                    ."\n"
                    ."\n_Factura:_ ";
                break;
            case PICK_UP_LOCALLY:
                $msg = "*_¡Hola $customerName!_* Recibimos tu pedido correctamente. Podes retirar por nuestro depósito. 📦"
                    ."\n"
                    ."\n*Vera Mujica 361 PB, Rosario*"
                    ."\n_(Lunes a Sabados de 10 a 16h)_"
                    ."\n"
                    ."\n_Gracias por tu compra_ 😄"
                    ."\n"
                    ."\n_Factura:_ ";
                break;
            case '':
                //virtual product
                $msg = "*_¡Hola $customerName!_* Recibimos tu pedido correctamente. En breve se te enviará el PDF. 📕"
                    ."\n"
                    ."\n_Gracias por tu compra_ 😄"
                    ."\n"
                    ."\n_Factura:_ ";
                break;
            case SHIPPING_IN_ROSARIO:
                $msg = "*_¡Hola $customerName!_* Recibimos tu pedido correctamente. El recorrido inicia todos los dias a las *15hs*. 🛵"
                    ."\n"
                    ."\n_(Ni bien lo despachemos te avisamos)_"
                    ."\n"
                    ."\n_Gracias por tu compra_ 😄"
                    ."\n"
                    ."\n_Factura:_ ";
                break;
            default:
                $msg = "*_¡Hola $customerName!_* Recibimos tu pedido correctamente. El recorrido inicia todos los dias a las *15hs*. 🛵"
                    ."\n"
                    ."\n_(Ni bien lo despachemos te avisamos)_"
                    ."\n"
                    ."\n_Gracias por tu compra_ 😄"
                    ."\n"
                    ."\n_Factura:_ ";
                break;
        }
        // ###### DELETE THIS COMMENT ####### //
        // .'%0ATe%20adjuntamos%20la%20factura%20de%20tu%20pedido.'
        // .'%0ATotal:%20$'.number_format($orderTotal,2,',','.')
        // .'%0A%0A'.$invoiceUrl;
        $msg = urlencode($msg).$invoiceUrl;
        $wspUrl = "https://api.whatsapp.com/send?phone=$customerPhone&text=$msg";
        return $wspUrl;
    }else{
        return "orderId undefined";
    }
}
?>