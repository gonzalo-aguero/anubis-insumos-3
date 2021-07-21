<?php 
// ====================================== //
// ====== GMA ADMIN PAGE FUNCTIONS ====== //
// ====================================== //
if(function_exists("add_shortcode") === true){
    add_shortcode('GMA_ADMIN_PAGE', 'GMA_AdminPageInit'); 
}

/**
 * This function runs on the GMA Admin Page through a [shortcode].
 **/
function GMA_AdminPageInit(){
    $pageName = get_query_var('pagename');
    if($pageName === 'gma-admin'){
        $contactsUrl = GMA_ADMIN_URL."?gma-action=".md5("printContacts");
        $dayOrdersUrl = GMA_ADMIN_URL."?gma-action=".md5("printDayOrders");
        $discountsUrl = GMA_ADMIN_URL."?gma-action=".md5("printDiscounts");
        $freeShippingsUrl = GMA_ADMIN_URL."?gma-action=".md5("printFreeShippings");
        $editableZonesUrl = GMA_ADMIN_URL."?gma-action=".md5("editableZones");
        ?>
        <section id='GMA-Admin-Content'>
            <nav id='primaryNavigation'>
                <li><a href='<?php echo $contactsUrl; ?>'>Contactos</a></li>
                <li><a href='<?php echo $dayOrdersUrl; ?>'>Pedidos del día</a></li>
                <li><a href='<?php echo $discountsUrl; ?>'>Descuentos</a></li>
                <li><a href='<?php echo $freeShippingsUrl; ?>'>Envíos gratis</a></li>
                <li><a href='<?php echo $editableZonesUrl; ?>'>Zonas editables</a></li>
            </nav>
            <?php
            getAndExecute();
            ?>
        </section>
        <?php
    }
}
/**
 * Returns the database connection.
 **/
function gma_connectToDB(){
	$connection = mysqli_connect( GMA_DB_HOST, GMA_DB_USER, GMA_DB_PASSWORD, GMA_DB_NAME);
	mysqli_set_charset($connection,'utf8');
	return $connection;
}


require_once(__DIR__ . '/Contacts.php');
require_once(__DIR__ . '/ShippingExcel.php');
require_once(__DIR__ . '/FreeShipping.php');
require_once(__DIR__ . '/Discounts.php');
?>