<?php
// ========== CONTACTS FUNCTIONS ========== //
/**
 * This function saves the name, phone number and email adrees of the customer.
 * Executed in thank you page.
 **/
function GMA_saveContact($orderId = null){
    if( $orderId === null ){
        ?>
        <div class='gma-actionMessage error'>
            <span>Ha ocurrido un error:<br>ID de orden nula.</span>
        </div>
        <?php
        return;
    }  
    
    // VALUES TO INSERT.
    $order = wc_get_order($orderId);
    $orderData = $order->get_data(); // The Order data
    $name = $orderData['billing']['first_name'];
    $email = $orderData['billing']['email'];
    $phone = $orderData['billing']['phone'];
    if(preg_match("/^\+54/", $phone) === 0){
        $phone = "+54".$phone;
    }

	$connection = gma_connectToDB();
	if($connection){
        // If the data is already in the database, it returns without doing anything.
        $sql = "SELECT * FROM gma_contacts WHERE email = '$email' OR phone = '$phone'";
        $result = mysqli_query($connection,$sql);
        $numRows = mysqli_num_rows($result);
        if($numRows > 0){
            return;
        }
		$sql = "SELECT * FROM gma_contacts";
        $result = mysqli_query($connection,$sql);
        $numRows = mysqli_num_rows($result);
        
        // Calculate the new ID.
        $possibleId = $numRows + 1;
        $sql2 = "SELECT * FROM gma_contacts WHERE id = $possibleId";
        $result2 = mysqli_query($connection, $sql2);
        $numRows2 = mysqli_num_rows($result2);
        if($numRows2 > 0){
            $possibleId = 0;
            do{
                $possibleId++;
                $sql3 = "SELECT * FROM gma_contacts WHERE id = $possibleId";
                $result3 = mysqli_query($connection, $sql3);
                $numRows3 = mysqli_num_rows($result3);
            }while($numRows3 > 0);
        }
        // Insert values.
        $sql = "INSERT INTO gma_contacts VALUES ($possibleId,'$name','$phone','$email')";
        mysqli_query($connection, $sql);
	}else{
        return;
    }
	mysqli_close($connection);
    return;
}
function GMA_printContacts(){
    $connection = gma_connectToDB();
    if($connection){
        $contactCount = 0;
        $contacts = [];
        $sql = "SELECT * FROM gma_contacts";
        $result = mysqli_query($connection,$sql);
        while ($resultArr = mysqli_fetch_array($result)) {
            $contact = array(
                "id"=>$resultArr[0],
                "name"=>$resultArr[1],
                "phone"=>$resultArr[2],
                "email"=>$resultArr[3],
            );
            $contacts[$contactCount] = $contact;
            $contactCount++;
        }
        ?>
        <h1>Contactos</h1>
        <table>
            <tr>
                <th>Id <input type="checkbox" id="selectOrDeselectAll"></th>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Correo</th>
            </tr>
        <?php
        for ($i=0; $i < $contactCount; $i++) { 
            $id = $contacts[$i]["id"];
            $name = $contacts[$i]["name"];
            $phone = $contacts[$i]["phone"];
            $email = $contacts[$i]["email"];
            ?>
            <tr>
                <td><input type='checkbox' id='gma-contact_<?php echo $id; ?>'><?php echo $id; ?></td>
                <td><?php echo $name; ?></td>
                <td><?php echo $phone; ?></td>
                <td><?php echo $email; ?></td>
            </tr>
            <?php
        }
        ?>  
        </table>
        <nav class="actionBar">
            <button id='deleteBtn'>Eliminar</button>
            <a href='<?php echo GMA_THEME_PATH; ?>phpModules/ContactsExcelGenerator.php'><button>Descargar Excel</button></a>
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
/**
 * Delete the selected contacts.
 **/
function GMA_deleteContacts(){
    $selectionArr = explode(',', $_POST['selection']);
    $selectionCount = count($selectionArr);
    $connection = gma_connectToDB();
    $count = 0;
    foreach ($selectionArr as $id) {
        $sql = "DELETE FROM `gma_contacts` WHERE id = '$id'";
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