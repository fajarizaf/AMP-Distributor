<?php

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == "Purchase Order") {

	$current_user = wp_get_current_user();
	$user_id	  = $current_user->ID;
	$customername = get_user_meta( $user_id, 'shipping_first_name', true ).' '.get_user_meta( $user_id, 'shipping_last_name', true );
	$addressname  = mwe_get_formatted_shipping_name_and_address($user_id);
	$number = mt_rand(10000,999999);
	$invoice = '#ENV'.$number;

	//store our post vars into variables for later use
	//now would be a good time to run some basic error checking/validation
	//to ensure that data for these values have been set
	$title     			= $invoice;
	$tags   			= $_POST['tag'];
	$address 			= $addressname;
	$total 				= 'Rp. '.$_POST['grandtotal'];
	$status 			= $_POST['status'];
	$ppn 				= $_POST['ppn'].'%';

	$categoryproduct_1 = $_POST['categoryproduct_1'];
	$categoryproduct_2 = $_POST['categoryproduct_2'];
	$categoryproduct_3 = $_POST['categoryproduct_3'];

	$total_harga_1 = $_POST['total_harga_1'];
	$total_harga_2 = $_POST['total_harga_2'];
	$total_harga_3 = $_POST['total_harga_3'];

	$product .= '1. '.$_POST['product_name_1'].' ( '.$categoryproduct_1.' ) &nbsp;&nbsp;&nbsp;&nbsp;= '.$_POST['qty_1'].' x '.$_POST['harga_1'].' = Rp. '.$total_harga_1.'<br/>';
	$product .= '2. '.$_POST['product_name_2'].' ( '.$categoryproduct_2.' ) &nbsp;&nbsp;&nbsp;&nbsp;= '.$_POST['qty_2'].' x '.$_POST['harga_2'].' = Rp. '.$total_harga_2.'<br/>';
	$product .= '3. '.$_POST['product_name_3'].' ( '.$categoryproduct_3.' ) &nbsp;&nbsp;&nbsp;&nbsp;= '.$_POST['qty_3'].' x '.$_POST['harga_3'].' = Rp. '.$total_harga_3.'<br/>';


	//the array of arguements to be inserted with wp_insert_post
	$new_post = array(
	'post_title'    => $invoice,
	'post_content'  => $title,
	'tags_input'  	=> $tags,
	'post_status'   => 'publish',        
	'post_type'     => 'purchase_order' 
	);

	//insert the the post into database by passing $new_post to wp_insert_post
	//store our post ID in a variable $pid
	//we now use $pid (post id) to help add out post meta data
	$pid = wp_insert_post($new_post);

	//we now use $pid (post id) to help add out post meta data
	add_post_meta($pid, 'nama_kustomer', $customername);
	add_post_meta($pid, 'alamat_kustomer', $address);
	add_post_meta($pid, 'produk_yang_dipesan', $product);
	add_post_meta($pid, 'total_yang_harus_dibayarkan', $total);
	add_post_meta($pid, 'status', $status);
	add_post_meta($pid, 'ppn', $ppn);
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
        	
        	
        	<input type="hidden" name="tag" value="Purchase Order">
        	<input type="hidden" name="action" value="Purchase Order" />
        	<input type="hidden" name="status" value="Belum Diproses" />

        	<h3>Form Purchase Order</h3>
        	<p style="margin-bottom: 17px;">Berikut adalah fasilitas untuk pemesanan barang dalam bentuk PO.</p>
        	<?php if( $_POST['action'] == "Purchase Order" ) { ?>
        	<div class="form-row col-sm-12 form-element">
	        	<span class="woocommerce-input-wrapper">
	        		<div class="return_success">
	        		Data pemesanan anda berhasil terkirim. segera tunggu follow up dari team kami.
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
        			<input  value="0" style="background: #efefef" type="text" class="form-control total_harga_1" name="total_harga_1">
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
        			<input value="0" style="background: #efefef" type="text" class="form-control total_harga_2" name="total_harga_2">
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
        			<input style="background: #efefef" type="text" class="form-control total_harga_3" value="0" name="total_harga_3">
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
        			<input style="background: #efefef" type="text" class="form-control grandtotal" name="grandtotal">
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
        			<input type="submit" class="submitbuttonclaim" name="buttonclaim" id="place_order" value="Submit"></input>
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


<?php 


	function mwe_get_formatted_shipping_name_and_address($user_id) {

	    $address = '';
	    $address .= get_user_meta( $user_id, 'shipping_company', true );
	    $address .= "\n";
	    $address .= get_user_meta( $user_id, 'shipping_address_1', true );
	    $address .= "\n";
	    $address .= get_user_meta( $user_id, 'shipping_address_2', true );
	    $address .= "\n";
	    $address .= get_user_meta( $user_id, 'shipping_city', true );
	    $address .= "\n";
	    $address .= get_user_meta( $user_id, 'shipping_state', true );
	    $address .= "\n";
	    $address .= get_user_meta( $user_id, 'shipping_postcode', true );
	    $address .= "\n";
	    $address .= get_user_meta( $user_id, 'shipping_country', true );

	    return $address;
	}

