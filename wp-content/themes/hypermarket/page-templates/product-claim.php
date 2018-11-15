<?php
/**
 * The template for displaying all pages with fluid width.
 * 
 * 
 * Template name: 		Product Claim
 * Template Post Type: 	post, page
 * @see 				https://codex.wordpress.org/Theme_Development
 * @author  			Mahdi Yazdani
 * @package 			Hypermarket
 * @since 				1.0.4
 */
get_header();

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == "submitclaim") {

	//store our post vars into variables for later use
	//now would be a good time to run some basic error checking/validation
	//to ensure that data for these values have been set
	$title     			= $_POST['subjectclaim'];
	$content   			= $_POST['issue'];
	$tags   			= $_POST['tag'];
	$customer_name 		= $_POST['customer_name'];
	$alamat 			= $_POST['alamat'];
	$kodestamping 		= $_POST['kodestamping'];
	$quantity 			= $_POST['qty'];
	$pemakaian 			= $_POST['pemakaian'];
	$category_product 	= $_POST['categoryproduct'];
	$type_product 		= $_POST['typeproduct'];
	$product_name 		= $_POST['product_name'];
	$tipe_kendaraan 	= $_POST['tipe_kendaraan'];
	$issue 				= $_POST['issue'];
	$status 			= $_POST['status'];


	//the array of arguements to be inserted with wp_insert_post
	$new_post = array(
	'post_title'    => $title,
	'post_content'  => $content,
	'tags_input'  => $tags,
	'post_status'   => 'publish',        
	'post_type'     => 'claim' 
	);

	//insert the the post into database by passing $new_post to wp_insert_post
	//store our post ID in a variable $pid
	//we now use $pid (post id) to help add out post meta data
	$pid = wp_insert_post($new_post);

	//we now use $pid (post id) to help add out post meta data
	add_post_meta($pid, 'customer_name', $customer_name);
	add_post_meta($pid, 'address', $alamat);
	add_post_meta($pid, 'kodestamping', $kodestamping);
	add_post_meta($pid, 'quantity', $quantity);
	add_post_meta($pid, 'pemakaian', $pemakaian);
	add_post_meta($pid, 'tipe_kendaraan', $tipe_kendaraan);
	add_post_meta($pid, 'category_product', $category_product);
	add_post_meta($pid, 'type_product', $type_product);
	add_post_meta($pid, 'product_name', $product_name);
	add_post_meta($pid, 'issue_description', $issue);
	add_post_meta($pid, 'status', $status);
}
?>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('.woocommerce-input-wrapper .woof_select').change( function(e)
		{
		     e.stopPropagation();
		     e.preventDefault();
		     e.stopImmediatePropagation();
		     return false;
		});
	});
</script>

<div class="container padding-top">
<div class="woocommerce">
	<div class="col-md-8">
        <form method="post" class="checkout woocommerce-checkout" id="formclaim" name="front_end" action="">
        	
        	
        	<input type="hidden" name="tag" value="Product Claim">
        	<input type="hidden" name="action" value="submitclaim" />
        	<input type="hidden" name="status" value="belum_diproses" />
        	<h3>Form Klaim Garansi</h3>
        	<p style="margin-bottom: 17px;">Untuk syarat dan ketentuan garansi yang harus di pahami dan disetujui pelanggan jika bermaksud melakukan proses klaim garansi. bisa dilihat di sini <a href="#">Ketentuan Garansi</a></p>
        	<?php if( $_POST['action'] == "submitclaim" ) { ?>
        	<div class="form-row col-sm-12 form-element">
	        	<span class="woocommerce-input-wrapper">
	        		<div class="return_success">
	        		Data Pengajuan klaim produk anda berhasil terkirim. segera tunggu follow up dari team kami.
					</div>
	        	</span>
	        </div>
	        <?php } ?>
        	<div class="woocommerce-billing-fields__field-wrapper">
        		<p class="form-row col-sm-12 form-element">
        			<input type="text" class="form-control customer_name" name="customer_name" placeholder="Nama Kustomer*">
        		</p>
        	</div>
        	<p class="form-row col-sm-12 form-element" style="margin-top: 20px;">
        		<textarea name="alamat" class="textarea alamat"  placeholder="Alamat Kustomer" rows="8" cols="5"></textarea>
        	</p>

        	<div style="clear: both;"></div>

	        <div class="woocommerce-billing-fields__field-wrapper">
        		<p class="form-row col-sm-6 form-element" style="margin-top:20px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<?php $args = array( 
	        				'show_option_none'   => 'Kategori Produk',
	        				'option_none_value'  => null,
	        				'name'				 => 'categoryproduct',
	        				'value_field'		 => 'name',
	        				'taxonomy'  		 => 'product_cat'
	        				); 
	        			?>
	        			<?php wp_dropdown_categories( $args ); ?>
        			</span>
        		</p>
        		<p class="form-row col-sm-6 form-element" style="margin-top:20px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<?php $args1 = array( 
    						'sort_order'    		=> 'ASC',
    						'show_option_none'      => 'Produk yang di klaim',
    						'option_none_value' 	=> null,
    						'name'					=> 'product_name',
	        				'sort_column'  			=> 'post_title',
	        				'value_field'			=> 'post_title',
	        				'post_type'  			=> 'product'
	        				); 
	        			?>
	        			<?php wp_dropdown_pages( $args1 ); ?>
        			</span>
        		</p>
        		<p class="form-row col-sm-6 form-element" style="margin-top:0px;">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control qty" name="qty" placeholder="Jumlah*">
        			</span>
        		</p>
        		<p class="form-row col-sm-6 form-element" style="margin-top:0px;">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control kodestamping" name="kodestamping" placeholder="Kode Stamping">
        			</span>
        		</p>
        		<p class="form-row col-sm-6 form-element" style="margin-top:-30px;">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control pemakaian" name="pemakaian" placeholder="Lama Pemakaian*">
        			</span>
        		</p>
        		<p class="form-row col-sm-6 form-element" style="margin-top:-30px;">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control jenis_kendaraan" name="jenis_kendaraan" placeholder="Jenis Kendaraan*">
        			</span>
        		</p>
        		<p class="form-row col-sm-12 form-element">
        			<textarea name="issue" class="textarea"  placeholder="Alasan mengajukan klaim garansi" rows="8" cols="5"></textarea>
        		</p>
        		<p class="form-row col-sm-12 form-element">
        			<span class="woocommerce-input-wrapper">
        			<button type="submit" class="submitbuttonclaim" name="buttonclaim" id="place_order" value="Submit">Kirim</button>
        			</span>
        		</p>
        	</div>
        </form>
    </div>
</div>
</div>

<style type="text/css">
	#formclaim .woocommerce-input-wrapper { padding-left: 15px; position: relative; margin-bottom: 24px; }
	#formclaim  .form-element { margin-bottom: 0px; }
	#formclaim  .address { height: 80px; }
	#formclaim  .chosen-container { width:100% !important;height:auto;margin-left: -14px; }
	#formclaim  .chosen-container-single .chosen-single { height: 47px !important; }
	#formclaim  .chosen-single { margin-bottom:20px; }
	#formclaim  #place_order { margin-top: 10px;width:200px;background-color: orange; }
	#formclaim  .woocommerce-input-wrapper .chosen-container { margin-left: 0px; }
	#formclaim  .error { color:orangered; }
	#formclaim  .return_success { width: 100%;padding:10px;padding-top:5px;padding-bottom:5px;background-color: #fbe48a;color:#af7328;border:1px solid #ecd166;-moz-border-radius:6px;-webkit-border-radius:6px;border-radius:6px; }
</style>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery.validator.setDefaults({ ignore: ":hidden:not(select)" })
		jQuery("#formclaim").validate({
		  	submitHandler: function(form) {
		    // do other things for a valid form
		    form.submit();
		  	},
		  	rules: {
				customer_name: { required: true },
				alamat: { required: true },
				kodestamping: { required: true },
				qty: {
					required: true,
					number: true
				},
				pemakaian: { required: true },
				jenis_kendaraan: { required: true },
				categoryproduct: { required: true },
				typeproduct: { required: true },
				product_name: { required: true },
				issue: { required: true },
			},
			messages: {
				subjectclaim: {
					required: "Mohon masukan subject"
				},
				alamat: {
					required: "Mohon masukan alamat anda"
				},
				kodestamping: {
					required: "Mohon input kode stamping"
				},
				qty: {
					required: "Mohon input jumlahnya",
					number: "Mohon input nomor yang valid."
				},
				pemakaian: {
					required: "Mohon input lama pemakaian"
				},
				jenis_kendaraan: {
					required: "Mohon input jenis kendaraan"
				},
				categoryproduct : "Mohon pilih kategori produk",
				typeproduct : "Mohon input tipe produk",
				product_name : "Mohon nama barang atau produk yang di klaim",
				issue : "Mohon beri penjelasan kenapa kenapa mengajukan klaim"
			}
		});
	});
</script>

<?php


get_footer();