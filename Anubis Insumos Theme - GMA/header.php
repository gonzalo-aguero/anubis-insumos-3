<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php do_action( 'storefront_before_site' ); ?>

<div id="page" class="hfeed site">
	<?php do_action("GMA_beforeHeader"); ?>
	<header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">
	    <div class="mainHeader">
            <div id="siteIdentity">
                <a href="<?php echo GMA_DOMAIN; ?>">
                    <img src="<?php echo GMA_THEME_PATH;?>assets/images/logoAfter-v2.jpg">
                    <h1 class="_0">Anubis</h1>
                    <h1 class="_1">Insumos</h1>
                </a>
            </div>
            <div id="mainNavContainer">
                <span class="icon-menu openMenuBtn"></span>
                <span class="animate__animated animate__fadeIn closeMenuBtn"></span>
                <nav class="animate__animated">
                    <span class="icon-cross closeMenuBtn"></span>
                    <li><a href="<?php echo GMA_DOMAIN; ?>" class="icon-home"><span>Home</span></a></li>
                    <li><a href="<?php echo GMA_FREQUENT_QUESTIONS_URL; ?>" class="icon-question"><span>Preguntas frecuentes</span></a></li>
                    <li><a class="icon-cart"><span class="misha-cart" id="cartCount">0</span><span>Carrito</span></a></li>
                    <li><a class="icon-search" id="searchBtn"><span>Buscar</span></a></li>
                </nav>
            </div>
        </div>
        <!-- Category Navigation -->
        <nav id="categoryNavigation">
            <ul>
                <?php
                echo GMA_SCROLL_X_CONTROL_LEFT;
                echo GMA_SCROLL_X_CONTROL_RIGHT;
                $cat_args = array(
                    'orderby'    => 'name',
                    'order'      => 'asc',
                    'hide_empty' => true,
                );
                $productCategories = get_terms( 'product_cat', $cat_args );
                if( !empty($productCategories) ){
                    foreach ($productCategories as $key => $category) {
                        echo "<li><a href='".GMA_DOMAIN."#$category->name'>$category->name</a></li>";
                    }
                }
                ?>
            </ul>
        </nav>
	</header><!-- #masthead -->
	<div id="cartPreview">
        <?php //the_widget( 'WC_Widget_Cart', 'title=' ); ?>
        <div class="GMA widget woocommerce widget_shopping_cart">
            <div class="widget_shopping_cart_content"></div>
        </div>
        <span class="closeSpan"></span>
    </div>
	<div id="transparentDarkBackground"></div>
	<?php
	/**
	 * Functions hooked in to storefront_before_content
	 *
	 * @hooked storefront_header_widget_region - 10
	 * @hooked woocommerce_breadcrumb - 10
	 */
	?>
	<div id="content" class="site-content" tabindex="-1">
		<div class="col-full">