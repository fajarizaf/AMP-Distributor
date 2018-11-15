<?php

/**
 * POK Admin Class
 */
class POK_Admin {

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler $license ) {
		global $pok_helper;
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'handle_actions' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		add_action( 'admin_notices', array( $this, 'admin_notice' ) );
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
		$this->helper   = $pok_helper;
		$this->setting  = new POK_Setting();
		$this->core     = new POK_Core( $license );
		$this->license  = $license;
	}

	/**
	 * Validate current admin screen
	 *
	 * @return boolean Screen is TIM or not.
	 */
	private function validate_screen() {
		$screen = get_current_screen();
		if ( is_null( $screen ) ) {
			return false;
		}

		$allowed_screens = array(
			'ongkos-kirim_page_pok_setting',
			'ongkos-kirim_page_pok_license',
			'ongkos-kirim_page_pok_about',
			'woocommerce_page_wc-settings',
		);
		if ( in_array( $screen->id, $allowed_screens, true ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Enqueue Script Inventory Manager
	 */
	public function enqueue_scripts() {
		if ( $this->validate_screen() ) {
			wp_register_style( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css', array(), '4.0.5' );
			wp_enqueue_style( 'pok-admin', POK_PLUGIN_URL . '/assets/css/admin.css', array( 'select2' ), POK_VERSION );
			wp_enqueue_script( 'pok-admin', POK_PLUGIN_URL . '/assets/js/admin.js', array( 'jquery', 'select2' ), POK_VERSION, true );
			wp_localize_script( 'pok-admin', 'pok_settings', $this->setting->get_all() );
			wp_localize_script(
				'pok-admin', 'pok_translations', array(
					'confirm_change_base_api'       => __( 'Are you sure? Switching base API will delete all cached data and custom shipping costs. Also, you might need to re-set your store location.', 'pok' ),
					'switch_base_api_rajaongkir'    => __( 'To activate Rajaongkir, provide the API key and set the API type, then click Check Rajaongkir Status.', 'pok' ),
					'rajaongkir_key_empty'          => __( 'API key is empty', 'pok' ),
					'cant_connect_server'           => __( 'Can not connect server', 'pok' ),
					'connecting_server'             => __( 'Connecting server...', 'pok' ),
					'all_province'                  => __( 'All Province', 'pok' ),
					'all_city'                      => __( 'All City', 'pok' ),
					'all_district'                  => __( 'All District', 'pok' ),
					'delete'                        => __( 'Delete', 'pok' ),
					'add'                           => __( 'Add', 'pok' ),
					'select_city'                   => __( 'Select city', 'pok' ),
					'select_district'               => __( 'Select district', 'pok' ),
				)
			);
			wp_localize_script(
				'pok-admin', 'pok_urls', array(
					'switch_base_api_tonjoo'        => wp_nonce_url( admin_url( 'admin.php?page=pok_setting&base_api=nusantara' ), 'change_base_api', 'pok_action' ),
					'switch_base_api_rajaongkir'    => wp_nonce_url( admin_url( 'admin.php?page=pok_setting&base_api=rajaongkir' ), 'change_base_api', 'pok_action' ),
				)
			);
			wp_localize_script(
				'pok-admin', 'pok_nonces', array(
					'set_rajaongkir_api_key'    => wp_create_nonce( 'set_rajaongkir_api_key' ),
					'search_city'               => wp_create_nonce( 'search_city' ),
					'get_list_city'             => wp_create_nonce( 'get_list_city' ),
					'get_list_district'         => wp_create_nonce( 'get_list_district' ),
				)
			);
			wp_localize_script(
				'pok-admin', 'wc_currency', array(
					'currency'      => get_woocommerce_currency_symbol( get_option( 'woocommerce_currency' ) ),
					'currency_pos'  => get_option( 'woocommerce_currency_pos' ),
					'sep_thousand'  => get_option( 'woocommerce_price_thousand_sep' ),
					'sep_decimal'   => get_option( 'woocommerce_price_decimal_sep' ),
					'num_decimal'   => get_option( 'woocommerce_price_num_decimals' ),
				)
			);
		}
	}

	/**
	 * Register admin menu
	 */
	public function admin_menu() {
		add_menu_page( 'Ongkos Kirim', 'Ongkos Kirim', 'manage_options', 'plugin_ongkos_kirim', null, POK_PLUGIN_URL . '/assets/img/icon.png', 58 );
		add_submenu_page( 'plugin_ongkos_kirim', __( 'Settings', 'pok' ), __( 'Settings', 'pok' ), 'manage_options', 'pok_setting', array( $this, 'render_page_setting' ) );
		add_submenu_page( 'plugin_ongkos_kirim', __( 'License', 'pok' ), __( 'License', 'pok' ), 'manage_options', 'pok_license', array( $this, 'render_page_license' ) );
		add_submenu_page( 'plugin_ongkos_kirim', __( 'About', 'pok' ), __( 'About', 'pok' ), 'manage_options', 'pok_about', array( $this, 'render_page_about' ) );
		remove_submenu_page( 'plugin_ongkos_kirim', 'plugin_ongkos_kirim' );
	}

	/**
	 * Render setting page
	 */
	public function render_page_setting() {
		if ( $this->license->is_license_active() ) {
			$tabs = apply_filters(
				'pok_setting_tabs', array(
					'setting'   => array(
						'label'     => __( 'Setting', 'pok' ),
						'callback'  => array( $this, 'render_subpage_setting_setting' ),
					),
					'custom'    => array(
						'label'     => __( 'Custom Shipping Costs', 'pok' ),
						'callback'  => array( $this, 'render_subpage_setting_custom' ),
					),
					'checker'   => array(
						'label'     => __( 'Shipping Cost Checker', 'pok' ),
						'callback'  => array( $this, 'render_subpage_setting_checker' ),
					),
					'log'   => array(
						'label'     => __( 'Error Logs', 'pok' ),
						'callback'  => array( $this, 'render_subpage_setting_log' ),
					),
				)
			);
			if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) ) {
				$tab = $_GET['tab'];
			} else {
				$tab = current( array_keys( $tabs ) );
			}
			include_once POK_PLUGIN_PATH . 'views/setting.php';
		} else {
			include_once POK_PLUGIN_PATH . 'views/setting-inactive.php';
		}
	}

	/**
	 * Render license page
	 */
	public function render_page_license() {
		?>
		<div class="wrap pok-wrapper">
			<h1 class="wp-heading-inline"><?php esc_html_e( 'License', 'pok' ); ?></h1>
			<hr class="wp-header-end">
			<br>
			<?php
			$this->license->render_form();
			do_action( 'pok_license_form', $this->license );
			?>
		</div>
		<?php
	}

	/**
	 * Render about page
	 */
	public function render_page_about() {
		$tabs = apply_filters(
			'pok_about_tabs', array(
				'onboard'   => array(
					'label'     => __( 'About', 'pok' ),
					'callback'  => array( $this, 'render_subpage_about_onboard' ),
				),
				'upsell'    => array(
					'label'     => __( 'Cool Stuffs for Your Site', 'pok' ),
					'callback'  => array( $this, 'render_subpage_about_upsell' ),
				),
			)
		);
		if ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array_keys( $tabs ) ) ) {
			$tab = $_GET['tab'];
		} else {
			$tab = current( array_keys( $tabs ) );
		}
		include_once POK_PLUGIN_PATH . 'views/about.php';
	}

	/**
	 * Render setting page, subpage setting
	 */
	public function render_subpage_setting_setting() {
		global $pok_helper;
		$settings = $this->setting->get_all();
		if ( $pok_helper->is_admin_active() ) {
			$all_couriers = $this->core->get_all_couriers();
			$couriers = $this->core->get_courier( $settings['base_api'], $settings['rajaongkir_type'] );
			$services = $this->core->get_courier_service();
			if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
				$cities = $this->core->get_all_city();
			}
		}
		include_once POK_PLUGIN_PATH . 'views/setting-setting.php';
	}

	/**
	 * Render setting page, subpage custom
	 */
	public function render_subpage_setting_custom() {
		$settings   = $this->setting->get_all();
		$costs      = $this->setting->get_custom_costs();
		$provinces  = $this->core->get_province();
		$couriers   = $this->core->get_courier();
		include_once POK_PLUGIN_PATH . 'views/setting-custom.php';
	}

	/**
	 * Render setting page, subpage checker
	 */
	public function render_subpage_setting_checker() {
		require_once POK_PLUGIN_PATH . 'classes/class-pok-checker.php';
		$checker = new POK_Checker( $this->license );
		$checker->render();
	}

	/**
	 * Render setting page, subpage log
	 */
	public function render_subpage_setting_log() {
		$log = new TJ_Logs( POK_LOG_NAME );
		$logs = $log->read( true );
		$logs = array_reverse( $logs );
		include_once POK_PLUGIN_PATH . 'views/setting-log.php';
	}

	public function render_subpage_about_onboard() {
		include_once POK_PLUGIN_PATH . 'views/about-onboard.php';
	}

	public function render_subpage_about_upsell() {
		require_once POK_PLUGIN_PATH . '/libs/class-tonjoo-plugins-upsell.php';
		$upsell = new Tonjoo_Plugins_Upsell( 'wooongkir-premium' );
		$upsell->render();
	}

	/**
	 * Handle actions
	 */
	public function handle_actions() {
		if ( isset( $_REQUEST['pok_action'] ) ) { // Input var okay.

			// update setting.
			if ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'update_setting' ) ) { // Input var okay.
				$new_setting = array();
				if ( isset( $_POST['pok_setting'] ) && is_array( $_POST['pok_setting'] ) ) { // Input var okay.
					foreach ( wp_unslash( $_POST['pok_setting'] ) as $key => $value ) { // Input var okay.
						if ( ! is_array( $value ) ) {
							$new_setting[ $key ] = sanitize_text_field( wp_unslash( $value ) );
						} else {
							$new_setting[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $value ) );
						}
					}
				}

				$old_setting = $this->setting->get_all();
				$new_setting['base_api']        = $old_setting['base_api'];
				$new_setting['rajaongkir_type'] = $old_setting['rajaongkir_type'];
				$new_setting['rajaongkir_key']  = $old_setting['rajaongkir_key'];

				$new_setting = wp_parse_args( $new_setting, $old_setting );
				$this->setting->save( $new_setting );
				$this->core->purge_cache( 'cost' );
				$this->add_notice( __( 'Settings saved', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// update shipping costs.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'update_custom_costs' ) ) { // Input var okay.
				if ( isset( $_POST['custom_cost'] ) && ! empty( $_POST['custom_cost'] ) ) { // Input var okay.
					$custom = wp_unslash( $_POST['custom_cost'] ); // Input var okay.
				} else {
					$custom = array();
				}
				$courier_name   = isset( $_POST['pok_setting']['custom_cost_courier'] ) ? sanitize_text_field( wp_unslash( $_POST['pok_setting']['custom_cost_courier'] ) ) : 'Custom'; // Input var okay.
				$custom_type    = isset( $_POST['pok_setting']['custom_cost_type'] ) ? sanitize_text_field( wp_unslash( $_POST['pok_setting']['custom_cost_type'] ) ) : 'append'; // Input var okay.
				$this->setting->set( 'custom_cost_courier', $courier_name );
				$this->setting->set( 'custom_cost_type', $custom_type );

				$custom = array_values( $custom );
				$this->setting->save_custom_costs( $custom );

				$this->add_notice( __( 'Custom costs saved', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting&tab=custom' ) );
				die;

				// change base api.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'change_base_api' ) ) { // Input var okay.
				if ( isset( $_GET['base_api'] ) ) { // Input var okay.
					if ( 'nusantara' === sanitize_text_field( wp_unslash( $_GET['base_api'] ) ) ) { // Input var okay.
						$this->setting->set( 'base_api', 'nusantara' );
						$this->setting->set( 'couriers', $this->core->get_courier() );
					} elseif ( 'rajaongkir' === sanitize_text_field( wp_unslash( $_GET['base_api'] ) ) ) { // Input var okay.
						$this->setting->set( 'base_api', 'rajaongkir' );
						$this->setting->set( 'couriers', $this->core->get_courier() );
						if ( ! $this->helper->is_rajaongkir_active() ) {
							$this->setting->set( 'store_location', 0 );
						}
					}
					$this->setting->set( 'store_location', array() );
					// delete custom costs.
					$this->setting->reset_custom_costs();
					// delete customer saved address data.
					global $wpdb;
					$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key IN ('billing_state','billing_city','billing_district','shipping_state','shipping_city','shipping_district')" );
					// purge cache.
					$this->core->purge_cache();

					$this->add_notice( __( 'Base API are switched. Please re-set your store location.', 'pok' ) );
				}
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// flush cache.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'flush_cache' ) ) { // Input var okay.
				$this->core->purge_cache();
				$this->add_notice( __( 'All caches has been purged.', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;

				// reset.
			} elseif ( wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['pok_action'] ) ), 'reset' ) ) { // Input var okay.
				$this->setting->reset();
				$this->core->purge_cache();
				$this->add_notice( __( 'All settings has been reset.', 'pok' ) );
				wp_safe_redirect( admin_url( 'admin.php?page=pok_setting' ) );
				die;
			}
		}
	}

	/**
	 * Add notice
	 *
	 * @param string  $message Message.
	 * @param string  $type    Type.
	 * @param boolean $p       Using paragraph?.
	 */
	private function add_notice( $message = '', $type = 'success', $p = true ) {
		$old_notice = get_option( 'pok_notices', array() );
		$old_notice[] = array(
			'type'      => $type,
			'message'   => $p ? '<p>' . $message . '</p>' : $message,
		);
		update_option( 'pok_notices', $old_notice, false );
	}

	/**
	 * Show all notices
	 */
	public function show_notices() {
		$notices = get_option( 'pok_notices', array() );
		foreach ( $notices as $notice ) {
			echo '
				<div class="notice is-dismissible notice-' . esc_attr( $notice['type'] ) . '">
					' . wp_kses_post( $notice['message'] ) . '
				</div>';
		}
		update_option( 'pok_notices', array() );
	}

	/**
	 * Admin notice
	 */
	public function admin_notice() {
		$errors = array();

		if ( ! $this->helper->is_woocommerce_active() ) {
			$errors[] = __( 'Woocommerce not active', 'pok' );
		}

		if ( ! function_exists( 'curl_version' ) ) {
			$errors[] = __( 'Plugin Ongkos Kirim needs active CURL', 'pok' );
		}

		if ( 'yes' === $this->setting->get( 'enable' ) && $this->helper->is_license_active() ) {
			if ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
				$rajaongkir_status = $this->setting->get( 'rajaongkir_status' );
				if ( ! $rajaongkir_status[0] ) {
					$errors[] = __( 'RajaOngkir API Key is not active.', 'pok' );
				}
			}
			if ( $this->helper->is_admin_active() ) {
				$store_location = $this->setting->get( 'store_location' );
				if ( empty( $store_location ) || empty( $store_location[0] ) ) {
					$errors[] = __( 'Store Location is empty.', 'pok' );
				}
				$courier = $this->setting->get( 'couriers' );
				if ( empty( $courier ) ) {
					$errors[] = __( 'Selected Couriers is empty.', 'pok' );
				}
			}
		}
		if ( ! empty( $errors ) ) {
			?>
			<div class="notice notice-error">
				<p><?php echo wp_kses_post( __( '<strong>Plugin Ongkos Kirim</strong> is disabled due to the following errors:', 'pok' ) ); ?></p>
				<?php foreach ( $errors as $e ) : ?>
					<p style="margin:0;">- <?php echo esc_html( $e ); ?></p>
				<?php endforeach; ?>
				<p style="margin-top: 10px;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=pok_setting' ) ); ?>" class="button"><?php esc_html_e( 'Go to Settings', 'pok' ); ?></a></p>
			</div>
			<?php
		}
	}

}
