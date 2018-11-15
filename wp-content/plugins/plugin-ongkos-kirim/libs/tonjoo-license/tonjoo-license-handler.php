<?php
/**
 * Tonjoo License Handler
 *
 * Version:         2.0.0
 * Contributors:    gama
 *
 * @package tonjoo-license-handler
 */

if ( ! defined( 'TONJOO_LICENSE_OPTION_NAME' ) ) {
	define( 'TONJOO_LICENSE_OPTION_NAME', 'tonjoo_plugin_license' );
}

if ( ! class_exists( 'Tonjoo_License_Handler' ) ) {

	require_once 'tonjoo-license-api.php';

	/**
	 * Tonjoo Plugin License Handler
	 */
	class Tonjoo_License_Handler {

		/**
		 * Plugin update name
		 *
		 * @var string
		 */
		public $plugin;

		/**
		 * Plugin main file path
		 *
		 * @var string
		 */
		public $plugin_path;

		/**
		 * Default args for license data
		 *
		 * @var string
		 */
		public $default_args;

		/**
		 * Tonjoo license API object
		 *
		 * @var object
		 */
		public $license;

		/**
		 * Max attempt number for license check
		 *
		 * @var int
		 */
		public $max_attempt;

		/**
		 * License check interval
		 *
		 * @var int
		 */
		public $check_interval;

		/**
		 * License form url
		 *
		 * @var string
		 */
		public $license_form;

		/**
		 * Constructor
		 *
		 * @param array $args Arguments.
		 */
		public function __construct( $args = array() ) {
			$args = wp_parse_args(
				$args, array(
					'plugin_name'   => '',
					'plugin_path'   => '',
					'max_attempt'   => 5,
					'check_interval' => DAY_IN_SECONDS,
					'license_form'  => '',
				)
			);
			$this->args = $args;
			add_action( 'init', array( $this, 'check_status' ), 25 );
			add_action( 'admin_init', array( $this, 'hide_notice' ), 30 );
			add_action( 'admin_init', array( $this, 'init_updater' ), 35 );
			$this->plugin       = $args['plugin_name'];
			$this->plugin_path  = $args['plugin_path'];
			$this->default_args = array(
				'active'    => false,
				'key'       => '',
				'type'      => '-',
				'expiry'    => '-',
				'last_check' => time(),
				'check_attempt' => 0,
				'email'     => '-',
				'activation_date' => '-',
				'hide_notice' => array(),
			);
			$this->license      = new Tonjoo_License_API( $this->plugin );
			$this->max_attempt   = $args['max_attempt'];
			$this->check_interval = $args['check_interval']; // a day after last check.
			$this->licenses = get_option( TONJOO_LICENSE_OPTION_NAME, array() );
			add_action( 'wp_ajax_tj_activate_plugin_' . $this->plugin, array( $this, 'action_activation' ) );
			add_action( 'wp_ajax_tj_deactivate_plugin_' . $this->plugin, array( $this, 'action_deactivation' ) );
			add_action( 'wp_ajax_tj_license_debug_' . $this->plugin, array( $this, 'action_debug' ) );
			add_action( 'admin_notices', array( $this, 'show_notice' ) );
		}

		/**
		 * Init option
		 */
		public function init_option() {
			$licenses = $this->licenses;
			if ( ! isset( $licenses[ $this->plugin ] ) ) {
				$licenses[ $this->plugin ] = $this->default_args;
				update_option( TONJOO_LICENSE_OPTION_NAME, $licenses );
			}
			$this->licenses = get_option( TONJOO_LICENSE_OPTION_NAME );
		}

		/**
		 * Init plugin updater
		 */
		public function init_updater() {
			if ( $this->is_license_active() ) {
				$license = $this->get_status();
				// if user manually click Check for update.
				if ( isset( $_GET['puc_check_for_updates'], $_GET['puc_slug'] ) && $_GET['puc_slug'] == $this->plugin && current_user_can( 'update_plugins' ) && check_admin_referer( 'puc_check_for_updates' ) ) {
					$this->license->update_json( $license['key'] );
				}
				$this->license->load_updater( $license['key'], $this->plugin_path, $this->check_interval );
			}
		}

		/**
		 * Scheduled check license status on server
		 *
		 * @param  boolean $force Force check?.
		 */
		public function check_status( $force = false ) {
			$license = $this->get_status();

			// forget it if license key is empty.
			if ( empty( $license['key'] ) ) {
				return;
			}

			// current status on website is must active.
			if ( true === $force || true === $license['active'] ) {

				// check if current attempt is below max.
				if ( true === $force || $this->max_attempt > $license['check_attempt'] ) {
					if ( true === $force || ( '-' === $license['expiry'] ) || ( '-' !== $license['expiry'] && time() > (int) $license['expiry'] ) || ( abs( $license['last_check'] - time() ) > $this->check_interval ) ) {
						$status = $this->license->status( $license['key'] );
						if ( true === $status['status'] ) {
							if ( strtotime( $status['data']->validUntil ) < time() ) { // expired.
								$this->license->logs->write( 'License marked as expired' );
								$args = array(
									'active'        => false,
									'key'           => $license['key'],
									'type'          => $license['type'],
									'expiry'        => strtotime( $status['data']->validUntil ),
									'last_check'    => time(),
									'check_attempt' => 0,
									'email'         => $license['email'],
									'activation_date' => $license['activation_date'],
								);
							} else { // license active.
								$args = array(
									'active'        => true,
									'key'           => $license['key'],
									'type'          => $status['data']->licenseType,
									'expiry'        => strtotime( $status['data']->validUntil ),
									'last_check'    => time(),
									'check_attempt' => 0,
									'email'         => $license['email'],
									'activation_date' => $license['activation_date'],
									'hide_notice'   => $license['hide_notice'],
								);
							}
						} else { // server returns false.
							if ( __( "Error: Can't connect to server", 'pok' ) !== $status['data'] ) {
								$args = array(
									'key'           => $license['key'],
									'active'        => false,
									'type'          => $license['type'],
									'expiry'        => $license['expiry'],
									'last_check'    => time(),
									'check_attempt' => 0,
									'email'         => $license['email'],
									'activation_date' => $license['activation_date'],
									'hide_notice'   => $license['hide_notice'],
								);
							} else { // can't connect to server.
								$this->license->logs->write( 'Failed to check license. Attempt ' . intval( $license['check_attempt'] ) + 1 );
								$args = array(
									'key'           => $license['key'],
									'active'        => $license['active'],
									'type'          => $license['type'],
									'expiry'        => $license['expiry'],
									'last_check'    => time(),
									'check_attempt' => intval( $license['check_attempt'] ) + 1,
									'email'         => $license['email'],
									'activation_date' => $license['activation_date'],
									'hide_notice'   => $license['hide_notice'],
								);
							}
						}
					} else {
						return;
					}

					// if the attempt is reached max, then set plugin to deactive.
				} else {
					$this->license->logs->write( 'Failed to check license and reached max attempt. License deactivated.' );
					$args = array(
						'active'        => false,
						'key'           => $license['key'],
						'type'          => $license['type'],
						'expiry'        => $license['expiry'],
						'last_check'    => time(),
						'check_attempt' => 0,
						'email'         => $license['email'],
						'activation_date' => $license['activation_date'],
					);
				}
				$this->set_status( $args );
			}
		}

		/**
		 * Get current plugin license status on database
		 *
		 * @return array License status.
		 */
		public function get_status() {
			if ( ! isset( $this->licenses[ $this->plugin ] ) ) {
				$this->init_option();
			}
			return wp_parse_args( $this->licenses[ $this->plugin ], $this->default_args );
		}

		/**
		 * Set plugin license status to database
		 *
		 * @param array $args License array.
		 */
		public function set_status( $args = array() ) {
			$args = wp_parse_args( $args, $this->default_args );
			$this->licenses[ $this->plugin ] = $args;
			update_option( TONJOO_LICENSE_OPTION_NAME, $this->licenses );
			$this->licenses = get_option( TONJOO_LICENSE_OPTION_NAME );
		}

		/**
		 * Get license status item
		 *
		 * @param  string $index Index name.
		 * @return mixed         Status item.
		 */
		public function get( $index = 'active' ) {
			$status = $this->get_status();
			if ( isset( $status[ $index ] ) ) {
				return $status[ $index ];
			}
			return false;
		}

		/**
		 * Render license activation form
		 */
		public function render_form() {
			$plugin_info    = get_plugin_data( $this->plugin_path );
			$license_info   = $this->get_status();
			$is_active      = $this->is_license_active();
			$is_expired     = $this->is_license_expired();
			include 'view/license-form.php';
		}

		/**
		 * License activation handler
		 */
		public function action_activation() {
			$response = array(
				'status'        => false,
				'message'       => __( 'Error: Authentication Failed. Please try again', 'pok' ),
				'last_check'    => $this->get_local_time(),
			);
			$current = $this->get_status();
			if ( isset( $_POST['tj_license'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tj_license'] ) ), 'tonjoo-activate-license' ) ) { // Input var okay.
				if ( isset( $_POST['key'] ) ) { // Input var okay.
					$activation = $this->license->activate( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
					if ( true === $activation['status'] ) {
						$status = $this->license->status( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
						if ( true === $status['status'] ) {
							if ( strtotime( $status['data']->validUntil ) < time() ) { // expired.
								$args = array(
									'active'        => false,
									'last_check'    => time(),
								);
								$response = array(
									'status'        => false,
									'message'       => sprintf( __( 'Your license has expired at %s', 'pok' ), $this->get_local_time( strtotime( $status['data']->validUntil ) ) ),
									'last_check'    => $this->get_local_time( $args['last_check'] ),
								);
							} else { // active.
								$args = array(
									'active'        => true,
									'key'           => sanitize_text_field( wp_unslash( $_POST['key'] ) ), // Input var okay.
									'type'          => $status['data']->licenseType,
									'expiry'        => strtotime( $status['data']->validUntil ),
									'last_check'    => time(),
									'check_attempt' => 0,
									'email'         => isset( $activation['activation_data']->email ) ? $activation['activation_data']->email : '',
									'activation_date' => time(),
								);
								$response = array(
									'status'        => true,
									'message'       => __( 'Activation Success', 'pok' ),
									'last_check'    => $this->get_local_time( $args['last_check'] ),
								);
							}
						} else {
							$this->license->deactivate( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
							$args = array(
								'last_check' => time(),
							);
							$response = array(
								'status'        => false,
								'message'       => $status['data'],
								'last_check'    => $this->get_local_time( $args['last_check'] ),
							);
						}
					} else {
						$args = array(
							'last_check' => time(),
						);
						$response = array(
							'status'        => false,
							'message'       => $activation['data'],
							'last_check'    => $this->get_local_time( $args['last_check'] ),
						);
					}
				} else {
					$args = array(
						'last_check' => time(),
					);
					$response = array(
						'status'        => false,
						'message'       => __( 'License key is empty', 'pok' ),
						'last_check'    => $this->get_local_time(),
					);
				}
			}
			$this->set_status( $args );
			wp_send_json( $response );
		}

		/**
		 * License deactivation handler
		 */
		public function action_deactivation() {
			$response = array(
				'status'        => false,
				'message'       => __( 'Error: Authentication Failed. Please try again', 'pok' ),
				'last_check'    => $this->get_local_time(),
			);
			$current = $this->get_status();
			if ( isset( $_POST['tj_license'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tj_license'] ) ), 'tonjoo-deactivate-license' ) ) { // Input var okay.
				if ( isset( $_POST['key'] ) ) { // Input var okay.
					$deactivation = $this->license->deactivate( sanitize_text_field( wp_unslash( $_POST['key'] ) ) ); // Input var okay.
					if ( true === $deactivation['status'] ) {
						$args = array(
							'last_check' => time(),
						);
						$response = array(
							'status'        => true,
							'message'       => __( 'Deactivation Success', 'pok' ),
							'last_check'    => $this->get_local_time( $args['last_check'] ),
						);
					} else {
						$args = $current;
						$args['last_check'] = time();
						$response = array(
							'status'        => false,
							'message'       => $deactivation['data'],
							'last_check'    => $this->get_local_time( $args['last_check'] ),
						);
					}
				} else {
					$args = array(
						'last_check' => time(),
					);
					$response = array(
						'status'        => false,
						'message'       => __( 'License key is empty', 'pok' ),
						'last_check'    => $this->get_local_time(),
					);
				}
			}
			$this->set_status( $args );
			wp_send_json( $response );
		}

		/**
		 * Show admin notices
		 */
		public function show_notice() {
			$license = $this->get_status();
			$plugin = get_plugin_data( $this->plugin_path );
			if ( ! $this->is_license_active() ) {
				if ( $this->is_license_expired() && ! in_array( 'expired', $license['hide_notice'], true ) ) {
					?>
						<div class="notice notice-warning is-dismissible">
							<?php if ( 'trial' === $license['type'] ) : ?>
								<p><?php printf( __( 'Your trial for <strong>%s</strong> has ended.', 'pok' ), esc_html( $plugin['Name'] ) ); ?></p>
							<?php else : ?>
								<p><?php printf( __( 'Your license for <strong>%s</strong> has expired.', 'pok' ), esc_html( $plugin['Name'] ) ); ?></p>
							<?php endif; ?>
							<p>
								<?php if ( ! empty( $this->args['license_form'] ) ) : ?>
									<a href="<?php echo esc_url( $this->args['license_form'] ); ?>" class="button-primary"><?php esc_html_e( 'Renew License', 'pok' ); ?></a>
								<?php endif; ?>
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?hide=expired&ref=' . $this->get_current_url() ), 'hide_notice_' . $this->plugin, 'tj_license' ) ); ?>" class="button"><?php esc_html_e( "Don't show again", 'pok' ); ?></a>
							</p>
						</div>
					<?php
				} elseif ( ! in_array( 'activate', $license['hide_notice'], true ) ) {
					?>
						<div class="notice notice-warning is-dismissible">
							<p><?php printf( __( 'Please activate your <strong>%s</strong> license.', 'pok' ), esc_html( $plugin['Name'] ) ); ?></p>
							<p>
								<?php if ( ! empty( $this->args['license_form'] ) ) : ?>
									<a href="<?php echo esc_url( $this->args['license_form'] ); ?>" class="button-primary"><?php esc_html_e( 'Activate License', 'pok' ); ?></a>
								<?php endif; ?>
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?hide=activate&ref=' . $this->get_current_url() ), 'hide_notice_' . $this->plugin, 'tj_license' ) ); ?>" class="button"><?php esc_html_e( "Don't show again", 'pok' ); ?></a>
							</p>
						</div>
					<?php
				}
			} else {
				if ( 'trial' !== $license['type'] && ( $license['expiry'] - time() ) < WEEK_IN_SECONDS && ! in_array( 'week_to_expired', $license['hide_notice'], true ) ) {
					?>
						<div class="notice notice-warning is-dismissible">
							<p><?php printf( __( 'Your license for <strong>%s</strong> will ended soon.', 'pok' ), esc_html( $plugin['Name'] ) ); ?></p>
							<p>
								<?php if ( ! empty( $this->args['license_form'] ) ) : ?>
									<a href="<?php echo esc_url( $this->args['license_form'] ); ?>" class="button-primary"><?php esc_html_e( 'Renew', 'pok' ); ?></a>
								<?php endif; ?>
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?hide=week_to_expired&ref=' . $this->get_current_url() ), 'hide_notice_' . $this->plugin, 'tj_license' ) ); ?>" class="button"><?php esc_html_e( "Don't show again", 'pok' ); ?></a>
							</p>
						</div>
					<?php
				} elseif ( 'trial' === $license['type'] && ( $license['expiry'] - time() ) < DAY_IN_SECONDS && ! in_array( 'day_to_expired', $license['hide_notice'], true ) ) {
					?>
						<div class="notice notice-warning is-dismissible">
							<p><?php printf( __( 'Your trial license for <strong>%s</strong> will ended soon.', 'pok' ), esc_html( $plugin['Name'] ) ); ?></p>
							<p>
								<?php if ( ! empty( $this->args['license_form'] ) ) : ?>
									<a href="<?php echo esc_url( $this->args['license_form'] ); ?>" class="button-primary"><?php esc_html_e( 'Renew', 'pok' ); ?></a>
								<?php endif; ?>
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( '?hide=day_to_expired&ref=' . $this->get_current_url() ), 'hide_notice_' . $this->plugin, 'tj_license' ) ); ?>" class="button"><?php esc_html_e( "Don't show again", 'pok' ); ?></a>
							</p>
						</div>
					<?php
				}
			}
		}

		/**
		 * Hide admin notice forever.
		 */
		public function hide_notice() {
			if ( isset( $_GET['tj_license'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['tj_license'] ) ), 'hide_notice_' . $this->plugin ) ) { // Input var okay.
				if ( isset( $_GET['hide'] ) ) { // Input var okay.
					$hide = sanitize_text_field( wp_unslash( $_GET['hide'] ) ); // Input var okay.
					$license = $this->get_status();
					if ( ! in_array( $hide, $license['hide_notice'], true ) ) {
						$license['hide_notice'][] = $hide;
						$this->set_status( $license );
					}
				}
				if ( isset( $_GET['ref'] ) ) { // Input var okay.
					wp_safe_redirect( esc_url_raw( wp_unslash( $_GET['ref'] ) ) ); // Input var okay.
				} else {
					wp_safe_redirect( admin_url() );
				}
			}
		}

		/**
		 * Check if plugin is active
		 *
		 * @return boolean License is active or not.
		 */
		public function is_license_active() {
			return $this->get( 'active' );
		}

		/**
		 * Check if license is expired
		 *
		 * @return boolean Check license expired or not.
		 */
		public function is_license_expired() {
			$license = $this->get_status();
			return ( '-' !== $license['expiry'] && time() > $license['expiry'] && ! $license['active'] );
		}

		/**
		 * Get local time from timestamps
		 *
		 * @param  integer $time Timestamps.
		 * @return string        Current time in local format.
		 */
		private function get_local_time( $time = 0 ) {
			if ( 0 === $time ) {
				$time = time();
			}
			$offset = get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			return date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $time + $offset );
		}

		/**
		 * Get current URL
		 *
		 * @return string Current URL.
		 */
		private function get_current_url() {
			return ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		/**
		 * Ajax debug action
		 */
		public function action_debug() {
			if ( isset( $_POST['debug_action'] ) ) {
				if ( 'delete-license' === $_POST['debug_action'] ) {
					$this->set_status( array() );
					echo 'success';
				} elseif ( 'clear-logs' === $_POST['debug_action'] ) {
					if ( $this->license->logs->clear() ) {
						echo 'success';
					}
				} elseif ( 're-check' === $_POST['debug_action'] ) {
					$this->license->check_status();
					echo 'success';
				}
			}
			die;
		}

	}

}
