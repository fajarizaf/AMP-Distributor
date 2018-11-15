<?php
/**
 * The template for displaying all pages with fluid width.
 * 
 * 
 * Template name: 		Register Corporate
 * Template Post Type: 	post, page
 * @see 				https://codex.wordpress.org/Theme_Development
 * @author  			Mahdi Yazdani
 * @package 			Hypermarket
 * @since 				1.0.4
 */
get_header();

if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == "registercorporate") {

	//store our post vars into variables for later use
	//now would be a good time to run some basic error checking/validation
	//to ensure that data for these values have been set
	
	$username = $_POST['username'];
	$email	  = $_POST['email'];
	$password = $_POST['password'];
	$type = $_POST['type_register'];  
    $new_user_id = wp_create_user($username, $password, $email);
    
    if(type == 'corporate') {
    	$user = new WP_User( $new_user_id );
		$user->remove_role( 'subscriber' );
		$user->add_role( 'corporate' );
	}
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
	<div class="col-md-5" style="margin:0px auto;float: none;">
        <form method="post" class="checkout woocommerce-checkout" name="front_end" action="">
        	
        	<input type="hidden" name="action" value="registercorporate" />
        	<div class="woocommerce-billing-fields__field-wrapper">
        	<h3 style="margin-left: 15px;">Customer Registration</h3>
        	</div>
        	<?php if( $_POST['action'] == "registercorporate" ) { ?>
        	<div class="form-row col-sm-12 form-element">
	        	<span class="woocommerce-input-wrapper">
	        		<div class="return_success">
	        		Registrasi berhasil, link aktivasi telah dikirimkan, silahkan lalukan aktifasi pendaftaran terlebih dahulu
					</div>
	        	</span>
	        </div>
	        <?php } ?>
        	<div class="woocommerce-billing-fields__field-wrapper">
        		<p class="form-row col-sm-12 form-element">
        			<input type="text" class="form-control" name="username" placeholder="Username*">
        		</p>
        	</div>

        	<div class="woocommerce-billing-fields__field-wrapper">
        		<p class="form-row col-sm-12 form-element">
        			<input type="text" class="form-control" name="email" placeholder="Email*">
        		</p>
        	</div>

        	<div class="woocommerce-billing-fields__field-wrapper">
        		<p class="form-row col-sm-12 form-element">
        			<input type="text" class="form-control" name="password" placeholder="Password*">
        		</p>
        	</div>

        	<div class="woocommerce-billing-fields__field-wrapper">
        		<p class="form-row col-sm-12 form-element">Register Type :</p>
        		<p class="form-row col-sm-12 form-element">
        		<select name="type_register" class="form-control">
        			<option value="subscriber">Personal</option>
        			<option value="corporate">Corporate</option>
        		</select>
        		</p>
        	</div>
        	
        		<p class="form-row col-sm-12 form-element">
        			<span class="woocommerce-input-wrapper">
        			<button type="submit" class="btn btn-warning" style="width: 50%" value="Submit">Register</button>
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


get_footer();