<?php
/**
 * Displayed when no products are found matching the current query.
 *
 * @package 		Hooked into "woocommerce_no_products_found"
 * @author  		Mahdi Yazdani
 * @package 		Hypermarket
 * @since 		    1.0.4.1
 */

remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar');
?>
<div class="row padding-top" style="padding-left: 18px;padding-right:18px;">
	<div class="col-sm-12 padding-bottom-2x" >
		<p><?php esc_html_e( 'Mohon maaf untuk pencarian aki dengan kriteria terebut tidak ditemukan.',
					'hypermarket' ); ?></p>
		<?php the_widget( 'WC_Widget_Product_Search' ); ?>
	</div><!-- .col-sm-12 -->			
</div><!-- .row -->
