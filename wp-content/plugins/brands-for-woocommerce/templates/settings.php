<div class="wrap">
<?php 
$dplugin_name = 'WooCommerce Brands';
$dplugin_link = 'http://berocket.com/product/woocommerce-brands';
$dplugin_price = 20;
$dplugin_lic   = 39;
$dplugin_desc = '';
@ include 'settings_head.php';
@ include 'discount.php';
?>
<div class="wrap br_settings br_product_brand_settings show_premium">
    <div id="icon-themes" class="icon32"></div>
    <h2>Product Brands Settings</h2>
    <?php settings_errors(); ?>

    <h2 class="nav-tab-wrapper">
        <a href="#general" class="nav-tab nav-tab-active general-tab" data-block="general"><?php _e('General', 'BeRocket_product_brand_domain') ?></a>
        <a href="#css" class="nav-tab css-tab" data-block="css"><?php _e('CSS', 'BeRocket_product_brand_domain') ?></a>
    </h2>

    <form class="product_brand_submit_form" method="post" action="options.php">
        <?php 
        $options = BeRocket_product_brand::get_option(); ?>
        <div class="nav-block general-block nav-block-active">
            <table class="form-table license">
                <tr>
                    <th scope="row"><?php _e('Display thumbnail on brand page', 'BeRocket_product_brand_domain') ?></th>
                    <td>
                        <input type="checkbox" name="br-product_brand-options[display_thumbnail]" value="1"<?php if( @ $options['display_thumbnail']) echo ' checked'; ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Display description on brand page', 'BeRocket_product_brand_domain') ?></th>
                    <td>
                        <input type="checkbox" name="br-product_brand-options[display_description]" value="1"<?php if( @ $options['display_description']) echo ' checked'; ?>>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Shortcodes</th>
                    <td>
                        <ul class="br_shortcode_info">
                            <li>
                                <strong>[brands_list]</strong> - list of brands
                                <ul>
                                    <li><i>use_image</i> - display brand image(1 or 0)</li>
                                    <li><i>use_name</i> - display brand name(1 or 0)</li>
                                    <li><i>per_row</i> - Count of columns for brands list(count of brand per slider)</li>
                                    <li><i>hide_empty</i> - Hide brands without products(1 or 0)</li>
                                </ul>
                            </li>
                            <li>
                                <strong>[brands_products]</strong> - product list by brand id
                                <ul>
                                    <li><i>brand_id</i> - brand ID(s). One or more brand ID(Example: 12,34,35)</li>
                                    <li><i>brand_slug</i> - brand slug(s). One or more brand slug name(Example: brand1,brand2,brand3)</li>
                                    <li><i>Use only one option brand_id or brand_slug</li>
                                    <li><i>columns</i> - count of columns for product list. Can doesn't work with some theme or plugin</li>
                                    <li><i>orderby</i> - order products by this field(title, name, date, modified)
                                        <ul>
                                            <li><i>title</i> - Order by title</li>
                                            <li><i>name</i> - Order by post name (post slug)</li>
                                            <li><i>date</i> - Order by date</li>
                                            <li><i>modified</i> - Order by last modified date</li>
                                            <li><i>rand</i> - Random order</li>
                                        </ul>
                                    </li>
                                    <li><i>order</i> - ascending(asc) or descending(desc) order</li>
                                </ul>
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
        <div class="nav-block css-block">
            <table class="form-table license">
                <tr>
                    <th scope="row"><?php _e('Custom CSS', 'BeRocket_product_brand_domain') ?></th>
                    <td>
                        <textarea name="br-product_brand-options[custom_css]"><?php echo $options['custom_css']?></textarea>
                    </td>
                </tr>
            </table>
        </div>
        <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'BeRocket_product_brand_domain') ?>" />
        <div class="br_save_error"></div>
    </form>
</div>
<?php
$feature_list = array(
    'Slider for brand links',
    'Additional customization',
    'Widget and shortcode for product brands list by name',
    'Shotcode to display brand image on product page',
    'Shortcode and widget to display brand information',
    'Option to display brand image on product pages',
    'Option to display brand image on product pages',
);
@ include 'settings_footer.php';
?>
</div>
