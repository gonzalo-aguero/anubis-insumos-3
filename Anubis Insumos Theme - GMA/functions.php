<?php
require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/templateFunctions.php');
require_once(__DIR__ . '/phpModules/GMA_Admin.php');
require_once(__DIR__ . '/phpModules/Invoice.php');
require_once(__DIR__ . '/libraries/Sendinblue/APIv3-php-library/vendor/autoload.php');
require_once(__DIR__ . '/phpModules/Mail.php');
require_once(__DIR__ . '/phpModules/ThankYouPage.php');

function GMA_Styles(){
    wp_register_style(
        'theme_style',
        'https://anubisinsumos.com/wp-content/themes/Anubis Insumos Theme - GMA/combined_theme_styles.min.css?v1.0.4',
        array(),
        '5.3.4',
        'all'
    );
    wp_enqueue_style('theme_style');
}
/**
 * Gets the function name through post and runs it.
 **/
function postAndExecute(){
    if(!isset($_POST['gma-action']))
        return;
    
    $action = $_POST['gma-action'];
    switch ($action) {
        case 'deleteContacts':
            GMA_deleteContacts();
            break;
        case 'deleteDayOrders':
            GMA_deleteDayOrders();
            break;
        case 'saveDiscounts':
            GMA_saveDiscounts();
            break;
        case 'saveFreeShippings':
            GMA_saveFreeShippings();
            break;
        case 'saveEditableZones':
            $GMA_ThankYouPage = new GMA_ThankYouPage();
            $GMA_ThankYouPage->saveEditableZones();
            break;
        default:
            echo "GMA-ERROR[invalid action name]";
            break;
    }
}
/**
 * Gets the function name through get and runs it.
 **/
function getAndExecute(){
    if(isset($_GET["gma-action"])){
        $function = $_GET["gma-action"];
        switch ($function) {
            case md5("printContacts"):
                GMA_printContacts();
                break;
            case md5("printDayOrders"):
                GMA_printDayOrders();
                break;
            case md5("submitToShippingExcel"):
                GMA_submitToShippingExcel($_GET['order_id']);
                break;
            case md5("orderDispatched"):
                GMA_orderDispatched($_GET['order_id']);
                break;
            case md5("printDiscounts"):
                GMA_printDiscounts();
                break;
            case md5("printFreeShippings"):
                GMA_printFreeShippings();
                break;
            case md5("editableZones"):
                $GMA_ThankYouPage = new GMA_ThankYouPage();
                $GMA_ThankYouPage->printEditableZones();
                break;
            default:
                echo "
                <div class='gma-actionMessage error'>
                    <span>GMA-ERROR[invalid action name]</span>
                </div>
                ";
                break;
        }
    }
}
/**
 * Gets the name of the JavaScript function through get and prints a script with this function to execute it.
 **/
function getAndExecuteJSFunction(){
    // params={productId:603}
    if(isset($_GET['jsf'])){
        $functionName = $_GET['jsf'];
        if(isset($_GET['params'])){
            $parameters = $_GET['params'];   
        }else{
            $parameters = "";
        }
        ?>
        <script>
            try{
                <?php echo $functionName; ?>(<?php echo $parameters; ?>);
            }catch(error){
                console.warn("Error en getAndExecuteJSFunction()\n",error);
            }
        </script>
        <?php
    }
}


// ======== Other functions ======== //
/**
 * Get the selected attribute values to add product to cart via ajax.
 **/
function getVariationAtributeValues(){
    $atributeValues = $_POST["atributeValues"];
    $atributeValuesToSend = [];
    foreach($atributeValues as $key => $value){
        $atributeValuesToSend[$key] = $value;
    }
    return $atributeValuesToSend;
}
/**
 * Add product to cart (for AJAX). It's used in the product detail blocks.
 **/
function woocommerce_ajax_add_to_cart() {
    $product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['product_id']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = absint($_POST['variation_id']);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
    $product_status = get_post_status($product_id);
    
    $variationAtributeValues = getVariationAtributeValues();
    
    if($passed_validation && WC()->cart->add_to_cart($product_id, $quantity, $variation_id , $variationAtributeValues) && 'publish' === $product_status){
        do_action('woocommerce_ajax_added_to_cart', $product_id);
        if('yes' === get_option('woocommerce_cart_redirect_after_add')){
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }
        WC_AJAX :: get_refreshed_fragments();
    }else{
        $data = array(
                'error' => true,
                'product_url' => apply_filters('woocommerce_cart_redirect_after_error', get_permalink($product_id), $product_id)
            );
        echo wp_send_json($data);
    }
    wp_die();
}
/**
 * Returns true if the value passed as a parameter is not empty and is not null. Else it returns false.
 **/
function noEmptyNoNull($value){
    if($value !== "" && $value !== null){
        return true;
    }else{
        return false;
    }
}
/**
 * Update the cart counter.
 **/
function misha_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	$fragments['.misha-cart'] = '<span class="misha-cart" id="cartCount">'.$woocommerce->cart->cart_contents_count .'</span>';
 	return $fragments;
 }
/***
 * Disable the unnecessary fields of the purchase form.
 **/
function custom_override_checkout_fields( $fields ) {
    // The commented lines correspond to the active fields.
    
    // unset($fields['billing']['billing_first_name']);
    // unset($fields['billing']['billing_email']);
    // unset($fields['billing']['billing_phone']);
    // unset($fields['billing']['billing_city']);
    // unset($fields['billing']['billing_address_1']);
    // unset($fields['billing']['billing_address_2']);
    // unset($fields['order']['order_comments']);
    
    unset($fields['billing']['billing_last_name']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_country']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_postcode']);
    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_last_name']);
    return $fields;
}
function searchPageStyle($query){
    if ( !is_admin() && $query->is_main_query() ) {
        if ($query->is_search) {
            ?>
            <style>
                #main{
                    padding: 2em;
                    text-align: center;
                }
                #main .storefront-sorting {
                    display: flex;
                    flex-flow: row wrap;
                    justify-content: center;
                }
            </style>
            <?php
        }
    }
}

// ======== End Other functions ======== //


if(function_exists("add_action") === true){
    add_action('wp_ajax_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
    add_action('wp_ajax_nopriv_woocommerce_ajax_add_to_cart', 'woocommerce_ajax_add_to_cart');
    add_action('pre_get_posts','searchPageStyle');
    add_action('wp_enqueue_scripts', 'GMA_Styles');
}
if(function_exists("add_filter") === true){
    add_filter( 'woocommerce_add_to_cart_fragments', 'misha_add_to_cart_fragment' );
    add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields' );
}
postAndExecute();
?>