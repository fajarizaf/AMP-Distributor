<?php
/**
 * The template for displaying the homepage.
 *
 * This page template will display any functions hooked into the `hypermarket_homepage_template` action.
 * By default this includes a variety of product displays and the page content itself.
 *
 * Template name: 	Homepage
 * @see 			http://codex.wordpress.org/Template_Hierarchy
 * @author  		Mahdi Yazdani
 * @package 		Hypermarket
 * @since 		    1.0
 *
 */
get_header();

/**
 * Functions hooked into "hypermarket_homepage_template" action
 *
 * @hooked hypermarket_shop_by_category            - 10
 * @hooked hypermarket_best_sellers				   - 20
 * @hooked hypermarket_new_arrivals         	   - 30
 * @hooked hypermarket_homepage_content            - 40
 *
 * @since 1.0
 */

?>

<div class="container-fluid container-slider">
	<div class="col-md-12">
		<?php layerslider(2); ?>
	</div>
</div>

<div class="container-fluid con-claim">
	<div class="col-md-12">
		<a style="text-decoration: none;" href="<?php bloginfo('url'); ?>/klaim-garansi">
		<input style="background-color:#ccc;color:#333;font-weight: bold;" type="button" id="place_order" class="btn-claimpro" value="Klaim Garansi" />
		</a>
	</div>
</div>

<div class="container-fluid con-afterslider">
	<div class="col-md-12 container-after-slider">
		<div class="col-sm-4 vendor-box">
			<div class="content-vendor-box">
				<div class="sub">
					Untuk pembelian dalam jumlah banyak klik link dibawah ini dan Dapatkan harga terbaik dari kami
				</div>
				<a href="#" class="btn-link-content-vendor-box">Join Sekarang</a>
			</div>
		</div>
		<div class="col-sm-8 carimobil-box">
			<div class="content-carimobil-box">
				<div class="sub">
					Cari aki yang tepat berdasarkan <b>jenis mobil</b> anda
				</div>
					<?php $args = array( 
	        			'show_option_none'   => 'Contoh : Mazda 2',
	        			'option_none_value'  => null,
	        			'name'				 => 'tipe_kendaraan',
	        			'class' 			 => 'woof_select required',
	        			'value_field'		 => 'slug',
	        			'taxonomy'  		 => 'tipe_kendaraan'
	        			); 
	        		?>
	        		<?php wp_dropdown_categories( $args ); ?>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid container-jaminan">
	<div class="col-md-12">
		<div class="col-sm-4 box-jaminan">
			<h2>Jaminan yang kami berikan untuk setiap produk</h2>
			<p>Kami menjamin disetiap produk yang kami jual memiliki kualitas dan pelayanan yang baik.</p>
			<a style="display: block;" href="<?php bloginfo('url'); ?>/jaminan-yang-diberikan/" class="btn-jaminan">Lihat Selengkapnya</a>
		</div>
		<div class="col-sm-8 box-jaminan">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<div class="box-featured-tab">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/css/img/global.png">
						<div class="tab-text">Jaminan produk dengan kualitas standard international</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="box-featured-tab">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/css/img/delivery-fast.png">
						<div class="tab-text">Jaminan pelayanan yang memuaskan dan pengiriman yang cepat</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="box-featured-tab">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/css/img/price-best.png">
						<div class="tab-text">Jaminan harga yang sangat kompetitif</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-6">
					<div class="box-featured-tab">
						<img src="<?php echo get_template_directory_uri(); ?>/assets/css/img/timely-delivery.gif">
						<div class="tab-text">Jaminan penggantian claim yang sangat cepat</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div id="container-brand" class="container-fluid padding-top-half">
	<div class="col-md-12 container-product-brand ">
		<div class="col-sm-6 brand-bosch">
			<div class="content-brand">
				<div class="p">
					<div class="sub">
						Pilihan Jenis aki yang tersedia yang dapat anda pilih sesuai kebutuhan anda
					</div>
					<a href="<?php bloginfo('url'); ?>/produk-aki-bosch" class="btn-link-brand-bosch-n">Selengkapnya</a>
					<a href="<?php bloginfo('url'); ?>/varian-aki-bosch" class="btn-link-brand-bosch">Lihat Katalog</a>
				</div>
			</div>
		</div>
		<div class="col-sm-6 brand-fb">
			<div class="content-brand">
				<div class="p">
					<div class="sub">
						Pilihan Jenis aki yang tersedia yang dapat anda pilih sesuai kebutuhan anda
					</div>
					<a href="<?php bloginfo('url'); ?>/produk-aki-fukurawa" class="btn-link-brand-fb-n">Selengkapnya</a>
					<a href="<?php bloginfo('url'); ?>/varian-aki-fukurawa" class="btn-link-brand-fb">Lihat Katalog</a>
				</div>
			</div>
		</div>
	</div>
</div>




<?php
get_footer();