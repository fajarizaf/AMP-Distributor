<?php
add_shortcode('WPT_SHOP','wpt_if_no_woocommerce');

function wpt_if_no_woocommerce(){
    echo '<a title="Tell us: if need Help" href="mailto:codersaiful@gmail.com" style="color: #d00;padding: 10px;">[WOO Product Table] WooCommerce not Active/Installed</a>';
}