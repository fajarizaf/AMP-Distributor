<?php

/**
 * POK API Handler
 */
class POK_API {

	/**
	 * API tonjoo
	 *
	 * @var object
	 */
	protected $tonjoo;

	/**
	 * API rajaongkir
	 *
	 * @var object
	 */
	protected $rajaongkir;

	/**
	 * Active vendor
	 *
	 * @var string
	 */
	protected $vendor;

	/**
	 * Rajaongkir type
	 *
	 * @var string
	 */
	protected $rajaongkir_type;

	/**
	 * Constructor
	 *
	 * @param Tonjoo_License_Handler $license License handler.
	 */
	public function __construct( Tonjoo_License_Handler $license ) {
		global $pok_helper;
		$this->setting          = new POK_Setting();
		$this->vendor           = $this->setting->get( 'base_api' );
		$this->rajaongkir_type  = $this->setting->get( 'rajaongkir_type' );
		$rajaongkir_status      = $this->setting->get( 'rajaongkir_status' );
		if ( $license->is_license_active() ) {
			require_once 'class-pok-api-tonjoo.php';
			$this->tonjoo = new POK_API_Tonjoo( $license->get( 'key' ) );
		}
		if ( $rajaongkir_status[0] ) {
			require_once 'class-pok-api-rajaongkir.php';
			$this->rajaongkir = new POK_API_RajaOngkir( $this->setting->get( 'rajaongkir_key' ), $this->rajaongkir_type );
		}
		$this->helper = $pok_helper;
	}

	/**
	 * Get courier service (only for API tonjoo)
	 *
	 * @return array Courier package
	 */
	public function get_courier_service() {
		$result = array();
		$courier = $this->tonjoo->get_courier_service();
		if ( $courier['status'] ) {
			foreach ( $courier['data'] as $c ) {
				$result[ strtolower( $c->nama ) ] = array();
				if ( ! empty( $c->data ) ) {
					foreach ( $c->data as $t ) {
						if ( 0 === stripos( $t, 'jtr' ) && 'JTR' !== $t ) {
							continue;
						}
						// take out unnecessary services.
						if ( in_array( $t, array( 'R', 'NOT FOUND', 'ERROR' ), true ) ) {
							continue;
						}
						$result[ strtolower( $c->nama ) ][ sanitize_title( $t ) ] = $t;
					}
				}
			}
		}
		return apply_filters( 'pok_courier_services', $result );
	}

	/**
	 * Get province options
	 *
	 * @return array Province options
	 */
	public function get_province() {
		$provinces = array();
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_province();
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				foreach ( $result['data'] as $state ) {
					$provinces[ $state->id ] = $state->nama;
				}
			}
		} else {
			if ( is_null( $this->rajaongkir ) ) {
				return $provinces;
			}
			$result = $this->rajaongkir->get_province();
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				foreach ( $result['data'] as $d ) {
					$provinces[ $d->province_id ] = $d->province;
				};
			}
		}
		return $provinces;
	}

	/**
	 * Get city options based on province id
	 *
	 * @param  integer $province_id Province ID.
	 * @return array                City list.
	 */
	public function get_city( $province_id = 0 ) {
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_city( $province_id );
			if ( $result['status'] ) {
				foreach ( $result['data'] as $d ) {
					$d->type = $d->jenis;
					if ( 'Kota' === $d->type ) {
						$d->nama = 'Kota ' . $d->nama;
					} else {
						$d->nama = 'Kab. ' . $d->nama;
					}
				};
				return $result['data'];
			}
		} else {
			if ( is_null( $this->rajaongkir ) ) {
				return null;
			}
			$result = $this->rajaongkir->get_city( $province_id );
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				foreach ( $result['data'] as $d ) {
					$d->id      = $d->city_id;
					$d->nama    = $d->city_name;
					if ( 'Kota' === $d->type ) {
						$d->nama = 'Kota ' . $d->nama;
					} else {
						$d->nama = 'Kab. ' . $d->nama;
					}
				};
				return $result['data'];
			}
		}
		return null;
	}

	/**
	 * Get single city by id
	 *
	 * @param  integer $city_id City ID.
	 * @return array            City details.
	 */
	public function get_single_city( $city_id = 0 ) {
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_single_city( $city_id );
			if ( $result['status'] ) {
				if ( isset( $result['data'][0] ) ) {
					$result['data'][0]->type = $result['data'][0]->jenis;
					return $result['data'][0];
				}
			}
		} else {
			if ( is_null( $this->rajaongkir ) ) {
				return null;
			}
			$result = $this->rajaongkir->get_single_city( $city_id );
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				$result['data']->id     = $result['data']->city_id;
				$result['data']->nama   = $result['data']->city_name;
				$result['data']->type   = 'Kabupaten' === $result['data']->type ? 'Kab.' : $result['data']->type;
				$result['data']->provinsi = $result['data']->province;
				return $result['data'];
			}
		}
		return null;
	}

	/**
	 * Get all city (only for API rajaongkir)
	 *
	 * @return array City list
	 */
	public function get_all_city() {
		if ( 'rajaongkir' !== $this->vendor || is_null( $this->rajaongkir ) ) {
			return null;
		}
		$result = $this->rajaongkir->get_all_city();
		if ( $result['status'] ) {
			return $result['data'];
		}
		return null;
	}

	/**
	 * Search city (only for API tonjoo)
	 *
	 * @param  string $search Search param.
	 * @return array          City list.
	 */
	public function search_city( $search = '' ) {
		if ( strlen( $search ) < 3 ) {
			return array();
		}
		$result = $this->tonjoo->get_all_city( $search );
		if ( $result['status'] ) {
			foreach ( $result['data'] as $d ) {
				$d->type = $d->jenis;
			};
			return $result['data'];
		}
		return null;
	}

	/**
	 * Get district by the City ID
	 *
	 * @param  integer $city_id City ID.
	 * @return array            District list.
	 */
	public function get_district( $city_id = 0 ) {
		if ( 'nusantara' === $this->vendor ) {
			$result = $this->tonjoo->get_district( $city_id );
			if ( $result['status'] ) {
				return $result['data'];
			}
		} else {
			if ( 'pro' !== $this->rajaongkir_type ) {
				return null;
			}
			$result = $this->rajaongkir->get_district( $city_id );
			if ( $result['status'] && ! empty( $result['data'] ) ) {
				foreach ( $result['data'] as $d ) {
					$d->id      = $d->subdistrict_id;
					$d->nama    = $d->subdistrict_name;
				};
				return $result['data'];
			}
		}
		return null;
	}

	/**
	 * Get all countries
	 *
	 * @return array Contry
	 */
	public function get_all_country() {
		if ( 'rajaongkir' === $this->vendor && 'starter' !== $this->rajaongkir_type ) {
			$result = $this->rajaongkir->get_all_country();
			if ( $result['status'] ) {
				$res = array();
				foreach ( $result['data'] as $d ) {
					// TODO: need filter to sanitize country name (exp: United States (US) -> United States of America).
					$res[ (int) $d->country_id ] = $d->country_name;
				}
				return $res;
			}
		}
		return null;
	}

	/**
	 * Get shipping cost
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination ID (city or district).
	 * @param  integer $weight      Weight in grams.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Costs.
	 */
	public function get_cost( $origin, $destination, $weight, $courier ) {
		if ( 'rajaongkir' === $this->vendor ) { // API rajaongkir.
			if ( is_null( $this->rajaongkir ) ) {
				return array();
			}
			$result = $this->rajaongkir->get_cost( $origin, $destination, $weight, $courier );
			if ( ! empty( $result ) ) {
				$costs = array();
				foreach ( $result as $c ) {
					if ( is_array( $c->costs ) && ! empty( $c->costs ) ) {
						foreach ( $c->costs as $t ) {
							if ( $t->cost[0]->value > 0 ) {
								$costs[] = array(
									'class'         => strtoupper( $c->code ) . ' - ' . $t->service,
									'courier'       => strtolower( str_replace( '&', 'n', $c->code ) ),
									'service'       => $t->service,
									'description'   => $t->description,
									'cost'          => $t->cost[0]->value,
									'time'          => trim( str_replace( ' HARI', '', $t->cost[0]->etd ) ),
								);
							}
						}
					}
				}
				return $costs;
			}
		} else { // API tonjoo.
			$result = $this->tonjoo->get_cost( $origin, $destination, $courier );
			if ( $result['status'] ) {
				$costs = array();
				foreach ( $result['data'] as $c ) {
					if ( ! empty( $c->tarif ) ) {
						foreach ( $c->tarif as $t ) {
							if ( 'JTR' === $t->namaLayanan && $weight >= 10000 ) { // JNE JTR.
								$cost = $t->tarif + ( ( $this->helper->round_weight( $weight / 1000 ) - 10 ) * ( isset( $t->tarif_11_kg ) && ! empty( $t->tarif_11_kg ) ? ( $t->tarif_11_kg - $t->tarif ) : 10000 ) );
							} elseif ( 0 === strpos( $t->namaLayanan, 'JTR' ) ) { // take out other JTR from result.
								continue;
							} elseif ( 'Paketpos Biasa' === $t->namaLayanan && $weight <= 2000 ) { // PaketPos Biasa only if weight>2kg.
								continue;
							} elseif ( 'pos' === strtolower( $c->nama ) ) {
								if ( $weight > 3000 ) {
									$cost = ( $t->tarif * 2 ) + ( $t->tarif_1_kg * ( $this->helper->round_weight( $weight / 1000 ) - 3 ) );
								} else {
									$cost = ($t->tarif * 2) / 3 * $this->helper->round_weight( $weight / 1000 );
								}
							} else {
								$cost = $t->tarif * $this->helper->round_weight( $weight / 1000 );
							}

							// fix etd time not valid.
							$etd = trim( str_replace( 'Hari', '', $t->etd ) );
							if ( false === strpos( $etd, '-' ) ) {
								if ( 0 !== intval( $etd ) ) {
									if ( 0 < floor( intval( $etd ) / 24 ) ) {
										$etd = ceil( intval( $etd ) / 24 );
									}
								}
							}
							$costs[] = array(
								'class'         => $c->nama . ' - ' . $t->namaLayanan,
								'courier'       => strtolower( $c->nama ),
								'service'       => $t->namaLayanan,
								'description'   => $t->jenis,
								'cost'          => $cost,
								'time'          => $etd,
							// "tarif_1_kg"	=> isset($t->tarif_1_kg) ? $t->tarif_1_kg : 0,
							// "tarif"			=> $t->tarif
							);
						}
					}
				}
				return $costs;
			}
		}
	}

	/**
	 * Get international shipping cost (only for API rajaongkir)
	 *
	 * @param  integer $origin      Origin city ID.
	 * @param  integer $destination Destination ID (country).
	 * @param  integer $weight      Weight in grams.
	 * @param  array   $courier     Selected couriers.
	 * @return array                Costs.
	 */
	public function get_cost_international( $origin, $destination, $weight, $courier ) {
		if ( 'rajaongkir' === $this->vendor && ! is_null( $this->rajaongkir ) ) {
			$result = $this->rajaongkir->get_cost_international( $origin, $destination, $weight, $courier );
			if ( ! empty( $result ) ) {
				$costs = array();
				$rates = $this->currency();
				foreach ( $result as $c ) {
					if ( is_array( $c->costs ) && ! empty( $c->costs ) ) {
						foreach ( $c->costs as $t ) {
							if ( 'IDR' !== $t->currency && isset( $rates['data']->value ) ) {
								$t->cost *= $rates['data']->value;
							}
							$costs[] = array(
								'class'         => strtoupper( $c->code ) . ' - ' . $t->service . ( 'yes' === $this->setting->get( 'show_long_description' ) && ! empty( $t->description ) && $t->service !== $t->description ? ' (' . $t->description . ')' : ''),
								'courier'       => strtolower( $c->code ),
								'description'   => '',
								'cost'          => $t->cost,
								'time'          => ! empty( $t->etd ) ? $t->etd : '',
							);
						}
					}
				}
				return $costs;
			}
		}
	}

	/**
	 * Get rajaongkir status
	 *
	 * @param  string $api_key API Key.
	 * @param  string $type    API Type.
	 * @return boolean          Status.
	 */
	public function get_rajaongkir_status( $api_key, $type ) {
		if ( 'pro' === $type ) {
			$base_url   = 'http://pro.rajaongkir.com/api';
		} else {
			$base_url   = 'http://api.rajaongkir.com/' . $type;
		}
		$args = array(
			'timeout'     => 30,
			'redirection' => 5,
			'httpversion' => '1.0',
			'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ),
			'blocking'    => true,
			'headers'     => array(
				'key' => $api_key,
			),
			'cookies'     => array(),
			'body'        => null,
			'compress'    => false,
			'decompress'  => true,
			'sslverify'   => true,
			'stream'      => false,
			'filename'    => null,
		);
		$content = wp_remote_get( $base_url . '/province', $args );
		if ( ! is_wp_error( $content ) ) {
			$body = json_decode( $content['body'] );
			if ( isset( $body->rajaongkir->status->code ) && 200 === $body->rajaongkir->status->code ) {
				return true;
			}
		} else {
			return __( 'can not connect server', 'pok' );
		}
		return false;
	}

	/**
	 * Get currency exchange based on IDR
	 *
	 * @return array Currency list.
	 */
	public function currency() {
		return $this->rajaongkir->get_currency();
	}

}
