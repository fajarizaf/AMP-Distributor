<form action="" method="page" class="pok-checker-form">
	<div class="pok-checker">
		<div class="sidebar">
			<input type="hidden" name="page" value="pok_setting">
			<input type="hidden" name="tab" value="checker">
			<?php if ( isset( $_GET['debug'] ) ) : ?>
				<input type="hidden" name="debug" value="">
			<?php endif; ?>
			<table class="widefat striped">
				<tbody>
					<tr>
						<td class="index"><?php esc_html_e( 'Base API', 'pok' ); ?></td>
						<td class="value"><?php echo esc_html( 'nusantara' === $settings['base_api'] ? 'Tonjoo' : 'Rajaongkir' ); ?></td>
					</tr>
					<tr>
						<td class="index"><?php esc_html_e( 'From', 'pok' ); ?></td>
						<td class="value">
							<?php
							if ( 'nusantara' === $settings['base_api'] ) {
								$city = $this->core->get_single_city( $settings['store_location'][0] );
								if ( $city ) {
									echo esc_html( $city->type . ' ' . $city->nama . ', ' . $city->provinsi );
								}
							} else {
								$cities = $this->core->get_all_city();
								foreach ( $cities as $city ) {
									if ( $city->city_id === $settings['store_location'][0] ) {
										echo esc_html( ( 'Kabupaten' === $city->type ? 'Kab. ' : 'Kota ' ) . $city->city_name . ', ' . $city->province );
										break;
									}
								}
							}
							?>
						</td>
					</tr>
					<tr>
						<td class="index"><?php esc_html_e( 'Destination', 'pok' ); ?></td>
						<td class="value">
							<select name="province" id="select_province">
								<option value=""><?php esc_html_e( 'Select province', 'pok' ); ?></option>
								<?php foreach ( $provinces as $key => $name ) : ?>
									<option <?php echo isset( $_GET['province'] ) && intval( $_GET['province'] ) === intval( $key ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $name ); ?></option>
								<?php endforeach; ?>
							</select>
							<select name="city" id="select_city">
								<option value=""><?php esc_html_e( 'Select city', 'pok' ); ?></option>
							</select>
							<?php if ( 'pro' === $this->helper->get_license_type() ) : ?>
								<select name="district" id="select_district">
									<option value=""><?php esc_html_e( 'Select district', 'pok' ); ?></option>
								</select>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<td class="index"><?php esc_html_e( 'Weight (kg)', 'pok' ); ?></td>
						<td class="value">
							<input type="number" name="weight" min="0" step="0.1" value="<?php echo isset( $_GET['weight'] ) ? floatval( $_GET['weight'] ) : 1; ?>">
						</td>
					</tr>
					<tr>
						<td class="index"><?php esc_html_e( 'Insurance', 'pok' ); ?></td>
						<td class="value">
							<input type="checkbox" name="insurance" value="yes" id="check-insurance" <?php echo isset( $_GET['insurance'] ) && 'yes' === $_GET['insurance'] ? 'checked' : ''; ?>>
						</td>
					</tr>
					<tr class="total <?php echo isset( $_GET['insurance'] ) && 'yes' === $_GET['insurance'] ? 'show' : ''; ?>">
						<td class="index"><?php esc_html_e( 'Total Price', 'pok' ); ?></td>
						<td class="value">
							<input type="number" min="0" name="total" value="<?php echo isset( $_GET['total'] ) ? floatval( $_GET['total'] ) : 0; ?>">
						</td>
					</tr>
				</tbody>
			</table>
			<?php wp_nonce_field( 'check_cost', 'pok_action' ); ?>
			<div class="submit">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting&tab=checker' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'pok' ); ?></a>
				<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'Get cost', 'pok' ); ?>">
			</div>
		</div>
		<div class="result">
			<?php if ( isset( $errors ) && ! empty( $errors ) ) : ?>
				<div class="pok-notice-error">
					<?php foreach ( $errors as $error ) : ?>
						<p><?php echo esc_html( $error ); ?></p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			<?php if ( isset( $result ) && empty( $errors ) ) : ?>
				<div class="result-section displayed-result">
					<div class="content">
						<?php if ( ! empty( $result ) ) : ?>
							<table class="widefat wp-list-table striped">
								<thead>
									<td class="courier"><?php esc_html_e( 'Courier', 'pok' ); ?></td>
									<td class="service"><?php esc_html_e( 'Service Name', 'pok' ); ?></td>
									<td class="cost"><?php esc_html_e( 'Cost', 'pok' ); ?></td>
									<?php if ( isset( $_GET['insurance'] ) && 'yes' === $_GET['insurance'] && isset( $total ) ) : ?>
										<td class="insurance"><?php esc_html_e( 'Insurance', 'pok' ); ?></td>
									<?php endif; ?>
									<td class="etd"><?php esc_html_e( 'Etd (days)', 'pok' ); ?></td>
								</thead>
								<tbody>
									<?php foreach ( $result as $res ) : ?>
										<tr>
											<td class="courier"><?php echo esc_html( $this->helper->get_courier_name( $res['courier'] ) ); ?></td>
											<td class="service"><?php echo esc_html( $this->helper->convert_service_name( $res['courier'], $res['service'] ) ); ?></td>
											<td class="cost"><?php echo wp_kses_post( wc_price( $res['cost'] ) ); ?></td>
											<?php if ( isset( $_GET['insurance'] ) && 'yes' === $_GET['insurance'] && isset( $total ) ) : ?>
												<td class="insurance">
													<?php echo wp_kses_post( wc_price( $this->helper->get_insurance( $res['courier'], $total ) ) ); ?>
												</td>
											<?php endif; ?>
											<td class="etd"><?php echo esc_html( $res['time'] ); ?></td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<?php esc_html_e( 'No result', 'pok' ); ?>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $this->debug ) : ?>
					<div class="result-section debug-result">
						<div class="title">API Return & Formatted Result</div>
						<div class="content">
							<div class="left">
								<pre><?php print_r( $original_cost ); ?></pre>
							</div>
							<div class="right">
								<pre><?php print_r( $formatted_cost ); ?></pre>
							</div>
						</div>
					</div>
				<?php endif; ?>
			<?php elseif ( ! isset( $errors ) || empty( $errors ) ) : ?>
				<div class="pok-notice">
					<p><?php esc_html_e( 'Input address to view results', 'pok' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</div>
</form>
<script>
	jQuery(function($) {
		$('#select_city').on('setvalue', function() {
			var value = '<?php echo isset( $_GET['city'] ) ? intval( $_GET['city'] ) : '0'; ?>';
			$('#select_city option').each(function() {
				if ( $(this).attr('value') == value ) {
					$('#select_city').val(value);
				}
			});
		});
		$('#select_district').on('setvalue', function() {
			var value = '<?php echo isset( $_GET['district'] ) ? intval( $_GET['district'] ) : '0'; ?>';
			$('#select_district option').each(function() {
				if ( $(this).attr('value') == value ) {
					$('#select_district').val(value);
				}
			});
		});
	});
</script>
