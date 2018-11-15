<?php 
	
	$current_user = wp_get_current_user();
	$user_id	  = $current_user->ID;

	$args = array(
			'author'		  => $user_id,
            'post_type'       => 'claim',
            'posts_per_page'  => 100,
            'supress_filters' => false,
        );

?>

<style type="text/css">
	.col-sm-12 p {
		display: none;
	}
</style>

<table id="table" class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
	<thead>
		<tr>
		<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr">Subject Klaim</span></th>
		<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr">Produk Yang Diklaim</span></th>
		<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr">Tanggal Submit</span></th>
		<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr">Status</span></th>
	</thead>

	<tbody>
	<?php query_posts( $args ); while ( have_posts() ) : the_post(); ?>
		<tr>
			<td><?php the_title(); ?></td>
			<td><?php the_field('product_name'); ?></td>
			<td><?php the_date(); ?></td>
			<td style="color:orange;font-weight: bold;"><?php the_field('status'); ?></td>
		</tr>
	<?php endwhile; ?>
						
	</tbody>
</table>