<?php

/**
 * POK API Tonjoo
 */
class POK_API_Tonjoo {

	/**
	 * API base url
	 *
	 * @var string
	 */
	protected $base_url;

	/**
	 * API url param
	 *
	 * @var string
	 */
	protected $api_param;

	/**
	 * API default args
	 *
	 * @var array
	 */
	protected $default_args;

	/**
	 * Constructor
	 *
	 * @param string $license_key License key.
	 */
	public function __construct( $license_key ) {
		global $wp_version;
		$this->base_url     = 'https://pluginongkoskirim.com/cek-tarif-ongkir/api';
		$this->api_param    = '?license=' . $license_key . '&website=' . $this->get_web_url();
		$this->default_args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$this->logs         = new TJ_Logs( POK_LOG_NAME );
	}

	/**
	 * Get current web base URL
	 *
	 * @return string URL.
	 */
	public function get_web_url() {
		preg_match_all( '#^.+?[^\/:](?=[?\/]|$)#', get_site_url(), $matches );
		return $matches[0][0];
	}

	/**
	 * Populate API response
	 *
	 * @param  string $url  URL to fetch.
	 * @param  array  $args Fetch args.
	 * @return array        Sanitized API response.
	 */
	private function remote_get( $url, $args ) {
		$content = wp_remote_get( $url, $args );
		if ( is_wp_error( $content ) ) {
			$this->logs->write( '(Error API Tonjoo) Trying fetch ' . str_replace( $this->base_url, '', $url ) . '. Error: ' . $content->get_error_message() );
			return array(
				'status'    => false,
				'data'      => 'Please try again ( Error: ' . $content->get_error_message() . ' )',
			);
		}
		if ( 200 !== $content['response']['code'] ) {
			$this->logs->write( '(Error API Tonjoo) Trying fetch ' . str_replace( $this->base_url, '', $url ) . '. Error code: ' . $content['response']['code'] );
			return array(
				'status'    => false,
				'data'      => 'Error code: ' . $content['response']['code'],
			);
		}
		$body = json_decode( $content['body'] );
		if ( isset( $body->error ) && $body->error ) {
			$this->logs->write( '(Error API Tonjoo) Trying fetch ' . str_replace( $this->base_url, '', $url ) . '. Error: ' . ( isset( $body->message ) ? $body->message : '' ) );
			return array(
				'status'    => false,
				'data'      => isset( $body->message ) ? $body->message : '',
			);
		}
		return array(
			'status'    => true,
			'data'      => isset( $body->data ) ? $body->data : $body,
		);
	}

	/**
	 * Get courier services
	 *
	 * @return array Courier options.
	 */
	public function get_courier_service() {
		return $this->remote_get( $this->base_url . '/ekspedisi/' . $this->api_param, $this->default_args );
	}

	/**
	 * Get province
	 *
	 * @return array Province options
	 */
	public function get_province() {
		return $this->remote_get( $this->base_url . '/provinsi/' . $this->api_param, $this->default_args );
	}

	/**
	 * Get city by province
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City options.
	 */
	public function get_city( $province_id = 0 ) {
		return $this->remote_get( $this->base_url . '/provinsi/' . $province_id . '/dati_ii' . $this->api_param, $this->default_args );
	}

	/**
	 * Get single city by ID
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		return $this->remote_get( $this->base_url . '/asal/' . $city_id . $this->api_param, $this->default_args );
	}

	/**
	 * Get all city by search param
	 *
	 * @param  string $search Search param.
	 * @return array          City options.
	 */
	public function get_all_city( $search = '' ) {
		return $this->remote_get( $this->base_url . '/asal' . $this->api_param . '&s=' . $search, $this->default_args );
	}

	/**
	 * Get disctricts
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District options.
	 */
	public function get_district( $city_id ) {
		return $this->remote_get( $this->base_url . '/kabupaten/' . $city_id . '/dati_iii' . $this->api_param, $this->default_args );
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination (district) ID.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Shipping costs.
	 */
	public function get_cost( $origin, $destination, $courier ) {
		$cur = '';
		foreach ( $courier as $key => $value ) {
			if ( 'jnt' === $value ) {
				$value = 'j&t';
			}
			$cur .= '&ekspedisi[' . $key . ']=' . urlencode( $value );
		}
		return $this->remote_get( $this->base_url . '/tarif/' . $origin . '/tujuan/' . $destination . $this->api_param . '&jenis=kecamatan' . $cur, $this->default_args );
	}

	/**
	 * Get currency exchange based on IDR
	 *
	 * @return array Currency exchange.
	 */
	public function currency() {
		return $this->remote_get( 'http://api.fixer.io/latest?base=IDR', $this->default_args );
	}

}
