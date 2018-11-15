<?php
/**
 * Plugin Name:     Plugin Ongkos Kirim
 * Plugin URI:      https://tonjoostudio.com/addons/woo-ongkir/
 * Description:     Support woocomerce calculation your shipping cost
 * Version:         3.0.0
 * Author:          Tonjoo Studio
 * Author URI:      https://tonjoostudio.com
 * License:         GPL
 * Text Domain:     pok
 * Domain Path:     /languages
 * WC requires at least: 2.6
 * WC tested up to: 3.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// constants.
define( 'POK_VERSION', '3.0.0' );
define( 'POK_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'POK_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );
define( 'POK_DEBUG', false );
define( 'POK_LOG_NAME', 'pok-error-logs' );

// load files.
require_once POK_PLUGIN_PATH . 'classes/class-pok-setting.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-helper.php';
require_once POK_PLUGIN_PATH . 'libs/tonjoo-license/tonjoo-license-handler.php';
require_once POK_PLUGIN_PATH . 'classes/api/class-pok-api.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-core.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-product-hooks.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-admin.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-ajax.php';
require_once POK_PLUGIN_PATH . 'classes/class-pok-checkout-fields.php';

if ( ! class_exists( 'Plugin_Ongkos_Kirim' ) ) {

	/**
	 * POK Main Class
	 */
	class Plugin_Ongkos_Kirim {

		/**
		 * Constructor
		 */
		public function __construct() {
			global $pok_helper;
			global $pok_core;
			$this->license = new Tonjoo_License_Handler(
				array(
					'plugin_name'   => 'wooongkir-premium',
					'license_form'  => admin_url( 'admin.php?page=pok_license' ),
					'plugin_path'   => __FILE__,
				)
			);
			$pok_helper = new POK_Helper( $this->license );
			$pok_core   = new POK_Core( $this->license );
			$this->helper = $pok_helper;

			register_activation_hook( __FILE__, array( $this, 'on_plugin_activation' ) );
			add_action( 'admin_init', array( $this, 'on_admin_init' ) );
			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'load_shipping_method' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'register_shipping_method' ) );
		}

		/**
		 * Actions when plugin activated
		 */
		public function on_plugin_activation() {

		}

		/**
		 * Actions when admin initialized
		 */
		public function on_admin_init() {

		}

		/**
		 * Actions when all plugins loaded
		 */
		public function on_plugins_loaded() {
			load_plugin_textdomain( 'pok', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
			new POK_Admin( $this->license );
			new POK_Ajax( $this->license );
			new POK_Product_Hooks();
			new POK_Checkout_Fields();
			if ( version_compare( get_option( 'pok_version', '2.1.3' ), POK_VERSION, '<' ) ) {
				$setting = new POK_Setting();
				$setting->setting_migration();
				$this->license->init_updater();
				$status = get_option( 'nusantara_ongkir_license_status', array( false, '' ) );
				$this->license->set_status(
					array(
						'active'    => $status[0],
						'key'       => get_option( 'nusantara_ongkir_lisensi', '' ),
					)
				);
				$this->license->check_status( true );
				update_option( 'pok_version', '3.0.0', true );
			}
		}

		/**
		 * Load POK Shipping method
		 */
		public function load_shipping_method() {
			require_once POK_PLUGIN_PATH . '/classes/class-pok-shipping-method.php';
		}

		/**
		 * Register POK Shipping Method
		 *
		 * @param  array $methods Currently registered methods.
		 * @return array          Registered methods.
		 */
		public function register_shipping_method( $methods ) {
			$methods['plugin_ongkos_kirim'] = 'POK_Shipping_Method';
			return $methods;
		}

	}

	// Initiate!.
	new Plugin_Ongkos_Kirim();

}
