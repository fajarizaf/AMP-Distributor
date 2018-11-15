	
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('body').css({'background':'#fff'});
		});
	</script>

	<div class="col-md-12 box-daf-product">
		<div class="col-sm-4">
			<?php 

			if( $_GET['product_cat'] ) {
				$terms = get_term_by('slug', $_GET['product_cat'], 'product_cat');
				$thumbnail_id = get_woocommerce_term_meta( $terms->term_id, 'thumbnail_id', true );
				$image = wp_get_attachment_url( $thumbnail_id );
				echo '<img src="'.$image.'" class="term_product" width="365"  />';

			} else if($_GET['kendaraan']) {

				$terms = get_term_by('slug', $_GET['kendaraan'], 'tipe_kendaraan');
				$listterms = get_terms( 
					array(
						'taxonomy' 	 => 'tipe_kendaraan',
						'hide_empty' => false, 
						'parent'	 => $terms->term_id
					));

			} else if($_GET['tipe_kendaraan']) {

				$terms = get_term_by('slug', $_GET['tipe_kendaraan'], 'tipe_kendaraan');
				$brand = get_term_by('id', $terms->parent, 'tipe_kendaraan');
				$listterms = get_terms( 
					array(
						'taxonomy' 	 => 'tipe_kendaraan',
						'hide_empty' => false, 
						'parent'	 => $terms->parent
					));

			}	
				?>

			<?php if(!isset($_GET['product_cat'])) { ?>
				<div class="box-result-kendaraan">

				<?php if($_GET['kendaraan']) { ?>	
					<p style="background: none;">Berikut Daftar Aki yang cocok untuk merek kendaraan <b><?php echo $terms->name; ?></b></p>
					<p class="lab" style="font-size: 14px;color:#765806;margin-top:-6px;">Berikut tipe kendaraan yang tersedia untuk merek kendaraan <b><?php echo $terms->name; ?></b> :</p>
				<?php } else if($_GET['tipe_kendaraan']) { ?>
					<p style="background: none;">Berikut Daftar Aki yang cocok untuk tipe kendaraan <b><?php echo $brand->name; ?>&nbsp;<?php echo $terms->name; ?></b></p>
					<p style="font-size: 14px;color:#765806;margin-top:-6px;">Berikut tipe kendaraan yang tersedia untuk merek kendaraan <b><?php echo $brand->name; ?></b> :</p>
				<?php } else { ?>
					<p style="background: none;">Untuk melihat informasi detail untuk masing masing produk silahkan klik link dibawah ini :</p>
					<p style="font-size: 14px;color:#765806;margin-top:-6px;">Berikut varian produk untuk merek bosch dan fukurawa yang bisa anda lihat</p>
					<ul>
						<li><a href="<?php echo get_bloginfo('url');?>/produk-aki-bosch">Produk Bosch</a></li>
						<li><a href="<?php echo get_bloginfo('url');?>/produk-aki-fukurawa">Produk Fukurawa</a></li>
					</ul>
				<?php } ?>

					<ul style="padding-left:30px;margin-top:20px;">
						<?php foreach ($listterms as $term) { ?>
							<li>
							<?php if($_GET['kendaraan']) { ?>
								<a style="color:#765806;text-decoration: none;font-size: 14px;" href="<?php echo get_bloginfo('url').'/produk/?produk=1&kendaraan='.$_GET['kendaraan'].'&tipe_kendaraan='.$term->slug; ?>"><?php echo $term->name; ?></a>
							<?php } else if($_GET['tipe_kendaraan']) { ?>
								<a style="color:#765806;text-decoration: none;font-size: 14px;" href="<?php echo get_bloginfo('url').'/produk/?produk=1&tipe_kendaraan='.$term->slug; ?>"><?php echo $term->name; ?></a>
							<?php } ?>
							</li>
						<?php } ?>
					</ul>

				</div>
			<?php } ?>

		</div>
		<div class="col-sm-8">
			<div id="page">
			<table id="table">
				<thead>
					<tr class="thead">
						<th>Seri</th>
						<th>Partnumber</th>
						<th>Ah</th>
						<th>Price</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>