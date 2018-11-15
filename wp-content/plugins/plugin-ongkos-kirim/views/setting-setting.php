<form action="" method="post" class="pok-setting-form">
	<div class="pok-setting">
	
		<?php do_action( 'pok_setting_before', $settings ); ?>

		<div class="setting-section" id="basic">
			<h4 class="section-title">
				<?php esc_html_e( 'Basic Setting', 'pok' ); ?>
			</h4>
			<div class="setting-table">
				<div class="setting-row">
					<div class="setting-index">
						<label for="pok-enable"><?php esc_html_e( 'Enabled', 'pok' ); ?></label>
						<p class="helper"><?php esc_html_e( 'Enable this shipping method?', 'pok' ); ?></p>
					</div>
					<div class="setting-option">
						<div class="toggle">
							<input type="radio" name="pok_setting[enable]" id="pok-enable-no" <?php echo 'no' === $settings['enable'] ? 'checked' : ''; ?> value="no">
							<label for="pok-enable-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
							<input type="radio" name="pok_setting[enable]" id="pok-enable-yes" <?php echo 'yes' === $settings['enable'] ? 'checked' : ''; ?> value="yes">
							<label for="pok-enable-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
						</div>
					</div>
				</div>
				<div class="setting-row">
					<div class="setting-index">
						<label for="pok-base_api"><?php esc_html_e( 'Base API', 'pok' ); ?></label>
						<p class="helper"><?php esc_html_e( 'Use our default premium API, or Rajaongkir API ', 'pok' ); ?></p>
					</div>
					<div class="setting-option">
						<div class="toggle">
							<input type="radio" name="pok_setting[base_api]" id="pok-base_api-nusantara" <?php echo 'nusantara' === $settings['base_api'] ? 'checked' : ''; ?> value="nusantara">
							<label for="pok-base_api-nusantara"><?php esc_html_e( 'Tonjoo', 'pok' ); ?></label>
							<input type="radio" name="pok_setting[base_api]" id="pok-base_api-rajaongkir" <?php echo 'rajaongkir' === $settings['base_api'] ? 'checked' : ''; ?> value="rajaongkir">
							<label for="pok-base_api-rajaongkir"><?php esc_html_e( 'Rajaongkir', 'pok' ); ?></label>
						</div>
						<div class="setting-sub-option rajakongkir-api-fields <?php echo 'rajaongkir' === $settings['base_api'] ? 'show' : ''; ?>">
							<label class="field-type"><?php esc_html_e( 'Type', 'pok' ); ?>
								<select name="pok_setting[rajaongkir_type]">
									<option value="starter" <?php echo 'starter' === $settings['rajaongkir_type'] ? 'selected' : ''; ?>>Starter</option>
									<option value="basic" <?php echo 'basic' === $settings['rajaongkir_type'] ? 'selected' : ''; ?>>Basic</option>
									<option value="pro" <?php echo 'pro' === $settings['rajaongkir_type'] ? 'selected' : ''; ?>>Pro</option>
								</select>
							</label>
							<label class="field-key"><?php esc_html_e( 'API Key', 'pok' ); ?>
								<input type="text" name="pok_setting[rajaongkir_key]" value="<?php echo esc_attr( $settings['rajaongkir_key'] ); ?>">
							</label>
							<div class="check">
								<button type="button" id="set-rajaongkir-key" class="button button-secondary"><?php esc_html_e( 'Check Rajaongkir Status', 'pok' ); ?></button>
								<span class="rajaongkir-key-response <?php echo $settings['rajaongkir_status'][0] ? 'success' : ''; ?>">
									<?php
									if ( $settings['rajaongkir_status'][0] ) {
										esc_html_e( 'API is active', 'pok' );
									} else {
										esc_html_e( 'API is inactive', 'pok' );
									}
									?>
								</span>
							</div>
						</div>
					</div>
				</div>
				<?php if ( $pok_helper->is_admin_active() ) : ?>
					<div class="setting-row <?php echo empty( $settings['store_location'] ) || ! isset( $settings['store_location'][0] ) ? 'setting-error' : ''; ?>">
						<div class="setting-index">
							<label for="pok-store_location"><?php esc_html_e( 'Store Location', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Location of your store', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<?php if ( 'rajaongkir' === $settings['base_api'] ) : ?>
								<select name="pok_setting[store_location][]" id="pok-store_location" class="init-select2" placeholder="<?php esc_attr_e( 'Select city', 'pok' ); ?>">
									<option value=""><?php esc_html_e( 'Select your store location', 'pok' ); ?></option>
									<?php foreach ( $cities as $city ) : ?>
										<option value="<?php echo esc_attr( $city->city_id ); ?>" <?php echo ! empty( $settings['store_location'] ) && $settings['store_location'][0] === $city->city_id ? 'selected' : ''; ?>><?php echo esc_html( ( 'Kabupaten' === $city->type ? 'Kab. ' : 'Kota ' ) . $city->city_name . ', ' . $city->province ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<select name="pok_setting[store_location][]" id="pok-store_location" class="select2-ajax" data-action="pok_search_city" data-nonce="<?php echo esc_attr( wp_create_nonce( 'search_city' ) ); ?>" placeholder="<?php esc_attr_e( 'Input city name...', 'pok' ); ?>">
									<?php
									if ( ! empty( $settings['store_location'] ) ) {
										$city = $this->core->get_single_city( $settings['store_location'][0] );
										if ( isset( $city ) && ! empty( $city ) ) {
											?>
											<option selected value="<?php echo esc_attr( $settings['store_location'][0] ); ?>"><?php echo esc_html( ( isset( $city->type ) ? $city->type . ' ' : '' ) . $city->nama . ', ' . $city->provinsi ); ?></option>
											<?php
										}
									}
									?>
								</select>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<?php do_action( 'pok_setting_basic', $settings ); ?>

			</div>
		</div>

		<?php if ( $pok_helper->is_admin_active() ) : ?>
			<div class="setting-section" id="courier">
				<h4 class="section-title">
					<?php esc_html_e( 'Courier', 'pok' ); ?>
				</h4>
				<div class="setting-table">
					<div class="setting-row <?php echo empty( $settings['couriers'] ) ? 'setting-error' : ''; ?>">
						<div class="setting-index">
							<label for="pok-couriers"><?php esc_html_e( 'Couriers', 'pok' ); ?></label>
							<p class="helper">
								<?php
								esc_html_e( 'Select couriers to display', 'pok' );
								?>
							</p>
						</div>
						<div class="setting-option">
							<div class="courier-options pro">
								<?php
								foreach ( $all_couriers as $courier ) {
									?>
									<input type="checkbox" value="<?php echo esc_attr( $courier ); ?>" name="pok_setting[couriers][]" id="setting-cour-<?php echo esc_attr( $courier ); ?>" <?php echo in_array( $courier, $couriers, true ) && in_array( $courier, $settings['couriers'], true ) ? 'checked' : ''; ?> <?php echo ! in_array( $courier, $couriers, true ) ? 'disabled' : ''; ?>>
									<label for="setting-cour-<?php echo esc_attr( $courier ); ?>"><?php echo esc_html( $this->helper->get_courier_name( $courier ) ); ?></label>
									<?php
								}
								?>
							</div>
							<p class="helper">
								<?php
								printf( __( 'Available couriers depends on the base API you choose. <a href="%s">Click here</a> to learn more.', 'pok' ), 'http://pustaka.tonjoostudio.com/plugins/woo-ongkir-manual/#section-couriers' );
								if ( 'rajaongkir' === $settings['base_api'] && 'starter' !== $settings['rajaongkir_type'] ) {
									echo ' ';
									esc_html_e( 'We recommend to use only 3 of these couriers to optimize the load speed', 'pok' );
								}
								?>
							</p>
						</div>
					</div>
					<?php if ( 'nusantara' === $settings['base_api'] ) : ?>
						<div class="setting-row">
							<div class="setting-index">
								<label><?php esc_html_e( 'Filter Courier Services', 'pok' ); ?></label>
								<p class="helper"><?php esc_html_e( 'Use specific services for each courier', 'pok' ); ?></p>
							</div>
							<div class="setting-option">
								<div class="toggle">
									<input type="radio" name="pok_setting[specific_service]" id="pok-specific_service-no" <?php echo 'no' === $settings['specific_service'] ? 'checked' : ''; ?> value="no">
									<label for="pok-specific_service-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
									<input type="radio" name="pok_setting[specific_service]" id="pok-specific_service-yes" <?php echo 'yes' === $settings['specific_service'] ? 'checked' : ''; ?> value="yes">
									<label for="pok-specific_service-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
								</div>
								<div class="setting-sub-option options-specific-service <?php echo 'yes' === $settings['specific_service'] ? 'show' : ''; ?>">
									<?php foreach ( $services as $courier => $courier_services ) : ?>
										<?php asort( $courier_services ); ?>
										<div class="options-specific-service-<?php echo esc_attr( $courier ); ?> courier-options">
											<p><?php echo esc_html( $this->helper->get_courier_name( $courier ) ); ?></p>
											<div class="courier-service-options">
												<?php
												foreach ( $courier_services as $key => $service ) {
													?>
													<input type="checkbox" value="<?php echo esc_attr( $courier . '-' . $key ); ?>" name="pok_setting[specific_service_option][]" id="setting-service-<?php echo esc_attr( $courier . '-' . $key ); ?>" <?php echo in_array( $courier . '-' . $key, $settings['specific_service_option'], true ) ? 'checked' : ''; ?>>
													<label for="setting-service-<?php echo esc_attr( $courier . '-' . $key ); ?>"><?php echo esc_html( $this->helper->convert_service_name( $courier, $service ) ); ?></label>
													<?php
												}
												?>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
						</div>
						<div class="setting-row">
							<div class="setting-index">
								<label><?php esc_html_e( 'Show Long Description on Checkout', 'pok' ); ?></label>
								<p class="helper"><?php echo wp_kses_post( __( 'Show long description for each courier service. Example: <strong>JNE - REG</strong> becomes <strong>JNE - REG (Layanan Reguler)</strong>', 'pok' ) ); ?></p>
							</div>
							<div class="setting-option">
								<div class="toggle">
									<input type="radio" name="pok_setting[show_long_description]" id="pok-show_long_description-no" <?php echo 'no' === $settings['show_long_description'] ? 'checked' : ''; ?> value="no">
									<label for="pok-show_long_description-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
									<input type="radio" name="pok_setting[show_long_description]" id="pok-show_long_description-yes" <?php echo 'yes' === $settings['show_long_description'] ? 'checked' : ''; ?> value="yes">
									<label for="pok-show_long_description-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
								</div>
							</div>
						</div>
					<?php else : ?>
						<?php if ( 'starter' !== $settings['rajaongkir_type'] ) : ?>
							<div class="setting-row">
								<div class="setting-index">
									<label><?php esc_html_e( 'Enable International Shipping', 'pok' ); ?></label>
									<p class="helper"><?php esc_html_e( 'Show international shipping costs on checkout page', 'pok' ); ?></p>
								</div>
								<div class="setting-option">
									<div class="toggle">
										<input type="radio" name="pok_setting[international_shipping]" id="pok-international_shipping-no" <?php echo 'no' === $settings['international_shipping'] ? 'checked' : ''; ?> value="no">
										<label for="pok-international_shipping-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
										<input type="radio" name="pok_setting[international_shipping]" id="pok-international_shipping-yes" <?php echo 'yes' === $settings['international_shipping'] ? 'checked' : ''; ?> value="yes">
										<label for="pok-international_shipping-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
									</div>
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<?php do_action( 'pok_setting_courier', $settings ); ?>

				</div>
			</div>

			<div class="setting-section" id="shipping">
				<h4 class="section-title">
					<?php esc_html_e( 'Shipping Weight & Cost Calculation', 'pok' ); ?>
				</h4>
				<div class="setting-table">
					<div class="setting-row">
						<div class="setting-index">
							<label for="pok-default_weight"><?php esc_html_e( 'Default Shipping Weight (kg)', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Default shipping weight if total weight is unknown.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<input id="pok-default_weight" type="number" name="pok_setting[default_weight]" value="<?php echo esc_attr( $settings['default_weight'] ); ?>" step="0.1" min="0.1">
						</div>
					</div>
					<?php if ( 'nusantara' === $settings['base_api'] ) : ?>
						<div class="setting-row">
							<div class="setting-index">
								<label><?php esc_html_e( 'Round Shipping Weight', 'pok' ); ?></label>
								<p class="helper"><?php esc_html_e( 'How shipping weight will be rounded', 'pok' ); ?></p>
							</div>
							<div class="setting-option">
								<div class="toggle">
									<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-auto" <?php echo 'auto' === $settings['round_weight'] ? 'checked' : ''; ?> value="auto">
									<label for="pok-round_weight-auto"><?php esc_html_e( 'Auto', 'pok' ); ?></label>
									<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-ceil" <?php echo 'ceil' === $settings['round_weight'] ? 'checked' : ''; ?> value="ceil">
									<label for="pok-round_weight-ceil"><?php esc_html_e( 'Ceil', 'pok' ); ?></label>
									<input type="radio" name="pok_setting[round_weight]" id="pok-round_weight-floor" <?php echo 'floor' === $settings['round_weight'] ? 'checked' : ''; ?> value="floor">
									<label for="pok-round_weight-floor"><?php esc_html_e( 'Floor', 'pok' ); ?></label>
								</div>
								<div class="setting-sub-option options-round-weight <?php echo 'auto' === $settings['round_weight'] ? 'show' : ''; ?>">
									<label for="pok-round_weight_tolerance"><?php esc_html_e( 'Weight Rounding Limit (gram)', 'pok' ); ?></label>
									<input id="pok-round_weight_tolerance" name="pok_setting[round_weight_tolerance]" type="number" value="<?php echo esc_attr( $settings['round_weight_tolerance'] ); ?>" min="0" max="1000">
									<p class="helper"><?php esc_html_e( 'If shipping weight is less equal to the limit, it will rounding down. Otherwise, it will be rounding up.', 'pok' ); ?></p>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Use Volume Metric Calculation', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Calculate shipping weight using product dimension. If the dimension is not set, it will use weight instead.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<div class="toggle">
								<input type="radio" name="pok_setting[enable_volume_calculation]" id="pok-enable_volume_calculation-no" <?php echo 'no' === $settings['enable_volume_calculation'] ? 'checked' : ''; ?> value="no">
								<label for="pok-enable_volume_calculation-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
								<input type="radio" name="pok_setting[enable_volume_calculation]" id="pok-enable_volume_calculation-yes" <?php echo 'yes' === $settings['enable_volume_calculation'] ? 'checked' : ''; ?> value="yes">
								<label for="pok-enable_volume_calculation-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
							</div>
							<p class="helper"><?php esc_html_e( 'The weight of the product will calculated with the formula:', 'pok' ); ?> <code>( <?php esc_html_e( 'length', 'pok' ); ?> * <?php esc_html_e( 'width', 'pok' ); ?> * <?php esc_html_e( 'height', 'pok' ); ?> ) / 6000</code></p>
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label for="pok-enable_insurance"><?php esc_html_e( 'Add Insurance Fee', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Add insurance fee to shipping cost', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<select name="pok_setting[enable_insurance]" id="pok-enable_insurance">
								<option <?php echo 'set' === $settings['enable_insurance'] ? 'selected' : ''; ?> value="set"><?php esc_html_e( 'Apply only if the setting on the product is set to true', 'pok' ); ?></option>
								<option <?php echo 'yes' === $settings['enable_insurance'] ? 'selected' : ''; ?> value="yes"><?php esc_html_e( 'Always add insurance fee no matter what the product', 'pok' ); ?></option>
								<option <?php echo 'no' === $settings['enable_insurance'] ? 'selected' : ''; ?> value="no"><?php esc_html_e( 'Do not add insurance fee', 'pok' ); ?></option>
							</select>
							<p class="helper"><?php printf( __( 'Each courier applies different rules for insurance calculations. For more info, <a href="%s">check here</a>.', 'pok' ), 'http://pustaka.tonjoostudio.com/plugins/woo-ongkir-manual/#section-add-insurance-fee' ); ?></p>
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label for="pok-enable_timber_packing"><?php esc_html_e( 'Add Timber Packing Fee', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Add timber packing fee to shipping cost.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<select name="pok_setting[enable_timber_packing]" id="pok-enable_timber_packing">
								<option <?php echo 'set' === $settings['enable_timber_packing'] ? 'selected' : ''; ?> value="set"><?php esc_html_e( 'Apply only if the setting on the product is set to true', 'pok' ); ?></option>
								<option <?php echo 'yes' === $settings['enable_timber_packing'] ? 'selected' : ''; ?> value="yes"><?php esc_html_e( 'Always add timber packing fee no matter what the product', 'pok' ); ?></option>
								<option <?php echo 'no' === $settings['enable_timber_packing'] ? 'selected' : ''; ?> value="no"><?php esc_html_e( 'Do not add timber packing fee', 'pok' ); ?></option>
							</select>
							<div class="setting-sub-option options-enable-timber_packing <?php echo 'set' === $settings['enable_timber_packing'] || 'yes' === $settings['enable_timber_packing'] ? 'show' : ''; ?>">
								<label for="pok-timber_packing_multiplier"><?php esc_html_e( 'Shipping cost multiplier', 'pok' ); ?></label>
								<input type="number" name="pok_setting[timber_packing_multiplier]" id="pok-timber_packing_multiplier" value="<?php echo esc_attr( $settings['timber_packing_multiplier'] ); ?>" step="0.1" min="0">
								<p class="helper">
									<?php esc_html_e( 'The shipping cost multiplier is used to determine how much the timber packing fee is. The value "1" means the timber packing fee is equal to the selected shipping cost.', 'pok' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Add Additional Shipping Cost', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'You can mark-up/mark-down your shipping cost based on your need.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<div class="toggle">
								<input type="radio" name="pok_setting[markup_fee]" id="pok-markup_fee-no" <?php echo 'no' === $settings['markup_fee'] ? 'checked' : ''; ?> value="no">
								<label for="pok-markup_fee-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
								<input type="radio" name="pok_setting[markup_fee]" id="pok-markup_fee-yes" <?php echo 'yes' === $settings['markup_fee'] ? 'checked' : ''; ?> value="yes">
								<label for="pok-markup_fee-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
							</div>
							<div class="setting-sub-option options-markup-fee <?php echo 'yes' === $settings['markup_fee'] ? 'show' : ''; ?>">
								<label for="pok-markup_fee_amount"><?php esc_html_e( 'Additional Shipping Cost', 'pok' ); ?></label>
								<input id="pok-markup_fee_amount" type="number" name="pok_setting[markup_fee_amount]" value="<?php echo esc_attr( $settings['markup_fee_amount'] ); ?>">
								<p class="helper"><?php esc_html_e( 'Additional/Reduction Shipping Price here. (insert negative value for reduction)', 'pok' ); ?></p>
							</div>
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Show Total Weight on Checkout', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Show total shipping weight on checkout page', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<div class="toggle">
								<input type="radio" name="pok_setting[show_weight_on_checkout]" id="pok-show_weight_on_checkout-no" <?php echo 'no' === $settings['show_weight_on_checkout'] ? 'checked' : ''; ?> value="no">
								<label for="pok-show_weight_on_checkout-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
								<input type="radio" name="pok_setting[show_weight_on_checkout]" id="pok-show_weight_on_checkout-yes" <?php echo 'yes' === $settings['show_weight_on_checkout'] ? 'checked' : ''; ?> value="yes">
								<label for="pok-show_weight_on_checkout-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
							</div>
						</div>
					</div>

					<?php do_action( 'pok_setting_shipping', $settings ); ?>

				</div>
			</div>

			<div class="setting-section" id="miscellaneous">
				<h4 class="section-title">
					<?php esc_html_e( 'Miscellaneous', 'pok' ); ?>
				</h4>
				<div class="setting-table">
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Add Unique Number on Checkout', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Add unique number to total purchase to easily differ an order from another', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<div class="toggle">
								<input type="radio" name="pok_setting[unique_number]" id="pok-unique_number-no" <?php echo 'no' === $settings['unique_number'] ? 'checked' : ''; ?> value="no">
								<label for="pok-unique_number-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
								<input type="radio" name="pok_setting[unique_number]" id="pok-unique_number-yes" <?php echo 'yes' === $settings['unique_number'] ? 'checked' : ''; ?> value="yes">
								<label for="pok-unique_number-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
							</div>
							<div class="setting-sub-option options-unique-number <?php echo 'yes' === $settings['unique_number'] ? 'show' : ''; ?>">
								<label for="pok-unique_number_length"><?php esc_html_e( 'Unique Number Length', 'pok' ); ?></label>
								<select name="pok_setting[unique_number_length]" id="pok-unique_number_length">
									<?php
									$lengths = array( 1, 2, 3, 4, 5 );
									foreach ( $lengths as $a ) {
										?>
										<option <?php echo $a === intval( $settings['unique_number_length'] ) ? 'selected' : ''; ?> value="<?php echo esc_attr( $a ); ?>"><?php echo esc_html( $a ); ?></option>
										<?php
									}
									?>
								</select>
								<p class="helper"><?php esc_html_e( 'Length of you unique number.', 'pok' ); ?></p>
							</div>
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Auto Fill Address', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Auto-fill checkout field with saved address if customer is a returning user.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<div class="toggle">
								<input type="radio" name="pok_setting[auto_fill_address]" id="pok-auto_fill_address-no" <?php echo 'no' === $settings['auto_fill_address'] ? 'checked' : ''; ?> value="no">
								<label for="pok-auto_fill_address-no"><?php esc_html_e( 'No', 'pok' ); ?></label>
								<input type="radio" name="pok_setting[auto_fill_address]" id="pok-auto_fill_address-yes" <?php echo 'yes' === $settings['auto_fill_address'] ? 'checked' : ''; ?> value="yes">
								<label for="pok-auto_fill_address-yes"><?php esc_html_e( 'Yes', 'pok' ); ?></label>
							</div>
						</div>
					</div>

					<?php do_action( 'pok_setting_miscellaneous', $settings ); ?>

				</div>
			</div>

			<div class="setting-section" id="caching">
				<h4 class="section-title">
					<?php esc_html_e( 'Caching', 'pok' ); ?>
				</h4>
				<div class="setting-table">
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Cache Expiration (in hours)', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Cache expiration is a feature that keep your shipping costs data or addresses data as a stored cache. This feature will significally increase your website speed.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<label for="pok-cache_expiration_costs"><?php esc_html_e( 'Shipping Costs Data', 'pok' ); ?></label>
							<input type="number" id="pok-cache_expiration_costs" name="pok_setting[cache_expiration_costs]" value="<?php echo esc_attr( $settings['cache_expiration_costs'] ); ?>" min="1">
							<br><br>
							<label for="pok-cache_expiration_addresses"><?php esc_html_e( 'Addresses Data (province list, city list, etc)', 'pok' ); ?></label>
							<input type="number" id="pok-cache_expiration_addresses" name="pok_setting[cache_expiration_addresses]" value="<?php echo esc_attr( $settings['cache_expiration_addresses'] ); ?>" min="1">
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Flush Cache', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Delete all cached data', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=pok_setting' ), 'flush_cache', 'pok_action' ) ); ?>" class="button button-warning" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'pok' ); ?>')">Flush Cache</a>
						</div>
					</div>
					<div class="setting-row">
						<div class="setting-index">
							<label><?php esc_html_e( 'Reset Configuration', 'pok' ); ?></label>
							<p class="helper"><?php esc_html_e( 'Delete all saved configuration just like fresh install.', 'pok' ); ?></p>
						</div>
						<div class="setting-option">
							<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=pok_setting' ), 'reset', 'pok_action' ) ); ?>" class="button button-warning" onclick="return confirm('<?php esc_attr_e( 'Are you sure?', 'pok' ); ?>')">Reset</a>
						</div>
					</div>

					<?php do_action( 'pok_setting_caching', $settings ); ?>
					
				</div>
			</div>
		<?php else : ?>
			<div class="pok-notice">
				<p><?php echo wp_kses_post( __( 'More advanced setting will show up here if you activate the license of Rajaongkir.', 'pok' ) ); ?></p>
			</div>
		<?php endif; ?>

		<?php do_action( 'pok_setting_after' ); ?>

	</div>
	<br>
	<?php wp_nonce_field( 'update_setting', 'pok_action' ); ?>
	<input type="submit" value="<?php esc_attr_e( 'Save Setting', 'pok' ); ?>" class="button button-primary">
</form>
