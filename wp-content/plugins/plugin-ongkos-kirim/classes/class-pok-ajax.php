<?php

/**
 * POK Ajax class
 */
class POK_Ajax {

	/**
	 * POK core function
	 *
	 * @var object
	 */
	protected $core;

	/**
	 * POK Setting
	 *
	 * @var object
	 */
	protected $setting;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $pok_core;
		$this->core = $pok_core;
		$this->setting = new POK_Setting();

		add_action( 'wp_ajax_pok_get_store_location_city', array( $this, 'get_store_location_city' ) );
		add_action( 'wp_ajax_nopriv_pok_get_store_location_city', array( $this, 'get_store_location_city' ) );

		add_action( 'wp_ajax_pok_get_courier_type', array( $this, 'get_courier_type' ) );
		add_action( 'wp_ajax_nopriv_pok_get_courier_type', array( $this, 'get_courier_type' ) );

		add_action( 'wp_ajax_pok_get_list_city', array( $this, 'get_list_city' ) );
		add_action( 'wp_ajax_nopriv_pok_get_list_city', array( $this, 'get_list_city' ) );

		add_action( 'wp_ajax_pok_get_list_district', array( $this, 'get_list_district' ) );
		add_action( 'wp_ajax_nopriv_pok_get_list_district', array( $this, 'get_list_district' ) );

		add_action( 'wp_ajax_pok_change_country', array( $this, 'change_country' ) );
		add_action( 'wp_ajax_nopriv_pok_change_country', array( $this, 'change_country' ) );

		add_action( 'wp_ajax_pok_get_cost', array( $this, 'get_cost' ) );
		add_action( 'wp_ajax_nopriv_pok_get_cost', array( $this, 'get_cost' ) );

		add_action( 'wp_ajax_pok_search_city', array( $this, 'search_city' ) );
		add_action( 'wp_ajax_pok_set_rajaongkir_api_key', array( $this, 'set_rajaongkir_api_key' ) );
		add_action( 'wp_ajax_pok_nopriv_set_rajaongkir_api_key', array( $this, 'set_rajaongkir_api_key' ) );

		$this->logs         = new TJ_Logs( POK_LOG_NAME );
	}

	/**
	 * Set rajaongkir API key
	 */
	public function set_rajaongkir_api_key() {
		check_ajax_referer( 'set_rajaongkir_api_key', 'pok_action' );
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : ''; // Input var okay.
		$api_type = isset( $_POST['api_type'] ) ? sanitize_text_field( wp_unslash( $_POST['api_type'] ) ) : ''; // Input var okay.
		$check = $this->core->get_rajaongkir_status( $api_key, $api_type );
		if ( true === $check ) {
			$this->core->delete_cache( 'courier' );
			$this->setting->set( 'rajaongkir_key', $api_key );
			$this->setting->set( 'rajaongkir_type', $api_type );
			$this->setting->set( 'rajaongkir_status', array( true, 'API Key Active' ) );
			$this->core->purge_cache( 'cost' );
			$this->setting->set( 'base_api', 'rajaongkir' );
			$this->setting->set( 'store_location', array() );
			$this->setting->set( 'couriers', $this->core->get_courier() );
			echo 'success';
		} else {
			if ( '' === $api_key ) {
				esc_html_e( 'API key is empty', 'pok' );
			} elseif ( ! in_array( $api_type, array( 'starter', 'basic', 'pro' ), true ) ) {
				esc_html_e( 'API type is not valid', 'pok' );
			} elseif ( false !== $check ) {
				echo esc_html( $check );
			} else {
				esc_html_e( 'API Key is not valid', 'pok' );
			}
		}
		die;
	}

	/**
	 * Search city
	 */
	public function search_city() {
		check_ajax_referer( 'search_city', 'pok_action' );
		$search = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : ''; // Input var okay.
		if ( 'nusantara' === $this->setting->get( 'base_api' ) ) {
			$result = $this->core->search_city( $search );
			$return = array();
			if ( isset( $result ) && ! empty( $result ) ) {
				foreach ( $result as $res ) {
					$return[] = array(
						'id'    => $res->id,
						'text'  => $res->type . ' ' . $res->nama . ', ' . $res->provinsi,
					);
				}
			}
		} elseif ( 'rajaongkir' === $this->setting->get( 'base_api' ) ) {
			$cities = $this->core->get_all_city();
		}
		echo wp_json_encode( $return );
		exit();
	}

	/**
	 * Change country on checkout page
	 */
	public function change_country() {
		check_ajax_referer( 'change_country', 'pok_action' );
		$new_value  = isset( $_POST['country'] ) ? sanitize_text_field( wp_unslash( $_POST['country'] ) ) : 'ID'; // Input var okay.
		$context    = isset( $_POST['context'] ) ? sanitize_text_field( wp_unslash( $_POST['context'] ) ) : 'billing'; // Input var okay.
		$customer = maybe_unserialize( WC()->session->get( 'customer' ) );
		if ( 'billing' === $context ) {
			$session_name = 'country';
		} else {
			$session_name = 'shipping_country';
		}
		$old_value  = isset( $customer[ $session_name ] ) ? $customer[ $session_name ] : 'ID';
		if ( $old_value !== $new_value ) {
			$customer[ $session_name ] = $new_value;
			WC()->session->set( 'customer', maybe_serialize( $customer ) );
			if ( 'ID' === $old_value || 'ID' === $new_value ) {
				echo 'reload';
			}
		}
		die();
	}

	/**
	 * Get list city
	 */
	public function get_list_city() {
		check_ajax_referer( 'get_list_city', 'pok_action' );
		$province_id = isset( $_POST['province_id'] ) ? sanitize_text_field( wp_unslash( $_POST['province_id'] ) ) : 0; // Input var okay.
		$city = $this->core->get_city( $province_id );
		$r_city = array();

		if ( is_array( $city ) ) {
			foreach ( $city as $key => $value ) {
				$r_city[ $value->id ] = $value->nama;
			}
		}

		echo wp_json_encode( $r_city );
		wp_die();
	}

	/**
	 * Get list district
	 */
	public function get_list_district() {
		check_ajax_referer( 'get_list_district', 'pok_action' );
		$city_id    = isset( $_POST['city_id'] ) ? sanitize_text_field( wp_unslash( $_POST['city_id'] ) ) : 0; // Input var okay.
		$city       = $this->core->get_district( $city_id );
		$r_city     = array();

		if ( is_array( $city ) ) {
			foreach ( $city as $key => $value ) {
				$r_city[ $value->id ] = $value->nama;
			}
		}

		echo wp_json_encode( $r_city );
		wp_die();
	}

	/**
	 * Get list district
	 */
	public function get_cost() {
		check_ajax_referer( 'get_cost', 'pok_action' );
		$destination    = isset( $_POST['destination'] ) ? sanitize_text_field( wp_unslash( $_POST['destination'] ) ) : 0; // Input var okay.
		$weight         = isset( $_POST['weight'] ) ? sanitize_text_field( wp_unslash( $_POST['weight'] ) ) : 0; // Input var okay.
		$cost           = $this->core->get_cost( $destination, $weight );

		echo wp_json_encode( $cost );
		wp_die();
	}

}
