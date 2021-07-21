<?php
// ========== SHIPPING EXCEL ========== //

/**
 * This function sends the shipping information to the database. This information will then be exported to a CSV file.
 * @param int $orderID
 **/
function GMA_submitToShippingExcel($orderId = null){
    if($orderId === null){
        ?>
        <div class='gma-actionMessage error'>
            <span>Ha ocurrido un error:<br>El número de pedido es inválido.</span>
        </div>
        <?php
    }
    
    //SE OBTIENE LA INFORMACIÓN DEL PEDIDO.
    $order = wc_get_order( $orderId );
    $data = $order->get_data();
    $total_amount = $order->get_total();
    $order_id = $data['id'];
    $shipping_addres = $data['billing']['address_1'];
    $phone = $data['billing']['phone'];
    
    //CONEXIÓN A BASE DE DATOS.
    $connection = gma_connectToDB();
    
    //SE VERIFICA QUE NO SE HAYA CARGADO ANTERIORMENTE.
    $sql = "SELECT * FROM gma_shipping_data WHERE order_id = $orderId";
    $result = mysqli_query($connection,$sql);
    $numRows = mysqli_num_rows($result);
    if($numRows > 0){
        ?>
        <div class='gma-actionMessage error'>
            <span>Ha ocurrido un error:<br>El pedido #<?php echo $orderId; ?> ya fue cargado anteriormente.</span>
        </div>
        <?php
        mysqli_close($connection);   
        return;
    }
    
    //SE CALCULA LA NUEVA ID.
    $sql = "SELECT * FROM gma_shipping_data";
    $result = mysqli_query($connection,$sql);
    $possibleId = mysqli_num_rows($result) + 1;
    $sql2 = "SELECT * FROM gma_shipping_data WHERE id = $possibleId";
    $result2 = mysqli_query($connection, $sql2);
    $numRows2 = mysqli_num_rows($result2);
    if($numRows2 > 0){
        $possibleId = 0;
        do{
            $possibleId++;
            $sql3 = "SELECT * FROM gma_shipping_data WHERE id = $possibleId";
            $result3 = mysqli_query($connection, $sql3);
            $numRows3 = mysqli_num_rows($result3);
        }while($numRows3 > 0);
    }
    $newLogId = $possibleId;
    
    //SI YA SE REALIZÓ EL PAGO EN VEZ DE MOSTRAR EL MONTO SE MOSTRARÁ "[PAGADO]".
    if($data['status'] == "processing" || $data['status'] == "wc-processing"){
        $total_amount = "[PAGADO]";
    }else{
        $total_amount = '$'.number_format($total_amount,2,',','.');
    }
    
    //SE INSERTA EL REGISTRO.
    $sql = "INSERT INTO gma_shipping_data VALUES ($newLogId, $order_id, '$shipping_addres', '$total_amount', '$phone')";
    if(mysqli_query($connection,$sql)){
        ?>
        <div class='gma-actionMessage success'>
            <span>El pedido #<?php echo $orderId; ?> se cargó a la lista "Pedidos del día" correctamente.</span>
        </div>
        <?php
    }else{
        ?>
        <div class='gma-actionMessage error'>
            <span>Ha ocurrido un error al intentar cargar el pedido #<?php echo $orderId; ?> a la lista "Pedidos del día".</span>
        </div>
        <?php
    }
    mysqli_close($connection);
}
/***
 * This function prints a list with the contacts data (name, email adrees and phone).
 **/
function GMA_printDayOrders(){
    $connection = gma_connectToDB();
    if($connection){
        $contactCount = 0;
        $contacts = [];
        $sql = "SELECT * FROM gma_shipping_data";
        $result = mysqli_query($connection,$sql);
        ?>
        <h1>Pedidos del día</h1>
        <table>
            <tr>
                <th>N° <input type="checkbox" id="selectOrDeselectAll"</th>
                <th>Dirección</th>
                <th>Monto</th>
                <th>Teléfono</th>
            </tr>
        <?php
        while ($resultArr = mysqli_fetch_array($result)) {
            $id = $resultArr['id'];
            $order_id = $resultArr['order_id'];
            $shipping_addres = $resultArr['shipping_addres'];
            $amount = $resultArr['amount'];
            $phone = $resultArr['phone'];
            ?>
            <tr>
                <td><input type='checkbox' id='gma-dayOrder_<?php echo $id; ?>'><?php echo $order_id; ?></td>
                <td><?php echo $shipping_addres; ?></td>
                <td><?php echo $amount; ?></td>
                <td><?php echo $phone; ?></td>
            </tr>
            <?php
        }
        ?>
        </table>
        <nav class="actionBar">
            <button id='deleteBtn2'>Eliminar</button>
            <a href='<?php echo GMA_THEME_PATH; ?>phpModules/ShippingExcelGenerator.php'><button>Descargar Excel</button></a>
        </nav>
        <?php
    }else{
        ?>
        <div class='gma-actionMessage error'>
            <span>Error en la conexión a la base de datos.</span>
        </div>
        <?php
    }
    mysqli_close($connection);
}
function GMA_deleteDayOrders(){
    $selectionArr = explode(',', $_POST['selection']);
    $selectionCount = count($selectionArr);
    $connection = gma_connectToDB();
    $count = 0;
    foreach ($selectionArr as $id) {
        $sql = "DELETE FROM `gma_shipping_data` WHERE id = '$id'";
        if(mysqli_query($connection, $sql)){
            $count++;
        }       
    }
    if($count == $selectionCount){
        echo 1;
    }else{
        echo 0;
    }
}
?>