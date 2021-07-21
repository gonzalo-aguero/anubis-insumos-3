<?php
// ========== MAIL FUNCTIONS ========== //
/**
 * Send to Anubis Insumos a "New Order" email.
 * @param $orderId
 **/
function GMA_newOrderEmail($orderId){
    // ====== Get the order data ====== //
    $order = wc_get_order($orderId);
    $data = $order->get_data();
    $invoiceUrl = GMA_getUrlToSeeInvoiceInPdf($order);//URL to see invoice in PDF.
    $invoiceUrl = str_replace("%26" , "&", $invoiceUrl);
    $customerWspUrl = GMA_invoiceViaWhatsAppURL($orderId);//URL to send invoice to the customer via WhatsApp.
    
    // ====== Headers ====== //
    header("api-key: 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");
    header("Content-Type: application/json");
    header("accept: Application/json");
    //Configure API key authorization: api-key
    // ====== API configuration ====== //
    $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
    $apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
    $sendSmtpEmail['sender'] = array(
            'name'=> GMA_ANUBIS_FROM_EMAIL_NAME,
            'email'=> GMA_ANUBIS_FROM_EMAIL
        );
    $sendSmtpEmail['to'] = array(array(
            'email'=> GMA_ANUBIS_TO_EMAIL,
            'name'=> GMA_ANUBIS_TO_EMAIL_NAME
        ));
    
    // ====== Html of the items ====== //
    $itemsHtml = "";
    $cartTotal = 0;
    $items = $order->get_items(); 
    if( sizeof( $items ) > 0 ) : 
        foreach( $items as $item_id => $item ) :
            $itemTotal = 0;
            $itemsHtml .= "
                <tr>
                    <td class='_0'>".$item['name']."</td>
                    <td class='_1'>".'$'.number_format($item['subtotal'],2,',','.')."</td>
                    <td class='_2'>".$item['quantity']."</td>
                    <td class='_3'>".'$'.number_format($item['total'],2,',','.')."</td>
                </tr>
            ";
            $cartTotal += $item['total'];
        endforeach;
    endif;
    $shippingTotal = $order->get_shipping_total();
    $discount = $data['discount_total'];
    $total = $data['total'];
    
     // ====== Email body ====== //
    $sendSmtpEmail['subject'] = "Nuevo pedido (#$orderId)";
    $sendSmtpEmail['htmlContent'] = "
        <html>
        <head>
            <style>
                ".GMA_getEmailStyles()."
            </style>
        </head>
        <body>
            <div id='content'>
                <h1>Nuevo pedido</h1>
                <p>Hola Anubis Insumos<br> Se ha realizado un nuevo pedido.</p>
                <span>¡Felicitaciones!</span>
                <div id='order'>
                    <h2>Items del pedido</h2>
                    <table>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Unidades</th>
                            <th>Subtotal</th>
                        </tr>
                        $itemsHtml
                        <tr>
                            <td></td><td></td><td>Subtotal</td>
                            <td>$".number_format($cartTotal,2,',','.')."</td>
                        </tr>
                        <tr>
                            <td></td><td></td><td>Descuento</td>
                            <td>$".number_format($discount,2,',','.')."</td>
                        </tr>
                        <tr>
                            <td></td><td></td><td>Envío</td>
                            <td>$".number_format($shippingTotal,2,',','.')."</td>
                        </tr>
                    </table>
                    <div id='total'><span>Total: ".'$'.number_format($total,2,',','.')."</span></div>
                    <div id='seeInvoice'>
                        <div>
                            <a href='$invoiceUrl'>Ver factura</a>
                        </div>
                        <span>Si estás desde tu móvil se descargará
                        <br>automáticamente con el nombre 'invoice-x.pdf'.
                        <br>(La x será el número de factura)</span>
                    </div>
                    <a id='sendToCustomer' href='$customerWspUrl'>Enviar al cliente por WhatsApp</a>
                </div>
                <div class='footer'>
                    <h2>Anubis Insumos</h2>
                    <h3>Copyright © 2020-2021</h3>
                </div>
            </div>
        </body>
        </html>
        ";
    try {
        // ====== Send email ====== //
        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        header("Content-Type: text/html");
        header("accept: text/html");
        // print_r($result); 
    } catch (Exception $e) {
        ?>
        <div class='gma-actionMessage error'>
            <span>
                Error al enviar correo nuevo pedido.<br>
                Exception when calling TransactionalEmailsApi->sendTransacEmail: <?php echo $e->getMessage(). PHP_EOL; ?>
            </span>
        </div>
        <?php
    }
}
/**
 * Send an "Purchase confirmation" email for the customer via Sendinblue.
 * @param int $orderId.
 **/
function GMA_purchaseConfirmationEmail($orderId){
    // ====== Get the order data ====== //
    $order = wc_get_order($orderId);
    $data = $order->get_data();
    $invoiceUrl = GMA_getUrlToSeeInvoiceInPdf($order);//URL to see invoice in PDF.
    $invoiceUrl = str_replace("%26" , "&", $invoiceUrl);
    
    // ====== Headers ====== //
    header("api-key: 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");
    header("Content-Type: application/json");
    header("accept: Application/json");
    // ====== API configuration ====== //
    $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
    $apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
        new GuzzleHttp\Client(),
        $config
    );
    $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
    $sendSmtpEmail['sender'] = array(
            'name'=> GMA_ANUBIS_FROM_EMAIL_NAME,
            'email'=> GMA_ANUBIS_FROM_EMAIL
        );
    $sendSmtpEmail['to'] = array(array(
            'email'=>$order->get_billing_email(),
            'name'=>$order->get_billing_first_name()
        ));
    
    // ====== Html of the items ====== //
    $itemsHtml = "";
    $cartTotal = 0;
    $items = $order->get_items(); 
    if( sizeof( $items ) > 0 ){
        foreach( $items as $item_id => $item ){
            $itemTotal = 0;
            $itemsHtml .= "
                <tr>
                    <td class='_0'>".$item['name']."</td>
                    <td class='_1'>".'$'.number_format($item['subtotal'],2,',','.')."</td>
                    <td class='_2'>".$item['quantity']."</td>
                    <td class='_3'>".'$'.number_format($item['total'],2,',','.')."</td>
                </tr>
            ";
            $cartTotal += $item['total'];
        }
    }
    $shippingTotal = $order->get_shipping_total();
    $discount = $data['discount_total'];
    $total = $data['total'];
    
    // ====== Email body  ====== //
    $sendSmtpEmail['subject'] = "Pedido recibido (#$orderId)";
    $sendSmtpEmail['htmlContent'] = "
        <html>
        <head>
            <style>
                ".GMA_getEmailStyles()."
            </style>
        </head>
        <body>
            <div id='content'>
                <h1>Pedido recibido</h1>
                <p>Hola ".$order->get_billing_first_name()."<br>Tu pedido fue recibido correctamente, en breve lo estaremos revisando.</p>
                <span>¡GRACIAS!</span>
                <div id='order'>
                    <h2>Tu pedido</h2>
                    <table>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Unidades</th>
                            <th>Subtotal</th>
                        </tr>
                        $itemsHtml
                        <tr>
                            <td></td><td></td><th>Subtotal</th>
                            <td>$".number_format($cartTotal,2,',','.')."</td>
                        </tr>
                        <tr>
                            <td></td><td></td><th>Descuento</th>
                            <td>$".number_format($discount,2,',','.')."</td>
                        </tr>
                        <tr>
                            <td></td><td></td><th>Envío</th>
                            <td>$".number_format($shippingTotal,2,',','.')."</td>
                        </tr>
                    </table>
                    <div id='total'>
                        <span>Total: ".'$'.number_format($total,2,',','.')."</span>
                    </div>
                    <div id='seeInvoice'>
                        <div>
                            <a href='$invoiceUrl'>Ver factura</a>
                        </div>
                        <span>Si estás desde tu móvil se descargará
                        <br>automáticamente con el nombre 'invoice-x.pdf'.
                        <br>(La x será el número de factura)</span>
                    </div>
                </div>
                <div class='footer'>
                    <h2>Anubis Insumos</h2>
                    <h3>Copyright © 2020-2021</h3>
                </div>
            </div>
        </body>
        </html>
        ";
    try {
        // ====== Send email ====== //
        $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
        header("Content-Type: text/html");
        header("accept: text/html");
        // print_r($result); 
    } catch (Exception $e) {
        ?>
        <div class='gma-actionMessage error'>
            <span>
                Exception when calling TransactionalEmailsApi->sendTransacEmail: ", <?php echo $e->getMessage() ?> <?php echo PHP_EOL; ?>, 
            </span>
        </div>
        <?php
    }
}
/**
 * When the second QR code on the invoice is scanned, this function is executed.
 * Sends to the customer an "Order dispatched" email and redirects to the user (the seller) to the customer WhatsApp chat to send the same message.
 **/
function GMA_orderDispatched($orderId = null){
    if($orderId === null){
        echo "
        <div class='gma-actionMessage error'>
            <span>Ha ocurrido un error:<br>Número de orden inválido.<span>
        </div>
        ";
        return;
    }
    
    // ====== Get the customer data ====== //
    $order = wc_get_order($orderId);
    $order_data = $order->get_data();
    $customerEmail = $order_data['billing']['email'];
    $customerName = $order_data['billing']['first_name'];
    $customerPhone = $order_data['billing']['phone'];
    
    // // ====== API configuration ====== //
    // header("api-key: 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");
    // header("Content-Type: application/json");
    // header("accept: Application/json");
    // $config = SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
    // $apiInstance = new SendinBlue\Client\Api\TransactionalEmailsApi(
    //     new GuzzleHttp\Client(),
    //     $config
    // );
    // $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
    // $sendSmtpEmail['sender'] = array(
    //         'name'=>GMA_ANUBIS_FROM_EMAIL_NAME,
    //         'email'=>GMA_ANUBIS_FROM_EMAIL
    //     );
    // $sendSmtpEmail['to'] = array(array(
    //         'email'=>$customerEmail,
    //         'name'=>$customerName
    //     ));
    
    // // ====== Email body ====== //
    // $sendSmtpEmail['subject'] = "Pedido despachado (#$orderId)";
    // $sendSmtpEmail['htmlContent'] = "
    //     <html>
    //     <head>
    //         <style>
    //             ".GMA_getEmailStyles()."
    //         </style>
    //     </head>
    //     <body>
    //         <div id='content'>
    //             <h1>Pedido despachado</h1>
    //             <p>Hola ".$customerName."<br>Tu pedido ya fue despachado correctamente, esperamos que lo disfrutes!</p>
    //             <span>¡GRACIAS!</span>
    //             <p style='display:none;'>
    //                 Una vez que hayas recibido los productos,<br> ¿Te importaría tomarte un minuto para contarnos como fue tu experiencia?
    //             </p>
    //             <a href='#' style='display:none;'>Dejar reseña</a>
    //             <div class='footer'>
    //                 <h2>Anubis Insumos</h2>
    //                 <h3>Copyright © 2020-2021</h3>
    //             </div>
    //         </div>
    //     </body>
    //     </html>
    //     ";
    // try{
    //     // ====== Send email ====== //
    //     $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
    //     header("Content-Type: text/html");
    //     header("accept: text/html");
    //     // print_r($result);// Uncomment to debug.
    //     ?>
         <!--<div class='gma-actionMessage success'>-->
         <!--    <span>Pedido #<?php //echo $orderId; ?> despachado con éxito.<span>-->
         <!--</div>-->
        <?php
    // }catch(Exception $e){
    //     ?>
         <!--<div class='gma-actionMessage error'>-->
         <!--    <span>-->
         <!--        Ha ocurrido un error al intentar enviar correo "Tu pedido ha sido despachado" al cliente.<br>-->
         <!--        Exception when calling TransactionalEmailsApi->sendTransacEmail: , <?php //echo $e->getMessage(); ?>, PHP_EOL, -->
         <!--    </span>-->
         <!--</div>-->
          <?php
    // }
    if(preg_match("/^\+54/", $customerPhone) === 0){
        $customerPhone = "+54".$customerPhone;
    }
        
    switch($order->get_shipping_method()){
        case REST_OF_THE_COUNTRY:
            $msg = null;
            break;
        case PICK_UP_LOCALLY:
            $msg = "_*¡Hola $customerName!* Tu pedido esta listo para retirar cuando gustes_ 🙂"
                ."\n"
                ."\n*Vera Mujica 361 PB, Rosario*"
                ."\n_(Lunes a Sabados de 10 a 16h)_";
            break;
        case '':
            // virtual product
            $msg = null;
            break;
        case SHIPPING_IN_ROSARIO:
            $msg = "_¡Tu pedido ya está en manos del cadete, en breve te va estar llegando!_ 🛵 📦";
            break;
        default:
            $msg = "_¡Tu pedido ya está en manos del cadete, en breve te va estar llegando!_ 🛵 📦";
            break;
    }
    if($msg !== null){
        $msg = urlencode($msg);
        $whatsappUrl = "https://api.whatsapp.com/send?phone=$customerPhone&text=$msg";
        header("Location: $whatsappUrl");
    }else{
        ?>
         <div class='gma-actionMessage error'>
             <span>
                 Ha ocurrido un error:<br>
                 La generación de mensajes de producto listo para retirar o producto despachado no está creada para productos virtuales o productos con envíos al resto del país.
             </span>
         </div>
        <?php
    }
}
/**
 * Returns the styles of the purchase confirmation email.
 **/
function GMA_getEmailStyles(){
    return "
        div#content{
            font-size: 20px;
            position: relative;
            /*min-height: 50em;*/
            border-radius: .25em;
            border: 0.1em #ccbb99 solid;
            text-align: center;
            font-family: sans-serif;
            background-color: #f6f5f2;
            color: #978051;
        }
        div#content > h1{
            font-size: 2.5em; 
            /*border-top-left-radius: 0.25em;
            border-top-right-radius: 0.25em;*/
            padding: 0.35em;
            margin: 0;
            font-weight: 400;
            background-color: #ccbb99;
            color: #826e45;
        }
        div#content > p{
            font-size: 1.1em;
        }
        div#content > span{
            display: block;
            font-size: 2.5em;
            margin-top: .75em;
            margin-bottom: .75em;
            color: #36ba36;
        }
        #order{
            border-top: 0.03em #eae4d6 solid;
            border-bottom: 0.03em #eae4d6 solid;
        }
        #order > h2{
            font-size: 1.65em;
            font-weight: 900;
        }
        #order > table{
            display:block;
            text-align:center;
        }
        #order > table tr{
            width: 100%;
            text-align:center;
            background-color: #f1efe9;
        }
        #order > table tr th,
        #order > table tr td{
            padding: 0.25em;
            text-align:center;
        }
        #order > table tr th{
            font-size: 1.32em;
        }
        #order > table tr td{
            font-size: 1.2em;
        }
        #order tr ._0{
            width: 40%;
        }
        #total{
            display: block;
            text-align: center;
            padding: 1em;
            padding-left: 3.5em;
            padding-right: 3.5em;
        }
        #total > span{
            font-size: 1.6em;
            border: .06em #36ba36 solid;
            color: #36ba36;
            border-radius: .1em;
            padding: .2em;
        }
        #seeInvoice{
            display: block;
            padding-top: 2em;
            padding-bottom: 2em;
        }
        #seeInvoice > div{
            /*text-align:center;*/
        }
        #seeInvoice > div > a{
            text-decoration: none;
            background-color: #36ba36;
            color: white;
            font-size: 2em;
            padding: .05em 2em;
            border-radius: .15em;
            display: inline-block;
            transition: .2s;
        }
        #seeInvoice > div > a:hover{
            transition: .2s;
            opacity: 75%;
        }
        #seeInvoice > span{
            text-align: center;
            font-size: .95em;
            margin-top: 1em;
            display: block;
        }
        div.footer > h2{
            color: #978051;
            font-size: 1.25em;
        }
        div.footer > h3{
            color: #978051;
            font-size: .85em;
        }
        
        a#sendToCustomer{
            font-size: 1.55em;
        }
    ";
}
?>