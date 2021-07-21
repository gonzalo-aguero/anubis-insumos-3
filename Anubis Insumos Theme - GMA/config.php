<?php
/**
 * =============================================
 *                GMA constants
 * =============================================
 **/


// ====== DB CONFIG ====== //
define("GMA_DB_NAME", null);
define("GMA_DB_USER", null);
define("GMA_DB_PASSWORD", null);
define("GMA_DB_HOST", null);
define("GMA_DATA_TABLE","gma_config");

// ====== WEB SITE DATA ====== //
define("GMA_DOMAIN", "https://anubisinsumos.com/");
define("GMA_FREQUENT_QUESTIONS_URL", GMA_DOMAIN."preguntas-frecuentes/");
define("GMA_CART_URL", GMA_DOMAIN."carrito/");
define("GMA_ADMIN_URL", GMA_DOMAIN."gma-admin/");
define("GMA_THEME_PATH", GMA_DOMAIN."wp-content/themes/Anubis Insumos Theme - GMA/");
define("GMA_RELATIVE_THEME_PATH", "wp-content/themes/Anubis Insumos Theme - GMA/");
define("GMA_IMAGES_PATH", GMA_THEME_PATH."assets/images/");

// ====== SHIPPING LABELS ====== //
define("PICK_UP_LOCALLY", "Retiro por deposito en zona terminal");
define("SHIPPING_IN_ROSARIO", "Envio dentro de Rosario");
define("REST_OF_THE_COUNTRY", "RESTO DEL PAIS (consultar costos)");

// ====== CURRENT PAGE DATA ====== //
define("CURRENT_PATH", $_SERVER["REQUEST_URI"]);
define("CURRENT_URL", "https://$_SERVER[HTTP_HOST]".CURRENT_PATH);

// ====== HTML CONSTANTS ====== //
define("GMA_SCROLL_X_CONTROL_LEFT", '<span class="GMA scrollXControl Left">&#60;</span>');
define("GMA_SCROLL_X_CONTROL_RIGHT", '<span class="GMA scrollXControl Right">&#62;</span>');

// ====== CONTACT DATA ====== //
define("GMA_ANUBIS_FROM_EMAIL", "anubisinsumos@hotmail.com");
define("GMA_ANUBIS_FROM_EMAIL_NAME", "Anubis Insumos");
define("GMA_ANUBIS_TO_EMAIL", "anubisinsumos@gmail.com");
// define("GMA_ANUBIS_TO_EMAIL", "gmadesarrolloweb@gmail.com");
define("GMA_ANUBIS_TO_EMAIL_NAME", "Anubis Insumos");
define("GMA_ANUBIS_PHONE_NUMBER", "5493412575916");

// ====== DEVELOPER DATA ====== //
define("GMA_DEVELOPER_NAME", "Gonzalo Agüero");
define("GMA_DEVELOPER_WEB_SITE", "https://gmadesarrolloweb.ml/");
define("GMA_DEVELOPER_WEB_SITE_NAME", "GMA Desarrollo web");
define("GMA_DEVELOPER_WEB_SITE_FAVICON", GMA_DEVELOPER_WEB_SITE."src/media/img/favicon/logo4.png");

// ====== ANUBIS DATA ====== //
define("GMA_ANUBIS_NAME", "Anubis Insumos");
define("GMA_ANUBIS_INSTAGRAM", "https://www.instagram.com/AnubisInsumos/");
define("GMA_ANUBIS_FACEBOOK", "https://www.facebook.com/anubisinsumos");
define("GMA_ANUBIS_WHATSAPP", "https://api.whatsapp.com/send?phone=".GMA_ANUBIS_PHONE_NUMBER );
define("GMA_ANUBIS_MERCADO_LIBRE", "https://listado.mercadolibre.com.ar/_CustId_709485815");
define("GMA_ANUBIS_PRICE_LIST", "https://bit.ly/3mLWIQt");
define("GMA_ANUBIS_COPYRIGHT", "Copyright © 2020-2021");
?>