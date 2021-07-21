<?php
// ========== THANK YOU PAGE ========== //
$GMA_ThankYouPage;
if(function_exists("add_action") === true){
    add_action('GMA_beforeThankYouPageContent', 'GMA_ThankYouPageInit');
}
/**
 * This function is executed on the Thank You Page through the hook "GMA_beforeThankYouPageContent".
 **/
function GMA_ThankYouPageInit(){
    global $GMA_ThankYouPage;
    $GMA_ThankYouPage = new GMA_ThankYouPage();
}
class GMA_ThankYouPage{
    public $editableZones;//array
    public $dataKey = "ThankYouPage";
    function __construct() {
        $this->getEditableZones();
    }
    public function page(){
        global $wp;
        if ( isset($wp->query_vars['order-received']) ) {
            $orderId = absint($wp->query_vars['order-received']); // The order ID
            GMA_newOrderEmail($orderId);
            
            /**
             * By default Woocommerce when the payment method is "cod" (cash on delivery), 
             * it set the order status as "processing".This function changes his status to "on-hold".
             */
            $order = wc_get_order($orderId);
            $data = $order->get_data();
            if($data['payment_method'] === "cod"){
                $order->set_status("on-hold");
                $order->save();
            }
            
            GMA_purchaseConfirmationEmail($orderId);
            GMA_saveContact($orderId);
        }else{
            echo "<h2>Lo sentimos, ha ocurrido un error (no order id).</h2>";
        }
    }
    private function getEditableZones(){
        $connection = gma_connectToDB();
        $tableName = GMA_DATA_TABLE;
        $sql = "SELECT data FROM $tableName WHERE dataKey = 'ThankYouPage'";
        $result = mysqli_query($connection , $sql);
        $resultArr = mysqli_fetch_array($result);
        $data = json_decode($resultArr['data']);
        $this->editableZones = $data->editableZones;
    }
    public function printEditableZones(){
        ?>
        <h1>Zonas editables</h1>
        <h2>Página "Gracias por tu compra"</h2>
        <table>
            <tr>
                <th>Número</th>
                <th>Contenido</th>
            </tr>
        <?php
        foreach($this->editableZones as $index => $content){
            $content = str_replace("<br>", "\n", $content);
            $content = str_replace('\"', '"', $content);
            ?>
            <tr>
                <td><? echo $index + 1; ?></td>
                <td><textarea class="editableZoneInput" id="editableZone_<?php echo $index; ?>" placeholder="Campo vacío = Zona desactivada"><?php echo $content; ?></textarea></td>
            </tr>
            <?php
        }
        ?>
        </table>
        <nav class="actionBar">
            <button id="saveEditableZones">Guardar cambios</button>
        </nav>
        <h2>Imagen de ejemplo</h2>
        <div class="explanationImages">
            <div>
                <h3>Zonas editables</h3>
                <img src="<?php echo GMA_IMAGES_PATH; ?>editableZones.jpg">
            </div>
        </div>
        <?php
    }
    public function saveEditableZones(){
        // $data = str_replace(array("\r\n", "\n\r", "\r", "\n"), "\n", $_POST['data']);
        $data = '{"editableZones":'.$_POST['data'].'}';
        $connection = gma_connectToDB();
        if($connection){
            $tableName = GMA_DATA_TABLE;
            $sql = "UPDATE $tableName SET data = '$data' WHERE dataKey = '$this->dataKey'";
            if(mysqli_query($connection,$sql)){
                echo 1; 
            }else{
                echo 0;
            }
            mysqli_close($connection);
        }else{
            echo "Error en la conexión a la base de datos";
        }
    }
}
?>