<?php
/**
 * The template for displaying all pages with fluid width.
 * 
 * 
 * Template name: 		Product Brand Bosch
 * Template Post Type: 	post, page
 * @see 				https://codex.wordpress.org/Theme_Development
 * @author  			Mahdi Yazdani
 * @package 			Hypermarket
 * @since 				1.0.4
 */

get_header();

	$getchilds = array(
        'parent'        => 236,
        'child_of'      => 236, 
        'sort_column'   => 'menu_order',
        'sort_order'    => 'ASC'        
    ); 

    $postliset = get_pages($getchilds);
?>

<div id="container-brand" class="container-fluid padding-top-half catalog">
	<div class="col-md-12 container-product-brand">

		<h3>Varian Aki Bosch</h3>

		<?php foreach ($postliset as $post) { ?>
		<div class="col-sm-6 brand-bosch" style="background:#fff;margin-top: 25px;">
			<div class="content-image-brand">
				<?php echo get_the_post_thumbnail( $post->ID, 'custom-size' ); ?>
			</div>
			<div class="content-brand">
				<div class="p">
					<h3><?php echo $post->post_title; ?></h3>
					<div class="sub" style="color:#333;text-align: left;">
						<?php echo wp_trim_words( $post->post_excerpt, 20, '...' ); ?>
					</div>
					<a href="<?php echo get_permalink($post->ID); ?>" style="color:#333;background: none;" class="btn-link-brand-bosch-n">Selengkapnya</a>
					<a href="<?php echo get_post_meta( $post->ID, 'urlharga', true) ?>" class="btn-link-brand-bosch">Daftar Harga</a>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>



<?php
get_footer();