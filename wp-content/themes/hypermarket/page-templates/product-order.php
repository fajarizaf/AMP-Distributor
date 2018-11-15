<?php
/**
 * The template for displaying all pages with fluid width.
 * 
 * 
 * Template name: 		Purchase Order Form
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
	$email_address 		= $_POST['email_address'];
	$address 			= $_POST['address'];
	$city 				= $_POST['city'];
	$telephone 			= $_POST['telephone'];
	$part_number 		= $_POST['partnumber'];
	$quantity 			= $_POST['qty'];
	$serial_number 		= $_POST['serialnumber'];
	$category_product 	= $_POST['categoryproduct'];
	$type_product 		= $_POST['typeproduct'];
	$product_name 		= $_POST['product_name'];
	$invoicenumber 		= $_POST['invoicenumber'];
	$issue 				= $_POST['issue'];


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
	add_post_meta($pid, 'email_address', $email_address);
	add_post_meta($pid, 'address', $address);
	add_post_meta($pid, 'city', $city);
	add_post_meta($pid, 'telephone', $telephone);
	add_post_meta($pid, 'part_number', $part_number);
	add_post_meta($pid, 'quantity', $quantity);
	add_post_meta($pid, 'serial_number', $serial_number);
	add_post_meta($pid, 'category_product', $category_product);
	add_post_meta($pid, 'type_product', $type_product);
	add_post_meta($pid, 'product_name', $product_name);
	add_post_meta($pid, 'invoice_number', $invoicenumber);
	add_post_meta($pid, 'issue_description', $issue);
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
	<div class="col-md-16 center-block">
        <form method="post" class="checkout woocommerce-checkout" id="formclaim" name="front_end" action="">
        	
        	
        	<input type="hidden" name="tag" value="Product Claim">
        	<input type="hidden" name="action" value="submitclaim" />
        	<h3>Form Purchase Order</h3>
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
	        	<p class="form-row col-sm-3 form-element"></p>
	        	<p class="form-row col-sm-3 form-element"></p>
	        	<p class="form-row col-sm-1 form-element">Qty</p>
	        	<p class="form-row col-sm-2 form-element">Harga</p>
	        	<p class="form-row col-sm-3 form-element">Total Harga</p>


        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<?php $args = array( 
	        				'show_option_none'   => 'Kategori Produk',
	        				'option_none_value'  => null,
	        				'name'				 => 'categoryproduct_1',
	        				'value_field'		 => 'name',
	        				'taxonomy'  		 => 'product_cat'
	        				); 
	        			?>
	        			<?php wp_dropdown_categories( $args ); ?>
        			</span>
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<select name="product_name_1" id="product_name_1">
							<option value="">Nama Produk</option>
						</select>
        			</span>
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control qty_1" name="qty_1">
        			</span>
        		</p>
        		<p class="form-row col-sm-2 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" value="0" class="form-control harga_1" name="harga_1">
        			</span>
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input disabled="disabled" value="0" style="background: #efefef" type="text" class="form-control total_harga_1" name="total_harga_1">
        			</span>
        		</p>




        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<?php $args = array( 
	        				'show_option_none'   => 'Kategori Produk',
	        				'option_none_value'  => null,
	        				'name'				 => 'categoryproduct_2',
	        				'value_field'		 => 'name',
	        				'taxonomy'  		 => 'product_cat'
	        				); 
	        			?>
	        			<?php wp_dropdown_categories( $args ); ?>
        			</span>
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<select name="product_name_2" id="product_name_2">
							<option value="">Nama Produk</option>
						</select>
        			</span>
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control qty_2" name="qty_2">
        			</span>
        		</p>
        		<p class="form-row col-sm-2 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" value="0" class="form-control harga_2" name="harga_2">
        			</span>
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input disabled="disabled" value="0" style="background: #efefef" type="text" class="form-control total_harga_2" name="total_harga_2">
        			</span>
        		</p>






        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<?php $args = array( 
	        				'show_option_none'   => 'Kategori Produk',
	        				'option_none_value'  => null,
	        				'name'				 => 'categoryproduct_3',
	        				'value_field'		 => 'name',
	        				'taxonomy'  		 => 'product_cat'
	        				); 
	        			?>
	        			<?php wp_dropdown_categories( $args ); ?>
        			</span>
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			<span class="woocommerce-input-wrapper" style="padding-left: 0px">
	        			<select name="product_name_3" id="product_name_3">
							<option value="">Nama Produk</option>
						</select>
        			</span>
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" class="form-control qty_3" name="qty_3">
        			</span>
        		</p>
        		<p class="form-row col-sm-2 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input type="text" value="0" class="form-control harga_3" name="harga_3">
        			</span>
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input disabled="disabled" style="background: #efefef" type="text" class="form-control total_harga_3" value="0" name="total_harga_3">
        			</span>
        		</p>





        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			
        		</p>
        		<p class="form-row col-sm-2 form-element" style="text-align: right;padding-top:30px;">
        			Total
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input disabled="disabled" style="background: #efefef" type="text" class="form-control total" name="total">
        			</span>
        		</p>


        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			
        		</p>
        		<p class="form-row col-sm-2 form-element" style="text-align: right;padding-top:30px;">
        			PPN(%)
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input value="10" type="text" class="form-control ppn" name="ppn">
        			</span>
        		</p>


        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			
        		</p>
        		<p class="form-row col-sm-2 form-element" style="text-align: right;padding-top:30px;">
        			Grand Total
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<input disabled="disabled" style="background: #efefef" type="text" class="form-control grandtotal" name="grandtotal">
        			</span>
        		</p>



        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-3 form-element" style="margin-top: 24px;">
        			
        		</p>
        		<p class="form-row col-sm-1 form-element">
        			
        		</p>
        		<p class="form-row col-sm-2 form-element" style="text-align: right;padding-top:30px;">
        			
        		</p>
        		<p class="form-row col-sm-3 form-element">
        			<span class="woocommerce-input-wrapper">
        			<button type="submit" class="submitbuttonclaim" name="buttonclaim" id="place_order" value="Submit">ORDER</button>
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
				customer_name: "required",
				subjectclaim: "required",
				email_address: {
					required: true,
					email: true
				},
				address: "required",
				city: "required",
				telephone: {
					required: true,
					number: true
				},
				partnumber: {
					required: true,
					number: true
				},
				qty: {
					required: true,
					number: true
				},
				invoicenumber: {
					required: true,
					number: true
				},
				categoryproduct: { required: true },
				typeproduct: { required: true },
				product_name: { required: true },
				issue: { required: true },
			},
			messages: {
				customer_name : "Please enter your firstname",
				email_address: {
					required: "Please enter a Email",
					email: "Your Email must consist of at least 2 characters"
				},
				address : "Please enter your Address",
				city : "Please enter your City",
				telephone: {
					required: "Please enter a Telephone",
					number: "Please enter a valid number."
				},
				partnumber: {
					required: "Please enter a Part Number",
					number: "Please enter a valid number."
				},
				qty: {
					required: "Please enter a Quantity",
					number: "Please enter a valid number."
				},
				invoicenumber: {
					required: "Please enter a Invoice Number",
					number: "Please enter a valid number."
				},
				categoryproduct : "Please enter your Category Product",
				typeproduct : "Please enter your Type Product",
				product_name : "Please enter your Product Name",
				issue : "Please enter your Issue Description"
			}
		});
	});
</script>

<?php

get_footer();