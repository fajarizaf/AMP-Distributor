<?php

/**
 * Product Hooks
 */
class POK_Product_Hooks {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'woocommerce_product_options_shipping', array( $this, 'custom_product_shipping_options' ) );
		add_action( 'save_post_product', array( $this, 'save_product' ), 20, 3 );
	}

	/**
	 * Add cutom shipping options on edit product
	 */
	public function custom_product_shipping_options() {
		global $post;
		?>
		</div>
		<div class="options_group">
			<p class="form-field">
				<label for="product_shipping_insurance"><?php esc_html_e( 'Shipping insurance', 'pok' ); ?></label>
				<input type="checkbox" name="enable_insurance" id="product_shipping_insurance" <?php echo 'yes' === get_post_meta( $post->ID, 'enable_insurance', true ) ? 'checked' : ''; ?> value="yes">
				<span class="description"><?php esc_html_e( 'Add shipping insurance fee on checkout', 'pok' ); ?></span>
				<?php echo wc_help_tip( __( 'If checked, the insurance fee will be added to the shipping cost.', 'pok' ) ); ?>
			</p>
			<p class="form-field">
				<label for="product_timber_packing"><?php esc_html_e( 'Timber packing', 'pok' ); ?></label>
				<input type="checkbox" name="enable_timber_packing" id="product_timber_packing" <?php echo 'yes' === get_post_meta( $post->ID, 'enable_timber_packing', true ) ? 'checked' : ''; ?> value="yes">
				<span class="description"><?php esc_html_e( 'Add timber packing fee on checkout', 'pok' ); ?></span>
				<?php echo wc_help_tip( __( 'If checked, the timber packing fee will be added to the shipping cost.', 'pok' ) ); ?>
			</p>
		<?php
	}

	/**
	 * Save product shipping options on save product
	 *
	 * @param  int    $post_id Product ID.
	 * @param  object $post    Product data.
	 * @param  mbuh   $update  Embuh.
	 */
	public function save_product( $post_id, $post, $update ) {
		if ( $product = wc_get_product( $post_id ) ) {
			if ( isset( $_POST['enable_insurance'] ) && 'yes' === $_POST['enable_insurance'] ) {
				update_post_meta( $post_id, 'enable_insurance', 'yes' );
			} else {
				update_post_meta( $post_id, 'enable_insurance', 'no' );
			}
			if ( isset( $_POST['enable_timber_packing'] ) && 'yes' === $_POST['enable_timber_packing'] ) {
				update_post_meta( $post_id, 'enable_timber_packing', 'yes' );
			} else {
				update_post_meta( $post_id, 'enable_timber_packing', 'no' );
			}
		}
	}

}
