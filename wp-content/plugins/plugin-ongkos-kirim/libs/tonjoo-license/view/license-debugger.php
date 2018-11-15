<div id="tj-debug" class="postbox">
	<h3 class="hndle">Debugger</h3>
	<div class="inside" style="z-index:1;">
		<ul class="debug-value">
			<li>
				<h4>Current Config</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Plugin Update Name</td>
							<td class="value"><?php echo $this->args['plugin_name']; ?></td>
						</tr>
						<tr>
							<td class="index">Plugin Path</td>
							<td class="value"><?php echo $this->args['plugin_path']; ?></td>
						</tr>
						<tr>
							<td class="index">License Form URL</td>
							<td class="value"><?php echo $this->args['license_form']; ?></td>
						</tr>
						<tr>
							<td class="index">Check Failure Max Attempt</td>
							<td class="value"><?php echo $this->args['max_attempt']; ?></td>
						</tr>
						<tr>
							<td class="index">Check Interval (sec)</td>
							<td class="value"><?php echo $this->args['check_interval']; ?></td>
						</tr>
						<tr>
							<td class="index">API Server</td>
							<td class="value"><?php echo $this->license->server; ?></td>
						</tr>
					</tbody>
				</table>
			</li>
			<li>
				<h4>Current Status (on local)</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Is Activated?</td>
							<td class="value"><?php echo $this->get( 'active' ) ? 'yes' : 'no'; ?></td>
						</tr>
						<tr>
							<td class="index">License Key</td>
							<td class="value"><?php echo $this->get( 'key' ); ?></td>
						</tr>
						<tr>
							<td class="index">License Type</td>
							<td class="value"><?php echo $this->get( 'type' ); ?></td>
						</tr>
						<tr>
							<td class="index">Registrar</td>
							<td class="value"><?php echo $this->get( 'email' ); ?></td>
						</tr>
						<tr>
							<td class="index">Activation Date</td>
							<td class="value"><?php echo '-' === $this->get( 'activation_date' ) ? '-' : $this->get_local_time( $this->get( 'activation_date' ) ); ?></td>
						</tr>
						<tr>
							<td class="index">License Expiration</td>
							<td class="value"><?php echo '-' === $this->get( 'expiry' ) ? '-' : $this->get_local_time( $this->get( 'expiry' ) ); ?></td>
						</tr>
						<tr>
							<td class="index">Last Check</td>
							<td class="value"><?php echo $this->get_local_time( $this->get( 'last_check' ) ); ?></td>
						</tr>
						<tr>
							<td class="index">Next Check</td>
							<td class="value"><?php echo $this->get_local_time( $this->get( 'last_check' ) + intval( $this->args['check_interval'] ) ); ?></td>
						</tr>
						<tr>
							<td class="index">Current Check Attempt</td>
							<td class="value"><?php echo intval( $this->get( 'check_attempt' ) ); ?></td>
						</tr>
					</tbody>
				</table>
			</li>
			<?php if ( '' !== $this->get( 'key' ) ) : ?>
				<?php $status = $this->license->status( $this->get( 'key' ), true ); ?>
				<li>
					<h4>License Status API Hit</h4>
					<table>
						<tbody>
							<tr>
								<td class="index">URL</td>
								<td class="value"><?php echo $status['url']; ?></td>
							</tr>
							<tr>
								<td class="index">Response</td>
								<td class="value">
									<?php
									if ( ! is_null( $status['response'] ) ) {
										if ( ! is_wp_error( $status['response'] ) ) {
											$response = json_decode( wp_remote_retrieve_body( $status['response'] ) );
											echo '<pre>';
											print_r( $response );
											echo '</pre>';
										} else {
											echo $status['response']->get_error_message();
										}
									} else {
										echo '-';
									}
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</li>
			<?php endif; ?>
			<li>
				<h4>Updater Data</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Fetch URL</td>
							<td class="value">
								<?php
								if ( '' !== $this->get( 'key' ) ) {
									echo $this->license->server . '/manage/ajax/license/?token=' . $this->get( 'key' ) . '&file=' . $this->args['plugin_name'];
								} else {
									echo 'Key not set';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Is Fetched?</td>
							<td class="value"><?php echo file_exists( $this->license->json_loc . $this->args['plugin_name'] . '.json' ) ? 'yes' : 'no'; ?></td>
						</tr>
						<tr>
							<td class="index">Last Fetch</td>
							<td class="value">
								<?php
								if ( file_exists( $this->license->json_loc . $this->args['plugin_name'] . '.json' ) ) {
									$local_json = json_decode( $this->license->read_json() );
									echo $this->get_local_time( $local_json->created );
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Installed Version</td>
							<td class="value"><?php echo $plugin_info['Version']; ?></td>
						</tr>
						<tr>
							<td class="index">Latest Version on Local Data</td>
							<td class="value">
								<?php
								if ( isset( $local_json ) ) {
									echo $local_json->version;
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Latest Version on Server</td>
							<td class="value">
								<?php
								if ( '' !== $this->get( 'key' ) ) {
									$remote_json = $this->license->update_json( $this->get( 'key' ), true );
									if ( ! is_wp_error( $remote_json ) ) {
										$response = json_decode( wp_remote_retrieve_body( $remote_json ) );
										echo isset( $response->version ) ? $response->version : '-';
									} else {
										echo '-';
									}
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Local Data URL</td>
							<td class="value">
								<?php
								if ( file_exists( $this->license->json_loc . $this->args['plugin_name'] . '.json' ) ) {
									echo str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $this->license->json_loc . $this->args['plugin_name'] . '.json' );
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Local Data Value</td>
							<td class="value hide">
								<?php
								if ( isset( $local_json ) ) {
									echo '<button type="button" class="button toggle">Show/Hide</button>';
									echo '<pre>';
									print_r( $local_json );
									echo '</pre>';
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Remote Data Value</td>
							<td class="value hide">
								<?php
								if ( isset( $remote_json ) ) {
									if ( ! is_wp_error( $remote_json ) ) {
										$response = json_decode( wp_remote_retrieve_body( $remote_json ) );
										echo '<button type="button" class="button toggle">Show/Hide</button>';
										echo '<pre>';
										print_r( $response );
										echo '</pre>';
									} else {
										echo $response->get_error_message();
									}
								} else {
									echo '-';
								}
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
			<li>
				<h4>WP Info</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">PHP Version</td>
							<td class="value"><?php echo esc_html( PHP_VERSION ); ?></td>
						</tr>
						<tr>
							<td class="index">WordPress Version</td>
							<td class="value">
								<?php
								global $wp_version;
								echo esc_html( $wp_version );
								?>
							</td>
						</tr>
						<tr>
							<td class="index">Is WooCommerce Installed</td>
							<td class="value">
								<?php echo file_exists( WP_PLUGIN_DIR . '/woocommerce/woocommerce.php' ) ? 'yes' : 'no'; ?>
							</td>
						</tr>
						<tr>
							<td class="index">Is WooCommerce Active</td>
							<td class="value">
								<?php echo in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ? 'yes' : 'no'; ?>
							</td>
						</tr>
						<tr>
							<td class="index">WooCommerce Version</td>
							<td class="value">
								<?php
								global $woocommerce;
								echo ! is_null( $woocommerce ) ? esc_html( $woocommerce->version ) : '-';
								?>
							</td>
						</tr>
					</tbody>
				</table>
			</li>
			<li>
				<h4>Logs <a href="<?php echo esc_url( $this->license->logs->get_file_url() ); ?>" target="_blank">(view file)</a></h4>
				<div class="logs">
					<?php
					$logs = $this->license->logs->read( true );
					if ( is_array( $logs ) ) {
						$logs = array_reverse( $logs );
						foreach ( $logs as $line ) {
							echo esc_html( $line ) . '<br>';
						}
					}
					?>
				</div>
			</li>
			<li>
				<h4>Actions</h4>
				<table>
					<tbody>
						<tr>
							<td class="index">Force Re-Check License</td>
							<td class="value"><button type="button" class="button debug-action" data-action="re-check">Re-Check</button></td>
						</tr>
						<tr>
							<td class="index">Delete Local License Data</td>
							<td class="value"><button type="button" class="button debug-action" data-action="delete-license">Delete</button></td>
						</tr>
						<tr>
							<td class="index">Clear Logs</td>
							<td class="value"><button type="button" class="button debug-action" data-action="clear-logs">Clear</button></td>
						</tr>
					</tbody>
				</table>
			</li>
		</ul>
	</div>
</div>
<script>
	jQuery(function($) {
		$('.button.toggle').on('click',function() {
			$(this).parents('.value').toggleClass('hide');
		});
		$('.debug-action').on('click', function() {
			var action = $(this).data('action');
			$.ajax({
				url: ajaxurl,
				type: 'post',
				data: {
					action: 'tj_license_debug_<?php echo esc_html( $this->args['plugin_name'] ); ?>',
					debug_action: action
				},
				context: this,
				beforeSend: function() {
					$(this).prop('disabled',true);
				},
				success: function(response) {
					if ( 'success' === response ) {
						location.reload();
					} else {
						$(this).prop('disabled',false);
						console.log( response );
					}
				},
				error: function(data) {
					$(this).prop('disabled',false);
					console.log( data );
				}
			});
		});
	});
</script>
