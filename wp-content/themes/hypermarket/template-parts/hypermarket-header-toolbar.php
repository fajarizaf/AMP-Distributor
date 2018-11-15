<?php
/**
 * Displaying header toolbar links.
 *
 * @package 	Hooked into "hypermarket_header_area"
 * @author  	Mahdi Yazdani
 * @package 	Hypermarket
 * @since 	    1.0.4.1
 */
?>
<!-- Toolbar -->
<div class="toolbar">
    <div class="inner">

            <a class="btnclaimgaransi" style="width: 180px;" href="<?php echo get_bloginfo('url') ?>/dashboard/form-klaim">
                <input type="button" class="btn-claim" value="KLAIM GARANSI"/ >
            </a>

        <?php
            if(!hypermarket_is_woocommerce_activated()):
                echo '<a href="#" class="mobile-menu-toggle menu-text-right"><i class="material-icons menu"></i></a>';
            else:
                echo '<a href="#" class="mobile-menu-toggle"><i class="material-icons menu"></i></a>';
            endif;
            echo '<a href="'.site_url().'/dashboard/"><i class="material-icons person"></i></a>';
            // Append WooCommerce Mini Cart
        	if (apply_filters('hypermarket_header_toolbar_mini_cart', true) && hypermarket_is_woocommerce_activated()):
        ?>
		        <div class="cart-btn">
	            	<?php do_action('hypermarket_items_present_in_cart'); ?>		
		            <!-- Cart Dropdown -->
		            <div class="cart-dropdown">
		            	<?php the_widget( 'WC_Widget_Cart', 'title=' ); ?>
					</div>
		            <!-- .cart-dropdown -->
		        </div>
		        <!-- .cart-btn -->
    	<?php endif; ?>
    </div>
    <!-- .inner -->
</div>
<!-- .toolbar -->