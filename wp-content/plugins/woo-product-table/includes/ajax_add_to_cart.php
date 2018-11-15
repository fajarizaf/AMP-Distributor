<?php

/**
 * Adding Item by Ajax. This Function is not for using to any others whee.
 * But we will use this function for Ajax
 * 
 * @since 1.0.4
 * @date 28.04.2018 (D.M.Y)
 * @updated 04.05.2018
 */
function wpt_ajax_add_to_cart() {
    $product_id = ( isset($_GET['add-to-cart']) && !empty($_GET['add-to-cart']) ? $_GET['add-to-cart'] : false );
    $quantity = ( isset($_GET['quantity']) && !empty($_GET['quantity']) && is_numeric($_GET['quantity']) ? $_GET['quantity'] : 1 );
    $variation_id = ( isset($_GET['variation_id']) && !empty($_GET['variation_id']) ? $_GET['variation_id'] : false );
    $variation = ( isset($_GET['variation']) && !empty($_GET['variation']) ? $_GET['variation'] : false );

    if (isset($_COOKIE['wpt_fix_double_curt_for_1_second']) && $_COOKIE['wpt_fix_double_curt_for_1_second'] == 'already_added_to_cart') {
        setcookie('wpt_fix_double_curt_for_1_second', 'already_added_to_cart', time() - 1);
        wpt_adding_to_cart($product_id, $quantity, $variation_id, $variation);
    } else {
        wpt_live_cart_for_table();
        //echo wc_get_template( 'cart/mini-cart.php' ); //Used as just Test. nor for this Plugin
        setcookie('wpt_fix_double_curt_for_1_second', 'already_added_to_cart', time() + 1);
    }
    
    die();
}

add_action('wp_ajax_wpt_ajax_add_to_cart', 'wpt_ajax_add_to_cart');
add_action('wp_ajax_nopriv_wpt_ajax_add_to_cart', 'wpt_ajax_add_to_cart');

/**
 * To use in Action Hook for Ajax
 * for Multiple product adding to cart by One click
 * 
 * @since 1.0.4
 * @version 1.0.4
 * @date 3.5.2018
 * return Void
 */
function wpt_ajax_multiple_add_to_cart() {
    $products = false;
    if (isset($_POST['products']) && is_array($_POST['products'])) {
        $products = $_POST['products'];
    }
    wpt_adding_to_cart_multiple_items($products);
    wpt_live_cart_for_table();
    
    die();
}

add_action('wp_ajax_wpt_ajax_mulitple_add_to_cart', 'wpt_ajax_multiple_add_to_cart');
add_action('wp_ajax_nopriv_wpt_ajax_mulitple_add_to_cart', 'wpt_ajax_multiple_add_to_cart');

/**
 * Adding Item to cart by Using WooCommerce WC() Static Object
 * WC()->cart->add_to_cart(); Need few Perameters
 * Normally we tried to Check Each/All Action, When Adding
 * 
 * @param Int $product_id
 * @param Int $quantity
 * @param Int $variation_id
 * @param Array $variation
 * @return Void
 */
function wpt_adding_to_cart( $product_id = false, $quantity = false, $variation_id = false, $variation = false ){
    if( !WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) ){
        return false;
    }
}

/**
 * Adding Multiple product to Cart by One click. So we used an Array
 * Array's each Item has indivisual Array with product_id,variation_id,quantity,variations's array
 * 
 * @param Array $products Product's Array which will use for adding to cart
 * @return Void
 */
function wpt_adding_to_cart_multiple_items( $products = false ){
    if ( $products && is_array($products) ){
        foreach ($products as $product) {
            $product_id = ( isset($product['product_id']) && !empty($product['product_id']) ? $product['product_id'] : false );
            $quantity = ( isset($product['quantity']) && !empty($product['quantity']) && is_numeric($product['quantity']) ? $product['quantity'] : 1 );
            $variation_id = ( isset($product['variation_id']) && !empty($product['variation_id']) ? $product['variation_id'] : false );

            $variation = ( isset($product['variation']) && !empty($product['variation']) ? $product['variation'] : false );
        
            wpt_adding_to_cart( $product_id, $quantity, $variation_id, $variation );
        }
    }else{
        return false;
    }
}
/**
 * Removing Added to cart Message. Actualy If we add cart item programitically,
 * and visit any page, Normally display WooCommerce Message. So we have removed
 * by filter wc_add_to_cart_message
 * 
 * @deprecated since 1.7 1.7_3_18.5.2018
 * @return False
 * @updated 18/5/2018 d/m/y
 */
function wpt_remove_add_to_cart_message() {
    return;
}

//add_filter('wc_add_to_cart_message', 'wpt_remove_add_to_cart_message');


/**
 * HTML version for Live cart in Header of Each Table
 * 
 * @global type $woocommerce used $woocommerce variable to get few data for cart live
 * @return self Just it will display element
 */
function wpt_live_cart_for_table() {
    ?>
    <div class="wpt_live_cart_box">
        <?php
        $items = WC()->cart->get_cart();
        global $woocommerce;
        $item_count = $woocommerce->cart->cart_contents_count;
        ?>
        <div class="wpt_live_cart_header">
            <a class="wpt_cart_totals" href="<?php echo wc_get_cart_url(); ?>" title="<?php _e('View your shopping cart','wpt_table'); ?>"><?php _e('Cart','wpt_table'); ?> (<span><?php echo $item_count; ?></span>)</a>
            <?php if ($items) { ?>
                <div class="wpt_live-cart-subtotal">
                    <strong>Subtotal: <?php echo WC()->cart->get_cart_total(); ?></strong>
                </div>

                <?php
                $cart_url = $woocommerce->cart->get_cart_url();
                $checkout_url = $woocommerce->cart->get_checkout_url();
                ?>

                <div class="wpt_live-cart-other_link">
                    <a href="<?php echo $cart_url; ?>"><?php _e('View Cart','wpt_table'); ?></a>
                    <a href="<?php echo $checkout_url; ?>"><?php _e('Checkout','wpt_table'); ?></a>
                </div>

            <?php } ?>
        </div>
        <div class="cart-dropdown">
            <div class="cart-dropdown-inner">
                <?php if ($items) { ?>
                    <ul class="wpt_dropdown">
                        <?php
                        foreach ($items as $item => $values) {
                            $_product = $values['data']->post;
                            $full_product = new WC_Product_Variable($values['product_id']);
                            $attributes = $full_product->get_available_variations();
                            $price = 0;
                            if ($values['variation_id'] && is_array($attributes)) {
                                foreach ($attributes as $attribute) {
                                    if ($attribute['variation_id'] == $values['variation_id']) {
                                        $price = $attribute['display_price'];
                                    }
                                }

                                $sale = false;
                            } else {
                                $price = get_post_meta($values['product_id'], '_regular_price', true);
                                $sale = get_post_meta($values['product_id'], '_sale_price', true);
                            }
                            ?>

                            <li class="li">
                                <span class="wpt_cart_title"><?php echo $_product->post_title; ?></span>

                                <?php
                                $currency = get_woocommerce_currency_symbol();
                                ?>

                                <?php if ($sale) { ?>
                                    <strong class="price"><strong><?php _e('Price:','wpt_table'); ?></strong> <del><?php
                                    echo $currency;
                                    echo $price;
                                    ?></del> <?php
                                            echo $currency;
                                            echo $sale;
                                            ?></strong>
                                        <?php } elseif ($price) { ?>
                                    <strong class="price"><strong><?php _e('Price:','wpt_table'); ?></strong> <?php
                            echo $currency;
                            echo $price;
                                            ?></strong>    
                                    <?php } ?>
                                <span> X </span>
                                <span class="wpt_cart_quantity"><?php echo $values['quantity']; ?></span>


                            </li>
                        <?php } ?>
                    </ul>

                <?php } else { ?>
                    <div class="dropdown-cart-wrap">
                        <p><?php _e('Your cart is empty.','wpt_table'); ?></p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}
