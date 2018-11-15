<?php
/**
 * Displaying product categorie(s) in homepage template file.
 *
 * @package 		Hooked into "hypermarket_homepage_template"
 * @author  		Mahdi Yazdani
 * @package 		Hypermarket
 * @since 		    1.0.4.1
 */
$args = apply_filters('hypermarket_shop_by_category_args', array(
	'parent'		=> 	0,
	'number' 		=> 	6,
	'show_count'   	=> 	1,
	'pad_counts'   	=> 	1,
	'hierarchical' 	=> 	0,
	'hide_empty'   	=> 	1,
	'orderby'      	=> 	'name',
	'taxonomy'     	=> 	'product_cat',
	'title'			=> 	__('Catalog', 'hypermarket')
));
$all_categories = get_categories( $args );
if(! empty($all_categories) ):
	// Get catalog image dimensions from WooCommerce settings
	$catalog_image_dimensions = get_option('shop_catalog_image_size');
	if(! empty($catalog_image_dimensions) ):
		$catalog_image_width = $catalog_image_dimensions['width'];
		$catalog_image_height = $catalog_image_dimensions['height'];
	endif;
?>
	<!-- Catalogs -->
	
<?php endif; ?>