<?php

if ( ! class_exists( 'Tonjoo_License_API' ) ) {

	require_once 'inc/class-tj-encryption.php';
	require_once 'inc/class-tj-logs.php';
	require_once 'inc/plugin-update-checker/plugin-update-checker.php';

	/**
	 * Tonjoo Plugin License Library
	 */
	class Tonjoo_License_API {

		/**
		 * Plugin update name
		 *
		 * @var string
		 */
		public $plugin;

		/**
		 * Site domain
		 *
		 * @var string
		 */
		public $site;

		/**
		 * JSON directory
		 *
		 * @var string
		 */
		public $json_loc;

		/**
		 * License API Server
		 *
		 * @var string
		 */
		public $server;

		/**
		 * Wp_remote_get args
		 *
		 * @var array
		 */
		public $wp_remote_args;

		/**
		 * Logs
		 *
		 * @var object
		 */
		public $logs;

		/**
		 * Constructor
		 *
		 * @param string $plugin      Plugin update name (slug).
		 */
		public function __construct( $plugin = '' ) {
			$this->plugin       = $plugin;
			$this->site         = $this->get_site();
			$this->json_loc     = WP_CONTENT_DIR . '/uploads/json/';
			$this->server       = 'https://tonjoostudio.com';
			$this->wp_remote_args = array(
				'sslverify'   => false,
			);
			$this->logs         = new TJ_Logs( $plugin );
		}

		/**
		 * Activate License
		 *
		 * @param  string $key License key.
		 * @return array       Server response status.
		 */
		public function activate( $key = '' ) {
			if ( empty( $key ) ) {
				return array(
					'status'    => false,
					'data'      => __( 'Error: License key is empty', 'pok' ),
				);
			}

			$url        = $this->server . '/manage/ajax/activateCode?code=' . rawurlencode( $this->encode_parameter( $key ) );
			$request    = wp_remote_get( $url, $this->wp_remote_args );

			if ( is_wp_error( $request ) ) {
				$this->logs->write( 'Activation failed, with error: ' . $request->get_error_message() );
				return array(
					'status'    => false,
					'data'      => __( "Can't connect to server. Please try again.", 'pok' ),
				);
			} else {
				$license_status = json_decode( wp_remote_retrieve_body( $request ) );
				if ( ! $license_status->status ) {
					$this->logs->write( 'Activation failed, with error: ' . $license_status->message );
					if ( 'failed activate, the license is already in use' === $license_status->message ) {
						return array(
							'status'    => false,
							'data'      => __( 'Error: The license is already in use. If you sure this license is belongs to you and no other sites are using this license, please contact us at the forum.', 'pok' ),
						);
					} else {
						return array(
							'status'    => false,
							'data'      => sprintf( __( 'Error: %s. Please try again.', 'pok' ), $license_status->message ),
						);
					}
				}

				$url        = 'https://tonjoostudio.com/manage/ajax/license/?token=' . $key . '&file=' . $this->plugin;
				$request    = wp_remote_get( $url, $this->wp_remote_args );

				if ( is_wp_error( $request ) ) {
					$this->logs->write( 'Activation failed, with error: ' . $request->get_error_message() );
					return array(
						'status'    => false,
						'data'      => sprintf( __( 'Error: %s. Please try again.', 'pok' ), $request->get_error_message() ),
					);
				} else {
					// write json file.
					$this->write_json( wp_json_encode( array_merge( (array) json_decode( wp_remote_retrieve_body( $request ) ), array( 'created' => time() ) ) ) );
					$this->logs->write( 'Activation success' );
					return array(
						'status'    => true,
						'data'      => json_decode( wp_remote_retrieve_body( $request ) ),
						'activation_data' => $license_status,
					);
				}
			}

		}

		/**
		 * Deactivate plugin
		 *
		 * @param  string $key License key.
		 * @return array       Server response status.
		 */
		public function deactivate( $key = '' ) {
			if ( empty( $key ) ) {
				return array(
					'status'    => false,
					'data'      => __( 'Error: License key is empty', 'pok' ),
				);
			}

			$url        = $this->server . '/manage/ajax/deactivateCode?code=' . rawurlencode( $this->encode_parameter( $key ) );
			$request    = wp_remote_get( $url, $this->wp_remote_args );

			if ( is_wp_error( $request ) ) {
				$this->logs->write( 'Deactivation failed, with error: ' . $request->get_error_message() );
				return array(
					'status'    => false,
					'data'      => sprintf( __( 'Error: %s. Please try again.', 'pok' ), $request->get_error_message() ),
				);
			} else {
				$this->logs->write( 'Deactivation success' );
				return array(
					'status'    => true,
					'data'      => json_decode( wp_remote_retrieve_body( $request ) ),
				);
			}
		}

		/**
		 * Get license status
		 *
		 * @param  string  $key   License key.
		 * @param  boolean $debug Debug.
		 * @return array       Server response status.
		 */
		public function status( $key = '', $debug = false ) {
			if ( empty( $key ) ) {
				return array(
					'status'    => false,
					'data'      => __( 'Error: License key is empty', 'pok' ),
				);
			}

			$url        = $this->server . '/manage/api/getStatusLicense/?license=' . $key . '&website=' . $this->site;
			$request    = wp_remote_get( $url, $this->wp_remote_args );

			if ( true === $debug ) {
				return array(
					'url'       => $url,
					'response'  => $request,
				);
			}

			if ( is_wp_error( $request ) ) {
				$this->logs->write( 'Check status failed, with error: ' . $request->get_error_message() );
				return array(
					'status'    => false,
					'data'      => __( "Error: Can't connect to server", 'pok' ),
				);
			} else {
				$response = json_decode( wp_remote_retrieve_body( $request ) );
				if ( isset( $response->error ) && $response->error ) {
					$this->logs->write( 'Check status failed, with error: ' . $response->message );
					return array(
						'status'    => false,
						'data'      => sprintf( __( 'Error: %s. Please try again.', 'pok' ), $response->message ),
					);
				}
				if ( isset( $response->success ) ) {
					return array(
						'status'    => true,
						'data'      => $response,
					);
				}
			}
			return array(
				'status'    => false,
				'data'      => __( "Error: Can't connect to server", 'pok' ),
			);
		}

		/**
		 * Check if plugin get updater
		 *
		 * @param  string  $key   License key.
		 * @param  boolean $debug Debug.
		 * @return bool Get or not.
		 */
		public function update_json( $key = '', $debug = false ) {
			if ( empty( $key ) ) {
				return false;
			}

			$url = $this->server . '/manage/ajax/license/?token=' . $key . '&file=' . $this->plugin;

			if ( true === $debug ) {
				return wp_remote_get( $url, $this->wp_remote_args );
			}

			$status = $this->status( $key );

			if ( isset( $status['status'] ) ) {
				if ( true === $status['status'] ) {

					$request = wp_remote_get( $url, $this->wp_remote_args );

					if ( is_wp_error( $request ) ) {
						$this->logs->write( 'Error when trying fetch updater data, with error: ' . $request->get_error_message() );
						return false;
					} else {
						// write json file.
						$this->write_json( wp_json_encode( array_merge( (array) json_decode( $request['body'] ), array( 'created' => time() ) ) ) );
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * Get Local JSON File
		 *
		 * @param  string $key              License key.
		 * @param  array  $plugin_path      Plugin path.
		 * @param  int    $check_interval   Plugin update check interval.
		 * @return mixed                    Check.
		 */
		public function load_updater( $key = '', $plugin_path = '', $check_interval = 0 ) {
			$json = $this->read_json();
			if ( $json ) {
				$r_json = json_decode( $json );
				if ( ! isset( $r_json->name ) || ( time() - $r_json->created ) > $check_interval ) {
					$this->update_json( $key );
				}
			} else {
				$this->update_json( $key );
			}
			$upload_dir = wp_upload_dir();
			$update = Puc_v4_Factory::buildUpdateChecker( $upload_dir['baseurl'] . '/json/' . $this->plugin . '.json', $plugin_path, $this->plugin );
			return $update;
		}

		/**
		 * Get Site Domain
		 *
		 * @return string Site domain
		 */
		private function get_site() {
			$matches = array();
			preg_match_all( '#^.+?[^\/:](?=[?\/]|$)#', get_site_url(), $matches );
			return $matches[0][0];
		}

		/**
		 * Create and write JSON file
		 *
		 * @param string $content JSON content.
		 */
		private function write_json( $content = '' ) {
			// load WP's File System.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once  ABSPATH . '/wp-admin/includes/file.php' ;
				WP_Filesystem();
			}

			// Create directory if not exists.
			if ( ! $wp_filesystem->exists( $this->json_loc ) ) {
				$wp_filesystem->mkdir( $this->json_loc );
			}

			// write json file.
			$wp_filesystem->put_contents( $this->json_loc . $this->plugin . '.json', $content );
		}

		/**
		 * Read JSON File
		 *
		 * @return mixed JSON Content.
		 */
		public function read_json() {
			// load WP's File System.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once  ABSPATH . '/wp-admin/includes/file.php' ;
				WP_Filesystem();
			}

			// read json file.
			return $wp_filesystem->get_contents( $this->json_loc . $this->plugin . '.json' );
		}

		/**
		 * Encode parameters before send it to server
		 *
		 * @param  string $key License key.
		 * @return string      Encoded parameter.
		 */
		private function encode_parameter( $key = '' ) {
			$parameter_url = array(
				'plugin_update_name'    => $this->plugin,
				'website'               => $this->site,
				'key'                   => $key,
			);
			return TJ_Encryption::encode( wp_json_encode( $parameter_url ) );
		}

	}
}
